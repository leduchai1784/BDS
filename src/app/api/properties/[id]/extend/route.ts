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

    if (!propertyId) {
      return NextResponse.json({ error: 'Property ID is required' }, { status: 400 })
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

    // Extend: push to top by touching createdAt and updatedAt
    const now = new Date()
    const updated = await prisma.property.update({
      where: { id: propertyId },
      data: {
        createdAt: now,
        updatedAt: now
      }
    })

    return NextResponse.json({
      success: true,
      message: 'Extend property success',
      property: updated
    })
  } catch (error: any) {
    console.error('Extend property error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
