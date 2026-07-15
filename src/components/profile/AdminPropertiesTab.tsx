'use client'

import { useState } from 'react'

interface PropertyItem {
  id: string
  title: string
  priceLabel: string
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
  const [status, setStatus] = useState('')
  const [isProcessing, setIsProcessing] = useState<string | null>(null)

  const updateStatus = async (id: string, newStatus: string) => {
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

  const deleteProperty = async (id: string) => {
    if (!confirm('Bạn có chắc chắn muốn xóa tin đăng này? Hành động này không thể hoàn tác!')) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/properties/${id}`, {
        method: 'DELETE'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setProperties(prev => prev.filter(p => p.id !== id))
      } else {
        alert(data.error || 'Lỗi xóa tin đăng')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  // Filter clientside
  const filteredProperties = properties.filter(p => {
    const matchesSearch = !search ||
      p.title.toLowerCase().includes(search.toLowerCase()) ||
      p.address.toLowerCase().includes(search.toLowerCase()) ||
      (p.owner && p.owner.name.toLowerCase().includes(search.toLowerCase()))

    const matchesStatus = !status || p.status === status

    return matchesSearch && matchesStatus
  })

  const getStatusBadge = (s: string) => {
    switch (s) {
      case 'approved':
        return (
          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-250">
            <i className="fa-solid fa-circle-check mr-1 text-[9px]" /> Đã duyệt
          </span>
        )
      case 'pending':
        return (
          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-250">
            <i className="fa-solid fa-clock mr-1 text-[9px]" /> Chờ duyệt
          </span>
        )
      case 'rejected':
        return (
          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-650 border border-red-250">
            <i className="fa-solid fa-circle-xmark mr-1 text-[9px]" /> Từ chối
          </span>
        )
      case 'hidden':
        return (
          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-250">
            <i className="fa-solid fa-eye-slash mr-1 text-[9px]" /> Đang ẩn
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
        <h2 className="text-xl font-bold text-slate-800">Quản lý tin đăng</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Phê duyệt, từ chối hoặc ẩn các bài đăng bất động sản trên toàn bộ hệ thống.</p>
      </div>

      {/* Filters Bar */}
      <div className="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-grow max-w-2xl">
          {/* Keyword Search */}
          <div className="relative">
            <i className="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
            <input
              type="text"
              placeholder="Tên tin đăng, địa chỉ hoặc chủ nhà..."
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
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending">Chờ duyệt</option>
            <option value="approved">Đã duyệt (Đang hiển thị)</option>
            <option value="rejected">Từ chối</option>
            <option value="hidden">Đang ẩn</option>
          </select>
        </div>
      </div>

      {/* Table view */}
      <div className="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th className="px-6 py-4 text-left">Tin đăng</th>
                <th className="px-6 py-4 text-left">Giá / Diện tích</th>
                <th className="px-6 py-4 text-left">Người đăng</th>
                <th className="px-6 py-4 text-left">Danh mục</th>
                <th className="px-6 py-4 text-left">Trạng thái</th>
                <th className="px-6 py-4 text-center">Hành động</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-xs">
              {filteredProperties.length > 0 ? (
                filteredProperties.map(p => (
                  <tr key={p.id} className="hover:bg-slate-50/50 transition">
                    {/* Property info */}
                    <td className="px-6 py-4 max-w-xs">
                      <div>
                        <strong className="block text-slate-800 font-bold truncate" title={p.title}>{p.title}</strong>
                        <span className="block text-[10px] text-slate-400 mt-0.5 truncate" title={p.address}>{p.address}</span>
                      </div>
                    </td>

                    {/* Price / Area */}
                    <td className="px-6 py-4 whitespace-nowrap font-bold text-primary">
                      <div>{p.priceLabel}</div>
                    </td>

                    {/* Owner info */}
                    <td className="px-6 py-4 whitespace-nowrap font-medium text-slate-650">
                      {p.owner ? (
                        <>
                          <div>{p.owner.name}</div>
                          <div className="text-[10px] text-slate-400">{p.owner.phone}</div>
                        </>
                      ) : (
                        <span className="text-[10px] text-slate-400 italic">Hệ thống</span>
                      )}
                    </td>

                    {/* Category */}
                    <td className="px-6 py-4 whitespace-nowrap font-semibold text-slate-500">
                      {p.categoryName}
                    </td>

                    {/* Status badge */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      {getStatusBadge(p.status)}
                    </td>

                    {/* Actions */}
                    <td className="px-6 py-4 whitespace-nowrap text-center">
                      <div className="flex items-center justify-center gap-1.5">
                        {/* Approve */}
                        {p.status !== 'approved' && (
                          <button
                            onClick={() => updateStatus(p.id, 'approved')}
                            disabled={isProcessing === p.id}
                            className="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center transition cursor-pointer"
                            title="Duyệt bài đăng"
                          >
                            <i className="fa-solid fa-check" />
                          </button>
                        )}

                        {/* Reject */}
                        {p.status !== 'rejected' && (
                          <button
                            onClick={() => updateStatus(p.id, 'rejected')}
                            disabled={isProcessing === p.id}
                            className="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-250 text-amber-600 flex items-center justify-center transition cursor-pointer"
                            title="Từ chối bài đăng"
                          >
                            <i className="fa-solid fa-ban" />
                          </button>
                        )}

                        {/* Delete */}
                        <button
                          onClick={() => deleteProperty(p.id)}
                          disabled={isProcessing === p.id}
                          className="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-655 flex items-center justify-center transition cursor-pointer"
                          title="Xóa tin đăng"
                        >
                          <i className="fa-solid fa-trash-can" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="px-6 py-12 text-center text-slate-400 font-bold uppercase tracking-wider">
                    Không tìm thấy bài đăng nào phù hợp
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
