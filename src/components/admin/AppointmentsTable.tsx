'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'

interface AppointmentItem {
  id: string
  name: string
  phone: string
  email: string
  date: string
  time: string
  message: string | null
  status: string
  rejectReason: string | null
  property: {
    title: string
    address: string
  }
}

interface AppointmentsTableProps {
  initialAppointments: AppointmentItem[]
  searchParams: {
    search?: string
    status?: string
  }
}

export default function AppointmentsTable({ initialAppointments, searchParams }: AppointmentsTableProps) {
  const router = useRouter()
  const [appointments, setAppointments] = useState<AppointmentItem[]>(initialAppointments)
  
  // Filtering states
  const [search, setSearch] = useState(searchParams.search || '')
  const [status, setStatus] = useState(searchParams.status || '')
  const [isProcessing, setIsProcessing] = useState<string | null>(null)

  const handleFilter = () => {
    const params = new URLSearchParams()
    if (search) params.set('search', search)
    if (status) params.set('status', status)
    router.push(`/admin/appointments?${params.toString()}`)
  }

  const cancelAppointment = async (id: string) => {
    if (!confirm('Bạn có chắc chắn muốn hủy lịch hẹn xem nhà này?')) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/appointments/${id}/cancel`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setAppointments(prev => 
          prev.map(app => app.id === id ? { ...app, status: 'cancelled' } : app)
        )
      } else {
        alert(data.error || 'Lỗi hủy lịch hẹn')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  return (
    <div className="space-y-6 text-left">
      
      {/* Search & Filters */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-grow max-w-2xl">
          <input
            type="text"
            placeholder="Tìm theo tên khách, sđt, dự án..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />

          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer"
          >
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending">Chờ duyệt (Pending)</option>
            <option value="approved">Đã duyệt (Approved)</option>
            <option value="rejected">Từ chối (Rejected)</option>
            <option value="cancelled">Đã hủy (Cancelled)</option>
            <option value="completed">Đã xem xong (Completed)</option>
          </select>
        </div>

        <button
          onClick={handleFilter}
          className="px-6 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer self-start md:self-auto flex items-center gap-1.5"
        >
          <i className="fa-solid fa-magnifying-glass" />
          <span>Lọc lịch</span>
        </button>
      </div>

      {/* Table grid layout */}
      <div className="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-xs font-semibold text-slate-650 border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 uppercase text-[9px] tracking-wider text-slate-400 font-bold">
                <th className="px-6 py-4 text-left">Khách hẹn</th>
                <th className="px-6 py-4 text-left">Bất động sản</th>
                <th className="px-6 py-4 text-left">Thời gian hẹn</th>
                <th className="px-6 py-4 text-left">Tin nhắn khách</th>
                <th className="px-6 py-4 text-left">Trạng thái</th>
                <th className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {appointments.length > 0 ? (
                appointments.map(app => (
                  <tr key={app.id} className="hover:bg-slate-50/50 transition">
                    <td className="px-6 py-3 text-left">
                      <strong className="block text-slate-800 font-semibold">{app.name}</strong>
                      <span className="block text-[10px] text-slate-400 font-semibold select-all">{app.phone}</span>
                      <span className="block text-[9px] text-slate-400 select-all truncate max-w-[150px]">{app.email}</span>
                    </td>
                    <td className="px-6 py-3 text-left max-w-xs truncate">
                      <strong className="block text-slate-800 font-semibold truncate" title={app.property.title}>{app.property.title}</strong>
                      <span className="block text-[10px] text-slate-400 font-semibold truncate" title={app.property.address}>{app.property.address}</span>
                    </td>
                    <td className="px-6 py-3 text-left font-semibold text-slate-700">
                      <div>{app.date}</div>
                      <div className="text-[10px] text-slate-400 font-semibold">{app.time}</div>
                    </td>
                    <td className="px-6 py-3 text-left max-w-xs font-medium text-slate-500 whitespace-pre-wrap leading-normal" title={app.message || ''}>
                      {app.message || '—'}
                    </td>
                    <td className="px-6 py-3 text-left">
                      <span className={`inline-flex px-2 py-0.5 rounded-md text-[8px] font-black uppercase ${
                        app.status === 'approved' 
                          ? 'bg-emerald-50 text-emerald-600'
                          : app.status === 'rejected'
                          ? 'bg-red-50 text-red-650'
                          : app.status === 'cancelled'
                          ? 'bg-slate-100 text-slate-500'
                          : 'bg-amber-50 text-amber-600'
                      }`}>
                        {app.status === 'approved' 
                          ? 'Đã duyệt' 
                          : app.status === 'rejected' 
                          ? 'Từ chối' 
                          : app.status === 'cancelled' 
                          ? 'Đã hủy' 
                          : 'Chờ duyệt'}
                      </span>
                      {app.rejectReason && (
                        <span className="block text-[9px] text-red-400 italic font-semibold mt-0.5">Lý do: {app.rejectReason}</span>
                      )}
                    </td>
                    <td className="px-6 py-3 text-right">
                      {app.status !== 'cancelled' && app.status !== 'rejected' && app.status !== 'completed' && (
                        <button
                          onClick={() => cancelAppointment(app.id)}
                          disabled={isProcessing === app.id}
                          className="px-3 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white border border-red-100/50 text-red-650 rounded-lg text-[10px] font-bold transition cursor-pointer"
                        >
                          Hủy lịch
                        </button>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="py-12 text-center text-slate-400 text-xs font-semibold">
                    Không tìm thấy lịch hẹn nào khớp với bộ lọc.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

    </div>
  )
}
