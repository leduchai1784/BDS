'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import Link from 'next/link'

interface AdminPropertiesTabProps {
  initialProperties: any[]
  categories: any[]
}

export default function AdminPropertiesTab({ initialProperties, categories }: AdminPropertiesTabProps) {
  const [properties, setProperties] = useState(initialProperties)
  const [searchTerm, setSearchTerm] = useState('')
  const [filterCategory, setFilterCategory] = useState('')
  const [filterType, setFilterType] = useState('')
  const [filterStatus, setFilterStatus] = useState('')

  const [categoryOpen, setCategoryOpen] = useState(false)
  const [typeOpen, setTypeOpen] = useState(false)
  const [statusOpen, setStatusOpen] = useState(false)

  const handleUpdatePropertyStatus = async (propertyId: string, newStatus: string) => {
    const statusLabels: Record<string, string> = {
      approved: 'duyệt',
      hidden: 'ẩn'
    }
    if (!window.confirm(`Bạn có chắc chắn muốn ${statusLabels[newStatus] || newStatus} tin đăng này?`)) return
    try {
      const res = await fetch(`/api/admin/properties/${propertyId}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: newStatus })
      })
      const data = await res.json()
      if (data.success) {
        toast.success(data.message)
        setProperties(prev => prev.map(p => p.id === propertyId ? { ...p, status: newStatus } : p))
      } else {
        toast.error(data.error || 'Có lỗi xảy ra')
      }
    } catch (e: any) {
      toast.error(e.message || 'Lỗi mạng')
    }
  }

  // Filter logic matching bds_php
  const filteredProperties = properties.filter(p => {
    const textMatch = searchTerm.trim() === '' ||
      p.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
      p.location.toLowerCase().includes(searchTerm.toLowerCase())

    const categoryMatch = filterCategory === '' || String(p.category?.id) === String(filterCategory)
    
    // Type parser: 'rent' or 'sale'
    const isSale = p.priceLabel && !p.priceLabel.toLowerCase().includes('tháng')
    const propertyType = isSale ? 'sale' : 'rent'
    const typeMatch = filterType === '' || propertyType === filterType

    const statusMatch = filterStatus === '' || p.status === filterStatus

    return textMatch && categoryMatch && typeMatch && statusMatch
  })

  return (
    <div className="space-y-6">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100 mb-6 text-left">
        <h2 className="text-xl font-bold text-slate-800">Quản lý tin đăng</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái duyệt và quản lý toàn bộ các bất động sản trên hệ thống.</p>
      </div>

      {/* Filters & Search Card */}
      <div className="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 shadow-sm text-left">
        <div className="grid grid-cols-1 sm:grid-cols-12 gap-4">
          {/* Search Keyword */}
          <div className="sm:col-span-3 relative">
            <i className="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Tìm kiếm tin..." 
              className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-850 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Category Filter */}
          <div className="relative sm:col-span-3">
            <button 
              type="button" 
              onClick={() => setCategoryOpen(!categoryOpen)} 
              className="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
            >
              <span>{filterCategory ? (categories.find(c => String(c.id) === String(filterCategory))?.name || '-- Tất cả danh mục --') : '-- Tất cả danh mục --'}</span>
              <i className="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>
          
            {categoryOpen && (
              <div 
                onMouseLeave={() => setCategoryOpen(false)}
                className="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden max-h-60 overflow-y-auto thin-scrollbar"
              >
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterCategory('')
                    setCategoryOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterCategory === '' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  -- Tất cả danh mục --
                </button>
                {categories.map((cat) => (
                  <button 
                    key={cat.id}
                    type="button" 
                    onClick={() => {
                      setFilterCategory(String(cat.id))
                      setCategoryOpen(false)
                    }}
                    className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterCategory === String(cat.id) ? 'bg-primary/5 text-primary font-bold' : ''}`}
                  >
                    {cat.name}
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Transaction Type Filter */}
          <div className="relative sm:col-span-3">
            <button 
              type="button" 
              onClick={() => setTypeOpen(!typeOpen)} 
              className="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
            >
              <span>{filterType === 'sale' ? 'Bán' : filterType === 'rent' ? 'Cho thuê' : '-- Tất cả kiểu --'}</span>
              <i className="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>
          
            {typeOpen && (
              <div 
                onMouseLeave={() => setTypeOpen(false)}
                className="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
              >
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterType('')
                    setTypeOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterType === '' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  -- Tất cả kiểu --
                </button>
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterType('sale')
                    setTypeOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterType === 'sale' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  Bán
                </button>
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterType('rent')
                    setTypeOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterType === 'rent' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  Cho thuê
                </button>
              </div>
            )}
          </div>

          {/* Status Filter */}
          <div className="relative sm:col-span-3">
            <button 
              type="button" 
              onClick={() => setStatusOpen(!statusOpen)} 
              className="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
            >
              <span>
                {filterStatus === 'pending' ? 'Chờ duyệt' :
                 filterStatus === 'approved' ? 'Đã duyệt' :
                 filterStatus === 'hidden' ? 'Đã ẩn' : '-- Trạng thái --'}
              </span>
              <i className="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>
          
            {statusOpen && (
              <div 
                onMouseLeave={() => setStatusOpen(false)}
                className="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
              >
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterStatus('')
                    setStatusOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterStatus === '' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  -- Trạng thái --
                </button>
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterStatus('pending')
                    setStatusOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterStatus === 'pending' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  Chờ duyệt
                </button>
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterStatus('approved')
                    setStatusOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterStatus === 'approved' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  Đã duyệt
                </button>
                <button 
                  type="button" 
                  onClick={() => {
                    setFilterStatus('hidden')
                    setStatusOpen(false)
                  }}
                  className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterStatus === 'hidden' ? 'bg-primary/5 text-primary font-bold' : ''}`}
                >
                  Đã ẩn
                </button>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Properties Table Card */}
      <div className="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
        <div className="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
          {filteredProperties.length > 0 ? (
            <table className="min-w-full text-left text-xs text-slate-600 font-semibold border-collapse">
              <thead className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                <tr>
                  <th scope="col" className="px-6 py-4">Bất động sản</th>
                  <th scope="col" className="px-6 py-4">Giá / Diện tích</th>
                  <th scope="col" className="px-6 py-4">Kiểu giao dịch</th>
                  <th scope="col" className="px-6 py-4">Trạng thái</th>
                  <th scope="col" className="px-6 py-4 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredProperties.map((propItem) => {
                  const isSale = propItem.priceLabel && !propItem.priceLabel.toLowerCase().includes('tháng')
                  return (
                    <tr key={propItem.id} className="hover:bg-slate-50/50 transition">
                      {/* Title */}
                      <td className="px-6 py-4 max-w-[280px]">
                        <Link href={`/property/${propItem.id}`} className="font-bold text-slate-800 hover:text-primary transition block truncate leading-none mb-1">
                          {propItem.title}
                        </Link>
                        <span className="text-[10px] text-slate-400 block"><i className="fa-solid fa-location-dot mr-1"></i>{propItem.location}</span>
                      </td>
                      {/* Price / Area */}
                      <td className="px-6 py-4">
                        <span className="block text-primary font-bold leading-none mb-1">
                          {propItem.price ? (isSale ? `${propItem.price.toLocaleString('vi-VN')} tỷ` : `${propItem.price.toLocaleString('vi-VN')} triệu/tháng`) : 'Liên hệ'}
                        </span>
                        <span className="text-[10px] text-slate-400 block">{propItem.area} m²</span>
                      </td>
                      {/* Transaction Type */}
                      <td className="px-6 py-4">
                        <span className={`inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold ${isSale ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-blue-50 text-blue-700 border border-blue-200'}`}>
                          {isSale ? 'Bán' : 'Cho thuê'}
                        </span>
                      </td>
                      {/* Status */}
                      <td className="px-6 py-4">
                        {propItem.status === 'approved' ? (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">Đã duyệt</span>
                        ) : propItem.status === 'hidden' ? (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 text-slate-650 border border-slate-200">Đã ẩn</span>
                        ) : propItem.status === 'rejected' ? (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Từ chối</span>
                        ) : (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                        )}
                      </td>
                      {/* Actions */}
                      <td className="px-6 py-4 text-right whitespace-nowrap">
                        <div className="flex items-center justify-end gap-1.5">
                          {propItem.status === 'pending' && (
                            <button 
                              type="button" 
                              onClick={() => handleUpdatePropertyStatus(propItem.id, 'approved')}
                              className="px-2.5 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 text-[10px] font-extrabold cursor-pointer transition shadow-sm"
                            >
                              Duyệt tin
                            </button>
                          )}
                          
                          {propItem.status === 'approved' && (
                            <button 
                              type="button" 
                              onClick={() => handleUpdatePropertyStatus(propItem.id, 'hidden')}
                              className="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition cursor-pointer" 
                              title="Ẩn tin đăng"
                            >
                              <i className="fa-solid fa-eye-slash text-xs"></i>
                            </button>
                          )}
                          
                          {propItem.status === 'hidden' && (
                            <button 
                              type="button" 
                              onClick={() => handleUpdatePropertyStatus(propItem.id, 'approved')}
                              className="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 border border-green-200 flex items-center justify-center transition cursor-pointer" 
                              title="Hiện tin đăng"
                            >
                              <i className="fa-solid fa-eye text-xs"></i>
                            </button>
                          )}
                        </div>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          ) : (
            <div className="py-16 text-center text-slate-400 font-semibold">
              <i className="fa-solid fa-folder-open text-3xl mb-3 block text-slate-350"></i>
              Chưa có tin đăng nào thỏa mãn bộ lọc.
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
