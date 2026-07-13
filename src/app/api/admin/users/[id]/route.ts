import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export async function DELETE(
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
      return NextResponse.json({ error: 'Bạn không thể tự xóa tài khoản của chính mình.' }, { status: 400 })
    }

    const user = await prisma.user.findUnique({
      where: { id: targetUserId }
    })

    if (!user) {
      return NextResponse.json({ error: 'Thành viên không tồn tại' }, { status: 404 })
    }

    await prisma.user.delete({
      where: { id: targetUserId }
    })

    return NextResponse.json({
      success: true,
      message: 'Xóa tài khoản thành viên thành công!'
    })
  } catch (error: any) {
    console.error('Delete user API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
