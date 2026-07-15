'use client'

import { useState } from 'react'

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

interface AdminUsersTabProps {
  initialUsers: UserItem[]
  currentUserId: string
}

export default function AdminUsersTab({ initialUsers, currentUserId }: AdminUsersTabProps) {
  const [users, setUsers] = useState<UserItem[]>(initialUsers)
  const [search, setSearch] = useState('')
  const [role, setRole] = useState('')
  const [status, setStatus] = useState('')
  const [isProcessing, setIsProcessing] = useState<string | null>(null)

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

  // Filter clientside
  const filteredUsers = users.filter(u => {
    const matchesSearch = !search || 
      u.name.toLowerCase().includes(search.toLowerCase()) ||
      u.email.toLowerCase().includes(search.toLowerCase()) ||
      (u.phone && u.phone.includes(search))

    const matchesRole = !role || u.role === role
    const matchesStatus = !status || u.status === status

    return matchesSearch && matchesRole && matchesStatus
  })

  return (
    <div className="space-y-6 text-left">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100">
        <h2 className="text-xl font-bold text-slate-800">Quản lý thành viên</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và khóa/mở khóa tài khoản khách thuê, chủ nhà hoặc quản trị viên.</p>
      </div>

      {/* Filters Bar */}
      <div className="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-grow">
          {/* Keyword Search */}
          <div className="relative">
            <i className="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
            <input
              type="text"
              placeholder="Tên, email hoặc SĐT..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Role selector */}
          <select
            value={role}
            onChange={(e) => setRole(e.target.value)}
            className="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
          >
            <option value="">-- Tất cả vai trò --</option>
            <option value="tenant">Khách thuê nhà</option>
            <option value="owner">Đối tác Chủ nhà</option>
            <option value="admin">Quản trị viên</option>
          </select>

          {/* Status selector */}
          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary transition"
          >
            <option value="">-- Trạng thái --</option>
            <option value="active">Hoạt động</option>
            <option value="locked">Đang khóa</option>
          </select>
        </div>
      </div>

      {/* Table view */}
      <div className="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full border-collapse">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th className="px-6 py-4 text-left">Thành viên</th>
                <th className="px-6 py-4 text-left">Liên hệ</th>
                <th className="px-6 py-4 text-left">Vai trò</th>
                <th className="px-6 py-4 text-left">Trạng thái</th>
                <th className="px-6 py-4 text-center">Hành động</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-xs">
              {filteredUsers.length > 0 ? (
                filteredUsers.map(u => (
                  <tr key={u.id} className="hover:bg-slate-50/50 transition">
                    {/* User profile */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center space-x-3">
                        <img
                          src={u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=0077bb&color=fff`}
                          alt={u.name}
                          className="w-8 h-8 rounded-full object-cover border border-slate-100"
                        />
                        <div>
                          <strong className="block text-slate-800 font-bold">{u.name}</strong>
                          <span className="text-[10px] text-slate-400 font-semibold">Mã: {u.id}</span>
                        </div>
                      </div>
                    </td>

                    {/* Contact */}
                    <td className="px-6 py-4 whitespace-nowrap font-medium text-slate-600">
                      <div>{u.email}</div>
                      <div className="text-[10px] text-slate-400 mt-0.5">{u.phone || 'Chưa cập nhật SĐT'}</div>
                    </td>

                    {/* Role badge */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      {u.role === 'admin' ? (
                        <span className="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-rose-50 text-rose-600 border border-rose-200 uppercase">
                          Admin
                        </span>
                      ) : u.role === 'owner' ? (
                        <span className="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase">
                          Chủ nhà
                        </span>
                      ) : (
                        <span className="inline-flex px-2 py-0.5 rounded-md text-[9px] font-black bg-blue-50 text-blue-600 border border-blue-200 uppercase">
                          Khách thuê
                        </span>
                      )}
                    </td>

                    {/* Status badge */}
                    <td className="px-6 py-4 whitespace-nowrap">
                      {u.status === 'locked' ? (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">
                          <i className="fa-solid fa-lock mr-1 text-[9px]" /> Đang khóa
                        </span>
                      ) : (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                          <i className="fa-solid fa-circle-check mr-1 text-[9px]" /> Hoạt động
                        </span>
                      )}
                    </td>

                    {/* Actions */}
                    <td className="px-6 py-4 whitespace-nowrap text-center">
                      <div className="flex items-center justify-center gap-1.5">
                        {u.id !== currentUserId ? (
                          <>
                            {/* Toggle Block status */}
                            <button
                              onClick={() => toggleStatus(u.id)}
                              disabled={isProcessing === u.id}
                              className={`w-8 h-8 rounded-lg border flex items-center justify-center transition cursor-pointer ${
                                u.status === 'locked'
                                  ? 'bg-emerald-50 hover:bg-emerald-100 border-emerald-200 text-emerald-600'
                                  : 'bg-amber-50 hover:bg-amber-100 border-amber-200 text-amber-600'
                              }`}
                              title={u.status === 'locked' ? 'Mở khóa tài khoản' : 'Khóa tài khoản'}
                            >
                              <i className={`fa-solid ${u.status === 'locked' ? 'fa-lock-open' : 'fa-lock'}`} />
                            </button>

                            {/* Delete Button */}
                            <button
                              onClick={() => deleteUser(u.id)}
                              disabled={isProcessing === u.id}
                              className="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 flex items-center justify-center transition cursor-pointer"
                              title="Xóa tài khoản"
                            >
                              <i className="fa-solid fa-trash-can" />
                            </button>
                          </>
                        ) : (
                          <span className="text-[10px] text-slate-400 font-bold italic">Tài khoản của bạn</span>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-slate-400 font-bold uppercase tracking-wider">
                    Không tìm thấy thành viên nào phù hợp
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
