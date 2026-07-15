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
    if (reason === null) return // User cancelled prompt

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

  const getStatusBadge = (s: string) => {
    switch (s) {
      case 'approved':
        return (
          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
            <i className="fa-solid fa-circle-check mr-1 text-[9px]" /> Chấp thuận
          </span>
        )
      case 'pending':
        return (
          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
            <i className="fa-solid fa-clock mr-1 text-[9px]" /> Chờ duyệt
          </span>
        )
      case 'rejected':
        return (
          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-650 border border-red-200">
            <i className="fa-solid fa-circle-xmark mr-1 text-[9px]" /> Từ chối
          </span>
        )
      case 'cancelled':
        return (
          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
            <i className="fa-solid fa-ban mr-1 text-[9px]" /> Đã hủy
          </span>
        )
      default:
        return null
    }
  }

  return (
    <div className="space-y-6 text-left">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100">
        <h2 className="text-xl font-bold text-slate-800">Quản lý lịch hẹn xem nhà</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Duyệt hoặc hủy các yêu cầu đặt lịch hẹn đi xem nhà trực tiếp của khách hàng.</p>
      </div>

      {/* Filters Bar */}
      <div className="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-grow max-w-2xl">
          {/* Keyword Search */}
          <div className="relative">
            <i className="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
            <input
              type="text"
              placeholder="Tên khách hàng, SĐT hoặc bất động sản..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Status selector */}
          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
          >
            <option value="">-- Tất cả lịch hẹn --</option>
            <option value="pending">Chờ duyệt</option>
            <option value="approved">Đã chấp thuận</option>
            <option value="rejected">Từ chối</option>
            <option value="cancelled">Đã hủy</option>
          </select>
        </div>
      </div>

      {/* Table view */}
      <div className="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th className="px-6 py-4 text-left">Khách hàng</th>
                <th className="px-6 py-4 text-left">Thời gian</th>
                <th className="px-6 py-4 text-left">Bất động sản</th>
                <th className="px-6 py-4 text-left">Trạng thái</th>
                <th className="px-6 py-4 text-center">Hành động</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-xs">
              {filteredAppointments.length > 0 ? (
                filteredAppointments.map(app => (
                  <tr key={app.id} className="hover:bg-slate-50/50 transition">
                    {/* Customer info */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      <strong className="block text-slate-800 font-bold">{app.name}</strong>
                      <div className="text-[10px] text-slate-400 mt-0.5">{app.phone}</div>
                      {app.email && <div className="text-[10px] text-slate-400">{app.email}</div>}
                    </td>

                    {/* Date/Time */}
                    <td className="px-6 py-4 whitespace-nowrap font-medium text-slate-600">
                      <div>{new Date(app.date).toLocaleDateString('vi-VN')}</div>
                      <div className="text-[10px] text-slate-400 mt-0.5">
                        {new Date(app.time).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}
                      </div>
                    </td>

                    {/* Property title */}
                    <td className="px-6 py-4 max-w-xs">
                      <strong className="block text-slate-800 font-semibold truncate" title={app.property.title}>
                        {app.property.title}
                      </strong>
                      <span className="block text-[10px] text-slate-400 truncate" title={app.property.address}>
                        {app.property.address}
                      </span>
                    </td>

                    {/* Status badge */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      {getStatusBadge(app.status)}
                      {app.status === 'rejected' && app.rejectReason && (
                        <div className="text-[10px] text-red-500 italic mt-1 font-semibold max-w-[150px] truncate" title={app.rejectReason}>
                          Lý do: {app.rejectReason}
                        </div>
                      )}
                    </td>

                    {/* Actions */}
                    <td className="px-6 py-4 whitespace-nowrap text-center">
                      <div className="flex items-center justify-center gap-1.5">
                        {/* Approve */}
                        {app.status === 'pending' && (
                          <button
                            onClick={() => handleApprove(app.id)}
                            disabled={isProcessing === app.id}
                            className="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center transition cursor-pointer"
                            title="Chấp thuận lịch hẹn"
                          >
                            <i className="fa-solid fa-circle-check" />
                          </button>
                        )}

                        {/* Reject */}
                        {app.status === 'pending' && (
                          <button
                            onClick={() => handleReject(app.id)}
                            disabled={isProcessing === app.id}
                            className="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-250 text-amber-600 flex items-center justify-center transition cursor-pointer"
                            title="Từ chối lịch hẹn"
                          >
                            <i className="fa-solid fa-circle-xmark" />
                          </button>
                        )}

                        {/* Cancel */}
                        {(app.status === 'pending' || app.status === 'approved') && (
                          <button
                            onClick={() => handleCancel(app.id)}
                            disabled={isProcessing === app.id}
                            className="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-655 flex items-center justify-center transition cursor-pointer"
                            title="Hủy lịch hẹn"
                          >
                            <i className="fa-solid fa-ban" />
                          </button>
                        )}

                        {app.status !== 'pending' && app.status !== 'approved' && (
                          <span className="text-[10px] text-slate-400 font-bold italic">Đã xử lý</span>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-slate-400 font-bold uppercase tracking-wider">
                    Không tìm thấy yêu cầu đặt lịch nào
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
