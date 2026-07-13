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
    const propertyId = resolvedParams.id
    const { status } = await req.json()

    if (!['pending', 'approved', 'hidden', 'rejected'].includes(status)) {
      return NextResponse.json({ error: 'Trạng thái không hợp lệ' }, { status: 400 })
    }

    const property = await prisma.property.findUnique({
      where: { id: propertyId }
    })

    if (!property) {
      return NextResponse.json({ error: 'Tin đăng không tồn tại' }, { status: 404 })
    }

    await prisma.property.update({
      where: { id: propertyId },
      data: { status }
    })

    const statusLabels: Record<string, string> = {
      approved: 'Duyệt đăng tin',
      hidden: 'Ẩn tin đăng',
      rejected: 'Từ chối tin đăng',
      pending: 'Chờ duyệt'
    }

    return NextResponse.json({
      success: true,
      message: `Cập nhật trạng thái thành công: ${statusLabels[status]}`
    })
  } catch (error: any) {
    console.error('Update property status API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
