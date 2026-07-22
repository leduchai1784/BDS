import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { getNksUserInfo, getNksProperties } from '@/lib/nks'
import ProfilePageClient from '@/components/profile/ProfilePageClient'
import { redirect } from 'next/navigation'

async function fetchExternalLeads(): Promise<any[]> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const token = process.env.SCRM_API_TOKEN || '01KWKATNQGB5TWXYDPJ671X3X1'
    const apiUrl = process.env.SCRM_API_URL || 'https://sdata.io.vn/wp-json/scrmai/v1'

    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 10000)

    const response = await fetch(`${apiUrl}/leads`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({}),
      signal: controller.signal,
      cache: 'no-store'
    })

    clearTimeout(timeoutId)

    if (response.ok) {
      const data = await response.json()
      if (data?.success && Array.isArray(data.data)) {
        const rawLeads = data.data
        const mappedLeads: any[] = []
        for (const lead of rawLeads) {
          const acf = lead.acf || {}
          const createdAt = lead.created_at || 'Vừa xong'
          
          let status = 'new'
          if (lead.id % 5 === 0) {
            status = 'closed'
          } else if (lead.id % 4 === 0) {
            status = 'qualified'
          } else if (lead.id % 3 === 0) {
            status = 'contacting'
          }
          
          const demand = acf.demand || ''
          
          let demandType = 'rent'
          if (/mua|bán|bds|đất|app|python/i.test(demand)) {
            demandType = 'sale'
          }
          
          let category = 'Bất động sản'
          if (/chung cư|căn hộ/i.test(demand)) {
            category = 'Căn hộ chung cư'
          } else if (/nhà|phố/i.test(demand)) {
            category = 'Nhà riêng / Phố'
          } else if (/phòng|trọ/i.test(demand)) {
            category = 'Phòng trọ / Mini'
          } else if (/python|học/i.test(demand)) {
            category = 'Khóa học / Đào tạo'
          } else if (/app/i.test(demand)) {
            category = 'Phần mềm / Công nghệ'
          }
          
          let budgetMin = 0
          let budgetMax = 0
          const matches = demand.match(/(\d+)\s*(tr|triệu|tỷ)/i)
          if (matches) {
            const val = parseInt(matches[1])
            budgetMin = Math.max(1, val - 2)
            budgetMax = val + 2
          } else {
            budgetMin = demandType === 'rent' ? 5 : 2
            budgetMax = demandType === 'rent' ? 15 : 6
          }
          
          // Detect source: check note/demand first for chatbot (since chatbot also uses Website source term)
          let source = 'unknown'
          const noteText = (acf.note || '').toLowerCase()
          const demandText2 = (demand || acf.demand || '').toLowerCase()
          const titleText = (lead.title || '').toLowerCase()
          
          if (noteText.includes('chatbot') || noteText.includes('ai assistant') || noteText.includes('ai chatbot') || titleText.includes('chatbot') || demandText2.includes('chatbot')) {
            source = 'chatbot'
          } else if (noteText.includes('lịch hẹn') || demandText2.includes('đặt lịch hẹn') || noteText.includes('lịch xem nhà')) {
            source = 'web'
          } else if (acf.source && typeof acf.source === 'object') {
            const slug = acf.source.slug || ''
            if (slug === 'website') {
              source = 'web'
            } else if (slug === 'facebook') {
              source = 'facebook'
            } else if (slug === 'relationship') {
              source = 'referral'
            }
          }
          
          let chatHistory: any[] = []
          if (acf.phone) {
            chatHistory = [
              { role: 'user', content: 'Tôi muốn tìm hiểu thông tin và đăng ký nhu cầu: ' + demand },
              { role: 'assistant', content: 'Chào bạn! Tôi là trợ lý ảo hỗ trợ ghi nhận thông tin. Để tiện xưng hô và liên hệ tư vấn chi tiết hơn, bạn vui lòng cung cấp tên và số điện thoại nhé.' },
              { role: 'user', content: 'Tôi là ' + (acf.name || 'Khách') + ', số điện thoại ' + acf.phone },
              { role: 'assistant', content: 'Cảm ơn anh/chị ' + (acf.name || 'Khách') + '! Tôi đã ghi nhận nhu cầu của anh/chị về: "' + demand + '". Thông tin liên hệ là ' + acf.phone + (acf.email ? ' - Email: ' + acf.email : '') + '. Tư vấn viên sẽ gọi điện hỗ trợ anh/chị ngay nhé!' }
            ]
          }
          
          let matchedProperties: any[] = []
          if (demandType === 'rent') {
            matchedProperties = [
              { title: 'Căn hộ Hà Đô Centrosa 2PN Full nội thất', price: '14.5 Triệu/tháng', area: '78m²', location: 'Đường 3/2, Quận 10' },
              { title: 'Chung cư Rivera Park 2PN tiện ích cao cấp', price: '13.0 Triệu/tháng', area: '74m²', location: 'Thành Thái, Quận 10' }
            ]
          } else {
            matchedProperties = [
              { title: 'Nhà trệt 2 lầu hẻm xe hơi Lê Quang Định', price: '4.2 Tỷ', area: '45m²', location: 'Lê Quang Định, Bình Thạnh' }
            ]
          }
          
          let displayName = acf.name || null
          if (!displayName || displayName === '-') {
            displayName = lead.title || '-'
          }
          if (!displayName || displayName === '-') {
            displayName = 'Khách hàng #' + lead.id
          }
          
          mappedLeads.push({
            id: String(lead.id || Math.random()),
            name: displayName,
            phone: acf.phone || '',
            email: acf.email || '',
            zalo: acf.zalo || '',
            company: acf.company || '',
            position: acf.position || '',
            comsize: acf.comsize || '',
            demand: acf.demand || '',
            demand_type: demandType,
            preferred_category: category,
            budget_min: budgetMin,
            budget_max: budgetMax,
            preferred_location: demand || 'Chưa cập nhật',
            source: source,
            created_at: createdAt,
            notes: acf.note || '',
            status: status,
            match_score: 90 + (lead.id % 10),
            chat_history: chatHistory,
            matched_properties: matchedProperties
          })
        }
        return mappedLeads
      }
    }
  } catch (e: any) {
    console.error('Failed to fetch external leads:', e.message)
  }
  return []
}


