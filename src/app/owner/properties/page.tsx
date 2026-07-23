'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'
import { Toaster, toast } from 'sonner'

interface Property {
  id: string;
  title: string;
  price: number;
  priceLabel?: string;
  area: number;
  address?: string;
  status: string;
  viewsCount?: number;
  createdAt: string;
  images: string[];
}

export default function OwnerPropertiesPage() {
  const [properties, setProperties] = useState<Property[]>([])
  const [loading, setLoading] = useState(true)
  const [actionLoadingId, setActionLoadingId] = useState<string | null>(null)

  const loadProperties = async () => {
    try {
      const res = await fetch('/api/profile')
      if (res.ok) {
        const json = await res.json()
        if (json?.success && json?.data) {
          setProperties(json.data.properties || [])
        }
      }
    } catch (err) {
      console.error('Failed to load properties:', err)
      toast.error('Lỗi khi tải danh sách tin đăng.')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadProperties()
  }, [])

  const handleToggleStatus = async (id: string, currentStatus: string) => {
    const nextStatus = currentStatus === 'rented' || currentStatus === 'hidden' ? 'approved' : 'rented'
    setActionLoadingId(id)

    try {
      const res = await fetch(`/api/properties/${id}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: nextStatus })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setProperties(prev => prev.map(item => item.id === id ? { ...item, status: nextStatus } : item))
        toast.success(nextStatus === 'approved' ? 'Đã kích hoạt hiển thị tin đăng!' : 'Đã ẩn tin đăng thành công!')
      } else {
        toast.error(data.error || 'Không thể đổi trạng thái tin đăng.')
      }
    } catch (err) {
      console.error(err)
      toast.error('Lỗi kết nối mạng.')
    } finally {
      setActionLoadingId(null)
    }
  }

  const handleExtend = async (id: string) => {
    setActionLoadingId(id)
    try {
      const res = await fetch(`/api/properties/${id}/extend`, {
        method: 'POST'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        toast.success('Gia hạn tin đăng thành công! Tin đăng đã được đẩy lên vị trí mới nhất.')
        loadProperties()
      } else {
        toast.error(data.error || 'Lỗi khi gia hạn tin đăng.')
      }
    } catch (err) {
      console.error(err)
      toast.error('Lỗi kết nối máy chủ.')
    } finally {
      setActionLoadingId(null)
    }
  }

  const formatPrice = (price: number, priceLabel?: string) => {
    if (priceLabel) return priceLabel
    if (price >= 1000000000) return `${(price / 1000000000).toFixed(1)} tỷ`
    if (price >= 1000000) return `${(price / 1000000).toFixed(1)} triệu`
    return `${price.toLocaleString()} đ`
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6 text-left">
      <Toaster position="top-right" richColors />

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-gray-800 pb-5">
        <div>
          <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
            Quản lý tin đăng
          </h1>
          <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
            Danh sách tất cả tin đăng bất động sản mua bán và cho thuê của bạn trên hệ thống.
          </p>
        </div>

        <Link
          href="/owner/properties/create"
          className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs transition shadow-md shadow-primary/15 hover:shadow-lg hover:shadow-primary/25 cursor-pointer active:scale-95"
        >
          <i className="fa-solid fa-plus-circle text-sm" />
          Đăng tin mới
        </Link>
      </div>

      {/* Table List */}
      <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 shadow-sm">
        {properties.length === 0 ? (
          <div className="text-center py-20">
            <div className="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
              <i className="fa-solid fa-folder-open" />
            </div>
            <h3 className="text-sm font-extrabold text-slate-700 dark:text-slate-300">Không có tin đăng nào</h3>
            <p className="text-xs text-slate-400 mt-1">Bạn chưa có bất động sản nào trong hệ thống BDS Rental.</p>
            <Link
              href="/owner/properties/create"
              className="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold text-xs transition mt-4 shadow-sm"
            >
              <i className="fa-solid fa-wand-magic-sparkles mr-1" />
              Tạo tin đăng mới ngay
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs border-collapse">
              <thead>
                <tr className="border-b border-slate-100 dark:border-gray-800 text-slate-400 font-extrabold uppercase">
                  <th className="pb-3 w-[80px]">Ảnh bìa</th>
                  <th className="pb-3">Thông tin chi tiết</th>
                  <th className="pb-3">Mức giá</th>
                  <th className="pb-3">Diện tích</th>
                  <th className="pb-3">Trạng thái</th>
                  <th className="pb-3 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                {properties.map((p) => {
                  const imageSrc = p.images && p.images.length > 0 
                    ? p.images[0] 
                    : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=400&q=80';

                  const isLive = p.status === 'active' || p.status === 'approved'
                  const isLoadingThis = actionLoadingId === p.id

                  return (
                    <tr key={p.id} className="border-b border-slate-50 dark:border-gray-800 last:border-b-0 hover:bg-slate-50/50 dark:hover:bg-gray-800/30">
                      <td className="py-4">
                        <div className="relative w-16 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200/50">
                          <img 
                            src={imageSrc} 
                            alt={p.title} 
                            className="w-full h-full object-cover"
                          />
                        </div>
                      </td>
                      <td className="py-4 pr-4">
                        <Link href={`/property/${p.id}`} className="font-extrabold text-sm text-slate-800 dark:text-slate-200 hover:text-primary transition leading-snug block line-clamp-1">
                          {p.title}
                        </Link>
                        <span className="block text-[10px] text-slate-400 font-semibold mt-1">
                          {p.address ? `${p.address}` : `ID: ${p.id.slice(0, 8)}...`}
                        </span>
                      </td>
                      <td className="py-4 font-black text-slate-800 dark:text-slate-200">{formatPrice(p.price, p.priceLabel)}</td>
                      <td className="py-4 font-bold text-slate-655">{p.area} m²</td>
                      <td className="py-4">
                        <span className={`inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold ${
                          isLive ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400'
                        }`}>
                          {isLive ? 'Đang hiển thị' : 'Đã ẩn / Tạm ngưng'}
                        </span>
                      </td>
                      <td className="py-4 text-right space-x-1 whitespace-nowrap">
                        {/* Edit */}
                        <Link
                          href={`/owner/properties/${p.id}/edit`}
                          className="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-500 dark:text-slate-400 hover:text-primary transition"
                          title="Chỉnh sửa tin"
                        >
                          <i className="fa-solid fa-pen-to-square text-xs" />
                        </Link>

                        {/* Toggle hide/show */}
                        <button
                          type="button"
                          disabled={isLoadingThis}
                          onClick={() => handleToggleStatus(p.id, p.status)}
                          className="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-500 dark:text-slate-400 hover:text-amber-500 transition cursor-pointer"
                          title={isLive ? 'Ẩn tin đăng' : 'Hiện tin đăng'}
                        >
                          <i className={`fa-solid ${isLive ? 'fa-eye-slash' : 'fa-eye'} text-xs`} />
                        </button>

                        {/* Extend */}
                        <button
                          type="button"
                          disabled={isLoadingThis}
                          onClick={() => handleExtend(p.id)}
                          className="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-500 dark:text-slate-400 hover:text-emerald-500 transition cursor-pointer"
                          title="Gia hạn tin đăng"
                        >
                          <i className="fa-solid fa-bolt text-xs" />
                        </button>

                        {/* View live public page */}
                        <Link
                          href={`/property/${p.id}`}
                          target="_blank"
                          className="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-500 dark:text-slate-400 hover:text-blue-500 transition"
                          title="Xem trang hiển thị công khai"
                        >
                          <i className="fa-solid fa-arrow-up-right-from-square text-xs" />
                        </Link>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}
