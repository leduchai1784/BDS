'use client'

import { useState, useEffect } from 'react'
import ProfileInfoForm from './ProfileInfoForm'
import PasswordForm from './PasswordForm'
import CccdForm from './CccdForm'
import AvatarCropper from './AvatarCropper'
import MyPropertiesTab from './MyPropertiesTab'
import AppointmentsTab from './AppointmentsTab'
import WishlistTab from './WishlistTab'
import RegisterOwnerForm from './RegisterOwnerForm'
import AiMarketingStudio from './AiMarketingStudio'
import AdminUsersTab from './AdminUsersTab'
import AdminPropertiesTab from './AdminPropertiesTab'
import AdminAppointmentsTab from './AdminAppointmentsTab'
import AdminLeadsTab from './AdminLeadsTab'
import { Toaster, toast } from 'sonner'
import Link from 'next/link'

interface ProfilePageClientProps {
  user: any
  properties: any[]
  tenantAppointments: any[]
  ownerAppointments: any[]
  wishlistProperties: any[]
  stats: {
    totalProperties: number
    totalAppointments: number
    totalViews?: number
    totalFavorites?: number
  }
  adminUsers?: any[]
  adminProperties?: any[]
  adminAppointments?: any[]
  categories?: any[]
  leads?: any[]
}