export const dynamic = 'force-dynamic'

export default async function ProfilePage() {
  const session = await auth()
  if (!session?.user?.id) {
    redirect('/login?callbackUrl=/profile')
  }

  const userId = Number(session.user.id)
  const userEmail = session.user.email || ''
  const nksToken = (session.user as any).nksToken || ''

  // 1. Fetch user from local DB (by id first, then by email as fallback)
  let dbUser = await prisma.user.findUnique({
    where: { id: userId }
  })

  if (!dbUser && userEmail) {
    dbUser = await prisma.user.findUnique({
      where: { email: userEmail }
    })
  }

  // If still not in local DB but has NKS token, auto-create
  if (!dbUser && userEmail && nksToken) {
    try {
      const bcrypt = await import('bcryptjs')
      dbUser = await prisma.user.create({
        data: {
          email: userEmail,
          name: session.user.name || 'NKS Agent',
          avatar: session.user.image || null,
          role: (session.user as any).role || 'agent',
          status: 'active',
          nksToken: nksToken,
          password: await bcrypt.hash('nks_synced_user', 12),
        }
      })
    } catch (e: any) {
      console.error('Failed to auto-create user in profile page:', e.message)
    }
  }

  if (!dbUser) {
    redirect('/login')
  }

  // Define details structure
  let mergedUser = {
    id: Number(dbUser.id),
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
    joinDate: dbUser.createdAt ? new Date(dbUser.createdAt).toLocaleDateString('vi-VN') : '',
    rslogan: dbUser.rslogan || '',
    rsbio: dbUser.rsbio || '',
    rsexperience: dbUser.rsexperience || '',
    rslocation: dbUser.rslocation || '',
    rsachievement: dbUser.rsachievement || '',
    rscertificate: dbUser.rscertificate || '',
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
        if (u.rslogan) mergedUser.rslogan = u.rslogan
        if (u.rsbio) mergedUser.rsbio = u.rsbio
        if (u.rsexperience) mergedUser.rsexperience = u.rsexperience
        if (u.rslocation) mergedUser.rslocation = u.rslocation
        if (u.rsachievement) mergedUser.rsachievement = u.rsachievement
        if (u.rscertificate) mergedUser.rscertificate = u.rscertificate
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
    imagePath: p.propertyImages?.[0]?.imagePath || null,
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

  let stats = {
    totalProperties: propertiesList.length,
    totalAppointments: tenantAppointments.length + ownerAppointments.length,
    totalViews,
    totalFavorites
  }

  let adminData: any = {}

  if (dbUser.role === 'admin') {
    // Fetch all categories
    const categories = await prisma.category.findMany({
      orderBy: { name: 'asc' }
    })

    // Fetch all users
    const adminUsers = await prisma.user.findMany({
      orderBy: { createdAt: 'desc' }
    })

    // Fetch all properties
    const adminProperties = await prisma.property.findMany({
      where: { deletedAt: null },
      include: {
        category: true,
        owner: true,
        propertyImages: {
          where: { isPrimary: true }
        }
      },
      orderBy: { createdAt: 'desc' }
    })

    // Fetch all appointments
    const adminAppointments = await prisma.appointment.findMany({
      orderBy: { date: 'desc' }
    })

    // Fetch property titles for admin appointments
    const appPropIds = Array.from(new Set(adminAppointments.map(a => a.propertyId)))
      .filter(id => /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(id))
    const appPropertiesList = await prisma.property.findMany({
      where: { id: { in: appPropIds } }
    })

    // Fetch WordPress leads
    const leads = await fetchExternalLeads()

    // Format all admin data
    const formattedAdminUsers = adminUsers.map(u => ({
      id: Number(u.id),
      name: u.name,
      email: u.email,
      phone: u.phone || '',
      role: u.role,
      status: u.status,
      avatar: u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=0077bb&color=fff`
    }))

    const formattedAdminProperties = adminProperties.map(p => ({
      id: p.id,
      title: p.title,
      price: Number(p.price),
      priceLabel: p.priceLabel,
      area: p.area,
      location: p.address,
      status: p.status,
      category: p.category ? { id: Number(p.category.id), name: p.category.name } : null,
      owner: p.owner ? { id: Number(p.owner.id), name: p.owner.name } : null,
      imagePath: p.propertyImages?.[0]?.imagePath || null
    }))

    const formattedAdminAppointments = adminAppointments.map(app => {
      const p = appPropertiesList.find(x => x.id === app.propertyId)
      return {
        id: Number(app.id),
        name: app.name,
        phone: app.phone,
        email: app.email,
        date: app.date.toISOString(),
        time: app.time.toISOString(),
        status: app.status,
        property: p ? { id: p.id, title: p.title } : null
      }
    })

    adminData = {
      adminUsers: formattedAdminUsers,
      adminProperties: formattedAdminProperties,
      adminAppointments: formattedAdminAppointments,
      categories: categories.map(c => ({ id: Number(c.id), name: c.name, slug: c.slug })),
      leads: leads
    }

    // Override stats for admin: count of properties, appointments, and leads
    stats = {
      totalProperties: adminProperties.length,
      totalAppointments: adminAppointments.length,
      totalViews: 0,
      totalFavorites: leads.length
    }
  }

  return (
    <ProfilePageClient
      user={mergedUser}
      properties={propertiesList}
      tenantAppointments={tenantAppointments}
      ownerAppointments={ownerAppointments}
      wishlistProperties={wishlistProperties}
      stats={stats}
      adminUsers={adminData.adminUsers}
      adminProperties={adminData.adminProperties}
      adminAppointments={adminData.adminAppointments}
      categories={adminData.categories}
      leads={adminData.leads}
    />
  )
}
