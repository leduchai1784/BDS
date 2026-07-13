import { prisma } from '@/lib/prisma'
import { notFound } from 'next/navigation'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

interface AdminUserDetailPageProps {
  params: Promise<{ id: string }>
}

export default async function AdminUserDetailPage({ params }: AdminUserDetailPageProps) {
  const resolvedParams = await params
  const userId = BigInt(resolvedParams.id)

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

        {/* Action Panel lists */}
        <div className="lg:col-span-2 space-y-6">
          
          {/* Owner properties list */}
          {user.role === 'owner' && (
            <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
              <h3 className="text-xs font-black uppercase text-slate-800 tracking-wider">Tin đăng đã đăng ({propertiesList.length})</h3>
              
              <div className="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
                {propertiesList.length > 0 ? (
                  propertiesList.map(p => (
                    <div key={p.id} className="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                      <div className="space-y-0.5 truncate">
                        <strong className="block text-xs font-bold text-slate-800 truncate max-w-md">{p.title}</strong>
                        <span className="block text-[10px] text-slate-400 font-semibold">{p.address} | {p.priceLabel}</span>
                      </div>
                      <Link 
                        href={`/admin/properties?id=${p.id}`}
                        className="px-3 py-1.5 bg-slate-100 hover:bg-primary hover:text-white rounded-lg text-[9px] font-bold transition cursor-pointer"
                      >
                        Xem tin
                      </Link>
                    </div>
                  ))
                ) : (
                  <div className="py-8 text-center text-slate-450 text-xs font-semibold">
                    Chủ nhà này chưa đăng tin nào.
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Tenant appointments list */}
          <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 className="text-xs font-black uppercase text-slate-800 tracking-wider">Lịch hẹn đã đặt ({mappedAppointments.length})</h3>
            
            <div className="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
              {mappedAppointments.length > 0 ? (
                mappedAppointments.map(app => (
                  <div key={app.id} className="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                    <div className="space-y-0.5 truncate">
                      <strong className="block text-xs font-bold text-slate-850 truncate max-w-md">{app.property.title}</strong>
                      <span className="block text-[10px] text-slate-400 font-semibold">Đặt ngày: {app.date} | Trạng thái: {app.status}</span>
                    </div>
                  </div>
                ))
              ) : (
                <div className="py-8 text-center text-slate-450 text-xs font-semibold">
                  Thành viên này chưa đặt lịch hẹn xem nhà nào.
                </div>
              )}
            </div>
          </div>

        </div>

      </div>
    </div>
  )
}
