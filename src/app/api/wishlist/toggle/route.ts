import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { getNksProperties } from '@/lib/nks'

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

    const isUuid = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(property_id)
    if (isUuid) {
      // Check if local property exists
      const propertyExists = await prisma.property.findUnique({
        where: { id: property_id }
      })
      if (!propertyExists) {
        return NextResponse.json({ error: 'Property not found' }, { status: 404 })
      }
    } else {
      // Check if NKS property exists
      const allNks = await getNksProperties()
      const nksExists = allNks.some((p: any) => String(p.id) === String(property_id))
      if (!nksExists) {
        return NextResponse.json({ error: 'Property not found' }, { status: 404 })
      }
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
