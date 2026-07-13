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
    const propertyId = resolvedParams.id

    const property = await prisma.property.findUnique({
      where: { id: propertyId }
    })

    if (!property) {
      return NextResponse.json({ error: 'Tin đăng không tồn tại' }, { status: 404 })
    }

    // Hard delete
    await prisma.property.delete({
      where: { id: propertyId }
    })

    return NextResponse.json({
      success: true,
      message: 'Xóa tin đăng bất động sản thành công!'
    })
  } catch (error: any) {
    console.error('Delete property API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
