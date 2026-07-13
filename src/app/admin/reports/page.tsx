import { prisma } from '@/lib/prisma'
import ReportCharts from '@/components/admin/ReportCharts'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function AdminReportsPage() {
  
  // 1. Fetch Top 10 most viewed properties
  const dbTopProperties = await prisma.property.findMany({
    where: { deletedAt: null },
    include: {
      category: true,
      owner: true
    },
    orderBy: {
      viewsCount: 'desc'
    },
    take: 10
  })

  const topProperties = dbTopProperties.map(p => ({
    id: p.id,
    title: p.title,
    priceLabel: p.priceLabel,
    viewsCount: p.viewsCount,
    address: p.address,
    categoryName: p.category?.name || 'Chưa phân loại',
    ownerName: p.owner?.name || 'Admin'
  }))

  // 2. Fetch Top 10 owners with most properties (sort in memory for safety)
  const dbTopOwnersRaw = await prisma.user.findMany({
    where: { role: 'owner' },
    include: {
      _count: {
        select: { properties: true }
      }
    }
  })

  const topOwners = dbTopOwnersRaw
    .map(u => ({
      id: u.id.toString(),
      name: u.name,
      email: u.email,
      phone: u.phone,
      propertiesCount: u._count.properties
    }))
    .sort((a, b) => b.propertiesCount - a.propertiesCount)
    .slice(0, 10)

  // 3. Fetch Category Share
  const dbCategories = await prisma.category.findMany({
    include: {
      _count: {
        select: { properties: true }
      }
    }
  })

  const categoryShare = dbCategories
    .filter(c => c._count.properties > 0)
    .map(c => ({
      name: c.name,
      value: c._count.properties
    }))

  // 4. Monthly trends in the last 6 months
  const sixMonthsAgo = new Date()
  sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 5)
  sixMonthsAgo.setDate(1)
  sixMonthsAgo.setHours(0, 0, 0, 0)

  const [properties, appointments] = await Promise.all([
    prisma.property.findMany({
      where: { createdAt: { gte: sixMonthsAgo } },
      select: { createdAt: true }
    }),
    prisma.appointment.findMany({
      where: { createdAt: { gte: sixMonthsAgo } },
      select: { createdAt: true }
    })
  ])

  const monthsList: string[] = []
  for (let i = 5; i >= 0; i--) {
    const d = new Date()
    d.setMonth(d.getMonth() - i)
    const label = `${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
    monthsList.push(label)
  }

  const monthlyTrends = monthsList.map(m => ({
    month: m,
    properties: 0,
    appointments: 0
  }))

  const formatMonthLabel = (d: Date | null | undefined): string => {
    if (!d) return ''
    const dateObj = new Date(d)
    return `${String(dateObj.getMonth() + 1).padStart(2, '0')}/${dateObj.getFullYear()}`
  }

  properties.forEach(p => {
    const key = formatMonthLabel(p.createdAt)
    const match = monthlyTrends.find(m => m.month === key)
    if (match) match.properties++
  })

  appointments.forEach(a => {
    const key = formatMonthLabel(a.createdAt)
    const match = monthlyTrends.find(m => m.month === key)
    if (match) match.appointments++
  })

  return (
    <div className="space-y-8">
      
      {/* Title Header */}
      <div className="text-left">
        <h1 className="text-xl font-bold text-slate-800">Thống kê & Báo cáo</h1>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Báo cáo lượt xem, tỷ lệ tin đăng theo chuyên mục và thống kê tương tác.</p>
      </div>

      {/* Render Recharts Visualizations */}
      <ReportCharts categoryShare={categoryShare} monthlyTrends={monthlyTrends} />

      {/* Lists of Top Performers */}
      <div className="grid grid-cols-1 xl:grid-cols-2 gap-6 text-left">
        
        {/* Top Viewed Properties */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
          <h3 className="text-xs font-black text-slate-800 uppercase tracking-wider">Top 10 tin đăng xem nhiều nhất</h3>
          
          <div className="overflow-x-auto">
            <table className="w-full text-xs font-semibold text-slate-650 border-collapse">
              <thead>
                <tr className="bg-slate-50 border-b border-slate-100 text-[9px] uppercase tracking-wider text-slate-400 font-bold">
                  <th className="px-4 py-2.5 text-left">Tin đăng</th>
                  <th className="px-4 py-2.5 text-left">Chuyên mục</th>
                  <th className="px-4 py-2.5 text-left">Chủ nhà</th>
                  <th className="px-4 py-2.5 text-right">Lượt xem</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {topProperties.length > 0 ? (
                  topProperties.map(p => (
                    <tr key={p.id} className="hover:bg-slate-50/50 transition">
                      <td className="px-4 py-2 text-left truncate max-w-[200px]" title={p.title}>
                        <Link href={`/property/${p.id}`} target="_blank" className="hover:text-primary transition font-bold text-slate-800">
                          {p.title}
                        </Link>
                      </td>
                      <td className="px-4 py-2 text-left text-slate-550 font-semibold">{p.categoryName}</td>
                      <td className="px-4 py-2 text-left text-slate-500 font-semibold">{p.ownerName}</td>
                      <td className="px-4 py-2 text-right font-black text-slate-800">{p.viewsCount.toLocaleString('vi-VN')}</td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan={4} className="py-8 text-center text-slate-400 text-xs font-semibold">
                      Chưa có dữ liệu thống kê lượt xem.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Top Active Owners */}
        <div className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
          <h3 className="text-xs font-black text-slate-800 uppercase tracking-wider">Top 10 chủ nhà đăng nhiều nhất</h3>
          
          <div className="overflow-x-auto">
            <table className="w-full text-xs font-semibold text-slate-650 border-collapse">
              <thead>
                <tr className="bg-slate-50 border-b border-slate-100 text-[9px] uppercase tracking-wider text-slate-400 font-bold">
                  <th className="px-4 py-2.5 text-left">Chủ nhà</th>
                  <th className="px-4 py-2.5 text-left">Số điện thoại</th>
                  <th className="px-4 py-2.5 text-right">Số tin đã đăng</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {topOwners.length > 0 ? (
                  topOwners.map(owner => (
                    <tr key={owner.id} className="hover:bg-slate-50/50 transition">
                      <td className="px-4 py-2 text-left">
                        <Link href={`/admin/users/${owner.id}`} className="hover:text-primary transition font-bold text-slate-800">
                          {owner.name}
                        </Link>
                        <span className="block text-[9px] text-slate-400 select-all">{owner.email}</span>
                      </td>
                      <td className="px-4 py-2 text-left text-slate-550 select-all font-semibold">{owner.phone || '—'}</td>
                      <td className="px-4 py-2 text-right font-black text-primary">{owner.propertiesCount} tin</td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan={3} className="py-8 text-center text-slate-400 text-xs font-semibold">
                      Chưa có dữ liệu thống kê chủ nhà.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>
  )
}
