'use client'

import { useState } from 'react'
import { toast } from 'sonner'

interface AdminUsersTabProps {
  initialUsers: any[]
}

export default function AdminUsersTab({ initialUsers }: AdminUsersTabProps) {
  const [users, setUsers] = useState(initialUsers)
  const [searchTerm, setSearchTerm] = useState('')
  const [filterRole, setFilterRole] = useState('')
  const [filterStatus, setFilterStatus] = useState('')
  const [roleOpen, setRoleOpen] = useState(false)
  const [statusOpen, setStatusOpen] = useState(false)

  const handleToggleUserStatus = async (targetUserId: number, currentStatus: string) => {
    const actionLabel = currentStatus === 'locked' ? 'mở khóa' : 'khóa'
    if (!window.confirm(`Bạn có chắc chắn muốn ${actionLabel} tài khoản này?`)) return
    try {
      const res = await fetch(`/api/admin/users/${targetUserId}/toggle-status`, {
        method: 'POST'
      })
      const data = await res.json()
      if (data.success) {
        toast.success(data.message)
        setUsers(prev => prev.map(u => u.id === targetUserId ? { ...u, status: data.status } : u))
      } else {
        toast.error(data.error || 'Có lỗi xảy ra')
      }
    } catch (e: any) {
      toast.error(e.message || 'Lỗi mạng')
    }
  }

  // Filter logic matching bds_php
  const filteredUsers = users.filter(u => {
    const textMatch = searchTerm.trim() === '' ||
      u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      u.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      (u.phone && u.phone.includes(searchTerm))

    const roleMatch = filterRole === '' || u.role === filterRole
    const statusMatch = filterStatus === '' || u.status === filterStatus

    return textMatch && roleMatch && statusMatch
  })

  const getRoleLabel = (role: string) => {
    if (role === 'admin') return 'Quản trị viên'
    if (role === 'owner') return 'Chủ nhà'
    if (role === 'tenant') return 'Khách thuê'
    return '-- Tất cả vai trò --'
  }

  const getStatusLabel = (status: string) => {
    if (status === 'active') return 'Hoạt động'
    if (status === 'locked') return 'Đang khóa'
    return '-- Trạng thái --'
  }

  return (
    <div className="space-y-6">
      {/* Title */}
      <div className="pb-5 border-b border-slate-100 mb-6 text-left">
        <h2 className="text-xl font-bold text-slate-800">Quản lý thành viên</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và khóa/mở khóa tài khoản khách thuê, chủ nhà hoặc quản trị viên.</p>
      </div>

      {/* Filters & Search Card */}
      <div className="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 shadow-sm text-left">
        <div className="grid grid-cols-1 sm:grid-cols-12 gap-4">
          {/* Search Keyword */}
          <div className="sm:col-span-6 relative">
            <i className="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              id="userSearchTerm"
              name="userSearchTerm"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              placeholder="Tìm kiếm theo tên, email hoặc SĐT..." 
              className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
            />
          </div>

          {/* Role Filter */}
          <div className="relative sm:col-span-3">
            <button 
              type="button" 
              onClick={() => setRoleOpen(!roleOpen)} 
              className="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
            >
              <span>{filterRole ? getRoleLabel(filterRole) : '-- Tất cả vai trò --'}</span>
              <i className="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>
          
            {roleOpen && (
              <div 
                onMouseLeave={() => setRoleOpen(false)}
                className="absolute z-35 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
              >
                {['', 'tenant', 'owner', 'admin'].map((role) => (
                  <button 
                    key={role}
                    type="button" 
                    onClick={() => {
                      setFilterRole(role)
                      setRoleOpen(false)
                    }}
                    className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterRole === role ? 'bg-primary/5 text-primary font-bold' : ''}`}
                  >
                    {getRoleLabel(role)}
                  </button>
                ))}
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
              <span>{filterStatus ? getStatusLabel(filterStatus) : '-- Trạng thái --'}</span>
              <i className="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>
          
            {statusOpen && (
              <div 
                onMouseLeave={() => setStatusOpen(false)}
                className="absolute z-35 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
              >
                {['', 'active', 'locked'].map((status) => (
                  <button 
                    key={status}
                    type="button" 
                    onClick={() => {
                      setFilterStatus(status)
                      setStatusOpen(false)
                    }}
                    className={`w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition ${filterStatus === status ? 'bg-primary/5 text-primary font-bold' : ''}`}
                  >
                    {getStatusLabel(status)}
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Users Table Card */}
      <div className="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
        <div className="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
          {filteredUsers.length > 0 ? (
            <table className="min-w-full text-left text-xs text-slate-600 font-semibold">
              <thead className="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                <tr>
                  <th scope="col" className="px-6 py-4">Thành viên</th>
                  <th scope="col" className="px-6 py-4">Liên hệ</th>
                  <th scope="col" className="px-6 py-4">Vai trò</th>
                  <th scope="col" className="px-6 py-4">Trạng thái</th>
                  <th scope="col" className="px-6 py-4 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredUsers.map((userItem) => (
                  <tr key={userItem.id} className="hover:bg-slate-50/50 transition">
                    {/* Avatar & Name */}
                    <td className="px-6 py-4 flex items-center space-x-3.5">
                      <img 
                        src={userItem.avatar} 
                        alt={userItem.name} 
                        className="w-9 h-9 rounded-full object-cover border border-slate-100 shadow-sm"
                        onError={(e) => {
                          e.currentTarget.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userItem.name)}&background=0077bb&color=fff`
                        }}
                      />
                      <div>
                        <span className="font-bold text-slate-800 text-xs block leading-none">
                          {userItem.name}
                        </span>
                        <span className="text-[9px] text-slate-400 block mt-1">ID: #{userItem.id}</span>
                      </div>
                    </td>
                    {/* Email & Phone */}
                    <td className="px-6 py-4">
                      <span className="block text-slate-750 font-semibold leading-none">{userItem.email}</span>
                      <span className="text-[10px] text-slate-400 block mt-1">{userItem.phone || 'Chưa cập nhật SĐT'}</span>
                    </td>
                    {/* Role */}
                    <td className="px-6 py-4">
                      {userItem.role === 'admin' ? (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Admin</span>
                      ) : userItem.role === 'owner' ? (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Chủ nhà</span>
                      ) : (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200">Khách thuê</span>
                      )}
                    </td>
                    {/* Status */}
                    <td className="px-6 py-4">
                      {userItem.status === 'locked' ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">
                          <i className="fa-solid fa-lock mr-1.5 text-[8px]"></i> Đã khóa
                        </span>
                      ) : (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">
                          <i className="fa-solid fa-circle-check mr-1.5 text-[8px]"></i> Hoạt động
                        </span>
                      )}
                    </td>
                    {/* Actions */}
                    <td className="px-6 py-4 text-right whitespace-nowrap">
                      <div className="flex items-center justify-end gap-1.5">
                        <button 
                          type="button" 
                          onClick={() => handleToggleUserStatus(userItem.id, userItem.status)}
                          title={userItem.status === 'locked' ? 'Mở khóa tài khoản' : 'Khóa tài khoản'}
                          className={`w-7 h-7 rounded-lg border text-xs cursor-pointer transition shadow-sm inline-flex items-center justify-center ${
                            userItem.status === 'locked' 
                              ? 'bg-green-50 hover:bg-green-100 text-green-600 border-green-200' 
                              : 'bg-red-50 hover:bg-red-100 text-red-650 border-red-200'
                          }`}
                        >
                          <i className={`fa-solid ${userItem.status === 'locked' ? 'fa-lock-open' : 'fa-lock'} text-[10px]`}></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <div className="py-16 text-center text-slate-400 font-semibold">
              <i className="fa-solid fa-users-slash text-3xl mb-3 block text-slate-350"></i>
              Chưa có thành viên nào thỏa mãn bộ lọc.
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
