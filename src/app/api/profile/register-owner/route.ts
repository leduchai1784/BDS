import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const { phone, company_name } = await req.json()

    // 1. Fetch current local user to get NKS Token
    const currentUser = await prisma.user.findUnique({
      where: { id: userId }
    })

    // 2. If NKS Token exists, call NKS API to sync info (without manually sending role_id)
    if (currentUser?.nksToken) {
      try {
        const { updateNksInfo } = require('@/lib/nks')
        await updateNksInfo(currentUser.nksToken, currentUser, {
          company: company_name || undefined,
          phone: phone || undefined
        })
      } catch (nksError) {
        console.error('Failed to sync owner info to NKS API:', nksError)
      }
    }

    // 3. Upgrade local user role to owner in DB
    await prisma.user.update({
      where: { id: userId },
      data: {
        role: 'owner',
        phone: phone || undefined,
        company: company_name || undefined
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Đăng ký đối tác chủ nhà thành công!'
    })
  } catch (error: any) {
    console.error('Register owner error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
