'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import Link from 'next/link'

interface AdminAppointmentsTabProps {
  initialAppointments: any[]
}

export default function AdminAppointmentsTab({ initialAppointments }: AdminAppointmentsTabProps) {
  const [appointments, setAppointments] = useState(initialAppointments)
  const [searchTerm, setSearchTerm] = useState('')
  const [filterStatus, setFilterStatus] = useState('')
  const [statusOpen, setStatusOpen] = useState(false)

  const handleCancelAppointment = async (appointmentId: number) => {
    if (!window.confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?')) return
    try {
      const res = await fetch(`/api/admin/appointments/${appointmentId}/cancel`, {
        method: 'POST'
      })
      const data = await res.json()
      if (data.success) {
        toast.success(data.message)
        setAppointments(prev => prev.map(a => a.id === appointmentId ? { ...a, status: 'cancelled' } : a))
      } else {
        toast.error(data.error || 'Có lỗi xảy ra')
      }
    } catch (e: any) {
      toast.error(e.message || 'Lỗi mạng')
    }
  }

  // Filter logic matching bds_php
  const filteredAppointments = appointments.filter(a => {
    const textMatch = searchTerm.trim() === '' ||
      a.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      a.phone.includes(searchTerm) ||
      (a.email && a.email.toLowerCase().includes(searchTerm.toLowerCase()))

    const statusMatch = filterStatus === '' || a.status === filterStatus

    return textMatch && statusMatch
  })

  const getStatusLabel = (status: string) => {
    if (status === 'approved') return 'Đã duyệt'
    if (status === 'pending') return 'Chờ duyệt'
    if (status === 'rejected') return 'Từ chối'
    if (status === 'cancelled') return 'Đã hủy'
    if (status === 'completed') return 'Đã xem nhà'
    return '-- Trạng thái --'
  }

  return (
    <div className="space-y-6">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100 mb-6 text-left">
        <h2 className="text-xl font-bold text-slate-800">Quản lý lịch hẹn</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và hủy/quản lý lịch hẹn đi xem nhà của khách hàng trên hệ thống.</p>
      </div>

      {/* Filters & Search Card */}
      <div className="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 shadow-sm text-left">
        <div className="grid grid-cols-1 sm:grid-cols-12 gap-4">
          {/* Search Keyword */}
          <div className="sm:col-span-8 relative">
            <i className="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              id="appointmentSearchTerm"
              name="appointmentSearchTerm"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Tìm kiếm theo tên khách hàng hoặc số điện thoại..." 
              className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Status Filter */}
          <div className="relative sm:col-span-4">
            <button 
              type="button" 
              onClick={() => setStatusOpen(!statusOpen)} 
              className="w-full px-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
            >
              <span>{filterStatus ? getStatusLabel(filterStatus) : '-- Trạng thái --'}</span>
              <i className="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>
          
            {statusOpen && (
              <div 
                onMouseLeave={() => setStatusOpen(false)}
                className="absolute z-35 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
              >
                {['', 'pending', 'approved', 'rejected', 'cancelled', 'completed'].map((status) => (
                  <button 
                    key={status}
                    type="button" 
                    onClick={() => {
                      setFilterStatus(status)
                      setStatusOpen(false)
                    }}
                    className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterStatus === status ? 'bg-primary/5 text-primary font-bold' : ''}`}
                  >
                    {getStatusLabel(status)}
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Appointments Table Card */}
      <div className="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
        <div className="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
          {filteredAppointments.length > 0 ? (
            <table className="min-w-full text-left text-xs text-slate-600 font-semibold">
              <thead className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                <tr>
                  <th scope="col" className="px-6 py-4">Khách hàng</th>
                  <th scope="col" className="px-6 py-4">Ngày giờ hẹn</th>
                  <th scope="col" className="px-6 py-4">Bất động sản</th>
                  <th scope="col" className="px-6 py-4">Trạng thái</th>
                  <th scope="col" className="px-6 py-4 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredAppointments.map((appItem) => (
                  <tr key={appItem.id} className="hover:bg-slate-50/50 transition">
                    {/* Guest info */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="block text-slate-800 font-bold">{appItem.name}</span>
                      <span className="text-[10px] text-slate-400"><i className="fa-solid fa-phone mr-1"></i>{appItem.phone}</span>
                    </td>
                    {/* Date/Time */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="block text-slate-800">{new Date(appItem.date).toLocaleDateString('vi-VN')}</span>
                      <span className="text-[10px] text-slate-400"><i className="fa-solid fa-clock mr-1"></i>{new Date(appItem.time).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}</span>
                    </td>
                    {/* Property */}
                    <td className="px-6 py-4 max-w-[200px] truncate">
                      {appItem.property ? (
                        <Link href={`/property/${appItem.property.id}`} className="hover:text-primary font-bold text-slate-800 block truncate" title={appItem.property.title}>
                          {appItem.property.title}
                        </Link>
                      ) : (
                        <span className="text-slate-400 italic">BĐS không tồn tại</span>
                      )}
                    </td>
                    {/* Status */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      {appItem.status === 'approved' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Đã duyệt</span>
                      ) : appItem.status === 'pending' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                      ) : appItem.status === 'rejected' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Từ chối</span>
                      ) : appItem.status === 'cancelled' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-500 border border-slate-200">Đã hủy</span>
                      ) : (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Đã xem nhà</span>
                      )}
                    </td>
                    {/* Actions */}
                    <td className="px-6 py-4 whitespace-nowrap text-right">
                      {appItem.status === 'pending' ? (
                        <button 
                          type="button" 
                          onClick={() => handleCancelAppointment(appItem.id)}
                          className="px-2.5 py-1.5 bg-red-500 hover:bg-red-650 text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer"
                        >
                          Hủy lịch
                        </button>
                      ) : (
                        <span className="text-[10px] text-slate-400">Không có thao tác</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <div className="py-16 text-center text-slate-400 font-semibold">
              <i className="fa-solid fa-calendar-xmark text-3xl mb-3 block text-slate-350"></i>
              Chưa có lịch hẹn xem nhà nào thỏa mãn bộ lọc.
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
