'use client'

import { useState } from 'react'

interface CategoryItem {
  id: string
  name: string
  slug: string
  description: string | null
  propertiesCount: number
}

interface CategoriesTableProps {
  initialCategories: CategoryItem[]
}

export default function CategoriesTable({ initialCategories }: CategoriesTableProps) {
  const [categories, setCategories] = useState<CategoryItem[]>(initialCategories)
  const [isProcessing, setIsProcessing] = useState<string | null>(null)

  // Modals management
  const [createOpen, setCreateOpen] = useState(false)
  const [newName, setNewName] = useState('')
  const [newDesc, setNewDesc] = useState('')

  const [editCategory, setEditCategory] = useState<CategoryItem | null>(null)
  const [editName, setEditName] = useState('')
  const [editDesc, setEditDesc] = useState('')

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!newName) return
    setIsProcessing('create')

    try {
      const res = await fetch('/api/admin/categories', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newName, description: newDesc })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setCategories(prev => [
          ...prev,
          {
            id: data.category.id,
            name: data.category.name,
            slug: data.category.slug,
            description: data.category.description,
            propertiesCount: 0
          }
        ])
        setNewName('')
        setNewDesc('')
        setCreateOpen(false)
      } else {
        alert(data.error || 'Lỗi thêm danh mục')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  const handleUpdate = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!editCategory || !editName) return
    const id = editCategory.id
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/categories/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: editName, description: editDesc })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setCategories(prev => 
          prev.map(c => c.id === id 
            ? { ...c, name: data.category.name, slug: data.category.slug, description: data.category.description } 
            : c
          )
        )
        setEditCategory(null)
      } else {
        alert(data.error || 'Lỗi cập nhật danh mục')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  const deleteCategory = async (id: string) => {
    if (!confirm('Bạn có chắc chắn muốn xóa danh mục này? Tất cả các tin đăng thuộc danh mục này sẽ chuyển sang không phân loại!')) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/categories/${id}`, {
        method: 'DELETE'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setCategories(prev => prev.filter(c => c.id !== id))
      } else {
        alert(data.error || 'Lỗi xóa danh mục')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  return (
    <div className="space-y-6 text-left">
      
      {/* Create Button toolbar */}
      <div className="flex justify-end">
        <button
          onClick={() => setCreateOpen(true)}
          className="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer flex items-center gap-1.5"
        >
          <i className="fa-solid fa-plus" />
          <span>Thêm danh mục mới</span>
        </button>
      </div>

      {/* Categories Table list */}
      <div className="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-xs font-semibold text-slate-650 border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 uppercase text-[9px] tracking-wider text-slate-400 font-bold">
                <th className="px-6 py-4 text-left">Tên danh mục</th>
                <th className="px-6 py-4 text-left">Đường dẫn tĩnh (Slug)</th>
                <th className="px-6 py-4 text-left">Mô tả chi tiết</th>
                <th className="px-6 py-4 text-left">Số lượng tin đăng</th>
                <th className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {categories.length > 0 ? (
                categories.map(c => (
                  <tr key={c.id} className="hover:bg-slate-50/50 transition">
                    <td className="px-6 py-3.5 text-left text-slate-800 font-bold">{c.name}</td>
                    <td className="px-6 py-3.5 text-left text-slate-450 font-semibold select-all">{c.slug}</td>
                    <td className="px-6 py-3.5 text-left text-slate-500 max-w-xs truncate font-medium" title={c.description || ''}>
                      {c.description || '—'}
                    </td>
                    <td className="px-6 py-3.5 text-left font-bold text-slate-700">{c.propertiesCount} tin</td>
                    <td className="px-6 py-3.5 text-right space-x-1.5">
                      <button
                        onClick={() => {
                          setEditCategory(c)
                          setEditName(c.name)
                          setEditDesc(c.description || '')
                        }}
                        disabled={isProcessing === c.id}
                        className="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 inline-flex items-center justify-center text-slate-500 hover:text-primary transition cursor-pointer"
                        title="Chỉnh sửa"
                      >
                        <i className="fa-regular fa-pen-to-square text-xs" />
                      </button>

                      <button
                        onClick={() => deleteCategory(c.id)}
                        disabled={isProcessing === c.id}
                        className="w-8 h-8 rounded-lg border border-red-100/50 bg-red-50 hover:bg-red-500 hover:text-white inline-flex items-center justify-center text-red-650 transition cursor-pointer"
                        title="Xóa danh mục"
                      >
                        <i className="fa-regular fa-trash-can text-xs" />
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={5} className="py-12 text-center text-slate-400 text-xs font-semibold">
                    Chưa có danh mục nào được khai báo.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Create Modal */}
      {createOpen && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4">
          <form onSubmit={handleCreate} className="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 space-y-4 relative animate-in fade-in zoom-in-95 duration-200">
            <button 
              type="button"
              onClick={() => setCreateOpen(false)}
              className="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
            >
              <i className="fa-solid fa-xmark text-sm" />
            </button>

            <div>
              <h3 className="text-sm font-black text-slate-800">Thêm danh mục mới</h3>
              <p className="text-[10px] text-slate-400 font-semibold mt-0.5">Thêm phân loại nhà đất mới vào hệ thống đăng tin.</p>
            </div>

            <div className="space-y-3 text-xs font-semibold">
              <div className="space-y-1">
                <label className="block text-[9px] uppercase tracking-wider text-slate-400">Tên danh mục <span className="text-red-500">*</span></label>
                <input
                  type="text"
                  value={newName}
                  onChange={(e) => setNewName(e.target.value)}
                  required
                  placeholder="Ví dụ: Biệt thự nghỉ dưỡng..."
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl outline-none"
                />
              </div>

              <div className="space-y-1">
                <label className="block text-[9px] uppercase tracking-wider text-slate-400">Mô tả chi tiết</label>
                <textarea
                  value={newDesc}
                  onChange={(e) => setNewDesc(e.target.value)}
                  rows={3}
                  placeholder="Mô tả tóm tắt..."
                  className="w-full px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl outline-none resize-none"
                />
              </div>
            </div>

            <div className="flex justify-end gap-2 border-t border-slate-100 pt-4">
              <button
                type="button"
                onClick={() => setCreateOpen(false)}
                className="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-[10px] font-bold text-slate-600 transition"
              >
                Hủy bỏ
              </button>
              <button
                type="submit"
                disabled={isProcessing === 'create'}
                className="px-5 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-[10px] font-bold shadow-md shadow-primary/20 transition cursor-pointer"
              >
                {isProcessing === 'create' ? 'Đang thêm...' : 'Lưu lại'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Edit Modal */}
      {editCategory && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4">
          <form onSubmit={handleUpdate} className="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 space-y-4 relative animate-in fade-in zoom-in-95 duration-200">
            <button 
              type="button"
              onClick={() => setEditCategory(null)}
              className="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
            >
              <i className="fa-solid fa-xmark text-sm" />
            </button>

            <div>
              <h3 className="text-sm font-black text-slate-800">Chỉnh sửa danh mục</h3>
              <p className="text-[10px] text-slate-400 font-semibold mt-0.5">Thay đổi tên hoặc mô tả phân loại danh mục.</p>
            </div>

            <div className="space-y-3 text-xs font-semibold">
              <div className="space-y-1">
                <label className="block text-[9px] uppercase tracking-wider text-slate-400">Tên danh mục <span className="text-red-500">*</span></label>
                <input
                  type="text"
                  value={editName}
                  onChange={(e) => setEditName(e.target.value)}
                  required
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl outline-none"
                />
              </div>

              <div className="space-y-1">
                <label className="block text-[9px] uppercase tracking-wider text-slate-400">Mô tả chi tiết</label>
                <textarea
                  value={editDesc}
                  onChange={(e) => setEditDesc(e.target.value)}
                  rows={3}
                  className="w-full px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl outline-none resize-none"
                />
              </div>
            </div>

            <div className="flex justify-end gap-2 border-t border-slate-100 pt-4">
              <button
                type="button"
                onClick={() => setEditCategory(null)}
                className="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-[10px] font-bold text-slate-600 transition"
              >
                Hủy bỏ
              </button>
              <button
                type="submit"
                disabled={isProcessing === editCategory.id}
                className="px-5 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-[10px] font-bold shadow-md shadow-primary/20 transition cursor-pointer"
              >
                {isProcessing === editCategory.id ? 'Đang lưu...' : 'Cập nhật'}
              </button>
            </div>
          </form>
        </div>
      )}

    </div>
  )
}
