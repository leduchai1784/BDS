import { prisma } from '@/lib/prisma'
import DashboardCharts from '@/components/admin/DashboardCharts'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function AdminDashboardPage() {
  
  // 1. Core overall stats queries
  const [
    totalAccounts,
    totalOwners,
    totalTenants,
    totalProperties,
    totalAppointments,
    viewsResult
  ] = await Promise.all([
    prisma.user.count(),
    prisma.user.count({ where: { role: 'owner' } }),
    prisma.user.count({ where: { role: 'tenant' } }),
    prisma.property.count(),
    prisma.appointment.count(),
    prisma.property.aggregate({
      _sum: {
        viewsCount: true
      }
    })
  ])

  const totalViews = Number(viewsResult._sum.viewsCount || 0)

  // 2. Fetch pending review properties
  const pendingProperties = await prisma.property.findMany({
    where: { status: 'pending', deletedAt: null },
    include: {
      category: true,
      owner: true
    },
    take: 5,
    orderBy: { createdAt: 'desc' }
  })

  // 3. Fetch recent appointments
  const dbRecentAppointments = await prisma.appointment.findMany({
    take: 5,
    orderBy: { createdAt: 'desc' }
  })

  const appPropIds = dbRecentAppointments.map(a => a.propertyId)
  const matchingProps = await prisma.property.findMany({
    where: { id: { in: appPropIds } }
  })

  const appointmentsList = dbRecentAppointments.map(app => {
    const p = matchingProps.find(x => x.id === app.propertyId)
    return {
      id: Number(app.id),
      name: app.name,
      phone: app.phone,
      email: app.email,
      date: app.date ? new Date(app.date).toLocaleDateString('vi-VN') : '',
      status: app.status,
      property: p ? {
        title: p.title,
        address: p.address
      } : {
        title: 'Bất động sản NKS',
        address: 'Liên hệ'
      }
    }
  })

  // 4. Calculate 6-month chart stats
  const sixMonthsAgo = new Date()
  sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 5)
  sixMonthsAgo.setDate(1)
  sixMonthsAgo.setHours(0, 0, 0, 0)

  const [properties, users, appointments] = await Promise.all([
    prisma.property.findMany({
      where: { createdAt: { gte: sixMonthsAgo } },
      select: { createdAt: true }
    }),
    prisma.user.findMany({
      where: { createdAt: { gte: sixMonthsAgo } },
      select: { createdAt: true }
    }),
    prisma.appointment.findMany({
      where: { createdAt: { gte: sixMonthsAgo } },
      select: { createdAt: true }
    })
  ])

  // Generate 6-month labels List
  const monthsList: string[] = []
  for (let i = 5; i >= 0; i--) {
    const d = new Date()
    d.setMonth(d.getMonth() - i)
    const label = `${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
    monthsList.push(label)
  }

  // Initialize counts mapping
  const chartData = monthsList.map(m => ({
    month: m,
    properties: 0,
    users: 0,
    appointments: 0
  }))

  const formatMonthLabel = (d: Date | null | undefined): string => {
    if (!d) return ''
    const dateObj = new Date(d)
    return `${String(dateObj.getMonth() + 1).padStart(2, '0')}/${dateObj.getFullYear()}`
  }

  properties.forEach(p => {
    const key = formatMonthLabel(p.createdAt)
    const match = chartData.find(c => c.month === key)
    if (match) match.properties++
  })

  users.forEach(u => {
    const key = formatMonthLabel(u.createdAt)
    const match = chartData.find(c => c.month === key)
    if (match) match.users++
  })

  appointments.forEach(a => {
    const key = formatMonthLabel(a.createdAt)
    const match = chartData.find(c => c.month === key)
    if (match) match.appointments++
  })

  return (
    <div className="space-y-8">
      
      {/* Page Title Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-left">
        <div>
          <h1 className="text-xl font-bold text-slate-800">Bảng điều khiển quản trị</h1>
          <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi hiệu suất hệ thống, tăng trưởng người dùng và duyệt tin đăng nhanh chóng.</p>
        </div>
      </div>

      {/* Grid Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
        {/* Users Stats */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex items-center justify-between hover:shadow-md transition">
          <div className="space-y-1">
            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Thành viên</span>
            <h3 className="text-2xl font-black text-slate-800">{totalAccounts}</h3>
            <span className="text-[9px] font-semibold text-slate-500 block">Chủ nhà: {totalOwners} | Khách thuê: {totalTenants}</span>
          </div>
          <div className="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-lg">
            <i className="fa-solid fa-users" />
          </div>
        </div>

        {/* Properties Stats */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex items-center justify-between hover:shadow-md transition">
          <div className="space-y-1">
            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tổng tin đăng</span>
            <h3 className="text-2xl font-black text-slate-800">{totalProperties}</h3>
            <span className="text-[9px] font-semibold text-slate-500 block">Nhà phố, căn hộ, phòng trọ...</span>
          </div>
          <div className="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-lg">
            <i className="fa-solid fa-house" />
          </div>
        </div>

        {/* Appointments Stats */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex items-center justify-between hover:shadow-md transition">
          <div className="space-y-1">
            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tổng lịch hẹn</span>
            <h3 className="text-2xl font-black text-slate-800">{totalAppointments}</h3>
            <span className="text-[9px] font-semibold text-slate-500 block">Yêu cầu xem nhà của khách</span>
          </div>
          <div className="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-lg">
            <i className="fa-regular fa-calendar-check" />
          </div>
        </div>

        {/* Views Stats */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex items-center justify-between hover:shadow-md transition">
          <div className="space-y-1">
            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Lượt truy cập</span>
            <h3 className="text-2xl font-black text-slate-800">{totalViews.toLocaleString('vi-VN')}</h3>
            <span className="text-[9px] font-semibold text-slate-500 block">Tổng số lượt xem tất cả tin đăng</span>
          </div>
          <div className="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center text-lg">
            <i className="fa-solid fa-eye" />
          </div>
        </div>
      </div>

      {/* Render Recharts Visualizations */}
      <DashboardCharts data={chartData} />

      {/* Pending Properties & Appointments sections */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 text-left">
        
        {/* Pending Properties */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider">Tin đăng chờ duyệt ({pendingProperties.length})</h4>
            <Link href="/admin/properties?status=pending" className="text-[10px] font-bold text-primary hover:underline">Xem tất cả</Link>
          </div>

          <div className="divide-y divide-slate-100">
            {pendingProperties.length > 0 ? (
              pendingProperties.map(p => (
                <div key={p.id} className="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                  <div className="space-y-0.5 truncate flex-grow">
                    <span className="inline-block px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded-md text-[8px] font-bold uppercase mb-1">CHỜ DUYỆT</span>
                    <strong className="block text-xs font-semibold text-slate-850 truncate max-w-sm">{p.title}</strong>
                    <span className="block text-[10px] text-slate-400 font-semibold">{p.address} | {p.priceLabel}</span>
                  </div>
                  <Link 
                    href={`/admin/properties?id=${p.id}`}
                    className="px-3 py-1.5 bg-slate-100 hover:bg-primary hover:text-white rounded-lg text-[9px] font-black transition cursor-pointer flex-shrink-0"
                  >
                    Xử lý
                  </Link>
                </div>
              ))
            ) : (
              <div className="py-8 text-center text-slate-400 text-xs font-semibold">
                Không có tin đăng nào đang chờ duyệt.
              </div>
            )}
          </div>
        </div>

        {/* Recent Appointments */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider">Yêu cầu đặt lịch mới</h4>
            <Link href="/admin/appointments" className="text-[10px] font-bold text-primary hover:underline">Xem tất cả</Link>
          </div>

          <div className="divide-y divide-slate-100">
            {appointmentsList.length > 0 ? (
              appointmentsList.map(app => (
                <div key={app.id} className="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                  <div className="space-y-0.5 truncate flex-grow">
                    <div className="flex items-center gap-1.5 mb-1">
                      <span className={`px-1.5 py-0.5 rounded-md text-[8px] font-bold uppercase ${
                        app.status === 'approved' 
                          ? 'bg-emerald-50 text-emerald-600'
                          : app.status === 'rejected'
                          ? 'bg-red-50 text-red-600'
                          : 'bg-amber-50 text-amber-600'
                      }`}>
                        {app.status === 'approved' ? 'Chấp thuận' : app.status === 'rejected' ? 'Từ chối' : 'Chờ duyệt'}
                      </span>
                      <span className="text-[9px] text-slate-450 font-bold">{app.date}</span>
                    </div>
                    <strong className="block text-xs font-semibold text-slate-850 truncate max-w-sm">{app.property.title}</strong>
                    <span className="block text-[10px] text-slate-400 font-semibold">Khách: {app.name} ({app.phone})</span>
                  </div>
                </div>
              ))
            ) : (
              <div className="py-8 text-center text-slate-400 text-xs font-semibold">
                Không có yêu cầu đặt lịch nào gần đây.
              </div>
            )}
          </div>
        </div>

      </div>

    </div>
  )
}
