import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { randomUUID } from 'crypto'
import { createNksProperty, updateNksProperty, deleteNksProperty, addNksPropertyImage } from '@/lib/nks'

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
    if (!['owner', 'agent', 'admin'].includes(user.role)) {
      return NextResponse.json({ error: 'Chỉ đối tác chủ nhà hoặc môi giới mới có thể chỉnh sửa.' }, { status: 403 })
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
      gallery_urls,
      nksProvinceId,
      nksAdministrativeId
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

    // 4. Đồng bộ Cập nhật lên NKS
    const dbUser = await prisma.user.findUnique({
      where: { id: userId },
      select: { nksToken: true, email: true, phone: true }
    })
    const nksToken = dbUser?.nksToken

    if (nksToken) {
      try {
        const onsale = purpose === 'sale' ? '1' : '0'
        let rstype = '3' // Căn hộ
        if (property_type === 'Nhà phố') rstype = '1'
        else if (property_type === 'Biệt thự') rstype = '2'
        else if (property_type === 'Mặt bằng') rstype = '4'

        const nksPayload = {
          title: title,
          slug: updatedProperty.slug,
          featureimg: '', // NKS tự đồng bộ
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
          phone: dbUser.phone || user.phone || '0932030958',
          email: dbUser.email || 'nks.mg0001@gmail.com'
        }

        if (property.nksId) {
          console.log(`Đang gọi cập nhật NKS tin đăng ID: ${property.nksId}...`)
          await updateNksProperty(nksToken, {
            id: Number(property.nksId),
            ...nksPayload
          })

          // Đồng bộ thêm ảnh mới (vì các ảnh cũ đã bị thay thế hoặc giữ nguyên tùy theo giao diện,
          // chúng ta cứ upload lại album ảnh của tin để đảm bảo NKS có đủ)
          if (image_url) {
            await addNksPropertyImage(nksToken, property.nksId, image_url, 'cover_image')
          }
          if (gallery_urls && Array.isArray(gallery_urls)) {
            for (const url of gallery_urls) {
              if (url) {
                await addNksPropertyImage(nksToken, property.nksId, url, 'gallery_image')
              }
            }
          }
        } else {
          // Nếu tin local chưa được sync sang NKS, tiến hành tạo mới trên NKS
          console.log('Tin đăng chưa có NKS ID, đang tạo mới trên NKS...')
          const nksCreateResult = await createNksProperty(nksToken, {
            code: property.id,
            ...nksPayload
          })
          if (nksCreateResult.success && nksCreateResult.data?.id) {
            const nksItemId = String(nksCreateResult.data.id)
            await prisma.property.update({
              where: { id: propertyId },
              data: { nksId: nksItemId }
            })

            // Tải ảnh đại diện và album ảnh phụ
            if (image_url) {
              await addNksPropertyImage(nksToken, nksItemId, image_url, 'cover_image')
            }
            if (gallery_urls && Array.isArray(gallery_urls)) {
              for (const url of gallery_urls) {
                if (url) {
                  await addNksPropertyImage(nksToken, nksItemId, url, 'gallery_image')
                }
              }
            }
          }
        }
      } catch (nksErr: any) {
        console.error('Lỗi khi cập nhật đồng bộ NKS:', nksErr.message)
      }
    }

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

    // 4. Đồng bộ xóa trên NKS
    const dbUser = await prisma.user.findUnique({
      where: { id: userId },
      select: { nksToken: true }
    })
    const nksToken = dbUser?.nksToken

    if (nksToken && property.nksId) {
      try {
        console.log(`Đang gọi xóa tin đăng trên NKS ID: ${property.nksId}...`)
        await deleteNksProperty(nksToken, property.nksId)
      } catch (nksErr: any) {
        console.error('Lỗi khi xóa đồng bộ trên NKS:', nksErr.message)
      }
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
