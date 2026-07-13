import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import bcrypt from 'bcryptjs'
import { updateNksPassword } from '@/lib/nks'

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const { current_password, new_password } = await req.json()

    if (!current_password || !new_password) {
      return NextResponse.json({ error: 'Missing parameters' }, { status: 400 })
    }

    // Get the user
    const user = await prisma.user.findUnique({
      where: { id: userId }
    })

    if (!user) {
      return NextResponse.json({ error: 'User not found' }, { status: 404 })
    }

    // Verify local password
    const isMatch = await bcrypt.compare(current_password, user.password)
    if (!isMatch) {
      return NextResponse.json({ error: 'Mật khẩu cũ không chính xác' }, { status: 400 })
    }

    // 1. Sync password to NKS if user has NKS Token
    if (user.nksToken) {
      const nksResult = await updateNksPassword(user.nksToken, current_password, new_password)
      if (!nksResult.success) {
        return NextResponse.json({ 
          success: false, 
          message: nksResult.message || 'Không thể cập nhật mật khẩu lên hệ thống NKS.' 
        }, { status: 400 })
      }
    }

    // 2. Hash and save new password locally
    const hashedPassword = await bcrypt.hash(new_password, 10)
    await prisma.user.update({
      where: { id: userId },
      data: { password: hashedPassword }
    })

    return NextResponse.json({
      success: true,
      message: 'Password changed successfully'
    })
  } catch (error: any) {
    console.error('Password API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
