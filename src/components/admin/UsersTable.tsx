'use client'

import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'

interface UserItem {
  id: string
  name: string
  email: string
  phone: string | null
  avatar: string | null
  role: string
  status: string
  createdAt: string
}

interface UsersTableProps {
  initialUsers: UserItem[]
  currentUserId: string
  searchParams: {
    search?: string
    role?: string
    status?: string
  }
}

export default function UsersTable({ initialUsers, currentUserId, searchParams }: UsersTableProps) {
  const router = useRouter()
  const [users, setUsers] = useState<UserItem[]>(initialUsers)
  
  // Local filter states
  const [search, setSearch] = useState(searchParams.search || '')
  const [role, setRole] = useState(searchParams.role || '')
  const [status, setStatus] = useState(searchParams.status || '')
  const [isProcessing, setIsProcessing] = useState<string | null>(null)
  const [previewUser, setPreviewUser] = useState<UserItem | null>(null)

  const handleFilter = () => {
    const params = new URLSearchParams()
    if (search) params.set('search', search)
    if (role) params.set('role', role)
    if (status) params.set('status', status)
    router.push(`/admin/users?${params.toString()}`)
  }

  const toggleStatus = async (id: string) => {
    if (id === currentUserId) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/users/${id}/toggle-status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setUsers(prev => 
          prev.map(u => u.id === id ? { ...u, status: data.status } : u)
        )
      } else {
        alert(data.error || 'Lỗi đổi trạng thái')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  const deleteUser = async (id: string) => {
    if (id === currentUserId) return
    if (!confirm('Bạn có chắc chắn muốn xóa thành viên này? Hành động này không thể hoàn tác!')) return
    setIsProcessing(id)

    try {
      const res = await fetch(`/api/admin/users/${id}`, {
        method: 'DELETE'
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setUsers(prev => prev.filter(u => u.id !== id))
      } else {
        alert(data.error || 'Lỗi xóa tài khoản')
      }
    } catch (err) {
      console.error(err)
    } finally {
      setIsProcessing(null)
    }
  }

  return (
    <div className="space-y-6 text-left">
      
      {/* Search and Filters Bar */}
      <div className="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-grow max-w-3xl">
          <input
            type="text"
            placeholder="Tìm theo tên, email, sđt..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />

          <select
            value={role}
            onChange={(e) => setRole(e.target.value)}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer"
          >
            <option value="">-- Tất cả vai trò --</option>
            <option value="tenant">Khách thuê (Tenant)</option>
            <option value="owner">Chủ nhà (Owner)</option>
            <option value="agent">Môi giới (NKS Agent)</option>
            <option value="admin">Quản trị viên (Admin)</option>
          </select>

          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer"
          >
            <option value="">-- Tất cả trạng thái --</option>
            <option value="active">Hoạt động (Active)</option>
            <option value="locked">Đã khóa (Locked)</option>
          </select>
        </div>

        <button
          onClick={handleFilter}
          className="px-6 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer self-start md:self-auto flex items-center gap-1.5"
        >
          <i className="fa-solid fa-magnifying-glass" />
          <span>Tìm kiếm</span>
        </button>
      </div>

      {/* Users Tabular list */}
      <div className="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-xs font-semibold text-slate-650 border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 uppercase text-[9px] tracking-wider text-slate-400 font-bold">
                <th className="px-6 py-4 text-left">Thành viên</th>
                <th className="px-6 py-4 text-left">Số điện thoại</th>
                <th className="px-6 py-4 text-left">Vai trò</th>
                <th className="px-6 py-4 text-left">Trạng thái</th>
                <th className="px-6 py-4 text-left">Ngày tham gia</th>
                <th className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {users.length > 0 ? (
                users.map(u => (
                  <tr key={u.id} className="hover:bg-slate-50/50 transition">
                    <td className="px-6 py-3 flex items-center space-x-3">
                      <div className="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0">
                        {u.avatar ? (
                          <img src={u.avatar} className="w-full h-full object-cover" />
                        ) : (
                          <i className="fa-regular fa-user text-slate-300 text-sm" />
                        )}
                      </div>
                      <div className="text-left truncate max-w-xs">
                        <strong className="block text-slate-800 font-semibold">{u.name}</strong>
                        <span className="block text-[10px] text-slate-400 font-semibold select-all truncate">{u.email}</span>
                      </div>
                    </td>
                    <td className="px-6 py-3 text-left select-all font-semibold text-slate-700">{u.phone || '—'}</td>
                    <td className="px-6 py-3 text-left">
                      <span className={`inline-block px-2 py-0.5 rounded-md text-[8px] font-bold uppercase ${
                        u.role === 'admin' 
                          ? 'bg-red-50 text-red-650'
                          : u.role === 'owner'
                          ? 'bg-primary-light text-primary'
                          : u.role === 'agent'
                          ? 'bg-teal-50 text-teal-600 border border-teal-200/55'
                          : 'bg-slate-100 text-slate-650'
                      }`}>
                        {u.role === 'admin' ? 'Admin' : u.role === 'owner' ? 'Chủ nhà' : u.role === 'agent' ? 'Môi giới (NKS)' : 'Khách thuê'}
                      </span>
                    </td>
                    <td className="px-6 py-3 text-left">
                      <span className={`font-black ${u.status === 'locked' ? 'text-red-500' : 'text-emerald-500'}`}>
                        {u.status === 'locked' ? 'Khóa 🔒' : 'Hoạt động ✓'}
                      </span>
                    </td>
                    <td className="px-6 py-3 text-left text-slate-500 font-semibold">
                      {u.id.toString().startsWith('nks-') ? 'Liên kết NKS' : new Date(u.createdAt).toLocaleDateString('vi-VN')}
                    </td>
                    <td className="px-6 py-3 text-right space-x-2 whitespace-nowrap">
                      {/* Xem thông tin nút */}
                      <button
                        onClick={() => setPreviewUser(u)}
                        className="px-2.5 py-1.5 border border-slate-200 hover:bg-slate-50 rounded-lg text-[10px] font-bold text-slate-550 transition cursor-pointer"
                      >
                        Xem thông tin
                      </button>

                      {!u.id.toString().startsWith('nks-') ? (
                        <>
                          <Link 
                            href={`/admin/users/${u.id}`}
                            className="inline-flex w-8 h-8 rounded-lg border border-slate-200/50 hover:bg-slate-50 items-center justify-center text-slate-500 hover:text-primary transition"
                            title="Chi tiết"
                          >
                            <i className="fa-solid fa-circle-info text-xs" />
                          </Link>

                          {u.id !== currentUserId && (
                            <>
                              <button
                                onClick={() => toggleStatus(u.id)}
                                disabled={isProcessing === u.id}
                                className={`w-8 h-8 rounded-lg border border-slate-200/50 items-center justify-center transition cursor-pointer inline-flex ${
                                  u.status === 'locked' 
                                    ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border-emerald-200/30'
                                    : 'bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200/30'
                                }`}
                                title={u.status === 'locked' ? 'Mở khóa' : 'Khóa tài khoản'}
                              >
                                <i className={`fa-solid ${u.status === 'locked' ? 'fa-lock-open' : 'fa-lock'} text-xs`} />
                              </button>

                              <button
                                onClick={() => deleteUser(u.id)}
                                disabled={isProcessing === u.id}
                                className="w-8 h-8 rounded-lg border border-red-100/50 bg-red-50 hover:bg-red-500 hover:text-white items-center justify-center text-red-650 transition cursor-pointer inline-flex"
                                title="Xóa tài khoản"
                              >
                                <i className="fa-regular fa-trash-can text-xs" />
                              </button>
                            </>
                          )}
                        </>
                      ) : (
                        <span className="text-[10px] text-slate-400 font-bold select-none italic pr-3">Dữ liệu từ API NKS</span>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="py-12 text-center text-slate-400 text-xs font-semibold">
                    Không tìm thấy thành viên nào khớp với bộ lọc.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* User Information Preview Modal */}
      {previewUser && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center z-[99999] p-4">
          <div className="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-sm w-full p-6 space-y-4 max-h-[85vh] overflow-y-auto relative animate-in fade-in zoom-in-95 duration-250 text-left">
            <button 
              onClick={() => setPreviewUser(null)}
              className="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
            >
              <i className="fa-solid fa-xmark text-sm" />
            </button>

            <div className="flex flex-col items-center text-center space-y-3 pt-4">
              <div className="w-20 h-20 rounded-full border-2 border-primary/20 bg-slate-50 overflow-hidden flex items-center justify-center shadow-lg shadow-slate-100">
                {previewUser.avatar ? (
                  <img src={previewUser.avatar} className="w-full h-full object-cover" />
                ) : (
                  <span className="w-full h-full flex items-center justify-center bg-brand-50 text-brand-650 font-bold text-2xl uppercase">
                    {previewUser.name.charAt(0)}
                  </span>
                )}
              </div>
              <div>
                <h3 className="font-black text-slate-850 text-sm">{previewUser.name}</h3>
                <span className={`inline-block mt-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase ${
                  previewUser.role === 'admin' 
                    ? 'bg-red-50 text-red-650'
                    : previewUser.role === 'owner'
                    ? 'bg-primary-light text-primary'
                    : previewUser.role === 'agent'
                    ? 'bg-teal-50 text-teal-600 border border-teal-200/55'
                    : 'bg-slate-100 text-slate-650'
                }`}>
                  {previewUser.role === 'admin' ? 'Quản trị viên' : previewUser.role === 'owner' ? 'Chủ nhà' : previewUser.role === 'agent' ? 'Môi giới NKS' : 'Khách thuê'}
                </span>
              </div>
            </div>

            <div className="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs font-semibold text-slate-600">
              <div className="flex justify-between items-center py-1 border-b border-slate-100/50">
                <span className="text-slate-400 text-[10px] uppercase font-bold">Email</span>
                <span className="text-slate-850 select-all truncate max-w-[180px] text-right font-bold">{previewUser.email}</span>
              </div>
              <div className="flex justify-between items-center py-1 border-b border-slate-100/50">
                <span className="text-slate-400 text-[10px] uppercase font-bold">Số điện thoại</span>
                <span className="text-slate-850 select-all font-bold">{previewUser.phone || '—'}</span>
              </div>
              <div className="flex justify-between items-center py-1 border-b border-slate-100/50">
                <span className="text-slate-400 text-[10px] uppercase font-bold">Trạng thái</span>
                <span className={`font-black ${previewUser.status === 'locked' ? 'text-red-500' : 'text-emerald-500'}`}>
                  {previewUser.status === 'locked' ? 'Khóa 🔒' : 'Hoạt động ✓'}
                </span>
              </div>
              <div className="flex justify-between items-center py-1">
                <span className="text-slate-400 text-[10px] uppercase font-bold">Nguồn dữ liệu</span>
                <span className="text-slate-850 font-bold">
                  {previewUser.id.toString().startsWith('nks-') ? 'Liên kết NKS Portal' : 'Thành viên hệ thống'}
                </span>
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-2">
              <button
                onClick={() => setPreviewUser(null)}
                className="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-500 text-xs font-bold rounded-xl transition cursor-pointer"
              >
                Đóng
              </button>
              {!previewUser.id.toString().startsWith('nks-') && (
                <Link
                  href={`/admin/users/${previewUser.id}`}
                  className="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl transition cursor-pointer flex items-center gap-1 shadow-md shadow-primary/10"
                >
                  Chi tiết tài khoản
                </Link>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
