import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

export async function POST(
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
    const { status } = await req.json() // e.g. 'approved' or 'hidden'

    if (!propertyId || !status) {
      return NextResponse.json({ error: 'Missing required parameters' }, { status: 400 })
    }

    const property = await prisma.property.findUnique({
      where: { id: propertyId }
    })

    if (!property) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    if (Number(property.ownerId) !== userId) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    const updated = await prisma.property.update({
      where: { id: propertyId },
      data: { status }
    })

    return NextResponse.json({
      success: true,
      message: 'Property status updated successfully',
      property: updated
    })
  } catch (error: any) {
    console.error('Update status error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
