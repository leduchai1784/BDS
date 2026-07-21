import { prisma } from '@/lib/prisma'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import UserDetailTabs from '@/components/admin/UserDetailTabs'

export const dynamic = 'force-dynamic'

interface AdminUserDetailPageProps {
  params: Promise<{ id: string }>
}

async function fetchNksAgentDetails(id: string): Promise<{ agent: any; properties: any[] }> {
  try {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'
    const agentId = id.replace('nks-', '')

    // 1. Fetch agent info
    const agentRes = await fetch('https://online.nks.vn/api/nks/rsagents', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({}),
      next: { revalidate: 30 }
    })
    
    if (!agentRes.ok) return { agent: null, properties: [] }
    const agentData = await agentRes.json()
    if (!agentData?.success || !Array.isArray(agentData.data)) return { agent: null, properties: [] }
    
    const agent = agentData.data.find((a: any) => a.id.toString() === agentId)
    if (!agent) return { agent: null, properties: [] }

    // 2. Fetch agent properties
    const itemsRes = await fetch('https://online.nks.vn/api/nks/rsitems', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({}),
      next: { revalidate: 30 }
    })

    let properties: any[] = []
    if (itemsRes.ok) {
      const itemsData = await itemsRes.json()
      if (itemsData?.success && Array.isArray(itemsData.data)) {
        properties = itemsData.data.filter((item: any) => {
          const saleEmail = item.sale?.email?.toLowerCase() || ''
          const salePhone = item.sale?.phone || ''
          const matchEmail = agent.email && saleEmail === agent.email.toLowerCase()
          const matchPhone = agent.phone && salePhone.replace(/\D/g, '') === agent.phone.replace(/\D/g, '')
          return matchEmail || matchPhone
        }).map((item: any) => ({
          id: `nks-${item.id}`,
          title: item.title,
          address: item.address || 'Đồng Nai',
          priceLabel: item.formatedPrice || `${(item.price / 1000000000).toFixed(1)} tỷ`
        }))
      }
    }

    return { agent, properties }
  } catch (err) {
    console.error('Failed to get agent NKS details:', err)
    return { agent: null, properties: [] }
  }
}

