'use client'

import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'

interface PropertyItem {
  id: string
  title: string
  address: string
  priceLabel: string
  area: number
  status: string
  createdAt: string
  owner: {
    name: string
    email: string
  } | null
  category: {
    name: string
  } | null
}

interface PropertiesTableProps {
  initialProperties: PropertyItem[]
  categories: { id: string; name: string }[]
  searchParams: {
    search?: string
    categoryId?: string
    status?: string
    id?: string
  }
}

export default function PropertiesTable({ initialProperties, categories, searchParams }: PropertiesTableProps) {
  const router = useRouter()
  const [properties, setProperties] = useState<PropertyItem[]>(initialProperties)
  
  // Filtering states
  const [search, setSearch] = useState(searchParams.search || '')
  const [categoryId, setCategoryId] = useState(searchParams.categoryId || '')
  const [status, setStatus] = useState(searchParams.status || '')
  const [isProcessing, setIsProcessing] = useState<string | null>(null)

  // Selected property for quick preview modal
  const [previewProperty, setPreviewProperty] = useState<PropertyItem | null>(() => {
    if (searchParams.id) {
      return initialProperties.find(p => p.id === searchParams.id) || null
    }
    return null
  })

  const handleFilter = () => {
    const params = new URLSearchParams()
    if (search) params.set('search', search)
    if (categoryId) params.set('categoryId', categoryId)
    if (status) params.set('status', status)
    router.push(`/admin/properties?${params.toString()}`)
  }

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
        if (previewProperty?.id === id) {
          setPreviewProperty(prev => prev ? { ...prev, status: newStatus } : null)
        }
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
    if (!confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tin đăng này?')) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/properties/${id}`, {
        method: 'DELETE'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setProperties(prev => prev.filter(p => p.id !== id))
        if (previewProperty?.id === id) {
          setPreviewProperty(null)
        }
      } else {
        alert(data.error || 'Lỗi xóa tin đăng')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  return (
    <div className="space-y-6 text-left relative">
      
      {/* Filtering Toolbar */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-grow max-w-3xl">
          <input
            type="text"
            placeholder="Tìm theo tiêu đề, địa chỉ..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />

          <select
            value={categoryId}
            onChange={(e) => setCategoryId(e.target.value)}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer"
          >
            <option value="">-- Tất cả danh mục --</option>
            {categories.map(cat => (
              <option key={cat.id} value={cat.id}>{cat.name}</option>
            ))}
          </select>

          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer"
          >
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending">Chờ duyệt (Pending)</option>
            <option value="approved">Đã duyệt (Approved)</option>
            <option value="rejected">Từ chối (Rejected)</option>
            <option value="hidden">Đã ẩn (Hidden)</option>
          </select>
        </div>

        <button
          onClick={handleFilter}
          className="px-6 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer self-start md:self-auto flex items-center gap-1.5"
        >
          <i className="fa-solid fa-magnifying-glass" />
          <span>Lọc tin</span>
        </button>
      </div>

      {/* Properties Table */}
      <div className="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-xs font-semibold text-slate-650 border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 uppercase text-[9px] tracking-wider text-slate-400 font-bold">
                <th className="px-6 py-4 text-left">Tin đăng</th>
                <th className="px-6 py-4 text-left">Danh mục</th>
                <th className="px-6 py-4 text-left">Giá & Diện tích</th>
                <th className="px-6 py-4 text-left">Người đăng</th>
                <th className="px-6 py-4 text-left">Trạng thái</th>
                <th className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {properties.length > 0 ? (
                properties.map(p => (
                  <tr key={p.id} className="hover:bg-slate-50/50 transition">
                    <td className="px-6 py-3.5 text-left max-w-sm truncate">
                      <strong className="block text-slate-800 font-semibold truncate hover:text-primary cursor-pointer" onClick={() => setPreviewProperty(p)}>
                        {p.title}
                      </strong>
                      <span className="block text-[10px] text-slate-450 font-semibold truncate mt-0.5">{p.address}</span>
                    </td>
                    <td className="px-6 py-3.5 text-left text-slate-550 font-semibold">{p.category?.name || '—'}</td>
                    <td className="px-6 py-3.5 text-left font-bold text-slate-800">
                      <div>{p.priceLabel}</div>
                      <div className="text-[10px] text-slate-400 font-semibold">{p.area} m²</div>
                    </td>
                    <td className="px-6 py-3.5 text-left">
                      <div className="font-semibold text-slate-700">{p.owner?.name || 'Admin'}</div>
                      <div className="text-[9px] text-slate-400 select-all">{p.owner?.email || ''}</div>
                    </td>
                    <td className="px-6 py-3.5 text-left">
                      <span className={`inline-flex px-2 py-0.5 rounded-md text-[8px] font-black uppercase ${
                        p.status === 'approved' 
                          ? 'bg-emerald-50 text-emerald-600'
                          : p.status === 'rejected'
                          ? 'bg-red-50 text-red-600'
                          : p.status === 'hidden'
                          ? 'bg-slate-100 text-slate-550'
                          : 'bg-amber-50 text-amber-600'
                      }`}>
                        {p.status === 'approved' ? 'Đã duyệt' : p.status === 'rejected' ? 'Từ chối' : p.status === 'hidden' ? 'Đã ẩn' : 'Chờ duyệt'}
                      </span>
                    </td>
                    <td className="px-6 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                      {/* Action buttons */}
                      <button
                        onClick={() => setPreviewProperty(p)}
                        className="px-2.5 py-1.5 border border-slate-200 hover:bg-slate-50 rounded-lg text-[10px] font-bold text-slate-550 transition cursor-pointer"
                      >
                        Xem nhanh
                      </button>

                      {p.status === 'pending' && (
                        <>
                          <button
                            onClick={() => updateStatus(p.id, 'approved')}
                            disabled={isProcessing === p.id}
                            className="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-500 hover:text-white border border-emerald-100 text-emerald-650 rounded-lg text-[10px] font-bold transition cursor-pointer"
                          >
                            Duyệt
                          </button>
                          <button
                            onClick={() => updateStatus(p.id, 'rejected')}
                            disabled={isProcessing === p.id}
                            className="px-2.5 py-1.5 bg-red-50 hover:bg-red-500 hover:text-white border border-red-100 text-red-650 rounded-lg text-[10px] font-bold transition cursor-pointer"
                          >
                            Từ chối
                          </button>
                        </>
                      )}

                      {p.status === 'approved' && (
                        <button
                          onClick={() => updateStatus(p.id, 'hidden')}
                          disabled={isProcessing === p.id}
                          className="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-700 hover:text-white border border-slate-200 text-slate-600 rounded-lg text-[10px] font-bold transition cursor-pointer"
                        >
                          Ẩn tin
                        </button>
                      )}

                      {p.status === 'hidden' && (
                        <button
                          onClick={() => updateStatus(p.id, 'approved')}
                          disabled={isProcessing === p.id}
                          className="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-500 hover:text-white border border-emerald-100 text-emerald-650 rounded-lg text-[10px] font-bold transition cursor-pointer"
                        >
                          Hiện tin
                        </button>
                      )}

                      <button
                        onClick={() => deleteProperty(p.id)}
                        disabled={isProcessing === p.id}
                        className="p-1.5 bg-red-50 hover:bg-red-500 hover:text-white border border-red-100 text-red-605 rounded-lg transition cursor-pointer inline-flex items-center justify-center"
                        title="Xóa tin đăng"
                      >
                        <i className="fa-regular fa-trash-can text-xs" />
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="py-12 text-center text-slate-400 text-xs font-semibold">
                    Không tìm thấy tin đăng bất động sản nào khớp.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Quick Preview Modal Overlay */}
      {previewProperty && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-xl w-full p-6 space-y-4 max-h-[85vh] overflow-y-auto relative animate-in fade-in zoom-in-95 duration-250">
            <button 
              onClick={() => setPreviewProperty(null)}
              className="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
            >
              <i className="fa-solid fa-xmark text-sm" />
            </button>

            <div className="space-y-1.5">
              <span className={`inline-block px-2 py-0.5 rounded-md text-[8px] font-black uppercase ${
                previewProperty.status === 'approved' 
                  ? 'bg-emerald-50 text-emerald-600'
                  : previewProperty.status === 'rejected'
                  ? 'bg-red-50 text-red-600'
                  : 'bg-amber-50 text-amber-600'
              }`}>
                {previewProperty.status === 'approved' ? 'Đã duyệt' : previewProperty.status === 'rejected' ? 'Từ chối' : 'Chờ duyệt'}
              </span>
              <h3 className="text-sm font-black text-slate-850 leading-snug pr-8">{previewProperty.title}</h3>
              <p className="text-xs text-slate-400 font-semibold"><i className="fa-solid fa-location-dot mr-1" />{previewProperty.address}</p>
            </div>

            <div className="grid grid-cols-3 gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center text-xs font-semibold text-slate-600">
              <div>
                <span className="block text-[9px] text-slate-400 uppercase font-bold mb-0.5">Giá cả</span>
                <strong className="text-slate-800 font-black">{previewProperty.priceLabel}</strong>
              </div>
              <div>
                <span className="block text-[9px] text-slate-400 uppercase font-bold mb-0.5">Diện tích</span>
                <strong className="text-slate-800 font-black">{previewProperty.area} m²</strong>
              </div>
              <div>
                <span className="block text-[9px] text-slate-400 uppercase font-bold mb-0.5">Danh mục</span>
                <strong className="text-slate-800 font-black">{previewProperty.category?.name || '—'}</strong>
              </div>
            </div>

            <div className="space-y-3 border-t border-slate-100 pt-4">
              <div className="text-xs font-semibold text-slate-650">
                <span className="block text-[9px] text-slate-400 uppercase font-bold mb-1">Thông tin người đăng</span>
                <p className="font-bold text-slate-800">{previewProperty.owner?.name}</p>
                <p className="text-slate-500 font-semibold">{previewProperty.owner?.email}</p>
              </div>
            </div>

            {/* Modal Actions */}
            <div className="flex justify-end gap-2 border-t border-slate-100 pt-4">
              <Link
                href={`/property/${previewProperty.id}`}
                target="_blank"
                className="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-650 transition flex items-center gap-1 cursor-pointer"
              >
                <i className="fa-solid fa-arrow-up-right-from-square" />
                Trang chi tiết
              </Link>

              {previewProperty.status === 'pending' && (
                <>
                  <button
                    onClick={() => updateStatus(previewProperty.id, 'approved')}
                    disabled={isProcessing === previewProperty.id}
                    className="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition cursor-pointer"
                  >
                    Phê duyệt
                  </button>
                  <button
                    onClick={() => updateStatus(previewProperty.id, 'rejected')}
                    disabled={isProcessing === previewProperty.id}
                    className="px-4 py-2 bg-red-500 hover:bg-red-650 text-white rounded-xl text-xs font-bold transition cursor-pointer"
                  >
                    Từ chối
                  </button>
                </>
              )}

              {previewProperty.status === 'approved' && (
                <button
                  onClick={() => updateStatus(previewProperty.id, 'hidden')}
                  disabled={isProcessing === previewProperty.id}
                  className="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition cursor-pointer"
                >
                  Ẩn tin đăng
                </button>
              )}

              <button
                onClick={() => deleteProperty(previewProperty.id)}
                disabled={isProcessing === previewProperty.id}
                className="p-2.5 bg-red-50 hover:bg-red-500 hover:text-white text-red-650 border border-red-150 rounded-xl transition cursor-pointer flex items-center justify-center"
              >
                <i className="fa-regular fa-trash-can text-sm" />
              </button>
            </div>
          </div>
        </div>
      )}

    </div>
  )
}
