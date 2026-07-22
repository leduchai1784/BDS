'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

interface DashboardStats {
  totalProperties: number;
  activeProperties: number;
  pendingProperties: number;
  totalAppointments: number;
  pendingAppointments: number;
  recentAppointments: any[];
}

export default function OwnerDashboardPage() {
  const [stats, setStats] = useState<DashboardStats>({
    totalProperties: 0,
    activeProperties: 0,
    pendingProperties: 0,
    totalAppointments: 0,
    pendingAppointments: 0,
    recentAppointments: []
  })
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    async function loadData() {
      try {
        const res = await fetch('/api/profile')
        if (res.ok) {
          const json = await res.json()
          if (json?.success && json?.data) {
            const properties = json.data.properties || []
            const appointments = json.data.receivedAppointments || []
            
            const activeProps = properties.filter((p: any) => p.status === 'active').length
            const pendingProps = properties.filter((p: any) => p.status === 'pending').length
            const pendingAppoints = appointments.filter((a: any) => a.status === 'pending').length

            setStats({
              totalProperties: properties.length,
              activeProperties: activeProps,
              pendingProperties: pendingProps,
              totalAppointments: appointments.length,
              pendingAppointments: pendingAppoints,
              recentAppointments: appointments.slice(0, 5)
            })
          }
        }
      } catch (err) {
        console.error('Failed to load dashboard data:', err)
      } finally {
        setLoading(false)
      }
    }
    loadData()
  }, [])

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Title */}
      <div>
        <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
          Hệ thống BDS - Tổng quan
        </h1>
        <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
          Báo cáo thống kê và quản lý hoạt động tin đăng của đối tác.
        </p>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        {/* Card 1 */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
          <div className="absolute -right-5 -bottom-5 w-20 h-20 bg-primary/5 rounded-full pointer-events-none"></div>
          <div className="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-lg">
            <i className="fa-solid fa-house" />
          </div>
          <div>
            <span className="block text-[10px] uppercase font-extrabold tracking-wider text-slate-400">Tổng tin đăng</span>
            <span className="block text-2xl font-black text-slate-800 dark:text-white mt-0.5">{stats.totalProperties}</span>
          </div>
        </div>

        {/* Card 2 */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
          <div className="absolute -right-5 -bottom-5 w-20 h-20 bg-emerald-500/5 rounded-full pointer-events-none"></div>
          <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg">
            <i className="fa-solid fa-circle-check" />
          </div>
          <div>
            <span className="block text-[10px] uppercase font-extrabold tracking-wider text-slate-400">Tin đang hiển thị</span>
            <span className="block text-2xl font-black text-slate-800 dark:text-white mt-0.5">{stats.activeProperties}</span>
          </div>
        </div>

        {/* Card 3 */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
          <div className="absolute -right-5 -bottom-5 w-20 h-20 bg-amber-500/5 rounded-full pointer-events-none"></div>
          <div className="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-lg">
            <i className="fa-solid fa-clock-rotate-left" />
          </div>
          <div>
            <span className="block text-[10px] uppercase font-extrabold tracking-wider text-slate-400">Tin chờ duyệt</span>
            <span className="block text-2xl font-black text-slate-800 dark:text-white mt-0.5">{stats.pendingProperties}</span>
          </div>
        </div>

        {/* Card 4 */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
          <div className="absolute -right-5 -bottom-5 w-20 h-20 bg-indigo-500/5 rounded-full pointer-events-none"></div>
          <div className="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-lg">
            <i className="fa-solid fa-calendar-check" />
          </div>
          <div>
            <span className="block text-[10px] uppercase font-extrabold tracking-wider text-slate-400">Tổng lịch hẹn</span>
            <span className="block text-2xl font-black text-slate-800 dark:text-white mt-0.5">{stats.totalAppointments}</span>
          </div>
        </div>
      </div>

      {/* Main Content Area */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Side: Recent Appointments */}
        <div className="lg:col-span-2 bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm">
          <div className="flex items-center justify-between mb-5">
            <h2 className="text-base font-extrabold text-slate-800 dark:text-white">Lịch đặt xem phòng mới</h2>
            <Link href="/owner/appointments" className="text-xs font-bold text-primary hover:underline">
              Xem tất cả
            </Link>
          </div>

          {stats.recentAppointments.length === 0 ? (
            <div className="text-center py-10 text-xs font-semibold text-slate-400">
              Bạn chưa nhận được lịch hẹn nào.
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs border-collapse">
                <thead>
                  <tr className="border-b border-slate-100 dark:border-gray-800 text-slate-400 font-extrabold uppercase">
                    <th className="pb-3">Khách thuê</th>
                    <th className="pb-3">SĐT</th>
                    <th className="pb-3">BĐS liên quan</th>
                    <th className="pb-3">Thời gian</th>
                    <th className="pb-3">Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  {stats.recentAppointments.map((a: any) => (
                    <tr key={a.id} className="border-b border-slate-50 dark:border-gray-800 last:border-b-0 hover:bg-slate-50/50 dark:hover:bg-gray-800/30">
                      <td className="py-3.5 font-bold text-slate-800 dark:text-slate-200">{a.tenant?.name || 'Khách thuê'}</td>
                      <td className="py-3.5 text-slate-500 dark:text-slate-400 font-medium">{a.tenant?.phone || 'Chưa cung cấp'}</td>
                      <td className="py-3.5 font-medium text-slate-655 max-w-[200px] truncate">{a.property?.title || 'Tin đăng'}</td>
                      <td className="py-3.5 font-semibold text-slate-655">{a.bookingDate} - {a.bookingTime}</td>
                      <td className="py-3.5">
                        <span className={`inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                          a.status === 'approved' ? 'bg-emerald-50 text-emerald-600' :
                          a.status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600'
                        }`}>
                          {a.status === 'approved' ? 'Đã duyệt' : a.status === 'rejected' ? 'Đã từ chối' : 'Chờ duyệt'}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>

        {/* Right Side: Quick Links */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
          <div>
            <h2 className="text-base font-extrabold text-slate-800 dark:text-white mb-4">Thao tác nhanh</h2>
            <div className="space-y-3">
              <Link
                href="/property/create"
                className="flex items-center gap-3 px-4 py-3 rounded-2xl bg-gradient-to-r from-primary to-indigo-650 hover:shadow-lg text-white font-bold text-xs transition"
              >
                <i className="fa-solid fa-plus-circle text-sm" />
                Đăng tin cho thuê mới
              </Link>
              
              <Link
                href="/owner/properties"
                className="flex items-center gap-3 px-4 py-3 rounded-2xl border border-slate-100 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-655 dark:text-slate-300 font-bold text-xs transition text-left"
              >
                <i className="fa-solid fa-list-check text-slate-400" />
                Quản lý các tin đã đăng
              </Link>

              <Link
                href="/profile"
                className="flex items-center gap-3 px-4 py-3 rounded-2xl border border-slate-100 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-655 dark:text-slate-300 font-bold text-xs transition text-left"
              >
                <i className="fa-solid fa-user-gear text-slate-400" />
                Chỉnh sửa Hồ sơ cá nhân
              </Link>
            </div>
          </div>

          <div className="mt-6 pt-6 border-t border-slate-100 dark:border-gray-800 text-left">
            <span className="block text-[10px] font-bold uppercase text-slate-400">Trợ giúp & Hỗ trợ</span>
            <p className="text-xs text-slate-500 mt-1 leading-relaxed">
              Nếu gặp khó khăn trong quá trình sử dụng hệ thống BDS Rental, vui lòng liên hệ hotline **1900.2626** để được hỗ trợ 24/7.
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
