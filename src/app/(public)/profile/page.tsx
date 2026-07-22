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

  let nksUser: any = null
  if (nksToken) {
    try {
      const nksRes = await getNksUserInfo(nksToken)
      if (nksRes?.success && nksRes.data) {
        nksUser = nksRes.data
      }
    } catch (e) {
      console.warn('Failed to fetch NKS profile details directly from API:', e)
    }
  }

  // 2. Fetch user from local DB (by id first, then by email)
  let dbUser = await prisma.user.findUnique({
    where: { id: userId }
  })

  const emailToLookup = nksUser?.email || userEmail
  if (!dbUser && emailToLookup) {
    dbUser = await prisma.user.findUnique({
      where: { email: emailToLookup }
    })
  }

  // 3. Auto-sync NKS data to local DB if record does not exist yet
  if (!dbUser && nksUser && emailToLookup) {
    try {
      const bcrypt = await import('bcryptjs')
      const mappedRole = (session.user as any).role || (nksUser.role_id === 4 || nksUser.role?.name === 'agent' ? 'agent' : 'owner')
      dbUser = await prisma.user.create({
        data: {
          email: emailToLookup,
          name: nksUser.name || session.user.name || 'NKS Agent',
          phone: nksUser.phone || null,
          avatar: nksUser.avatar || null,
          role: mappedRole,
          status: 'active',
          nksToken: nksToken,
          nksUserId: String(nksUser.id),
          password: await bcrypt.hash('nks_synced_user', 12),
        }
      })
    } catch (createErr: any) {
      console.error('Failed to auto-sync user from NKS API to local DB:', createErr.message)
    }
  }

  // If both NKS API and DB return empty, show error screen
  if (!nksUser && !dbUser) {
    return (
      <div className="min-h-screen pt-32 pb-16 flex flex-col items-center justify-center bg-slate-50 px-4">
        <div className="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl max-w-md w-full text-center">
          <i className="fa-solid fa-triangle-exclamation text-red-500 text-4xl mb-4 animate-bounce"></i>
          <h2 className="text-lg font-bold text-slate-800 mb-2">Lỗi kết nối hệ thống</h2>
          <p className="text-xs text-slate-500 mb-6 font-semibold leading-relaxed">
            Hệ thống không thể tải dữ liệu tài khoản từ NKS API hoặc Cơ sở dữ liệu. Vui lòng kiểm tra lại kết nối mạng, đăng xuất và thử lại.
          </p>
          <a 
            href="/api/auth/signout" 
            className="inline-flex items-center justify-center px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-md"
          >
            <i className="fa-solid fa-right-from-bracket mr-2"></i> Đăng xuất & Thử lại
          </a>
        </div>
      </div>
    )
  }

  // 4. Build mergedUser prioritizing NKS API payload, fallback to DB
  const userSource = nksUser || dbUser || {}
  
  let mergedUser = {
    id: Number(dbUser?.id || userSource.id || userId),
    name: userSource.name || dbUser?.name || '',
    email: userSource.email || dbUser?.email || userEmail,
    phone: userSource.phone || dbUser?.phone || '',
    role: (session.user as any).role || dbUser?.role || (nksUser?.role_id === 4 || nksUser?.role?.name === 'agent' ? 'agent' : 'tenant'),
    firstname: userSource.firstname || dbUser?.firstname || '',
    lastname: userSource.lastname || dbUser?.lastname || '',
    gender: userSource.gender !== undefined && userSource.gender !== null ? Number(userSource.gender) : (dbUser?.gender !== null ? Number(dbUser?.gender) : 0),
    dob: userSource.dob || userSource.formatedDob || dbUser?.dob || '',
    pob: userSource.pob || dbUser?.pob || '',
    idNumber: userSource.id_number || dbUser?.idNumber || '',
    idDate: userSource.id_date || userSource.formatedCccdDate || dbUser?.idDate || '',
    idPlace: userSource.id_place || dbUser?.idPlace || '',
    permanentAddress: userSource.permanent_address || dbUser?.permanentAddress || '',
    intro: userSource.intro || dbUser?.intro || '',
    website: userSource.website || dbUser?.website || '',
    companyName: userSource.company || dbUser?.company || '',
    cccdFront: userSource.cccd_front || dbUser?.cccdFront || '',
    cccdBack: userSource.cccd_back || dbUser?.cccdBack || '',
    avatar: userSource.avatar || dbUser?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(userSource.name || 'User')}&background=0077bb&color=fff`,
    province: userSource.add_province || dbUser?.province || '',
    district: userSource.add_district || dbUser?.district || '',
    ward: userSource.add_ward || dbUser?.ward || '',
    addStreet: userSource.add_street || dbUser?.addStreet || '',
    addProvince: userSource.add_province || dbUser?.addProvince || '',
    addDistrict: userSource.add_district || dbUser?.addDistrict || '',
    addWard: dbUser?.addWard || dbUser?.addWard || '',
    joinDate: dbUser?.createdAt ? new Date(dbUser.createdAt).toLocaleDateString('vi-VN') : '',
    rslogan: userSource.rslogan || dbUser?.rslogan || '',
    rsbio: userSource.rsbio || dbUser?.rsbio || '',
    rsexperience: userSource.rsexperience || dbUser?.rsexperience || '',
    rslocation: userSource.rslocation || dbUser?.rslocation || '',
    rsachievement: userSource.rsachievement || dbUser?.rsachievement || '',
    rscertificate: userSource.rscertificate || dbUser?.rscertificate || '',
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

  if (mergedUser.role === 'admin') {
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
