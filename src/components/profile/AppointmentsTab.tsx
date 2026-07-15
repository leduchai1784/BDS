'use client'

import { useState } from 'react'
import Link from 'next/link'

interface Property {
  id: string
  title: string
  address: string
}

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
  property: Property
}

interface AppointmentsTabProps {
  initialTenantAppointments: AppointmentItem[]
  initialOwnerAppointments: AppointmentItem[]
  isOwner: boolean
}

export default function AppointmentsTab({
  initialTenantAppointments,
  initialOwnerAppointments,
  isOwner
}: AppointmentsTabProps) {
  const [tenantList, setTenantList] = useState<AppointmentItem[]>(initialTenantAppointments)
  const [ownerList, setOwnerList] = useState<AppointmentItem[]>(initialOwnerAppointments)
  
  const [activeSubTab, setActiveSubTab] = useState<'tenant' | 'owner'>(isOwner ? 'owner' : 'tenant')
  const [loadingId, setLoadingId] = useState<number | null>(null)
  const [selectedApp, setSelectedApp] = useState<AppointmentItem | null>(null)

  const handleApprove = async (id: number) => {
    setLoadingId(id)
    try {
      const res = await fetch(`/api/appointments/${id}/approve`, {
        method: 'POST'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setOwnerList(prev => prev.map(item => item.id === id ? { ...item, status: 'approved' } : item))
      }
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingId(null)
    }
  }

  const handleReject = async (id: number) => {
    const reason = prompt('Vui lòng nhập lý do từ chối lịch hẹn:')
    if (reason === null) return // User cancelled prompt

    setLoadingId(id)
    try {
      const res = await fetch(`/api/appointments/${id}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reason })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setOwnerList(prev => prev.map(item => item.id === id ? { ...item, status: 'rejected', rejectReason: reason } : item))
      }
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingId(null)
    }
  }

  const handleCancel = async (id: number, type: 'tenant' | 'owner') => {
    if (!confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?')) return

    setLoadingId(id)
    try {
      const res = await fetch(`/api/appointments/${id}/cancel`, {
        method: 'POST'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        const updater = (list: AppointmentItem[]) => 
          list.map(item => item.id === id ? { ...item, status: 'cancelled' } : item)
        
        if (type === 'tenant') {
          setTenantList(updater)
        } else {
          setOwnerList(updater)
        }
      }
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingId(null)
    }
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'approved':
        return <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-green-55 text-green-700">Đã xác nhận</span>
      case 'rejected':
        return <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-red-50 text-red-650">Bị từ chối</span>
      case 'cancelled':
        return <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-500">Đã hủy</span>
      default:
        return <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-55 text-amber-700">Chờ duyệt</span>
    }
  }

  const currentList = activeSubTab === 'owner' ? ownerList : tenantList

  const formatDateTime = (dateStr: string, timeStr: any) => {
    const d = new Date(dateStr).toLocaleDateString('vi-VN')
    let t = timeStr
    if (typeof timeStr === 'string' && timeStr.includes('T')) {
      t = timeStr.split('T')[1].substring(0, 5)
    } else if (timeStr instanceof Date) {
      t = timeStr.toTimeString().substring(0, 5)
    }
    return `📅 ${d} lúc ⏰ ${t}`
  }

  return (
    <div className="space-y-6 text-left">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h3 className="text-base font-black text-slate-800">Quản lý lịch hẹn</h3>
          <p className="text-[11px] text-slate-500 font-semibold">Theo dõi cuộc gặp xem nhà đất của bạn.</p>
        </div>

        {/* Tab switch inside */}
        {isOwner && (
          <div className="flex border border-slate-200 rounded-xl p-1 bg-slate-100 self-start">
            <button
              onClick={() => setActiveSubTab('owner')}
              className={`px-3 py-1.5 rounded-lg text-[10px] font-bold transition cursor-pointer ${
                activeSubTab === 'owner' ? 'bg-white text-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'
              }`}
            >
              Khách đặt lịch hẹn ({ownerList.length})
            </button>
            <button
              onClick={() => setActiveSubTab('tenant')}
              className={`px-3 py-1.5 rounded-lg text-[10px] font-bold transition cursor-pointer ${
                activeSubTab === 'tenant' ? 'bg-white text-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'
              }`}
            >
              Lịch hẹn tôi đặt ({tenantList.length})
            </button>
          </div>
        )}
      </div>

      {currentList.length > 0 ? (
        <div className="space-y-4">
          {currentList.map(item => (
            <div 
              key={item.id} 
              className="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            >
              {/* Left: Property Info and Status */}
              <div className="space-y-1.5 flex-grow text-left min-w-0">
                <div className="flex flex-wrap items-center gap-2">
                  <h4 className="text-xs sm:text-sm font-extrabold text-slate-800 hover:text-primary transition leading-tight truncate max-w-full">
                    <Link href={`/property/${item.property.id}`}>{item.property.title}</Link>
                  </h4>
                  {getStatusBadge(item.status)}
                </div>
                <p className="text-[10px] text-slate-400 font-semibold truncate">{item.property.address}</p>
              </div>

              {/* Right: Actions */}
              <div className="flex items-center gap-2 flex-shrink-0">
                <button
                  type="button"
                  onClick={() => setSelectedApp(item)}
                  className="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-650 text-[10px] font-bold rounded-lg transition cursor-pointer"
                >
                  Xem chi tiết
                </button>

                {item.status !== 'cancelled' && item.status !== 'rejected' && (
                  <button
                    onClick={() => handleCancel(item.id, activeSubTab)}
                    disabled={loadingId === item.id}
                    className="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-650 text-[10px] font-bold rounded-lg transition cursor-pointer disabled:opacity-50"
                  >
                    Hủy lịch
                  </button>
                )}
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="text-center py-12 bg-white border border-slate-100 rounded-3xl text-slate-400 font-semibold text-xs">
          Chưa có lịch hẹn xem nhà nào được ghi nhận.
        </div>
      )}

      {/* Detail Modal */}
      {selectedApp && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-xl border border-slate-100">
            {/* Modal Header */}
            <div className="bg-primary px-6 py-4 flex items-center justify-between text-white">
              <h4 className="font-extrabold text-sm flex items-center gap-2">
                <i className="fa-regular fa-calendar-check text-base"></i>
                <span>Chi tiết lịch hẹn</span>
              </h4>
              <button 
                onClick={() => setSelectedApp(null)}
                className="text-white/80 hover:text-white transition cursor-pointer text-xs"
              >
                <i className="fa-solid fa-xmark text-base"></i>
              </button>
            </div>

            {/* Modal Body */}
            <div className="p-6 space-y-4 text-slate-700">
              {/* Property Details */}
              <div className="pb-3 border-b border-slate-100">
                <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Bất động sản</span>
                <Link 
                  href={`/property/${selectedApp.property.id}`}
                  className="font-extrabold text-slate-800 hover:text-primary transition text-xs leading-snug block"
                >
                  {selectedApp.property.title}
                </Link>
                <span className="text-[10px] text-slate-450 font-semibold mt-1 block">{selectedApp.property.address}</span>
              </div>

              {/* Time Details */}
              <div className="pb-3 border-b border-slate-100">
                <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Thời gian gặp</span>
                <span className="text-xs font-bold text-slate-800">{formatDateTime(selectedApp.date, selectedApp.time)}</span>
              </div>

              {/* Contact Details */}
              <div className="pb-3 border-b border-slate-100 space-y-1">
                <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                  {activeSubTab === 'owner' ? 'Thông tin khách hàng' : 'Thông tin liên hệ'}
                </span>
                <div className="text-xs font-bold text-slate-800">{selectedApp.name}</div>
                <div className="text-[11px] font-semibold text-slate-500">SĐT: {selectedApp.phone}</div>
                {selectedApp.email && (
                  <div className="text-[11px] font-semibold text-slate-500">Email: {selectedApp.email}</div>
                )}
              </div>

              {/* Message Details */}
              <div className="pb-3 border-b border-slate-100">
                <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Ghi chú & Lời nhắn</span>
                <p className="text-xs italic bg-slate-50 p-2.5 rounded-xl border border-slate-100/80 text-slate-600 margin-0 whitespace-pre-line">
                  {selectedApp.message || 'Không có.'}
                </p>
              </div>

              {/* Status Details */}
              <div className="flex items-center justify-between">
                <div>
                  <span className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Trạng thái</span>
                  {getStatusBadge(selectedApp.status)}
                </div>
                {selectedApp.status !== 'cancelled' && selectedApp.status !== 'rejected' && (
                  <button
                    onClick={() => {
                      handleCancel(selectedApp.id, activeSubTab)
                      setSelectedApp(null)
                    }}
                    className="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-650 text-xs font-bold rounded-xl transition cursor-pointer"
                  >
                    Hủy lịch hẹn
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
