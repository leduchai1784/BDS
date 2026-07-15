'use client'

import { useState } from 'react'

interface AppointmentItem {
  id: number
  name: string
  phone: string
  email: string | null
  date: string
  time: string
  message: string | null
  status: string
  rejectReason: string | null
  property: {
    id: string
    title: string
    address: string
  }
}

interface AdminAppointmentsTabProps {
  initialAppointments: AppointmentItem[]
}

export default function AdminAppointmentsTab({ initialAppointments }: AdminAppointmentsTabProps) {
  const [appointments, setAppointments] = useState<AppointmentItem[]>(initialAppointments)
  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('')
  const [isProcessing, setIsProcessing] = useState<number | null>(null)

  const handleApprove = async (id: number) => {
    if (!confirm('Bạn có chắc chắn muốn duyệt lịch hẹn này?')) return
    setIsProcessing(id)
    try {
      const res = await fetch(`/api/appointments/${id}/approve`, {
        method: 'POST'
      })
      const data = await res.json()
      if (res.ok && data.success) {
        setAppointments(prev =>
          prev.map(app => app.id === id ? { ...app, status: 'approved' } : app)
        )
      } else {
        alert(data.error || 'Lỗi chấp thuận lịch hẹn')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  const handleReject = async (id: number) => {
    const reason = prompt('Nhập lý do từ chối lịch hẹn:')
    if (reason === null) return // User cancelled

    setIsProcessing(id)
    try {
      const res = await fetch(`/api/appointments/${id}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rejectReason: reason || 'Lịch bận, hẹn khách vào thời gian khác.' })
      })
      const data = await res.json()
      if (res.ok && data.success) {
        setAppointments(prev =>
          prev.map(app => app.id === id ? { ...app, status: 'rejected', rejectReason: reason } : app)
        )
      } else {
        alert(data.error || 'Lỗi từ chối lịch hẹn')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  const handleCancel = async (id: number) => {
    if (!confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?')) return
    setIsProcessing(id)
    try {
      const res = await fetch(`/api/appointments/${id}/cancel`, {
        method: 'POST'
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

  // Filter clientside
  const filteredAppointments = appointments.filter(app => {
    const matchesSearch = !search ||
      app.name.toLowerCase().includes(search.toLowerCase()) ||
      app.phone.includes(search) ||
      app.property.title.toLowerCase().includes(search.toLowerCase())

    const matchesStatus = !status || app.status === status

    return matchesSearch && matchesStatus
  })

  return (
    <div className="space-y-6 text-left">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100">
        <h2 className="text-xl font-bold text-slate-800">Quản lý lịch hẹn</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và phê duyệt lịch đi xem nhà của khách hàng trên hệ thống.</p>
      </div>

      {/* Filters & Search Card */}
      <div className="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 shadow-sm">
        <div className="grid grid-cols-1 sm:grid-cols-12 gap-3">
          {/* Keyword Search */}
          <div className="sm:col-span-8 relative">
            <i className="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
            <input
              type="text"
              placeholder="Tìm kiếm theo tên khách hàng hoặc số điện thoại..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Status filter */}
          <div className="sm:col-span-4">
            <select
              value={status}
              onChange={(e) => setStatus(e.target.value)}
              className="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
            >
              <option value="">-- Trạng thái --</option>
              <option value="pending">Chờ duyệt</option>
              <option value="approved">Đã duyệt</option>
              <option value="rejected">Từ chối</option>
              <option value="cancelled">Đã hủy</option>
            </select>
          </div>
        </div>
      </div>

      {/* Appointments Table Card */}
      <div className="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
          <table className="min-w-full text-left text-xs text-slate-600 font-semibold border-collapse">
            <thead className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 sticky top-0 z-10">
              <tr>
                <th scope="col" className="px-6 py-4">Khách hàng</th>
                <th scope="col" className="px-6 py-4">Ngày giờ hẹn</th>
                <th scope="col" className="px-6 py-4">Bất động sản</th>
                <th scope="col" className="px-6 py-4">Trạng thái</th>
                <th scope="col" className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filteredAppointments.length > 0 ? (
                filteredAppointments.map(app => (
                  <tr key={app.id} className="hover:bg-slate-50/50 transition">
                    {/* Guest info */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="block text-slate-800 font-bold text-[13px]">{app.name}</span>
                      <span className="text-[10px] text-slate-400 block mt-0.5">
                        <i className="fa-solid fa-phone mr-1" />
                        {app.phone}
                      </span>
                    </td>

                    {/* Date/Time */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="block text-slate-800 text-[13px]">
                        {new Date(app.date).toLocaleDateString('vi-VN')}
                      </span>
                      <span className="text-[10px] text-slate-400 block mt-0.5">
                        <i className="fa-solid fa-clock mr-1" />
                        {new Date(app.time).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </td>

                    {/* Property */}
                    <td className="px-6 py-4 max-w-[200px] truncate">
                      {app.property ? (
                        <a 
                          href={`/property/${app.property.id}`} 
                          className="hover:text-primary font-bold text-slate-850 block truncate text-[13px]"
                          title={app.property.title}
                        >
                          {app.property.title}
                        </a>
                      ) : (
                        <span className="text-slate-400 italic">BĐS không tồn tại</span>
                      )}
                    </td>

                    {/* Status */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      {app.status === 'approved' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">
                          Đã duyệt
                        </span>
                      ) : app.status === 'pending' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                          Chờ duyệt
                        </span>
                      ) : app.status === 'rejected' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">
                          Từ chối
                        </span>
                      ) : (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                          Đã hủy
                        </span>
                      )}
                    </td>

                    {/* Actions */}
                    <td className="px-6 py-4 whitespace-nowrap text-right">
                      <div className="flex items-center justify-end gap-1.5">
                        {app.status === 'pending' && (
                          <>
                            {/* Approve */}
                            <button
                              onClick={() => handleApprove(app.id)}
                              disabled={isProcessing === app.id}
                              className="px-2.5 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 rounded-xl text-[10px] font-extrabold cursor-pointer transition shadow-sm"
                            >
                              Duyệt lịch
                            </button>

                            {/* Reject */}
                            <button
                              onClick={() => handleReject(app.id)}
                              disabled={isProcessing === app.id}
                              className="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-[10px] font-extrabold cursor-pointer transition shadow-sm"
                            >
                              Từ chối
                            </button>
                          </>
                        )}

                        {(app.status === 'pending' || app.status === 'approved') && (
                          <button
                            onClick={() => handleCancel(app.id)}
                            disabled={isProcessing === app.id}
                            className="px-2.5 py-1.5 bg-red-500 hover:bg-red-650 text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer"
                          >
                            Hủy lịch
                          </button>
                        )}

                        {app.status !== 'pending' && app.status !== 'approved' && (
                          <span className="text-[10px] text-slate-400">Không có thao tác</span>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={5} className="py-16 text-center text-slate-400 font-semibold">
                    <i className="fa-solid fa-calendar-xmark text-3xl mb-3 block text-slate-350" />
                    Chưa có lịch hẹn xem nhà nào trên hệ thống.
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
