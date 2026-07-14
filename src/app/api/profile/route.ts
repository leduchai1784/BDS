import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { updateNksInfo } from '@/lib/nks'

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
