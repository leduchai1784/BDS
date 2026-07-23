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

  // Search & Filter state
  const [searchQuery, setSearchQuery] = useState('')
  const [filterTab, setFilterTab] = useState<'all' | 'live' | 'hidden'>('all')

  // Delete modal state
  const [deleteModalItem, setDeleteModalItem] = useState<Property | null>(null)
  const [isDeleting, setIsDeleting] = useState(false)

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

  const confirmDeleteProperty = async () => {
    if (!deleteModalItem) return
    setIsDeleting(true)

    try {
      const res = await fetch(`/api/properties/${deleteModalItem.id}`, {
        method: 'DELETE'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setProperties(prev => prev.filter(p => p.id !== deleteModalItem.id))
        toast.success('Đã xóa tin đăng và đồng bộ hệ thống NKS thành công!')
        setDeleteModalItem(null)
      } else {
        toast.error(data.error || 'Lỗi khi xóa tin đăng.')
      }
    } catch (err) {
      console.error(err)
      toast.error('Lỗi kết nối máy chủ khi xóa.')
    } finally {
      setIsDeleting(false)
    }
  }

  const formatPrice = (price: number, priceLabel?: string) => {
    if (priceLabel) return priceLabel
    if (price >= 1000000000) return `${(price / 1000000000).toFixed(1).replace(/\.0$/, '')} tỷ`
    if (price >= 1000000) return `${(price / 1000000).toFixed(1).replace(/\.0$/, '')} triệu/tháng`
    return `${price.toLocaleString('vi-VN')} đ/tháng`
  }

  const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'Vừa xong'
    try {
      const d = new Date(dateStr)
      return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`
    } catch (e) {
      return 'Vừa xong'
    }
  }

  // Calculated Stats
  const totalCount = properties.length
  const liveCount = properties.filter(p => p.status === 'active' || p.status === 'approved').length
  const hiddenCount = totalCount - liveCount
  const totalViews = properties.reduce((acc, p) => acc + (p.viewsCount || 0), 0)

  // Filtered Properties List
  const filteredProperties = properties.filter(p => {
    const isLive = p.status === 'active' || p.status === 'approved'
    if (filterTab === 'live' && !isLive) return false
    if (filterTab === 'hidden' && isLive) return false
    
    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim()
      const titleMatch = p.title.toLowerCase().includes(q)
      const addressMatch = (p.address || '').toLowerCase().includes(q)
      return titleMatch || addressMatch
    }
    return true
  })

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

      {/* Header Title & Create Action */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-gray-800 pb-5">
        <div>
          <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
            Quản lý tin đăng
          </h1>
          <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
            Quản lý, chỉnh sửa, ẩn hiện và xem phân tích toàn bộ bất động sản của bạn.
          </p>
        </div>

        <Link
          href="/owner/properties/create"
          className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-primary hover:bg-primary-hover text-white font-bold text-xs transition shadow-md shadow-primary/15 hover:shadow-lg hover:shadow-primary/25 cursor-pointer active:scale-95"
        >
          <i className="fa-solid fa-plus-circle text-sm" />
          Đăng tin mới
        </Link>
      </div>

      {/* Summary Stat Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {/* Total Properties */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-5 shadow-xs flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
            <i className="fa-solid fa-building" />
          </div>
          <div>
            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tổng tin đăng</span>
            <span className="text-2xl font-black text-slate-800 dark:text-white">{totalCount}</span>
          </div>
        </div>

        {/* Live Properties */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-5 shadow-xs flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0">
            <i className="fa-solid fa-circle-check" />
          </div>
          <div>
            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Đang hiển thị</span>
            <span className="text-2xl font-black text-emerald-600 dark:text-emerald-400">{liveCount}</span>
          </div>
        </div>

        {/* Total Views */}
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-5 shadow-xs flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
            <i className="fa-solid fa-eye" />
          </div>
          <div>
            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tổng lượt xem</span>
            <span className="text-2xl font-black text-slate-800 dark:text-white">{totalViews.toLocaleString('vi-VN')}</span>
          </div>
        </div>
      </div>

      {/* Toolbar: Search Input & Filter Tabs */}
      <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        {/* Search Bar */}
        <div className="relative flex-1 max-w-md">
          <i className="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Tìm kiếm theo tiêu đề, địa chỉ..."
            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 rounded-2xl text-xs font-semibold outline-none focus:border-primary focus:bg-white dark:focus:bg-gray-900 transition"
          />
          {searchQuery && (
            <button
              onClick={() => setSearchQuery('')}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-655 text-xs"
            >
              <i className="fa-solid fa-xmark" />
            </button>
          )}
        </div>

        {/* Filter Status Tabs */}
        <div className="flex bg-slate-100 dark:bg-gray-850 p-1 rounded-2xl shrink-0 self-start sm:self-auto">
          <button
            onClick={() => setFilterTab('all')}
            className={`px-4 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer ${
              filterTab === 'all' ? 'bg-white dark:bg-gray-800 text-slate-800 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            Tất cả ({totalCount})
          </button>
          <button
            onClick={() => setFilterTab('live')}
            className={`px-4 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer ${
              filterTab === 'live' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            Đang hiển thị ({liveCount})
          </button>
          <button
            onClick={() => setFilterTab('hidden')}
            className={`px-4 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer ${
              filterTab === 'hidden' ? 'bg-white dark:bg-gray-800 text-rose-500 shadow-xs' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            Đã ẩn ({hiddenCount})
          </button>
        </div>
      </div>

      {/* Property Cards List */}
      {filteredProperties.length === 0 ? (
        <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-12 text-center shadow-sm">
          <div className="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
            <i className="fa-solid fa-folder-open" />
          </div>
          <h3 className="text-sm font-extrabold text-slate-700 dark:text-slate-300">Không tìm thấy tin đăng phù hợp</h3>
          <p className="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc chuyển sang bộ lọc khác.</p>
          {(searchQuery || filterTab !== 'all') && (
            <button
              onClick={() => { setSearchQuery(''); setFilterTab('all'); }}
              className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-gray-800 text-slate-700 dark:text-slate-200 font-bold text-xs transition mt-4"
            >
              Xóa bộ lọc
            </button>
          )}
        </div>
      ) : (
        <div className="space-y-4">
          {filteredProperties.map((p) => {
            const imageSrc = p.images && p.images.length > 0 
              ? p.images[0] 
              : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=400&q=80';

            const isLive = p.status === 'active' || p.status === 'approved'
            const isLoadingThis = actionLoadingId === p.id

            return (
              <div 
                key={p.id}
                className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-slate-200 dark:hover:border-gray-750 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
              >
                {/* Left: Thumbnail & Details */}
                <div className="flex items-start sm:items-center gap-4 flex-1 min-w-0">
                  <div className="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border border-slate-100 dark:border-gray-800 shrink-0 bg-slate-100">
                    <img 
                      src={imageSrc} 
                      alt={p.title} 
                      className="w-full h-full object-cover"
                    />
                  </div>

                  <div className="flex-1 min-w-0 space-y-1">
                    {/* Line 1: Title & Status */}
                    <div className="flex items-center gap-2.5 flex-wrap">
                      <Link 
                        href={`/property/${p.id}`} 
                        className="font-extrabold text-sm sm:text-base text-slate-800 dark:text-white hover:text-primary transition line-clamp-1"
                      >
                        {p.title}
                      </Link>
                      <span className={`text-xs font-bold ${isLive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'}`}>
                        {isLive ? 'Đang hiển thị' : 'Đã ẩn / Tạm ngưng'}
                      </span>
                    </div>

                    {/* Line 2: Address */}
                    <p className="text-xs text-slate-400 font-medium line-clamp-1">
                      {p.address || 'Chưa cập nhật địa chỉ'}
                    </p>

                    {/* Line 3: Price, Views, Date */}
                    <div className="flex items-center gap-4 text-xs text-slate-800 dark:text-slate-200 font-semibold pt-1 flex-wrap">
                      <span>
                        Giá: <strong className="text-primary font-extrabold">{formatPrice(p.price, p.priceLabel)}</strong>
                      </span>
                      <span>
                        Lượt xem: <strong className="font-extrabold">{p.viewsCount || 0}</strong>
                      </span>
                      <span>
                        Ngày đăng: <strong className="font-extrabold">{formatDate(p.createdAt)}</strong>
                      </span>
                    </div>
                  </div>
                </div>

                {/* Right: Action Icon Buttons */}
                <div className="flex items-center gap-2 shrink-0 self-end sm:self-center pt-2 sm:pt-0">
                  {/* Edit */}
                  <Link
                    href={`/owner/properties/${p.id}/edit`}
                    className="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-50 hover:bg-slate-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-slate-655 dark:text-slate-300 flex items-center justify-center text-sm transition cursor-pointer border border-slate-100 dark:border-gray-700"
                    title="Chỉnh sửa tin đăng"
                  >
                    <i className="fa-regular fa-pen-to-square text-sm" />
                  </Link>

                  {/* Hide / Show */}
                  <button
                    type="button"
                    disabled={isLoadingThis}
                    onClick={() => handleToggleStatus(p.id, p.status)}
                    className="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-50 hover:bg-slate-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-slate-655 dark:text-slate-300 flex items-center justify-center text-sm transition cursor-pointer border border-slate-100 dark:border-gray-700"
                    title={isLive ? 'Ẩn tin đăng' : 'Hiện tin đăng'}
                  >
                    <i className={`fa-solid ${isLive ? 'fa-eye-slash' : 'fa-eye'} text-sm`} />
                  </button>

                  {/* Extend */}
                  <button
                    type="button"
                    disabled={isLoadingThis}
                    onClick={() => handleExtend(p.id)}
                    className="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-sky-50 hover:bg-sky-100 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 flex items-center justify-center text-sm transition cursor-pointer border border-sky-100 dark:border-sky-900/30"
                    title="Gia hạn tin đăng"
                  >
                    <i className="fa-solid fa-chart-line text-sm" />
                  </button>

                  {/* Delete button (triggers Modal) */}
                  <button
                    type="button"
                    disabled={isLoadingThis}
                    onClick={() => setDeleteModalItem(p)}
                    className="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-500 dark:bg-rose-955/40 dark:text-rose-400 flex items-center justify-center text-sm transition cursor-pointer border border-rose-100 dark:border-rose-900/30"
                    title="Xóa tin đăng"
                  >
                    <i className="fa-solid fa-trash-can text-sm" />
                  </button>
                </div>
              </div>
            )
          })}
        </div>
      )}

      {/* Delete Confirmation Modal */}
      {deleteModalItem && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 animate-fadeIn">
          <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 text-left">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-lg shrink-0">
                <i className="fa-solid fa-triangle-exclamation" />
              </div>
              <div>
                <h3 className="text-base font-extrabold text-slate-800 dark:text-white">Xác nhận xóa tin đăng</h3>
                <p className="text-xs text-slate-400 font-semibold mt-0.5">Hành động này không thể hoàn tác.</p>
              </div>
            </div>

            <p className="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed bg-slate-50 dark:bg-gray-955 p-3.5 rounded-2xl border border-slate-100 dark:border-gray-850">
              Bạn có chắc chắn muốn xóa tin đăng <span className="font-extrabold text-slate-800 dark:text-white">"{deleteModalItem.title}"</span>? Tin đăng sẽ bị xóa khỏi hệ thống local và đồng bộ xóa khỏi NKS API.
            </p>

            <div className="flex items-center justify-end gap-2.5 pt-2">
              <button
                type="button"
                disabled={isDeleting}
                onClick={() => setDeleteModalItem(null)}
                className="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-gray-800 text-slate-655 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-gray-800 transition cursor-pointer"
              >
                Hủy bỏ
              </button>
              <button
                type="button"
                disabled={isDeleting}
                onClick={confirmDeleteProperty}
                className="px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 disabled:bg-rose-400 text-white text-xs font-bold transition cursor-pointer flex items-center gap-2 shadow-md shadow-rose-500/20"
              >
                {isDeleting ? (
                  <>
                    <i className="fa-solid fa-spinner animate-spin" /> Đang xóa...
                  </>
                ) : (
                  <>
                    <i className="fa-solid fa-trash-can" /> Đồng ý xóa
                  </>
                )}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
