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
        return <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-green-55 text-green-700">Đã đồng ý</span>
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
    // timeStr is returned from Postgres as ISO String or Time, let's extract hh:mm
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
              className="bg-white border border-slate-100 rounded-2xl p-4 sm:p-5 shadow-xs space-y-4"
            >
              {/* Header */}
              <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 border-b border-slate-100/60 pb-3">
                <div className="space-y-1">
                  <h4 className="text-xs sm:text-sm font-extrabold text-slate-800 hover:text-primary transition leading-tight">
                    <Link href={`/property/${item.property.id}`}>{item.property.title}</Link>
                  </h4>
                  <p className="text-[10px] text-slate-400 font-semibold">{item.property.address}</p>
                </div>
                <div className="flex-shrink-0">
                  {getStatusBadge(item.status)}
                </div>
              </div>

              {/* Client Info Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs font-medium text-slate-650">
                <div>
                  <span className="block text-[9px] font-bold uppercase tracking-wider text-slate-450 mb-1">Thời gian gặp</span>
                  <span>{formatDateTime(item.date, item.time)}</span>
                </div>

                <div>
                  <span className="block text-[9px] font-bold uppercase tracking-wider text-slate-450 mb-1">
                    {activeSubTab === 'owner' ? 'Khách hàng' : 'Thông tin liên hệ'}
                  </span>
                  <div><strong>{item.name}</strong></div>
                  <div className="text-[10px] text-slate-450">SĐT: {item.phone}</div>
                </div>

                <div>
                  <span className="block text-[9px] font-bold uppercase tracking-wider text-slate-450 mb-1">Ghi chú & Lời nhắn</span>
                  <span className="text-[11px] italic">{item.message || 'Không có.'}</span>
                </div>
              </div>

              {/* Rejection notice if rejected */}
              {item.status === 'rejected' && item.rejectReason && (
                <div className="p-3 bg-red-50 border border-red-100/50 rounded-xl text-[11px] text-red-700 font-semibold">
                  <strong>Lý do từ chối:</strong> {item.rejectReason}
                </div>
              )}

              {/* Actions footer */}
              <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100/60">
                {activeSubTab === 'owner' && item.status === 'pending' && (
                  <>
                    <button
                      onClick={() => handleApprove(item.id)}
                      disabled={loadingId === item.id}
                      className="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold rounded-lg shadow-sm transition cursor-pointer disabled:opacity-50"
                    >
                      Xác nhận
                    </button>
                    <button
                      onClick={() => handleReject(item.id)}
                      disabled={loadingId === item.id}
                      className="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-650 text-[10px] font-bold rounded-lg transition cursor-pointer disabled:opacity-50"
                    >
                      Từ chối
                    </button>
                  </>
                )}

                {item.status !== 'cancelled' && item.status !== 'rejected' && (
                  <button
                    onClick={() => handleCancel(item.id, activeSubTab)}
                    disabled={loadingId === item.id}
                    className="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg transition cursor-pointer disabled:opacity-50"
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
    </div>
  )
}
