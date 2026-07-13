import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { randomUUID } from 'crypto'

function slugify(text: string): string {
  return text
    .toString()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') // remove vietnamese diacritics
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim() + '-' + Date.now()
}

function formatPriceLabel(price: number, purpose: 'rent' | 'sale'): string {
  const suffix = purpose === 'sale' ? '' : '/tháng'
  if (price >= 1000000000) {
    return (price / 1000000000).toFixed(1).replace(/\.0$/, '') + ' tỷ' + suffix
  } else if (price >= 1000000) {
    return (price / 1000000).toFixed(1).replace(/\.0$/, '') + ' triệu' + suffix
  }
  return price.toLocaleString('vi-VN') + 'đ' + suffix
}

export async function GET(
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

    if (!propertyId || !/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(propertyId)) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    const property = await prisma.property.findUnique({
      where: { id: propertyId },
      include: {
        propertyImages: true
      }
    })

    if (!property || property.deletedAt) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    // Permit access if admin or property owner
    const isOwner = Number(property.ownerId) === userId
    const isAdmin = (session.user as any).role === 'admin'

    if (!isOwner && !isAdmin) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    // Convert BigInt values to safe representation
    const formatted = {
      ...property,
      price: Number(property.price),
      deposit: property.deposit ? Number(property.deposit) : null,
      ownerId: Number(property.ownerId),
      projectId: property.projectId ? Number(property.projectId) : null,
      categoryId: property.categoryId ? Number(property.categoryId) : null
    }

    return NextResponse.json({
      success: true,
      property: formatted
    })
  } catch (error: any) {
    console.error('Get property detail API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}

export async function PUT(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const user = session.user as any
    if (user.role !== 'owner') {
      return NextResponse.json({ error: 'Chỉ đối tác chủ nhà mới có thể chỉnh sửa.' }, { status: 403 })
    }

    const userId = Number(user.id)
    const resolvedParams = await params
    const propertyId = resolvedParams.id

    if (!propertyId || !/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(propertyId)) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    const property = await prisma.property.findUnique({
      where: { id: propertyId }
    })

    if (!property || property.deletedAt) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
    }

    if (Number(property.ownerId) !== userId) {
      return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
    }

    const body = await req.json()
    const {
      purpose,
      title,
      property_type,
      price,
      area,
      bedroom,
      bathroom,
      frontage,
      road_width,
      floors,
      deposit,
      lease_term,
      direction,
      legal,
      furniture,
      description,
      city,
      district,
      ward,
      address,
      latitude,
      longitude,
      image_url,
      gallery_urls
    } = body

    if (!title || !property_type || !price || !area || !city || !district || !ward || !address) {
      return NextResponse.json({ error: 'Missing required parameters' }, { status: 400 })
    }

    // 1. Dynamic category detection
    const cat = await prisma.category.findFirst({
      where: {
        name: {
          contains: property_type
        }
      }
    })

    // 2. Update property record
    const updatedProperty = await prisma.property.update({
      where: { id: propertyId },
      data: {
        categoryId: cat?.id || 1,
        title,
        slug: title !== property.title ? slugify(title) : property.slug,
        description,
        price: BigInt(price),
        priceLabel: formatPriceLabel(price, purpose),
        area: Number(area),
        bedroom: Number(bedroom) || 0,
        bathroom: Number(bathroom) || 0,
        address,
        ward,
        district,
        city,
        latitude: Number(latitude),
        longitude: Number(longitude),
        phone: user.phone || '0977.758.217',
        direction: direction || null,
        furniture: furniture || null,
        legal: legal || null,
        deposit: deposit ? BigInt(deposit) : null,
        leaseTerm: lease_term || null,
        frontage: frontage || null,
        roadWidth: road_width || null,
        floors: floors ? Number(floors) : null,
        propertyType: property_type,
        transactionType: purpose === 'sale' ? 'sale' : 'rent'
      }
    })

    // 3. Replace property images
    await prisma.propertyImage.deleteMany({
      where: { propertyId }
    })

    const imagesData = []
    
    // Primary cover
    imagesData.push({
      id: randomUUID(),
      propertyId,
      imagePath: image_url,
      isPrimary: true
    })

    // Gallery images
    if (gallery_urls && Array.isArray(gallery_urls)) {
      for (const url of gallery_urls) {
        if (url) {
          imagesData.push({
            id: randomUUID(),
            propertyId,
            imagePath: url,
            isPrimary: false
          })
        }
      }
    }

    await prisma.propertyImage.createMany({
      data: imagesData
    })

    return NextResponse.json({
      success: true,
      message: 'Cập nhật tin đăng thành công!',
      propertyId
    })
  } catch (error: any) {
    console.error('Update property API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}

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

    if (!propertyId || !/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(propertyId)) {
      return NextResponse.json({ error: 'Property not found' }, { status: 404 })
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
