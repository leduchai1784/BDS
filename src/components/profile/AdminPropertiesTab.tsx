'use client'

import { useState } from 'react'

interface PropertyItem {
  id: string
  title: string
  priceLabel: string
  price: number
  area: number
  transactionType: string
  address: string
  status: string
  owner: { name: string; phone: string } | null
  categoryName: string
  createdAt: string
}

interface AdminPropertiesTabProps {
  initialProperties: PropertyItem[]
}

export default function AdminPropertiesTab({ initialProperties }: AdminPropertiesTabProps) {
  const [properties, setProperties] = useState<PropertyItem[]>(initialProperties)
  const [search, setSearch] = useState('')
  const [categoryId, setCategoryId] = useState('')
  const [transactionType, setTransactionType] = useState('')
  const [status, setStatus] = useState('')
  const [isProcessing, setIsProcessing] = useState<string | null>(null)

  const updateStatus = async (id: string, newStatus: string) => {
    const confirmMessage = newStatus === 'approved' 
      ? 'Bạn có chắc chắn muốn duyệt đăng tin này?' 
      : newStatus === 'hidden'
      ? 'Bạn có chắc chắn muốn ẩn tin đăng này không?'
      : `Bạn có chắc chắn muốn chuyển trạng thái tin đăng này thành ${newStatus}?`

    if (!confirm(confirmMessage)) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/properties/${id}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: newStatus })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setProperties(prev =>
          prev.map(p => p.id === id ? { ...p, status: newStatus } : p)
        )
      } else {
        alert(data.error || 'Lỗi cập nhật trạng thái')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  // Get unique categories from list
  const uniqueCategories = Array.from(new Set(properties.map(p => p.categoryName))).filter(Boolean)

  // Filter clientside
  const filteredProperties = properties.filter(p => {
    const matchesSearch = !search ||
      p.title.toLowerCase().includes(search.toLowerCase()) ||
      p.address.toLowerCase().includes(search.toLowerCase()) ||
      (p.owner && p.owner.name.toLowerCase().includes(search.toLowerCase()))

    const matchesCategory = !categoryId || p.categoryName === categoryId

    // Detect transaction type (rent vs sale)
    const isSaleType = p.transactionType === 'sale' || (p.priceLabel && !p.priceLabel.toLowerCase().includes('tháng'))
    const matchesTxType = !transactionType || 
      (transactionType === 'sale' && isSaleType) ||
      (transactionType === 'rent' && !isSaleType)

    const matchesStatus = !status || p.status === status

    return matchesSearch && matchesCategory && matchesTxType && matchesStatus
  })

  return (
    <div className="space-y-6 text-left">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100">
        <h2 className="text-xl font-bold text-slate-800">Quản lý tin đăng</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái duyệt và quản lý toàn bộ các bất động sản trên hệ thống.</p>
      </div>

      {/* Filters & Search Card */}
      <div className="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 shadow-sm">
        <div className="grid grid-cols-1 sm:grid-cols-12 gap-3">
          {/* Keyword Search */}
          <div className="sm:col-span-3 relative">
            <i className="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
            <input
              type="text"
              placeholder="Tìm kiếm tin..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Category filter */}
          <div className="sm:col-span-3">
            <select
              value={categoryId}
              onChange={(e) => setCategoryId(e.target.value)}
              className="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
            >
              <option value="">-- Tất cả danh mục --</option>
              {uniqueCategories.map(cat => (
                <option key={cat} value={cat}>{cat}</option>
              ))}
            </select>
          </div>

          {/* Transaction type filter */}
          <div className="sm:col-span-3">
            <select
              value={transactionType}
              onChange={(e) => setTransactionType(e.target.value)}
              className="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
            >
              <option value="">-- Tất cả kiểu --</option>
              <option value="sale">Bán</option>
              <option value="rent">Cho thuê</option>
            </select>
          </div>

          {/* Status filter */}
          <div className="sm:col-span-3">
            <select
              value={status}
              onChange={(e) => setStatus(e.target.value)}
              className="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
            >
              <option value="">-- Trạng thái --</option>
              <option value="pending">Chờ duyệt</option>
              <option value="approved">Đã duyệt</option>
              <option value="hidden">Đã ẩn</option>
              <option value="rejected">Từ chối</option>
            </select>
          </div>
        </div>
      </div>

      {/* Properties Table Card */}
      <div className="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
          <table className="min-w-full text-left text-xs text-slate-600 font-semibold border-collapse">
            <thead className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 sticky top-0 z-10">
              <tr>
                <th scope="col" className="px-6 py-4">Bất động sản</th>
                <th scope="col" className="px-6 py-4">Giá / Diện tích</th>
                <th scope="col" className="px-6 py-4">Kiểu giao dịch</th>
                <th scope="col" className="px-6 py-4">Trạng thái</th>
                <th scope="col" className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filteredProperties.length > 0 ? (
                filteredProperties.map(p => {
                  const isSale = p.transactionType === 'sale' || (p.priceLabel && !p.priceLabel.toLowerCase().includes('tháng'))
                  return (
                    <tr key={p.id} className="hover:bg-slate-50/50 transition">
                      {/* Property detail */}
                      <td className="px-6 py-4 max-w-[280px]">
                        <a 
                          href={`/property/${p.id}`} 
                          className="font-bold text-slate-850 hover:text-primary transition block truncate leading-none mb-1 text-[13px]"
                        >
                          {p.title}
                        </a>
                        <span className="text-[10px] text-slate-400 block truncate">
                          <i className="fa-solid fa-location-dot mr-1" />
                          {p.address}
                        </span>
                      </td>

                      {/* Price & Area */}
                      <td className="px-6 py-4">
                        <span className="block text-primary font-bold leading-none mb-1 text-[13px]">{p.priceLabel}</span>
                        <span className="text-[10px] text-slate-400 block">{p.area} m²</span>
                      </td>

                      {/* Transaction Type */}
                      <td className="px-6 py-4">
                        <span className={`inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold ${
                          isSale 
                            ? 'bg-orange-50 text-orange-700 border border-orange-200' 
                            : 'bg-blue-50 text-blue-700 border border-blue-200'
                        }`}>
                          {isSale ? 'Bán' : 'Cho thuê'}
                        </span>
                      </td>

                      {/* Status badge */}
                      <td className="px-6 py-4">
                        {p.status === 'approved' ? (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">
                            Đã duyệt
                          </span>
                        ) : p.status === 'hidden' ? (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 text-slate-600 border border-slate-200">
                            Đã ẩn
                          </span>
                        ) : p.status === 'rejected' ? (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">
                            Từ chối
                          </span>
                        ) : (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Chờ duyệt
                          </span>
                        )}
                      </td>

                      {/* Actions */}
                      <td className="px-6 py-4 text-right whitespace-nowrap">
                        <div className="flex items-center justify-end gap-1.5">
                          {p.status === 'pending' && (
                            <button
                              onClick={() => updateStatus(p.id, 'approved')}
                              disabled={isProcessing === p.id}
                              className="px-2.5 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 text-[10px] font-extrabold cursor-pointer transition shadow-sm"
                            >
                              Duyệt tin
                            </button>
                          )}

                          {p.status === 'approved' && (
                            <button
                              onClick={() => updateStatus(p.id, 'hidden')}
                              disabled={isProcessing === p.id}
                              className="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition cursor-pointer"
                              title="Ẩn tin đăng"
                            >
                              <i className="fa-solid fa-eye-slash text-xs" />
                            </button>
                          )}

                          {p.status === 'hidden' && (
                            <button
                              onClick={() => updateStatus(p.id, 'approved')}
                              disabled={isProcessing === p.id}
                              className="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 border border-green-200 flex items-center justify-center transition cursor-pointer"
                              title="Hiện tin đăng"
                            >
                              <i className="fa-solid fa-eye text-xs" />
                            </button>
                          )}
                        </div>
                      </td>
                    </tr>
                  )
                })
              ) : (
                <tr>
                  <td colSpan={5} className="py-16 text-center text-slate-400 font-semibold">
                    <i className="fa-solid fa-folder-open text-3xl mb-3 block text-slate-350" />
                    Không tìm thấy tin đăng nào phù hợp.
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
