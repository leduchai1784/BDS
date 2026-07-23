'use client'

import { useState } from 'react'
import Link from 'next/link'

interface PropertyItem {
  id: string
  title: string
  priceLabel: string
  address: string
  status: string
  viewsCount: number
  imagePath?: string | null
  createdAt: string | null
}

interface MyPropertiesTabProps {
  initialProperties: PropertyItem[]
  onSuccess: (message: string) => void
}

export default function MyPropertiesTab({ initialProperties, onSuccess }: MyPropertiesTabProps) {
  const [list, setList] = useState<PropertyItem[]>(initialProperties)
  const [loadingId, setLoadingId] = useState<string | null>(null)

  const handleToggleStatus = async (id: string, currentStatus: string) => {
    const nextStatus = currentStatus === 'rented' ? 'approved' : 'rented'
    setLoadingId(id)

    try {
      const res = await fetch(`/api/properties/${id}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: nextStatus })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setList(prev => prev.map(item => item.id === id ? { ...item, status: nextStatus } : item))
        onSuccess(nextStatus === 'rented' ? 'Đã ẩn tin đăng thành công!' : 'Đã hiện tin đăng thành công!')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingId(null)
    }
  }

  const handleExtend = async (id: string) => {
    setLoadingId(id)
    try {
      const res = await fetch(`/api/properties/${id}/extend`, {
        method: 'POST'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        onSuccess('Gia hạn tin đăng thành công! Tin đăng đã được đẩy lên đầu.')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingId(null)
    }
  }

  const handleDelete = async (id: string) => {
    if (!confirm('Bạn có chắc chắn muốn xóa tin đăng này?')) return
    setLoadingId(id)

    try {
      const res = await fetch(`/api/properties/${id}`, {
        method: 'DELETE'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setList(prev => prev.filter(item => item.id !== id))
        onSuccess('Xóa tin đăng thành công!')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingId(null)
    }
  }

  return (
    <div className="space-y-6 text-left">
      <div className="flex justify-between items-center">
        <div>
          <h3 className="text-base font-black text-slate-800">Quản lý tin đăng</h3>
          <p className="text-[11px] text-slate-500 font-semibold">Danh sách các bất động sản bạn đang đăng bán hoặc cho thuê.</p>
        </div>
        <Link 
          href="/property/create" 
          className="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 transition cursor-pointer"
        >
          <i className="fa-solid fa-plus mr-1.5" />
          Đăng tin mới
        </Link>
      </div>

      {list.length > 0 ? (
        <div className="space-y-4">
          {list.map(p => (
            <div 
              key={p.id} 
              className="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
              <div className="flex items-start gap-4 flex-grow text-left min-w-0">
                {/* Property Thumbnail Image */}
                <div className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100/60 flex-shrink-0 relative">
                  <img 
                    src={p.imagePath ? (p.imagePath.startsWith('http') ? p.imagePath : `/${p.imagePath}`) : '/images/apartment_placeholder.png'} 
                    alt={p.title} 
                    className="w-full h-full object-cover"
                    onError={(e) => {
                      e.currentTarget.src = '/images/apartment_placeholder.png'
                    }}
                  />
                </div>

                <div className="space-y-1.5 flex-grow min-w-0">
                  <div className="flex flex-wrap items-center gap-2">
                    <h4 className="text-xs sm:text-sm font-extrabold text-slate-800 hover:text-primary transition truncate max-w-full">
                      <Link href={`/property/${p.id}`}>{p.title}</Link>
                    </h4>
                    {/* Status Badges */}
                    {p.status === 'approved' && (
                      <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-green-55 text-green-700 whitespace-nowrap">Đang hiển thị</span>
                    )}
                    {p.status === 'rented' && (
                      <span className="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-500 whitespace-nowrap">Đang ẩn</span>
                    )}
                  </div>
                  <p className="text-[11px] text-slate-400 font-semibold truncate">{p.address}</p>
                  <div className="flex flex-wrap items-center gap-x-4 gap-y-1 text-[10px] text-slate-450 font-bold">
                    <span>Giá: <strong className="text-primary">{p.priceLabel}</strong></span>
                    <span>Lượt xem: <strong>{p.viewsCount}</strong></span>
                    {p.createdAt && (
                      <span>Ngày đăng: <strong>{new Date(p.createdAt).toLocaleDateString('vi-VN')}</strong></span>
                    )}
                  </div>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex items-center flex-wrap gap-2 flex-shrink-0">
                {/* Edit */}
                <Link
                  href={`/property/${p.id}/edit`}
                  title="Sửa tin"
                  className="w-8 h-8 bg-slate-50 hover:bg-slate-100 text-slate-655 hover:text-slate-800 rounded-xl flex items-center justify-center transition cursor-pointer active:scale-95 border border-slate-200/40"
                >
                  <i className="fa-solid fa-pen-to-square text-[13px]"></i>
                </Link>

                {/* Hide / Show */}
                <button
                  onClick={() => handleToggleStatus(p.id, p.status)}
                  disabled={loadingId === p.id}
                  title={p.status === 'rented' ? 'Hiện tin' : 'Ẩn tin'}
                  className={`w-8 h-8 rounded-xl flex items-center justify-center transition cursor-pointer active:scale-95 disabled:opacity-50 border ${
                    p.status === 'rented' 
                      ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border-emerald-100/50' 
                      : 'bg-slate-100 hover:bg-slate-200 text-slate-600 border-slate-200/50'
                  }`}
                >
                  {p.status === 'rented' ? (
                    <i className="fa-solid fa-eye text-[13px]"></i>
                  ) : (
                    <i className="fa-solid fa-eye-slash text-[13px]"></i>
                  )}
                </button>

                {/* Extend */}
                <button
                  onClick={() => handleExtend(p.id)}
                  disabled={loadingId === p.id}
                  title="Gia hạn (Đẩy top)"
                  className="w-8 h-8 bg-primary/10 hover:bg-primary/20 text-primary rounded-xl flex items-center justify-center transition cursor-pointer active:scale-95 disabled:opacity-50 border border-primary/20"
                >
                  <i className="fa-solid fa-arrow-trend-up text-[13px]"></i>
                </button>

                {/* Delete */}
                <button
                  onClick={() => handleDelete(p.id)}
                  disabled={loadingId === p.id}
                  title="Xóa"
                  className="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl flex items-center justify-center transition cursor-pointer active:scale-95 disabled:opacity-50 border border-red-100/50"
                >
                  <i className="fa-solid fa-trash-can text-[13px]"></i>
                </button>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="text-center py-12 bg-white border border-slate-100 rounded-3xl text-slate-400 font-semibold text-xs">
          Bạn chưa đăng bất kỳ tin rao bất động sản nào.
        </div>
      )}
    </div>
  )
}
