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

    // Upgrade user role to owner
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