export default function ProfilePageClient({
  user: initialUser,
  properties,
  tenantAppointments,
  ownerAppointments,
  wishlistProperties,
  stats,
  adminUsers = [],
  adminProperties = [],
  adminAppointments = [],
  categories = [],
  leads = []
}: ProfilePageClientProps) {
  const [activeTab, setActiveTab] = useState('profile')
  const [activeSubTab, setActiveSubTab] = useState('info') // info, password, cccd, avatar
  const [user, setUser] = useState(initialUser)
  const [profileMenuOpen, setProfileMenuOpen] = useState(false)

  // Parse tab search params on load
  useEffect(() => {
    const params = new URLSearchParams(window.location.search)
    const tab = params.get('tab')
    const subtab = params.get('subtab')
    if (tab) setActiveTab(tab)
    if (subtab) setActiveSubTab(subtab)
  }, [])

  const handleTabChange = (tab: string, sub?: string) => {
    setActiveTab(tab)
    if (sub) {
      setActiveSubTab(sub)
    }
    
    // Sync URL params without full page reload
    const url = new URL(window.location.href)
    url.searchParams.set('tab', tab)
    if (sub) {
      url.searchParams.set('subtab', sub)
    } else {
      url.searchParams.delete('subtab')
    }
    window.history.pushState(null, '', url.pathname + url.search)
  }

  const handleSuccess = (message: string, updatedFields?: any) => {
    toast.success(message)
    if (updatedFields) {
      setUser((prev: any) => ({ ...prev, ...updatedFields }))
    }
  }

  const handleAvatarSuccess = (message: string, newAvatarUrl: string) => {
    toast.success(message)
    setUser((prev: any) => ({ ...prev, avatar: newAvatarUrl }))
  }

  const isOwner = user.role === 'owner'
  const isCCCDVerified = !!user.idNumber

  return (
    <div className="bg-slate-50 pt-28 pb-16 min-h-screen relative text-slate-800">
      <Toaster position="top-right" richColors />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2 text-left" aria-label="Breadcrumb">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-800">Dashboard thành viên</span>
        </nav>
        
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Left Column: Dashboard Sidebar Navigation (3/12 cols) */}
          <div className="lg:col-span-3 lg:self-stretch">
            <div className="lg:sticky lg:top-24 max-h-[calc(100vh-7rem)] overflow-y-auto thin-scrollbar text-left flex flex-col">
              <div className="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex-grow">
                
                {/* Banner Profile Summary */}
                <div className="relative text-center pb-6 border-b border-slate-100 bg-white">
                  <div className="h-24 bg-gradient-to-r from-primary/80 via-primary to-indigo-650/90 relative overflow-hidden">
                    <div className="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-white/10 blur-lg" />
                    <div className="absolute -left-8 -bottom-8 w-24 h-24 rounded-full bg-white/10 blur-md" />
                  </div>
                  
                  {/* Avatar Upload Overlay */}
                  <div className="relative w-20 h-20 mx-auto -mt-10 mb-3 group">
                    <img 
                      src={user.avatar} 
                      alt={user.name} 
                      className="w-full h-full rounded-full object-cover border-4 border-white shadow-lg"
                      onError={(e) => {
                        e.currentTarget.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0077bb&color=fff`
                      }}
                    />
                    <span className="absolute bottom-0.5 right-0.5 w-4.5 h-4.5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center shadow-md">
                      <span className="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 animate-ping" />
                    </span>
                    
                    <button 
                      onClick={() => handleTabChange('profile', 'avatar')}
                      className="absolute bottom-0.5 left-0.5 w-6 h-6 rounded-full bg-white hover:bg-slate-50 text-slate-500 hover:text-primary border border-slate-200 shadow-md flex items-center justify-center cursor-pointer transition-all active:scale-90 z-10 p-0"
                      title="Thay đổi ảnh đại diện"
                    >
                      <i className="fa-solid fa-camera text-[9px]" />
                    </button>
                  </div>

                  {/* User info */}
                  <div className="px-5">
                    <h4 className="text-base font-extrabold text-slate-800 leading-snug tracking-tight mb-2 flex items-center justify-center gap-1.5">
                      {user.name}
                      {isCCCDVerified && (
                        <span className="inline-flex items-center text-emerald-500" title="Tài khoản đã xác thực CCCD">
                          <i className="fa-solid fa-circle-check text-sm" />
                        </span>
                      )}
                    </h4>
                    
                    <div className="flex flex-wrap items-center justify-center gap-2 mb-3">
                      {user.role === 'admin' ? (
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-violet-50 text-violet-650 border border-violet-100/85 shadow-sm">
                          <i className="fa-solid fa-user-shield mr-1.5 text-violet-500" />
                          Quản trị viên
                        </span>
                      ) : isOwner ? (
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100/85 shadow-sm">
                          <i className="fa-solid fa-circle-check mr-1.5 text-emerald-500" />
                          Đối tác Chủ nhà
                        </span>
                      ) : (
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-sky-50 text-sky-650 border border-sky-100/85 shadow-sm">
                          <i className="fa-solid fa-house-user mr-1.5 text-sky-500" />
                          Khách thuê nhà
                        </span>
                      )}

                      {isCCCDVerified ? (
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-500 text-white border border-emerald-650 shadow-sm">
                          Đã xác thực
                        </span>
                      ) : (
                        <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm">
                          Chưa xác thực
                        </span>
                      )}
                    </div>
                  </div>
                </div>

                {/* Navigation Links */}
                <nav className="flex flex-col overflow-y-auto thin-scrollbar max-h-[320px] border-b lg:border-b-0 border-slate-100 pb-4 lg:pb-6">
                  {/* Info dropdown menu */}
                  <button 
                    onClick={() => {
                      if (activeTab === 'profile') {
                        setProfileMenuOpen(!profileMenuOpen)
                      } else {
                        handleTabChange('profile', 'info')
                        setProfileMenuOpen(true)
                      }
                    }}
                    className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition focus:outline-none w-full text-left ${
                      activeTab === 'profile' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-650 border-transparent hover:bg-slate-50 hover:text-primary'
                    }`}
                  >
                    <div className="flex items-center space-x-3">
                      <i className="fa-solid fa-user-gear text-sm" />
                      <span>Thông tin & Bảo mật</span>
                    </div>
                    <i className={`fa-solid fa-chevron-down text-[10px] transition-transform duration-200 ml-2 ${
                      activeTab === 'profile' && profileMenuOpen ? 'rotate-180 text-primary' : 'text-slate-400'
                    }`} />
                  </button>

                  {/* Subtabs list (Only active if profile tab selected and menu is expanded) */}
                  {activeTab === 'profile' && profileMenuOpen && (
                    <div className="pl-9 pr-4 py-2 space-y-1 bg-slate-50/50 border-l border-slate-100/80">
                      <button 
                        onClick={() => handleTabChange('profile', 'info')}
                        className={`flex items-center space-x-2.5 px-3 py-2 rounded-xl text-[11px] w-full text-left transition ${
                          activeSubTab === 'info' ? 'text-primary font-black bg-primary/10' : 'text-slate-500 font-semibold hover:text-primary'
                        }`}
                      >
                        <i className="fa-solid fa-user text-[10px]" />
                        <span>Thông tin cá nhân</span>
                      </button>
                      
                      <button 
                        onClick={() => handleTabChange('profile', 'password')}
                        className={`flex items-center space-x-2.5 px-3 py-2 rounded-xl text-[11px] w-full text-left transition ${
                          activeSubTab === 'password' ? 'text-primary font-black bg-primary/10' : 'text-slate-500 font-semibold hover:text-primary'
                        }`}
                      >
                        <i className="fa-solid fa-key text-[10px]" />
                        <span>Đổi mật khẩu</span>
                      </button>
                      
                      <button 
                        onClick={() => handleTabChange('profile', 'cccd')}
                        className={`flex items-center space-x-2.5 px-3 py-2 rounded-xl text-[11px] w-full text-left transition ${
                          activeSubTab === 'cccd' ? 'text-primary font-black bg-primary/10' : 'text-slate-500 font-semibold hover:text-primary'
                        }`}
                      >
                        <i className="fa-solid fa-id-card text-[10px]" />
                        <span>Xác thực CCCD</span>
                      </button>
                    </div>
                  )}

                  {/* Properties tab (For Owner) */}
                  {isOwner && (
                    <button 
                      onClick={() => handleTabChange('properties')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'properties' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-list-check text-sm" />
                        <span>Quản lý tin đăng</span>
                      </div>
                      <span className="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                        {stats.totalProperties}
                      </span>
                    </button>
                  )}

                  {/* Agent Profile tab (For Agent only) */}
                  {user.role === 'agent' && (
                    <button 
                      onClick={() => handleTabChange('agent_profile')}
                      className={`flex items-center space-x-3 px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'agent_profile' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <i className="fa-solid fa-id-badge text-sm text-amber-500" />
                      <span>Hồ sơ Môi giới</span>
                    </button>
                  )}

                  {/* Appointments tab (Tenant and Owner only) */}
                  {user.role !== 'admin' && (
                    <button 
                      onClick={() => handleTabChange('appointments')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'appointments' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-calendar-days text-sm" />
                        <span>Lịch hẹn</span>
                      </div>
                      <span className="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                        {stats.totalAppointments}
                      </span>
                    </button>
                  )}

                  {/* Admin: Quản lý thành viên */}
                  {user.role === 'admin' && (
                    <button 
                      onClick={() => handleTabChange('admin_users')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'admin_users' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-users text-sm" />
                        <span>Quản lý thành viên</span>
                      </div>
                      <span className="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                        {adminUsers.length}
                      </span>
                    </button>
                  )}

                  {/* Admin: Quản lý tin đăng */}
                  {user.role === 'admin' && (
                    <button 
                      onClick={() => handleTabChange('admin_properties')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'admin_properties' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-list-check text-sm" />
                        <span>Quản lý tin đăng</span>
                      </div>
                      <span className="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                        {adminProperties.length}
                      </span>
                    </button>
                  )}

                  {/* Admin: Quản lý lịch hẹn */}
                  {user.role === 'admin' && (
                    <button 
                      onClick={() => handleTabChange('admin_appointments')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'admin_appointments' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-calendar-check text-sm" />
                        <span>Quản lý lịch hẹn</span>
                      </div>
                      <span className="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                        {adminAppointments.length}
                      </span>
                    </button>
                  )}

                  {/* Admin: Quản lý Lead */}
                  {user.role === 'admin' && (
                    <button 
                      onClick={() => handleTabChange('leads')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'leads' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-user-group text-sm" />
                        <span>Quản lý Lead</span>
                      </div>
                      <span className="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                        {leads.length}
                      </span>
                    </button>
                  )}

                  {/* AI Content Studio tab (For Owner) */}
                  {isOwner && (
                    <button 
                      onClick={() => handleTabChange('marketing')}
                      className={`flex items-center justify-between px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'marketing' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <i className="fa-solid fa-wand-magic-sparkles text-sm" />
                        <span>AI Content Studio</span>
                      </div>
                    </button>
                  )}

                  {/* Favorites tab */}
                  <button 
                    onClick={() => handleTabChange('favorites')}
                    className={`flex items-center space-x-3 px-5 py-4 text-xs font-bold border-l-4 transition ${
                      activeTab === 'favorites' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'
                    }`}
                  >
                    <i className="fa-solid fa-heart text-sm" />
                    <span>Tin yêu thích</span>
                  </button>

                  {/* Upgrade Role (For Tenant only) */}
                  {user.role === 'tenant' && (
                    <button 
                      onClick={() => handleTabChange('register_owner')}
                      className={`flex items-center space-x-3 px-5 py-4 text-xs font-bold border-l-4 transition ${
                        activeTab === 'register_owner' ? 'bg-primary/5 text-primary border-primary font-extrabold' : 'text-slate-655 border-transparent hover:bg-slate-50 hover:text-primary'
                      }`}
                    >
                      <i className="fa-solid fa-crown text-sm text-amber-500" />
                      <span>Đăng ký đối tác chủ nhà</span>
                    </button>
                  )}
                </nav>
              </div>
            </div>
          </div>

          {/* Right Column: Tab View Workspace (9/12 cols) */}
          <div className="lg:col-span-9 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8">
            
            {/* 1. Profile Tab Wrapper */}
            {activeTab === 'profile' && (
              <div className="space-y-8">
                {/* Title */}
                <div className="pb-5 border-b border-slate-100 text-left">
                  <h2 className="text-xl font-bold text-slate-800">Thông tin cá nhân</h2>
                  <p className="text-xs text-slate-400 mt-1 font-semibold">Cập nhật hồ sơ cá nhân và đồng bộ với cổng thông tin NKS Online.</p>
                </div>
                
                {/* Statistics Cards Grid */}
                {user.role !== 'admin' && (
                  isOwner ? (
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 text-left">
                      {/* Stat Item 1 */}
                      <div className="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                        <div className="w-12 h-12 rounded-xl bg-primary/5 text-primary flex items-center justify-center text-lg">
                          <i className="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin đã đăng</span>
                          <span className="text-xl font-black text-slate-800">{stats.totalProperties} tin</span>
                        </div>
                      </div>

                      {/* Stat Item 2 */}
                      <div className="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                        <div className="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-lg">
                          <i className="fa-solid fa-eye"></i>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lượt xem tin</span>
                          <span className="text-xl font-black text-slate-800">{stats.totalViews?.toLocaleString('vi-VN') || 0} lượt</span>
                        </div>
                      </div>

                      {/* Stat Item 3 */}
                      <div className="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                        <div className="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-lg">
                          <i className="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn khách đặt</span>
                          <span className="text-xl font-black text-slate-800">{ownerAppointments.length} cuộc</span>
                        </div>
                      </div>
                    </div>
                  ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 text-left">
                      {/* Stat Item 1 */}
                      <div className="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                        <div className="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-lg">
                          <i className="fa-solid fa-heart"></i>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin yêu thích</span>
                          <span className="text-xl font-black text-slate-800">{stats.totalFavorites || 0} tin</span>
                        </div>
                      </div>

                      {/* Stat Item 2 */}
                      <div className="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                        <div className="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-lg">
                          <i className="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn</span>
                          <span className="text-xl font-black text-slate-800">{tenantAppointments.length} cuộc</span>
                        </div>
                      </div>
                    </div>
                  )
                )}

                {/* Subtab Contents */}
                <div className="mt-8 border-t border-slate-100 pt-8">
                  {activeSubTab === 'info' && (
                    <div className="space-y-6 text-left">
                      <div>
                        <h3 className="text-sm font-black text-slate-800">Thông tin cá nhân</h3>
                        <p className="text-[11px] text-slate-500 font-semibold">Cập nhật hồ sơ cá nhân và đồng bộ với cổng thông tin NKS Online.</p>
                      </div>
                      <ProfileInfoForm user={user} onSuccess={handleSuccess} />
                    </div>
                  )}

                  {activeSubTab === 'password' && (
                    <div className="space-y-6 text-left">
                      <div>
                        <h3 className="text-sm font-black text-slate-800">Đổi mật khẩu tài khoản</h3>
                        <p className="text-[11px] text-slate-500 font-semibold">Thiết lập mật khẩu bảo mật mới.</p>
                      </div>
                      <PasswordForm onSuccess={handleSuccess} />
                    </div>
                  )}

                  {activeSubTab === 'cccd' && (
                    <div className="space-y-6 text-left">
                      <CccdForm user={user} onSuccess={handleSuccess} />
                    </div>
                  )}

                  {activeSubTab === 'avatar' && (
                    <div className="space-y-6 text-left">
                      <div>
                        <h3 className="text-sm font-black text-slate-800">Ảnh đại diện</h3>
                        <p className="text-[11px] text-slate-500 font-semibold">Cập nhật ảnh đại diện của bạn.</p>
                      </div>
                      <AvatarCropper currentAvatar={user.avatar} onSuccess={handleAvatarSuccess} />
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* 5. Properties Tab */}
            {activeTab === 'properties' && isOwner && (
              <MyPropertiesTab initialProperties={properties} onSuccess={handleSuccess} />
            )}

            {/* 6. Appointments Tab */}
            {activeTab === 'appointments' && (
              <AppointmentsTab 
                initialTenantAppointments={tenantAppointments} 
                initialOwnerAppointments={ownerAppointments} 
                isOwner={isOwner} 
              />
            )}

            {/* 7. Wishlist / Favorites Tab */}
            {activeTab === 'favorites' && (
              <WishlistTab initialProperties={wishlistProperties} />
            )}

            {/* 8. Register Owner Tab */}
            {activeTab === 'register_owner' && !isOwner && (
              <div className="space-y-6">
                <div>
                  <h3 className="text-base font-black text-slate-800">Đăng ký Đối tác Chủ nhà</h3>
                  <p className="text-[11px] text-slate-500 font-semibold">Nâng cấp tài khoản để bắt đầu đăng tin cho thuê, bán bất động sản miễn phí.</p>
                </div>
                <RegisterOwnerForm 
                  onSuccess={handleSuccess} 
                  initialPhone={user.phone || ''}
                  initialCompany={user.company || ''}
                />
              </div>
            )}

            {/* Agent Profile Tab */}
            {activeTab === 'agent_profile' && user.role === 'agent' && (
              <div className="space-y-8 text-left">
                <div className="pb-5 border-b border-slate-100">
                  <h2 className="text-xl font-bold text-slate-800">Hồ sơ Môi giới NKS</h2>
                  <p className="text-xs text-slate-400 mt-1 font-semibold">Thông tin môi giới chuyên nghiệp đồng bộ từ NKS API.</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  {/* Slogan */}
                  <div className="sm:col-span-2 bg-amber-50/50 border border-amber-100 p-5 rounded-2xl">
                    <div className="flex items-center space-x-2 mb-2">
                      <i className="fa-solid fa-quote-left text-amber-400 text-sm" />
                      <span className="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Slogan Môi giới</span>
                    </div>
                    <p className="text-sm font-semibold text-slate-700">{user.rslogan || 'Chưa cập nhật slogan'}</p>
                  </div>

                  {/* Bio */}
                  <div className="sm:col-span-2 bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <div className="flex items-center space-x-2 mb-2">
                      <i className="fa-solid fa-user-pen text-slate-400 text-sm" />
                      <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Giới thiệu bản thân</span>
                    </div>
                    <p className="text-xs font-semibold text-slate-600 leading-relaxed">{user.rsbio || user.intro || 'Chưa có thông tin giới thiệu'}</p>
                  </div>

                  {/* Experience */}
                  <div className="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <div className="flex items-center space-x-2 mb-2">
                      <i className="fa-solid fa-briefcase text-blue-400 text-sm" />
                      <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kinh nghiệm</span>
                    </div>
                    <p className="text-xs font-semibold text-slate-600">{user.rsexperience || 'Chưa cập nhật'}</p>
                  </div>

                  {/* Location */}
                  <div className="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <div className="flex items-center space-x-2 mb-2">
                      <i className="fa-solid fa-location-dot text-red-400 text-sm" />
                      <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Khu vực hoạt động</span>
                    </div>
                    <p className="text-xs font-semibold text-slate-600">{user.rslocation || 'Chưa cập nhật'}</p>
                  </div>

                  {/* Achievement */}
                  <div className="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <div className="flex items-center space-x-2 mb-2">
                      <i className="fa-solid fa-trophy text-yellow-500 text-sm" />
                      <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Thành tựu</span>
                    </div>
                    <p className="text-xs font-semibold text-slate-600">{user.rsachievement || 'Chưa cập nhật'}</p>
                  </div>

                  {/* Certificate */}
                  <div className="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <div className="flex items-center space-x-2 mb-2">
                      <i className="fa-solid fa-certificate text-emerald-500 text-sm" />
                      <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Chứng chỉ Môi giới</span>
                    </div>
                    <p className="text-xs font-semibold text-slate-600">{user.rscertificate || 'Chưa cập nhật'}</p>
                  </div>
                </div>

                {/* Contact Info */}
                <div className="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 p-6 rounded-2xl">
                  <h3 className="text-sm font-black text-slate-800 mb-4 flex items-center space-x-2">
                    <i className="fa-solid fa-address-card text-amber-500" />
                    <span>Thông tin liên hệ công khai</span>
                  </h3>
                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="flex items-center space-x-3">
                      <i className="fa-solid fa-phone text-emerald-500 text-sm" />
                      <div>
                        <span className="text-[10px] font-bold text-slate-400 uppercase block">Điện thoại</span>
                        <span className="text-xs font-bold text-slate-700">{user.phone || 'Chưa cập nhật'}</span>
                      </div>
                    </div>
                    <div className="flex items-center space-x-3">
                      <i className="fa-solid fa-envelope text-blue-500 text-sm" />
                      <div>
                        <span className="text-[10px] font-bold text-slate-400 uppercase block">Email</span>
                        <span className="text-xs font-bold text-slate-700">{user.email}</span>
                      </div>
                    </div>
                    <div className="flex items-center space-x-3">
                      <i className="fa-solid fa-globe text-cyan-500 text-sm" />
                      <div>
                        <span className="text-[10px] font-bold text-slate-400 uppercase block">Website</span>
                        <span className="text-xs font-bold text-slate-700">{user.website || 'Chưa cập nhật'}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* 9. AI Content Studio Tab */}
            {activeTab === 'marketing' && isOwner && (
              <AiMarketingStudio properties={properties} />
            )}

            {/* Admin Tabs */}
            {activeTab === 'admin_users' && user.role === 'admin' && (
              <AdminUsersTab initialUsers={adminUsers} />
            )}

            {activeTab === 'admin_properties' && user.role === 'admin' && (
              <AdminPropertiesTab initialProperties={adminProperties} categories={categories} />
            )}

            {activeTab === 'admin_appointments' && user.role === 'admin' && (
              <AdminAppointmentsTab initialAppointments={adminAppointments} />
            )}

            {activeTab === 'leads' && user.role === 'admin' && (
              <AdminLeadsTab initialLeads={leads} />
            )}

          </div>

        </div>
      </div>
    </div>
  )
}
