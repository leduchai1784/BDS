import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { randomUUID } from 'crypto'
import { createNksProperty, updateNksProperty, deleteNksProperty, addNksPropertyImage, getNksProperties } from '@/lib/nks'

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

    if (!propertyId) {
      return NextResponse.json({ error: 'Property ID is required' }, { status: 400 })
    }

    // 1. Check local DB by id or nksId
    let property = await prisma.property.findFirst({
      where: {
        OR: [
          { id: propertyId },
          { nksId: propertyId }
        ],
        deletedAt: null
      },
      include: {
        propertyImages: true
      }
    })

    if (property) {
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
    }

    // 2. Fallback check from NKS API if property is from NKS dataset
    try {
      const nksProps = await getNksProperties()
      const nksItem = nksProps.find(item => String(item.id) === String(propertyId))

      if (nksItem) {
        const formatted = {
          id: String(nksItem.id),
          nksId: String(nksItem.id),
          ownerId: userId,
          title: nksItem.title || 'Bất động sản',
          description: nksItem.description || nksItem.title || '',
          propertyType: nksItem.propertyType || 'Căn hộ',
          transactionType: nksItem.isRent ? 'rent' : 'sale',
          price: Number(nksItem.price || 0),
          priceLabel: nksItem.priceLabel || '',
          area: Number(nksItem.area || 0),
          bedroom: Number(nksItem.bedroom || 0),
          bathroom: Number(nksItem.bathroom || 0),
          address: nksItem.address || 'Thành phố Hồ Chí Minh',
          ward: nksItem.district || 'HCMC',
          district: nksItem.district || 'HCMC',
          city: nksItem.city || 'Thành phố Hồ Chí Minh',
          latitude: nksItem.latitude || 10.7769,
          longitude: nksItem.longitude || 106.7009,
          phone: nksItem.salePhone || '0977.758.217',
          status: 'approved',
          direction: nksItem.direction || null,
          furniture: null,
          legal: null,
          deposit: null,
          leaseTerm: null,
          frontage: null,
          roadWidth: null,
          floors: nksItem.floors || null,
          propertyImages: (nksItem.images && nksItem.images.length > 0 ? nksItem.images : [nksItem.imagePath]).map((url: string, index: number) => ({
            id: `nks-img-${index}`,
            propertyId: String(nksItem.id),
            imagePath: url.startsWith('http://') ? url.replace('http://', 'https://') : url,
            isPrimary: index === 0
          }))
        }

        return NextResponse.json({
          success: true,
          property: formatted
        })
      }
    } catch (nksErr: any) {
      console.warn('Failed to query NKS detail fallback:', nksErr.message)
    }

    return NextResponse.json({ error: 'Property not found' }, { status: 404 })
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
    if (!['owner', 'agent', 'admin'].includes(user.role)) {
      return NextResponse.json({ error: 'Chỉ đối tác chủ nhà hoặc môi giới mới có thể chỉnh sửa.' }, { status: 403 })
    }

    const userId = Number(user.id)
    const resolvedParams = await params
    const propertyId = resolvedParams.id

    if (!propertyId) {
      return NextResponse.json({ error: 'Property ID is required' }, { status: 400 })
    }

    const property = await prisma.property.findFirst({
      where: {
        OR: [
          { id: propertyId },
          { nksId: propertyId }
        ],
        deletedAt: null
      }
    })

    if (!property) {
      // If updating an external NKS property that is not in local DB yet
      return NextResponse.json({
        success: true,
        message: 'Cập nhật tin đăng thành công!'
      })
    }

    if (Number(property.ownerId) !== userId && user.role !== 'admin') {
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
      gallery_urls,
      nksProvinceId,
      nksAdministrativeId
    } = body

    if (!title || !property_type || !price || !area || !city || !district || !ward || !address) {
      return NextResponse.json({ error: 'Missing required parameters' }, { status: 400 })
    }

    // Dynamic category resolution
    let cat = await prisma.category.findFirst({
      where: {
        OR: [
          { name: { contains: property_type } },
          { name: { equals: property_type } }
        ]
      }
    })

    if (!cat) {
      const allCategories = await prisma.category.findMany()
      cat = allCategories.find(c => 
        property_type.toLowerCase().includes(c.name.toLowerCase()) ||
        c.name.toLowerCase().includes(property_type.toLowerCase())
      ) || null
    }

    let finalCategoryId = cat?.id
    if (!finalCategoryId) {
      const fallbackCat = await prisma.category.findFirst()
      finalCategoryId = fallbackCat?.id || 1n
    }

    const updatedSlug = slugify(title)

    // Update Property record
    const updatedProperty = await prisma.property.update({
      where: { id: property.id },
      data: {
        categoryId: finalCategoryId,
        title,
        slug: updatedSlug,
        description,
        propertyType: property_type || 'Căn hộ',
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
        direction: direction || null,
        furniture: furniture || null,
        legal: legal || null,
        deposit: deposit ? BigInt(deposit) : null,
        leaseTerm: lease_term || null,
        frontage: frontage || null,
        roadWidth: road_width || null,
        floors: floors ? Number(floors) : null
      }
    })

    // Update Property Images
    if (image_url) {
      await prisma.propertyImage.deleteMany({
        where: { propertyId: property.id }
      })

      const imagesData = []
      imagesData.push({
        id: randomUUID(),
        propertyId: property.id,
        imagePath: image_url,
        isPrimary: true
      })

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
    }

    // Sync updates to NKS API if token exists
    const dbUser = await prisma.user.findUnique({
      where: { id: userId },
      select: { nksToken: true, email: true, phone: true }
    })
    const nksToken = dbUser?.nksToken

    if (nksToken && (property.nksId || propertyId)) {
      const targetNksId = property.nksId || propertyId
      try {
        console.log(`Đang đồng bộ cập nhật NKS ID: ${targetNksId}...`)
        const onsale = purpose === 'sale' ? '1' : '0'
        let rstype = '3'
        if (property_type === 'Nhà phố') rstype = '1'
        else if (property_type === 'Biệt thự') rstype = '2'
        else if (property_type === 'Mặt bằng') rstype = '4'

        const nksPayload = {
          code: property.id,
          title: title,
          slug: updatedSlug,
          featureimg: '',
          geolocation: `${latitude},${longitude}`,
          street_area: ward || '',
          street_number: address || '',
          road_id: '1',
          administrative_id: String(nksAdministrativeId || '12227'),
          province_id: String(nksProvinceId || '79'),
          country_id: '192',
          price: onsale === '1' ? String(price) : '0',
          sqrprice: onsale === '1' && area > 0 ? String(Math.round(Number(price) / area)) : null,
          rentprice: onsale === '0' ? String(price) : '0',
          sqrrentprice: onsale === '0' && area > 0 ? String(Math.round(Number(price) / area)) : null,
          rentdeposit: onsale === '0' && deposit ? String(deposit) : '0',
          total_area: String(area),
          bed: String(bedroom || 0),
          bath: String(bathroom || 0),
          onsale: onsale,
          rstype: rstype,
          description: description || '',
          phone: dbUser?.phone || user.phone || '0932030958',
          email: dbUser?.email || 'nks.mg0001@gmail.com'
        }

        await updateNksProperty(nksToken, { id: targetNksId, ...nksPayload })

        if (image_url) {
          await addNksPropertyImage(nksToken, targetNksId, image_url, 'cover_image')
        }
      } catch (nksErr: any) {
        console.error('Lỗi khi đồng bộ cập nhật NKS:', nksErr.message)
      }
    }

    return NextResponse.json({
      success: true,
      message: 'Cập nhật bất động sản thành công!',
      propertyId: property.id
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

    if (!propertyId) {
      return NextResponse.json({ error: 'Property ID is required' }, { status: 400 })
    }

    const property = await prisma.property.findFirst({
      where: {
        OR: [
          { id: propertyId },
          { nksId: propertyId }
        ]
      }
    })

    const dbUser = await prisma.user.findUnique({
      where: { id: userId },
      select: { nksToken: true }
    })
    const nksToken = dbUser?.nksToken

    if (nksToken && (property?.nksId || propertyId)) {
      const targetNksId = property?.nksId || propertyId
      try {
        console.log(`Đang gọi xóa tin đăng trên NKS ID: ${targetNksId}...`)
        await deleteNksProperty(nksToken, targetNksId)
      } catch (nksErr: any) {
        console.error('Lỗi khi xóa đồng bộ trên NKS:', nksErr.message)
      }
    }

    if (property) {
      await prisma.property.update({
        where: { id: property.id },
        data: {
          deletedAt: new Date()
        }
      })
    }

    return NextResponse.json({
      success: true,
      message: 'Property deleted successfully'
    })
  } catch (error: any) {
    console.error('Delete property error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
