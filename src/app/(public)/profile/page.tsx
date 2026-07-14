import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { getNksUserInfo, getNksProperties } from '@/lib/nks'
import ProfilePageClient from '@/components/profile/ProfilePageClient'
import { redirect } from 'next/navigation'

export const dynamic = 'force-dynamic'

export default async function ProfilePage() {
  const session = await auth()
  if (!session?.user?.id) {
    redirect('/login?callbackUrl=/profile')
  }

  const userId = Number(session.user.id)

  // 1. Fetch user from local DB
  const dbUser = await prisma.user.findUnique({
    where: { id: userId }
  })

  if (!dbUser) {
    redirect('/login')
  }

  // Define details structure
  let mergedUser = {
    id: dbUser.id,
    name: dbUser.name,
    email: dbUser.email,
    phone: dbUser.phone || '',
    role: dbUser.role || 'tenant',
    firstname: dbUser.firstname || '',
    lastname: dbUser.lastname || '',
    gender: dbUser.gender !== null ? Number(dbUser.gender) : 0,
    dob: dbUser.dob || '',
    pob: dbUser.pob || '',
    idNumber: dbUser.idNumber || '',
    idDate: dbUser.idDate || '',
    idPlace: dbUser.idPlace || '',
    permanentAddress: dbUser.permanentAddress || '',
    intro: dbUser.intro || '',
    website: dbUser.website || '',
    companyName: dbUser.company || '',
    cccdFront: dbUser.cccdFront || '',
    cccdBack: dbUser.cccdBack || '',
    avatar: dbUser.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(dbUser.name)}&background=0077bb&color=fff`,
    province: dbUser.province || '',
    district: dbUser.district || '',
    ward: dbUser.ward || '',
    addStreet: dbUser.addStreet || '',
    addProvince: dbUser.addProvince || '',
    addDistrict: dbUser.addDistrict || '',
    addWard: dbUser.addWard || '',
    joinDate: dbUser.createdAt ? new Date(dbUser.createdAt).toLocaleDateString('vi-VN') : ''
  }

  // 2. Sync with NKS if nksToken is active
  if (dbUser.nksToken) {
    try {
      const nksRes = await getNksUserInfo(dbUser.nksToken)
      if (nksRes?.success && nksRes.data) {
        const u = nksRes.data
        mergedUser.firstname = u.firstname || mergedUser.firstname
        mergedUser.lastname = u.lastname || mergedUser.lastname
        if (u.name) mergedUser.name = u.name
        if (u.phone) mergedUser.phone = u.phone
        if (u.avatar) mergedUser.avatar = u.avatar
        if (u.gender !== undefined) mergedUser.gender = Number(u.gender)
        if (u.dob) mergedUser.dob = u.dob
        if (u.pob) mergedUser.pob = u.pob
        if (u.id_number) mergedUser.idNumber = u.id_number
        if (u.id_date) mergedUser.idDate = u.id_date
        if (u.id_place) mergedUser.idPlace = u.id_place
        if (u.permanent_address) mergedUser.permanentAddress = u.permanent_address
        if (u.cccd_front) mergedUser.cccdFront = u.cccd_front
        if (u.cccd_back) mergedUser.cccdBack = u.cccd_back
        if (u.add_street) mergedUser.addStreet = u.add_street
        if (u.add_province) mergedUser.province = u.add_province
        if (u.add_district) mergedUser.district = u.add_district
        if (u.add_ward) mergedUser.ward = u.add_ward
      }
    } catch (e) {
      console.warn('Failed to fetch NKS profile details for dashboard sync:', e)
    }
  }

  // 3. Fetch properties owned by user
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

  const propertiesList = dbProperties.map(p => ({
    id: p.id,
    title: p.title,
    priceLabel: p.priceLabel,
    address: p.address,
    status: p.status,
    viewsCount: p.viewsCount,
    createdAt: p.createdAt ? p.createdAt.toISOString() : null
  }))

  // 4. Fetch Tenant Appointments (appointments user made)
  const dbTenantAppointments = await prisma.appointment.findMany({
    where: { userId },
    orderBy: {
      date: 'desc'
    }
  })

  // Fetch unique properties for tenant appointments
  const tenantPropIds = Array.from(new Set(dbTenantAppointments.map(a => a.propertyId)))
    .filter(id => /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(id))
  const tenantPropertiesList = await prisma.property.findMany({
    where: { id: { in: tenantPropIds } }
  })

  const tenantAppointments = dbTenantAppointments.map(app => {
    const p = tenantPropertiesList.find(x => x.id === app.propertyId)
    return {
      id: Number(app.id),
      name: app.name,
      phone: app.phone,
      email: app.email,
      date: app.date.toISOString(),
      time: app.time.toISOString(),
      message: app.message,
      status: app.status,
      rejectReason: app.rejectReason,
      property: {
        id: app.propertyId,
        title: p?.title || 'Bất động sản',
        address: p?.address || 'Liên hệ'
      }
    }
  })

  // 5. Fetch Owner Appointments (appointments made by others on user's properties)
  const userPropertyIds = dbProperties.map(p => p.id)
  const dbOwnerAppointments = await prisma.appointment.findMany({
    where: {
      propertyId: { in: userPropertyIds }
    },
    orderBy: {
      date: 'desc'
    }
  })

  const ownerAppointments = dbOwnerAppointments.map(app => {
    const p = dbProperties.find(x => x.id === app.propertyId)
    return {
      id: Number(app.id),
      name: app.name,
      phone: app.phone,
      email: app.email,
      date: app.date.toISOString(),
      time: app.time.toISOString(),
      message: app.message,
      status: app.status,
      rejectReason: app.rejectReason,
      property: {
        id: app.propertyId,
        title: p?.title || 'Bất động sản',
        address: p?.address || 'Liên hệ'
      }
    }
  })

  // 6. Fetch Wishlist properties details
  const dbWishlist = await prisma.wishlist.findMany({
    where: { userId }
  })

  const rawWishlistPropIds = dbWishlist.map(w => w.propertyId)
  const uuidPropIds = rawWishlistPropIds.filter(id => /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(id))
  const nksPropIds = rawWishlistPropIds.filter(id => !/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(id))

  // Fetch local db wishlist properties
  const dbWishlistProps = await prisma.property.findMany({
    where: {
      id: { in: uuidPropIds },
      deletedAt: null
    },
    include: {
      propertyImages: {
        where: { isPrimary: true }
      }
    }
  })

  const localWishlist = dbWishlistProps.map(p => ({
    id: p.id,
    title: p.title,
    price: Number(p.price),
    priceLabel: p.priceLabel,
    area: p.area,
    bedroom: p.bedroom,
    bathroom: p.bathroom,
    address: p.address,
    district: p.district,
    city: p.city,
    isVip: p.isVip,
    isNew: p.isNew,
    propertyType: p.propertyType,
    imagePath: p.propertyImages?.[0]?.imagePath || null
  }))

  // Fetch NKS API wishlist properties
  let nksWishlist: any[] = []
  if (nksPropIds.length > 0) {
    try {
      const allNks = await getNksProperties()
      const filteredNks = allNks.filter((p: any) => nksPropIds.includes(String(p.id)))
      nksWishlist = filteredNks.map((p: any) => ({
        id: String(p.id),
        title: p.title,
        price: Number(p.price),
        priceLabel: p.priceLabel,
        area: p.area,
        bedroom: p.bedroom,
        bathroom: p.bathroom,
        address: p.address,
        district: p.district,
        city: p.city,
        isVip: p.isVip,
        isNew: p.isNew,
        propertyType: p.propertyType,
        imagePath: p.imagePath || null
      }))
    } catch (e) {
      console.error('Error loading NKS properties for profile wishlist:', e)
    }
  }

  const wishlistProperties = [...localWishlist, ...nksWishlist]

  // 7. Calculate overall stats
  const totalViews = dbProperties.reduce((sum, p) => sum + (p.viewsCount || 0), 0)
  const totalFavorites = await prisma.wishlist.count({
    where: { userId }
  })

  const stats = {
    totalProperties: propertiesList.length,
    totalAppointments: tenantAppointments.length + ownerAppointments.length,
    totalViews,
    totalFavorites
  }

  return (
    <ProfilePageClient
      user={mergedUser}
      properties={propertiesList}
      tenantAppointments={tenantAppointments}
      ownerAppointments={ownerAppointments}
      wishlistProperties={wishlistProperties}
      stats={stats}
    />
  )
}
