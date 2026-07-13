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
    const { property_id } = await req.json()

    if (!property_id) {
      return NextResponse.json({ error: 'Missing property_id' }, { status: 400 })
    }

    // Check if property exists
    const propertyExists = await prisma.property.findUnique({
      where: { id: property_id }
    })

    if (!propertyExists) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    // Toggle logic using unique constraint
    const existing = await prisma.wishlist.findUnique({
      where: {
        userId_propertyId: {
          userId,
          propertyId: property_id
        }
      }
    })

    let isFavorite = false

    if (existing) {
      await prisma.wishlist.delete({
        where: {
          id: existing.id
        }
      })
      isFavorite = false
    } else {
      await prisma.wishlist.create({
        data: {
          userId,
          propertyId: property_id
        }
      })
      isFavorite = true
    }

    return NextResponse.json({
      success: true,
      is_favorite: isFavorite
    })
  } catch (error: any) {
    console.error('Wishlist toggle error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