export default async function AdminUserDetailPage({ params }: AdminUserDetailPageProps) {
  const resolvedParams = await params
  const idStr = resolvedParams.id

  const isNks = idStr.startsWith('nks-')

  if (isNks) {
    const { agent, properties } = await fetchNksAgentDetails(idStr)
    if (!agent) {
      notFound()
    }

    return (
      <div className="space-y-6 text-left">
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/admin" className="hover:text-primary transition">Bảng điều khiển</Link>
          <span>/</span>
          <Link href="/admin/users" className="hover:text-primary transition">Quản lý thành viên</Link>
          <span>/</span>
          <span className="text-slate-800">Chi tiết môi giới NKS</span>
        </nav>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          {/* User Card & Info */}
          <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6 h-fit">
            <div className="flex flex-col items-center text-center space-y-3">
              <div className="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-200 overflow-hidden flex items-center justify-center">
                {agent.avatar ? (
                  <img src={agent.avatar} className="w-full h-full object-cover" />
                ) : (
                  <i className="fa-regular fa-user text-slate-300 text-3xl" />
                )}
              </div>
              <div>
                <h2 className="text-base font-bold text-slate-800">{agent.name}</h2>
                <span className="inline-block px-2.5 py-0.5 mt-1.5 rounded-md text-[9px] font-black uppercase bg-teal-50 text-teal-600 border border-teal-200/55">
                  Môi giới NKS
                </span>
              </div>
            </div>

            <div className="border-t border-slate-100 pt-4 space-y-3 text-xs font-semibold">
              <div className="flex justify-between">
                <span className="text-slate-400">Email:</span>
                <span className="text-slate-700 select-all">{agent.email || 'Chưa cung cấp'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Số điện thoại:</span>
                <span className="text-slate-700 select-all">{agent.phone || 'Chưa cung cấp'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Trạng thái:</span>
                <span className="font-black text-emerald-500">Hoạt động ✓</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Nguồn liên kết:</span>
                <span className="text-slate-700">NKS Portal (API)</span>
              </div>
            </div>
          </div>

          {/* Tabbed Panel */}
          <div className="lg:col-span-2">
            <UserDetailTabs properties={properties} appointments={[]} isNks={true} />
          </div>

        </div>
      </div>
    )
  }

  // Else, normal database flow
  const userId = BigInt(idStr)

  const user = await prisma.user.findUnique({
    where: { id: userId }
  })

  if (!user) {
    notFound()
  }

  // Fetch properties posted by this user
  const propertiesList = await prisma.property.findMany({
    where: { ownerId: Number(userId), deletedAt: null },
    include: { category: true },
    orderBy: { createdAt: 'desc' }
  })

  // Fetch appointments scheduled by this user
  const appointmentsList = await prisma.appointment.findMany({
    where: { userId },
    orderBy: { date: 'desc' }
  })

  const appPropIds = appointmentsList.map(a => a.propertyId)
    .filter(id => /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(id))
  const propertiesForApps = await prisma.property.findMany({
    where: { id: { in: appPropIds } }
  })

  const mappedAppointments = appointmentsList.map(app => {
    const p = propertiesForApps.find(x => x.id === app.propertyId)
    return {
      id: Number(app.id),
      name: app.name,
      phone: app.phone,
      date: app.date ? new Date(app.date).toLocaleDateString('vi-VN') : '',
      status: app.status,
      property: p ? {
        title: p.title,
        address: p.address
      } : {
        title: 'Bất động sản',
        address: 'Liên hệ'
      }
    }
  })

  return (
    <div className="space-y-6 text-left">
      {/* Breadcrumbs */}
      <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
        <Link href="/admin" className="hover:text-primary transition">Bảng điều khiển</Link>
        <span>/</span>
        <Link href="/admin/users" className="hover:text-primary transition">Quản lý thành viên</Link>
        <span>/</span>
        <span className="text-slate-800">Chi tiết thành viên</span>
      </nav>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {/* User Card & Info */}
        <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6 h-fit">
          <div className="flex flex-col items-center text-center space-y-3">
            <div className="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-200 overflow-hidden flex items-center justify-center">
              {user.avatar ? (
                <img src={user.avatar} className="w-full h-full object-cover" />
              ) : (
                <i className="fa-regular fa-user text-slate-300 text-3xl" />
              )}
            </div>
            <div>
              <h2 className="text-base font-bold text-slate-800">{user.name}</h2>
              <span className={`inline-block px-2.5 py-0.5 mt-1.5 rounded-md text-[9px] font-black uppercase ${
                user.role === 'admin' 
                  ? 'bg-red-50 text-red-650' 
                  : user.role === 'owner'
                  ? 'bg-primary-light text-primary'
                  : 'bg-slate-100 text-slate-600'
              }`}>
                {user.role === 'admin' ? 'Quản trị viên' : user.role === 'owner' ? 'Chủ nhà' : 'Khách thuê'}
              </span>
            </div>
          </div>

          <div className="border-t border-slate-100 pt-4 space-y-3 text-xs font-semibold">
            <div className="flex justify-between">
              <span className="text-slate-400">Email:</span>
              <span className="text-slate-700 select-all">{user.email}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">Số điện thoại:</span>
              <span className="text-slate-700 select-all">{user.phone || 'Chưa cung cấp'}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">Ngày sinh:</span>
              <span className="text-slate-700">{user.dob || 'Chưa cung cấp'}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">Trạng thái:</span>
              <span className={`font-black ${user.status === 'locked' ? 'text-red-500' : 'text-emerald-500'}`}>
                {user.status === 'locked' ? 'Đã khóa 🔒' : 'Hoạt động ✓'}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">Xác thực CCCD:</span>
              <span className={user.cccdFront && user.cccdBack ? 'text-emerald-550 font-black' : 'text-slate-450'}>
                {user.cccdFront && user.cccdBack ? 'Đã xác thực ✓' : 'Chưa xác thực'}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">Ngày tham gia:</span>
              <span className="text-slate-750">{user.createdAt ? new Date(user.createdAt).toLocaleDateString('vi-VN') : ''}</span>
            </div>
          </div>
        </div>

        {/* Tabbed Panel */}
        <div className="lg:col-span-2">
          <UserDetailTabs 
            properties={propertiesList.map(p => ({
              id: p.id,
              title: p.title,
              address: p.address,
              priceLabel: p.priceLabel
            }))}
            appointments={mappedAppointments}
          />
        </div>

      </div>
    </div>
  )
}
