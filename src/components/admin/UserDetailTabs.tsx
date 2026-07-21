'use client'

import { useState } from 'react'
import Link from 'next/link'

interface PropertyItem {
  id: string
  title: string
  address: string
  priceLabel: string
}

interface AppointmentItem {
  id: number
  name: string
  phone: string
  date: string
  status: string
  property: {
    title: string
    address: string
  }
}

interface UserDetailTabsProps {
  properties: PropertyItem[]
  appointments: AppointmentItem[]
  isNks?: boolean
}

export default function UserDetailTabs({ properties, appointments, isNks = false }: UserDetailTabsProps) {
  const [activeTab, setActiveTab] = useState<'properties' | 'appointments'>('properties')

  return (
    <div className="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
      {/* Tab Headers */}
      <div className="flex border-b border-slate-100">
        <button
          onClick={() => setActiveTab('properties')}
          className={`flex-1 flex items-center justify-center gap-2 py-4 text-xs font-bold transition cursor-pointer relative ${
            activeTab === 'properties'
              ? 'text-primary'
              : 'text-slate-400 hover:text-slate-600'
          }`}
        >
          <i className="fa-solid fa-key" />
          <span>Tin đăng</span>
          <span className={`inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-black ${
            activeTab === 'properties'
              ? 'bg-primary/10 text-primary'
              : 'bg-slate-100 text-slate-400'
          }`}>
            {properties.length}
          </span>
          {activeTab === 'properties' && (
            <span className="absolute bottom-0 left-0 right-0 h-[2px] bg-primary" />
          )}
        </button>

        <button
          onClick={() => setActiveTab('appointments')}
          className={`flex-1 flex items-center justify-center gap-2 py-4 text-xs font-bold transition cursor-pointer relative ${
            activeTab === 'appointments'
              ? 'text-primary'
              : 'text-slate-400 hover:text-slate-600'
          }`}
        >
          <i className="fa-solid fa-calendar-check" />
          <span>Lịch hẹn</span>
          <span className={`inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-black ${
            activeTab === 'appointments'
              ? 'bg-primary/10 text-primary'
              : 'bg-slate-100 text-slate-400'
          }`}>
            {appointments.length}
          </span>
          {activeTab === 'appointments' && (
            <span className="absolute bottom-0 left-0 right-0 h-[2px] bg-primary" />
          )}
        </button>
      </div>

      {/* Tab Content */}
      <div className="p-6">
        {activeTab === 'properties' ? (
          <div className="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
            {properties.length > 0 ? (
              properties.map(p => (
                <div key={p.id} className="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                  <div className="space-y-0.5 truncate">
                    <strong className="block text-xs font-bold text-slate-800 truncate max-w-md">{p.title}</strong>
                    <span className="block text-[10px] text-slate-400 font-semibold">{p.address} | {p.priceLabel}</span>
                  </div>
                  <Link 
                    href={isNks ? `/property/${p.id}` : `/admin/properties?id=${p.id}`}
                    className="px-3 py-1.5 bg-slate-100 hover:bg-primary hover:text-white rounded-lg text-[9px] font-bold transition cursor-pointer flex-shrink-0"
                  >
                    Xem tin
                  </Link>
                </div>
              ))
            ) : (
              <div className="py-12 flex flex-col items-center justify-center text-center space-y-3">
                <div className="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center">
                  <i className="fa-solid fa-key text-slate-300 text-lg" />
                </div>
                <div>
                  <p className="text-xs font-bold text-slate-500">Chưa có tin đăng</p>
                  <p className="text-[10px] text-slate-400 font-semibold mt-0.5">
                    {isNks ? 'Môi giới này chưa đăng tin nào.' : 'Thành viên này chưa đăng tin nào.'}
                  </p>
                </div>
              </div>
            )}
          </div>
        ) : (
          <div className="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
            {appointments.length > 0 ? (
              appointments.map(app => (
                <div key={app.id} className="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                  <div className="space-y-0.5 truncate">
                    <strong className="block text-xs font-bold text-slate-850 truncate max-w-md">{app.property.title}</strong>
                    <span className="block text-[10px] text-slate-400 font-semibold">Đặt ngày: {app.date} | Trạng thái: {app.status}</span>
                  </div>
                </div>
              ))
            ) : (
              <div className="py-12 flex flex-col items-center justify-center text-center space-y-3">
                <div className="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center">
                  <i className="fa-solid fa-calendar-check text-slate-300 text-lg" />
                </div>
                <div>
                  <p className="text-xs font-bold text-slate-500">Chưa có lịch hẹn</p>
                  <p className="text-[10px] text-slate-400 font-semibold mt-0.5">
                    {isNks ? 'Môi giới này chưa có lịch hẹn nào trên hệ thống.' : 'Thành viên này chưa đặt lịch hẹn xem nhà nào.'}
                  </p>
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  )
}
