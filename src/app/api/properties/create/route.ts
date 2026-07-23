import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { randomUUID } from 'crypto'
import { createNksProperty, addNksPropertyImage } from '@/lib/nks'

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

    if (!['owner', 'agent', 'admin'].includes(user.role)) {
      return NextResponse.json({ error: 'Chỉ đối tác chủ nhà hoặc môi giới mới được đăng tin.' }, { status: 403 })
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
      gallery_urls,
      nksProvinceId,
      nksAdministrativeId
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
        phone: user.phone || '0977.758.217',
        status: 'approved', // auto-approve for now
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

    // 4. Đồng bộ NKS nếu tài khoản có nksToken
    const dbUser = await prisma.user.findUnique({
      where: { id: userId },
      select: { nksToken: true, email: true, phone: true }
    })
    const nksToken = dbUser?.nksToken

    if (nksToken) {
      try {
        console.log('Bắt đầu đồng bộ tin đăng mới sang NKS API...')
        const onsale = purpose === 'sale' ? '1' : '0'
        
        let rstype = '3' // Mặc định: Căn hộ
        if (property_type === 'Nhà phố') rstype = '1'
        else if (property_type === 'Biệt thự') rstype = '2'
        else if (property_type === 'Mặt bằng') rstype = '4'

        const nksPayload = {
          code: property.id, // UUID local làm code NKS
          title: title,
          slug: slug,
          featureimg: '', // Để trống, ảnh bìa sẽ được tải lên CDN của NKS qua rsitemimg/add
          geolocation: `${latitude},${longitude}`,
          street_area: ward || '',
          street_number: address || '',
          road_id: '1',
          administrative_id: String(nksAdministrativeId || '12227'),
          province_id: String(nksProvinceId || '79'),
          country_id: '192', // Việt Nam
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

        const nksCreateResult = await createNksProperty(nksToken, nksPayload)
        
        if (nksCreateResult.success && nksCreateResult.data?.id) {
          const nksItemId = String(nksCreateResult.data.id)
          console.log(`Đăng tin NKS thành công. NKS ID: ${nksItemId}`)

          // Cập nhật nksId local
          await prisma.property.update({
            where: { id: property.id },
            data: { nksId: nksItemId }
          })

          // Đồng bộ Ảnh bìa (Primary Cover)
          if (image_url) {
            console.log('Đang đồng bộ ảnh đại diện chính lên NKS...')
            await addNksPropertyImage(nksToken, nksItemId, image_url, 'cover_image')
          }

          // Đồng bộ Album ảnh phụ (Gallery)
          if (gallery_urls && Array.isArray(gallery_urls)) {
            console.log('Đang đồng bộ album ảnh phụ lên NKS...')
            for (const url of gallery_urls) {
              if (url) {
                await addNksPropertyImage(nksToken, nksItemId, url, 'gallery_image')
              }
            }
          }
        } else {
          console.warn('Không thể đồng bộ tin đăng NKS:', nksCreateResult.message)
        }
      } catch (nksErr: any) {
        console.error('Lỗi trong quá trình đồng bộ NKS API:', nksErr.message)
        // Không block phản hồi thành công local nếu NKS API bị lỗi
      }
    }

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
