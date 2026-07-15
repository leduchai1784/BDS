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

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const user = session.user as any

    if (user.role !== 'owner') {
      return NextResponse.json({ error: 'Chỉ đối tác chủ nhà mới được đăng tin.' }, { status: 403 })
    }

    const userId = Number(user.id)
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
    let cat = await prisma.category.findFirst({
      where: {
        OR: [
          { name: { contains: property_type } },
          { name: { equals: property_type } }
        ]
      }
    })

    // Bidirectional fallback check (e.g., matching "Căn hộ chung cư" to "Căn hộ")
    if (!cat) {
      const allCategories = await prisma.category.findMany()
      cat = allCategories.find(c => 
        property_type.toLowerCase().includes(c.name.toLowerCase()) ||
        c.name.toLowerCase().includes(property_type.toLowerCase())
      ) || null
    }

    // Dynamic database fallback to prevent properties_category_id_foreign violation
    let finalCategoryId = cat?.id
    if (!finalCategoryId) {
      const fallbackCat = await prisma.category.findFirst()
      finalCategoryId = fallbackCat?.id || 1n
    }

    const slug = slugify(title)

    // 2. Create property record
    const property = await prisma.property.create({
      data: {
        ownerId: userId,
        categoryId: finalCategoryId,
        title,
        slug,
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
        status: 'approved', // auto-approve for now
        direction: direction || null,
        furniture: furniture || null,
        legal: legal || null,
        deposit: deposit ? BigInt(deposit) : null,
        leaseTerm: lease_term || null,
        frontage: frontage || null,
        roadWidth: road_width || null,
        floors: floors ? Number(floors) : null,
        propertyType: property_type
      }
    })

    // 3. Create property images
    const imagesData = []
    
    // Primary cover
    imagesData.push({
      id: randomUUID(),
      propertyId: property.id,
      imagePath: image_url,
      isPrimary: true
    })

    // Gallery images
    if (gallery_urls && Array.isArray(gallery_urls)) {
      for (const url of gallery_urls) {
        if (url) {
          imagesData.push({
            id: randomUUID(),
            propertyId: property.id,
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
      message: 'Đăng tin bất động sản thành công!',
      propertyId: property.id
    })
  } catch (error: any) {
    console.error('Create property API error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
