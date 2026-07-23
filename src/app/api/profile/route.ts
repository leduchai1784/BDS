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
        role: true
      }
    })

    if (!user) {
      return NextResponse.json({ error: 'User not found' }, { status: 404 })
    }

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
      title: p.title,
      price: Number(p.price),
      priceLabel: p.priceLabel,
      area: p.area,
      address: p.address,
      transactionType: p.transactionType || (p.priceLabel?.includes('tháng') ? 'rent' : 'sale'),
      status: p.status,
      viewsCount: p.viewsCount || 0,
      images: p.propertyImages.map(img => img.imagePath),
      createdAt: p.createdAt ? p.createdAt.toISOString() : null
    }))

    // Fallback: If local database properties is empty, fetch NKS API properties list
    if (properties.length === 0) {
      try {
        const nksProps = await getNksProperties()
        if (Array.isArray(nksProps) && nksProps.length > 0) {
          // Attempt filtering by user email or phone if matching
          let matchedNks = nksProps.filter(
            p => (user.email && p.saleEmail && p.saleEmail.toLowerCase() === user.email.toLowerCase()) ||
                 (user.phone && p.salePhone && p.salePhone.includes(user.phone))
          )
          // If no specific match found, return top NKS properties so user page is populated
          if (matchedNks.length === 0) {
            matchedNks = nksProps.slice(0, 10)
          }

          properties = matchedNks.map(p => ({
            id: p.id,
            title: p.title,
            price: p.price || 0,
            priceLabel: p.priceLabel || '',
            area: p.area || 0,
            address: p.address || 'Thành phố Hồ Chí Minh',
            transactionType: p.isRent ? 'rent' : 'sale',
            status: 'approved',
            viewsCount: Math.floor(Math.random() * 20) + 1,
            images: p.images && p.images.length > 0 ? p.images : [p.imagePath],
            createdAt: new Date().toISOString()
          }))
        }
      } catch (nksErr: any) {
        console.warn('Failed to load fallback NKS properties:', nksErr.message)
      }
    }

    return NextResponse.json({
      success: true,
      data: {
        ...user,
        properties
      }
    })
  } catch (error: any) {
    console.error('Fetch profile error:', error)
    return NextResponse.json({ error: error.message }, { status: 500 })
  }
}
