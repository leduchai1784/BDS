import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export async function POST(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id || session.user.role !== 'admin') {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const resolvedParams = await params
    const targetUserId = BigInt(resolvedParams.id)
    const currentAdminId = BigInt(session.user.id)

    if (targetUserId === currentAdminId) {
      return NextResponse.json({ error: 'Bạn không thể tự khóa tài khoản của chính mình.' }, { status: 400 })
    }

    const user = await prisma.user.findUnique({
      where: { id: targetUserId }
    })

    if (!user) {
      return NextResponse.json({ error: 'Thành viên không tồn tại' }, { status: 404 })
    }

    const newStatus = user.status === 'locked' ? 'active' : 'locked'

    await prisma.user.update({
      where: { id: targetUserId },
      data: { status: newStatus }
    })

    return NextResponse.json({
      success: true,
      message: newStatus === 'locked' ? 'Khóa tài khoản thành công!' : 'Mở khóa tài khoản thành công!',
      status: newStatus
    })
  } catch (error: any) {
    console.error('Toggle user status API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
