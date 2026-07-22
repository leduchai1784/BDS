'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

interface Appointment {
  id: string;
  tenant: {
    name: string;
    phone: string;
    email: string;
  };
  property: {
    id: string;
    title: string;
  };
  bookingDate: string;
  bookingTime: string;
  status: string;
  notes?: string;
}

export default function OwnerAppointmentsPage() {
  const [appointments, setAppointments] = useState<Appointment[]>([])
  const [loading, setLoading] = useState(true)
  const [processingId, setProcessingId] = useState<string | null>(null)

  async function loadAppointments() {
    try {
      const res = await fetch('/api/profile')
      if (res.ok) {
        const json = await res.json()
        if (json?.success && json?.data) {
          setAppointments(json.data.receivedAppointments || [])
        }
      }
    } catch (err) {
      console.error('Failed to load appointments:', err)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadAppointments()
  }, [])

  const handleStatusChange = async (id: string, newStatus: 'approve' | 'reject') => {
    setProcessingId(id)
    try {
      const res = await fetch(`/api/appointments/${id}/${newStatus}`, {
        method: 'POST',
      })
      if (res.ok) {
        await loadAppointments()
      } else {
        alert('Có lỗi xảy ra khi cập nhật trạng thái lịch hẹn.')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setProcessingId(null)
    }
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
          Lịch hẹn xem nhà
        </h1>
        <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
          Quản lý các lịch đặt hẹn xem phòng từ những khách thuê quan tâm tới tin đăng của bạn.
        </p>
      </div>

      {/* Appointments List */}
      <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm">
        {appointments.length === 0 ? (
          <div className="text-center py-20">
            <div className="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
              <i className="fa-solid fa-calendar-times" />
            </div>
            <h3 className="text-sm font-extrabold text-slate-700 dark:text-slate-300">Không có lịch hẹn nào</h3>
            <p className="text-xs text-slate-400 mt-1">Hiện tại chưa có khách hàng nào đặt lịch hẹn xem phòng của bạn.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs border-collapse">
              <thead>
                <tr className="border-b border-slate-100 dark:border-gray-800 text-slate-400 font-extrabold uppercase">
                  <th className="pb-3">Khách hàng</th>
                  <th className="pb-3">Liên hệ</th>
                  <th className="pb-3">Bất động sản liên quan</th>
                  <th className="pb-3">Thời gian đặt</th>
                  <th className="pb-3">Ghi chú</th>
                  <th className="pb-3">Trạng thái</th>
                  <th className="pb-3 text-right">Hành động</th>
                </tr>
              </thead>
              <tbody>
                {appointments.map((a) => (
                  <tr key={a.id} className="border-b border-slate-50 dark:border-gray-800 last:border-b-0 hover:bg-slate-50/50 dark:hover:bg-gray-800/30">
                    <td className="py-4 font-bold text-slate-800 dark:text-slate-200">{a.tenant?.name || 'Khách thuê'}</td>
                    <td className="py-4">
                      <span className="block font-semibold text-slate-700 dark:text-slate-300">{a.tenant?.phone}</span>
                      <span className="block text-[10px] text-slate-400 mt-0.5">{a.tenant?.email}</span>
                    </td>
                    <td className="py-4 pr-4">
                      <Link href={`/property/${a.property?.id}`} className="font-semibold text-primary hover:underline block max-w-[180px] truncate">
                        {a.property?.title}
                      </Link>
                    </td>
                    <td className="py-4 font-extrabold text-slate-800 dark:text-slate-200">
                      <div>{a.bookingDate}</div>
                      <div className="text-[10px] text-slate-400 font-semibold mt-0.5">{a.bookingTime}</div>
                    </td>
                    <td className="py-4 text-slate-500 max-w-[150px] truncate">{a.notes || '_'}</td>
                    <td className="py-4">
                      <span className={`inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                        a.status === 'approved' ? 'bg-emerald-50 text-emerald-600' :
                        a.status === 'rejected' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600'
                      }`}>
                        {a.status === 'approved' ? 'Đã duyệt' : a.status === 'rejected' ? 'Đã từ chối' : 'Chờ duyệt'}
                      </span>
                    </td>
                    <td className="py-4 text-right space-x-1.5 whitespace-nowrap">
                      {a.status === 'pending' && (
                        <>
                          <button
                            onClick={() => handleStatusChange(a.id, 'approve')}
                            disabled={processingId === a.id}
                            className="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[10px] transition disabled:opacity-50 cursor-pointer"
                          >
                            Duyệt
                          </button>
                          <button
                            onClick={() => handleStatusChange(a.id, 'reject')}
                            disabled={processingId === a.id}
                            className="px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-600 text-white font-bold text-[10px] transition disabled:opacity-50 cursor-pointer"
                          >
                            Từ chối
                          </button>
                        </>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}
