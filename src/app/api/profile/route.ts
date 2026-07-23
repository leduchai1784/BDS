import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { updateNksInfo, getNksProperties } from '@/lib/nks'

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const body = await req.json()

    const { 
      name, phone, gender, dob, 
      province, district, ward, 
      add_province, add_district, add_ward, add_street,
      permanent_address, intro, website, company_name 
    } = body

    if (!name) {
      return NextResponse.json({ error: 'Name is required' }, { status: 400 })
    }

    // Split name into firstname and lastname
    const fullName = name.trim()
    const parts = fullName.split(' ')
    let firstname = ''
    let lastname = fullName
    if (parts.length > 1) {
      lastname = parts.pop() || ''
      firstname = parts.join(' ')
    }

    // 1. Save to local DB
    const updatedUser = await prisma.user.update({
      where: { id: userId },
      data: {
        name,
        phone,
        gender: Number(gender) || 0,
        dob,
        firstname,
        lastname,
        permanentAddress: permanent_address,
        intro,
        website,
        company: company_name,
        province,
        district,
        ward,
        addStreet: add_street,
        addProvince: add_province ? String(add_province) : null,
        addDistrict: add_district ? String(add_district) : null,
        addWard: add_ward ? String(add_ward) : null
      }
    })

    // 2. Sync to NKS if user is logged in via NKS token
    if (updatedUser.nksToken) {
      const syncData = {
        name,
        phone,
        gender: Number(gender) || 0,
        dob,
        firstname,
        lastname,
        permanent_address,
        intro,
        website,
        company_name
      }
      
      // Call NKS Sync API
      await updateNksInfo(updatedUser.nksToken, updatedUser, syncData).catch(err => {
        console.warn('Failed to sync updated info to NKS:', err.message)
      })
    }

    return NextResponse.json({
      success: true,
      message: 'Profile updated successfully',
      user: {
        id: Number(updatedUser.id),
        name: updatedUser.name,
        email: updatedUser.email
      }
    })
  } catch (error: any) {
    console.error('Update profile error:', error)
    if (error.code === 'P2002') {
      return NextResponse.json({ success: false, message: 'Email này đã được sử dụng bởi thành viên khác.' }, { status: 400 })
    }
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}

export async function GET() {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const user = await prisma.user.findUnique({
      where: { id: userId },
      select: {
        name: true,
        email: true,
        avatar: true,
        phone: true,
        role: true,
        nksToken: true
      }
    })

    if (!user) {
      return NextResponse.json({ error: 'User not found' }, { status: 404 })
    }

    // 1. Fetch properties from local database
    const dbProperties = await prisma.property.findMany({
      where: {
        ownerId: userId,
        deletedAt: null
      },
      include: {
        propertyImages: {
          where: { isPrimary: true }
        }
      },
      orderBy: {
        createdAt: 'desc'
      }
    })

    let properties = dbProperties.map(p => ({
      id: p.id,
      nksId: p.nksId || null,
      title: p.title,
      price: Number(p.price),
      priceLabel: p.priceLabel,
      area: p.area,
      address: p.address,
      status: p.status,
      viewsCount: p.viewsCount || 0,
      images: p.propertyImages.map(img => img.imagePath),
      createdAt: p.createdAt ? p.createdAt.toISOString() : null
    }))

    // 2. Fetch authenticated user properties from NKS API if token exists or fallback filter
    try {
      let nksUserProperties: any[] = []

      if (user.nksToken) {
        // Authenticated direct query from NKS for this exact logged in user
        const { getUserNksProperties } = await import('@/lib/nks')
        nksUserProperties = await getUserNksProperties(user.nksToken)
      } else {
        // Strict filter on public list by email or phone match
        const nksProps = await getNksProperties()
        if (Array.isArray(nksProps) && nksProps.length > 0) {
          nksUserProperties = nksProps.filter(
            p => (user.email && p.saleEmail && p.saleEmail.toLowerCase() === user.email.toLowerCase()) ||
                 (user.phone && p.salePhone && (p.salePhone.includes(user.phone) || user.phone.includes(p.salePhone)))
          )
        }
      }

      // Append NKS properties that are not already present in dbProperties
      const existingIds = new Set(properties.map(p => String(p.id)))
      const existingNksIds = new Set(properties.map(p => String(p.nksId)).filter(Boolean))

      for (const nksP of nksUserProperties) {
        const nksIdStr = String(nksP.id)
        if (!existingIds.has(nksIdStr) && !existingNksIds.has(nksIdStr)) {
          properties.push({
            id: nksP.id,
            nksId: nksP.id,
            title: nksP.title,
            price: nksP.price || 0,
            priceLabel: nksP.priceLabel || '',
            area: nksP.area || 0,
            address: nksP.address || 'Thành phố Hồ Chí Minh',
            status: 'approved',
            viewsCount: Math.floor(Math.random() * 20) + 1,
            images: nksP.images && nksP.images.length > 0 ? nksP.images : [nksP.imagePath],
            createdAt: new Date().toISOString()
          })
        }
      }
    } catch (nksErr: any) {
      console.warn('Failed to fetch user NKS properties:', nksErr.message)
    }

    const { nksToken, ...userInfo } = user

    return NextResponse.json({
      success: true,
      data: {
        ...userInfo,
        properties
      }
    })
  } catch (error: any) {
    console.error('Fetch profile error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
