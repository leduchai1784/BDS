import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export async function DELETE(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const resolvedParams = await params
    const propertyId = resolvedParams.id

    if (!propertyId) {
      return NextResponse.json({ error: 'Property ID is required' }, { status: 400 })
    }

    const property = await prisma.property.findUnique({
      where: { id: propertyId }
    })

    if (!property) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    if (property.ownerId !== userId) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    // Soft delete
    const updated = await prisma.property.update({
      where: { id: propertyId },
      data: {
        deletedAt: new Date()
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Property soft deleted successfully',
      property: updated
    })
  } catch (error: any) {
    console.error('Delete property error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
