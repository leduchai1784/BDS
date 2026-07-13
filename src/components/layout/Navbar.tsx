'use client'

import { useState, useEffect } from 'react'
import { usePathname, useRouter } from 'next/navigation'
import { useSession, signOut } from 'next-auth/react'
import Link from 'next/link'

export default function Navbar() {
  const pathname = usePathname()
  const router = useRouter()
  const { data: session, status } = useSession()
  const user = session?.user

  // States
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [isScrolled, setIsScrolled] = useState(false)
  
  // Hover dropdowns
  const [rentDropdownOpen, setRentDropdownOpen] = useState(false)
  const [partnerDropdownOpen, setPartnerDropdownOpen] = useState(false)
  const [userDropdownOpen, setUserDropdownOpen] = useState(false)
  const [guestDropdownOpen, setGuestDropdownOpen] = useState(false)
  
  // Mobile accordions
  const [mobileRentOpen, setMobileRentOpen] = useState(false)
  const [mobilePartnerOpen, setMobilePartnerOpen] = useState(false)

  // LocalStorage state
  const [diaChiMoi, setDiaChiMoi] = useState(false)

  useEffect(() => {
    // Read initial scrolled state or path change
    const checkScroll = () => {
      setIsScrolled(window.pageYOffset > 20 || pathname !== '/')
    }

    checkScroll()
    window.addEventListener('scroll', checkScroll)
    return () => window.removeEventListener('scroll', checkScroll)
  }, [pathname])

  // Handle localStorage state
  useEffect(() => {
    const saved = localStorage.getItem('diaChiMoi') === 'true'
    setDiaChiMoi(saved)
  }, [])

  const handleDiaChiMoiToggle = () => {
    const nextVal = !diaChiMoi
    setDiaChiMoi(nextVal)
    localStorage.setItem('diaChiMoi', String(nextVal))
    // Dispatch custom event to notify other components (e.g. Map, Listings)
    window.dispatchEvent(new CustomEvent('dia-chi-moi-toggled', { detail: { active: nextVal } }))
  }

  const handleLogout = async (e: React.MouseEvent) => {
    e.preventDefault()
    await signOut({ callbackUrl: '/login' })
  }

  const isActive = (path: string) => {
    if (path === '/') return pathname === '/'
    return pathname.startsWith(path)
  }

  const navClass = (path: string) => {
    const active = isActive(path)
    if (isScrolled) {
      return active ? 'text-primary' : 'text-slate-600 hover:text-primary'
    } else {
      return active ? 'text-white' : 'text-slate-200 hover:text-white'
    }
  }

  return (
    <header 
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 w-full ${
        isScrolled 
          ? 'bg-white/95 backdrop-blur-md shadow-md border-b border-slate-100 py-3' 
          : 'bg-transparent py-5'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="w-full flex items-center justify-between gap-4">
          
          {/* Logo */}
          <div className="flex-shrink-0 flex items-center">
            <Link href="/" className="flex items-center space-x-2">
              <div className="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                <i className="fa-solid fa-house-chimney text-lg"></i>
              </div>
              <span 
                className={`font-bold text-2xl tracking-tight transition-colors duration-300 ${
                  isScrolled ? 'text-slate-900' : 'text-white'
                }`}
              >
                BDS<span className="text-primary">Rental</span>
              </span>
            </Link>
          </div>

          {/* Desktop Navigation Menu */}
          <nav className="hidden md:flex space-x-3 lg:space-x-5 items-center">
            {/* Nhà Đất Dropdown */}
            <div 
              className="relative"
              onMouseEnter={() => setRentDropdownOpen(true)}
              onMouseLeave={() => setRentDropdownOpen(false)}
            >
              <Link 
                href="/listings"
                className={`flex items-center space-x-1.5 font-bold text-sm lg:text-base cursor-pointer focus:outline-none transition duration-150 whitespace-nowrap ${navClass('/listings')}`}
              >
                <span>Nhà đất</span>
                <i className={`fa-solid fa-chevron-down text-[10px] transition duration-200 ${rentDropdownOpen ? 'rotate-180' : ''}`}></i>
              </Link>
              
              {rentDropdownOpen && (
                <div className="absolute left-0 top-full pt-2 w-48 z-50 animate-dropdown">
                  <div className="rounded-3xl bg-white border border-slate-100/80 shadow-2xl p-2 text-left">
                    <Link 
                      href="/listings?purpose=rent" 
                      className={`block px-4 py-2.5 text-[14px] font-semibold rounded-2xl transition duration-150 whitespace-nowrap ${
                        pathname === '/listings' && pathname.includes('purpose=rent') 
                          ? 'bg-slate-100 text-primary' 
                          : 'text-slate-800 hover:bg-slate-100 hover:text-primary'
                      }`}
                    >
                      Cho thuê
                    </Link>
                    <Link 
                      href="/listings?purpose=sale" 
                      className={`block px-4 py-2.5 text-[14px] font-semibold rounded-2xl transition duration-150 mt-0.5 whitespace-nowrap ${
                        pathname === '/listings' && pathname.includes('purpose=sale') 
                          ? 'bg-slate-100 text-primary' 
                          : 'text-slate-800 hover:bg-slate-100 hover:text-primary'
                      }`}
                    >
                      Mua bán
                    </Link>
                  </div>
                </div>
              )}
            </div>

            <Link href="/map" className={`font-bold text-sm lg:text-base transition duration-150 whitespace-nowrap ${navClass('/map')}`}>
              Bản đồ
            </Link>
            
            <Link href="/projects" className={`font-bold text-sm lg:text-base transition duration-150 whitespace-nowrap ${navClass('/projects')}`}>
              Dự án
            </Link>

            {/* Đối Tác Dropdown */}
            <div 
              className="relative"
              onMouseEnter={() => setPartnerDropdownOpen(true)}
              onMouseLeave={() => setPartnerDropdownOpen(false)}
            >
              <Link 
                href="/agents"
                className={`flex items-center space-x-1.5 font-bold text-sm lg:text-base cursor-pointer focus:outline-none transition duration-150 whitespace-nowrap ${navClass('/agents')}`}
              >
                <span>Đối tác</span>
                <i className={`fa-solid fa-chevron-down text-[10px] transition duration-200 ${partnerDropdownOpen ? 'rotate-180' : ''}`}></i>
              </Link>
              
              {partnerDropdownOpen && (
                <div className="absolute left-0 top-full pt-2 w-48 z-50 animate-dropdown">
                  <div className="rounded-3xl bg-white border border-slate-100/80 shadow-2xl p-2 text-left">
                    <Link 
                      href="/agents" 
                      className={`block px-4 py-2.5 text-[14px] font-semibold rounded-2xl transition duration-150 whitespace-nowrap ${
                        pathname === '/agents' ? 'bg-slate-100 text-primary' : 'text-slate-800 hover:bg-slate-100 hover:text-primary'
                      }`}
                    >
                      Nhà môi giới
                    </Link>
                    <Link 
                      href="/agents?type=company" 
                      className={`block px-4 py-2.5 text-[14px] font-semibold rounded-2xl transition duration-150 mt-0.5 whitespace-nowrap ${
                        pathname.includes('type=company') ? 'bg-slate-100 text-primary' : 'text-slate-800 hover:bg-slate-100 hover:text-primary'
                      }`}
                    >
                      Doanh nghiệp
                    </Link>
                  </div>
                </div>
              )}
            </div>

            <Link href="/news" className={`font-bold text-sm lg:text-base transition duration-150 whitespace-nowrap ${navClass('/news')}`}>
              Tin tức
            </Link>
          </nav>

          {/* Actions (Profile & CTA) */}
          <div className="hidden md:flex items-center space-x-2.5 lg:space-x-4">
            
            {/* Địa chỉ mới Toggle */}
            <div className="flex items-center space-x-2 mr-3.5">
              <span 
                className={`text-xs font-extrabold transition-colors duration-300 select-none ${
                  isScrolled ? 'text-slate-700' : 'text-slate-100'
                }`}
              >
                Địa chỉ mới
              </span>
              <button 
                type="button" 
                onClick={handleDiaChiMoiToggle}
                className={`relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${
                  diaChiMoi ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-700'
                }`}
              >
                <span 
                  className={`pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${
                    diaChiMoi ? 'translate-x-5' : 'translate-x-0'
                  }`}
                ></span>
              </button>
            </div>

            {/* Đăng tin Button */}
            <div className="relative flex-shrink-0">
              <Link 
                href="/property/create"
                className="inline-flex items-center justify-center px-2.5 lg:px-3.5 py-1.5 lg:py-2 border border-transparent text-xs lg:text-sm font-extrabold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 hover:shadow-primary/30 transform hover:-translate-y-0.5 transition duration-200 whitespace-nowrap flex-shrink-0 cursor-pointer"
              >
                <i className="fa-solid fa-circle-plus mr-1 lg:mr-1.5"></i> Đăng tin
              </Link>
            </div>

            {/* User Account State */}
            {status === 'authenticated' && user ? (
              <div className="relative flex-shrink-0">
                <button 
                  onClick={() => setUserDropdownOpen(!userDropdownOpen)}
                  onBlur={() => setTimeout(() => setUserDropdownOpen(false), 200)}
                  type="button"
                  className={`flex items-center space-x-1.5 lg:space-x-2.5 focus:outline-none cursor-pointer py-1.5 px-2.5 rounded-xl transition whitespace-nowrap flex-shrink-0 ${
                    isScrolled ? 'hover:bg-slate-50' : 'hover:bg-white/10'
                  }`}
                >
                  <img 
                    src={user.image || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || '')}&background=0077bb&color=fff`} 
                    alt={user.name || ''} 
                    className="w-7 h-7 rounded-full object-cover border border-primary/20 shadow-sm"
                  />
                  <span 
                    className={`text-sm font-black transition-colors duration-250 whitespace-nowrap ${
                      isScrolled ? 'text-slate-700' : 'text-slate-100'
                    }`}
                  >
                    {user.name}
                  </span>
                  <i 
                    className={`fa-solid fa-chevron-down text-[10px] transition duration-200 ${
                      isScrolled ? 'text-slate-500' : 'text-slate-300'
                    } ${userDropdownOpen ? 'rotate-180' : ''}`}
                  ></i>
                </button>

                {/* Dropdown Panel */}
                {userDropdownOpen && (
                  <div className="absolute right-0 mt-2.5 w-48 rounded-2xl overflow-hidden bg-white border border-slate-150/50 shadow-xl py-2 z-50 text-left animate-dropdown">
                    <Link href="/profile" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                      <i className="fa-solid fa-user-gear mr-2 text-sm text-slate-400"></i> {user.role === 'admin' ? 'Trang quản lý' : 'Trang cá nhân'}
                    </Link>
                    <Link href="/profile?tab=favorites" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                      <i className="fa-solid fa-heart mr-2 text-sm text-slate-400"></i> Tin yêu thích
                    </Link>
                    {user.role === 'owner' ? (
                      <>
                        <Link href="/profile?tab=properties" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                          <i className="fa-solid fa-list-check mr-2 text-sm text-slate-400"></i> Quản lý tin đăng
                        </Link>
                        <Link href="/profile?tab=appointments" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                          <i className="fa-solid fa-calendar-days mr-2 text-sm text-slate-400"></i> Lịch hẹn
                        </Link>
                      </>
                    ) : user.role === 'tenant' ? (
                      <Link href="/profile?tab=appointments" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                        <i className="fa-solid fa-calendar-days mr-2 text-sm text-slate-400"></i> Lịch hẹn
                      </Link>
                    ) : null}
                    <div className="border-t border-slate-100 my-1"></div>
                    <button 
                      onClick={handleLogout}
                      className="w-full text-left block px-4 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 transition cursor-pointer"
                    >
                      <i className="fa-solid fa-right-from-bracket mr-2 text-sm text-red-400"></i> Đăng xuất
                    </button>
                  </div>
                )}
              </div>
            ) : (
              <div className="relative flex-shrink-0">
                <button 
                  onClick={() => setGuestDropdownOpen(!guestDropdownOpen)}
                  onBlur={() => setTimeout(() => setGuestDropdownOpen(false), 200)}
                  type="button"
                  className={`flex items-center space-x-2 focus:outline-none cursor-pointer py-1.5 px-3 rounded-xl transition whitespace-nowrap flex-shrink-0 ${
                    isScrolled ? 'hover:bg-slate-50' : 'hover:bg-white/10'
                  }`}
                >
                  <i 
                    className={`fa-regular fa-circle-user text-lg transition-colors ${
                      isScrolled ? 'text-slate-600' : 'text-slate-200'
                    }`}
                  ></i>
                  <span 
                    className={`text-sm font-bold transition-colors duration-250 whitespace-nowrap ${
                      isScrolled ? 'text-slate-700' : 'text-slate-100'
                    }`}
                  >
                    Tài khoản
                  </span>
                  <i 
                    className={`fa-solid fa-chevron-down text-[10px] transition duration-200 ${
                      isScrolled ? 'text-slate-500' : 'text-slate-300'
                    } ${guestDropdownOpen ? 'rotate-180' : ''}`}
                  ></i>
                </button>

                {/* Guest Dropdown Panel */}
                {guestDropdownOpen && (
                  <div className="absolute right-0 mt-2.5 w-44 rounded-2xl overflow-hidden bg-white border border-slate-150/50 shadow-xl py-2 z-50 text-left animate-dropdown">
                    <Link href="/login" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                      <i className="fa-solid fa-right-to-bracket mr-2 text-sm text-slate-400"></i> Đăng nhập
                    </Link>
                    <Link href="/register" className="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition border-t border-slate-50">
                      <i className="fa-solid fa-user-plus mr-2 text-sm text-slate-400"></i> Đăng ký
                    </Link>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Hamburger Button for Mobile */}
          <div className="flex items-center md:hidden">
            <button 
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)} 
              type="button" 
              className={`inline-flex items-center justify-center p-2 rounded-xl focus:outline-none transition duration-150 ${
                isScrolled ? 'text-slate-700 hover:bg-slate-100' : 'text-white hover:bg-white/10'
              }`}
            >
              <span className="sr-only">Mở menu</span>
              {mobileMenuOpen ? (
                <svg className="h-6 w-6 block" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              ) : (
                <svg className="h-6 w-6 block" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              )}
            </button>
          </div>

        </div>
      </div>

      {/* Mobile Drawer Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden bg-white shadow-xl border-b border-slate-100 absolute top-full left-0 right-0 z-40 overflow-hidden">
          <div className="px-4 pt-2 pb-6 space-y-2">
            
            <div className="space-y-1">
              <div className={`flex items-center justify-between w-full rounded-xl transition group ${isActive('/listings') ? 'bg-primary-light' : 'hover:bg-slate-50'}`}>
                <Link 
                  href="/listings" 
                  onClick={() => setMobileMenuOpen(false)}
                  className={`flex-1 text-left px-3 py-3 text-base font-semibold transition flex items-center ${isActive('/listings') ? 'text-primary' : 'text-slate-700 hover:text-primary'}`}
                >
                  <i className="fa-solid fa-house-laptop text-slate-400 mr-3 text-base w-5 text-center group-hover:text-primary transition-colors"></i>
                  <span>Nhà đất</span>
                </Link>
                <button 
                  onClick={() => setMobileRentOpen(!mobileRentOpen)}
                  type="button"
                  className="px-4 py-3 text-slate-500 hover:text-primary transition focus:outline-none cursor-pointer"
                >
                  <i className={`fa-solid fa-chevron-down text-xs transition duration-200 ${mobileRentOpen ? 'rotate-180' : ''}`}></i>
                </button>
              </div>
              
              {mobileRentOpen && (
                <div className="pl-4 space-y-1">
                  <Link href="/listings?purpose=rent" onClick={() => setMobileMenuOpen(false)} className="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-650 hover:bg-slate-50 hover:text-primary transition">Cho thuê</Link>
                  <Link href="/listings?purpose=sale" onClick={() => setMobileMenuOpen(false)} className="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-650 hover:bg-slate-50 hover:text-primary transition">Mua bán</Link>
                </div>
              )}
            </div>
            
            <Link 
              href="/map" 
              onClick={() => setMobileMenuOpen(false)} 
              className={`block px-3 py-3 rounded-xl text-base font-semibold transition flex items-center ${isActive('/map') ? 'text-primary bg-primary-light font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-primary'}`}
            >
              <i className="fa-solid fa-map-location-dot text-slate-400 mr-3 text-base w-5 text-center transition-colors"></i>
              <span>Bản đồ</span>
            </Link>
            
            <Link 
              href="/projects" 
              onClick={() => setMobileMenuOpen(false)} 
              className={`block px-3 py-3 rounded-xl text-base font-semibold transition flex items-center ${isActive('/projects') ? 'text-primary bg-primary-light font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-primary'}`}
            >
              <i className="fa-solid fa-building-user text-slate-400 mr-3 text-base w-5 text-center transition-colors"></i>
              <span>Dự án</span>
            </Link>
            
            {/* Đối tác Mobile */}
            <div className="space-y-1">
              <div className={`flex items-center justify-between w-full rounded-xl transition group ${isActive('/agents') ? 'bg-primary-light' : 'hover:bg-slate-50'}`}>
                <Link 
                  href="/agents" 
                  onClick={() => setMobileMenuOpen(false)}
                  className={`flex-1 text-left px-3 py-3 text-base font-semibold transition flex items-center ${isActive('/agents') ? 'text-primary' : 'text-slate-700 hover:text-primary'}`}
                >
                  <i className="fa-solid fa-handshake text-slate-400 mr-3 text-base w-5 text-center group-hover:text-primary transition-colors"></i>
                  <span>Đối tác</span>
                </Link>
                <button 
                  onClick={() => setMobilePartnerOpen(!mobilePartnerOpen)}
                  type="button"
                  className="px-4 py-3 text-slate-500 hover:text-primary transition focus:outline-none cursor-pointer"
                >
                  <i className={`fa-solid fa-chevron-down text-xs transition duration-200 ${mobilePartnerOpen ? 'rotate-180' : ''}`}></i>
                </button>
              </div>
              {mobilePartnerOpen && (
                <div className="pl-4 space-y-1">
                  <Link href="/agents" onClick={() => setMobileMenuOpen(false)} className="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-650 hover:bg-slate-50 hover:text-primary transition">Nhà môi giới</Link>
                  <Link href="/agents?type=company" onClick={() => setMobileMenuOpen(false)} className="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-650 hover:bg-slate-50 hover:text-primary transition">Doanh nghiệp</Link>
                </div>
              )}
            </div>

            <Link 
              href="/news" 
              onClick={() => setMobileMenuOpen(false)} 
              className={`block px-3 py-3 rounded-xl text-base font-semibold transition flex items-center ${isActive('/news') ? 'text-primary bg-primary-light font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-primary'}`}
            >
              <i className="fa-solid fa-newspaper text-slate-400 mr-3 text-base w-5 text-center transition-colors"></i>
              <span>Tin tức</span>
            </Link>
            
            <div className="pt-4 border-t border-slate-100 flex flex-col space-y-2">
              {status === 'authenticated' && user ? (
                <>
                  <div className="flex items-center space-x-3 px-3 py-2.5 mb-2 bg-slate-50 rounded-xl">
                    <img 
                      src={user.image || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || '')}&background=0077bb&color=fff`} 
                      alt={user.name || ''} 
                      className="w-8 h-8 rounded-full object-cover border border-primary/20"
                    />
                    <div className="text-left">
                      <span className="block text-sm font-extrabold text-slate-800 leading-none">{user.name}</span>
                      <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                        {user.role === 'admin' ? 'Quản trị viên' : (user.role === 'owner' ? 'Đối tác Chủ nhà' : 'Khách thuê nhà')}
                      </span>
                    </div>
                  </div>

                  <Link href="/profile" onClick={() => setMobileMenuOpen(false)} className="flex items-center space-x-3 px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <i className="fa-solid fa-user-gear text-slate-400 text-lg w-6 text-center"></i>
                    <span>{user.role === 'admin' ? 'Trang quản lý' : 'Trang cá nhân'}</span>
                  </Link>
                  <button 
                    onClick={handleLogout}
                    className="w-full text-left flex items-center space-x-3 px-3 py-3 rounded-xl text-base font-semibold text-red-500 hover:bg-red-50 transition cursor-pointer"
                  >
                    <i className="fa-solid fa-right-from-bracket text-red-400 text-lg w-6 text-center"></i>
                    <span>Đăng xuất</span>
                  </button>
                </>
              ) : (
                <>
                  <Link href="/login" onClick={() => setMobileMenuOpen(false)} className="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Đăng nhập</Link>
                  <Link href="/register" onClick={() => setMobileMenuOpen(false)} className="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Đăng ký</Link>
                </>
              )}

              {/* Mobile Toggle & CTA */}
              <div className="flex flex-col items-center justify-between gap-3 pt-2 w-full">
                {/* Toggle Địa chỉ mới (Mobile) */}
                <div className="flex items-center justify-between w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                  <span className="text-sm font-extrabold text-slate-700 select-none">Địa chỉ mới</span>
                  <button 
                    type="button" 
                    onClick={handleDiaChiMoiToggle}
                    className={`relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${
                      diaChiMoi ? 'bg-primary' : 'bg-slate-300'
                    }`}
                  >
                    <span 
                      className={`pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${
                        diaChiMoi ? 'translate-x-5' : 'translate-x-0'
                      }`}
                    ></span>
                  </button>
                </div>

                {/* Đăng tin (Mobile CTA) */}
                <div className="w-full">
                  <Link 
                    href="/property/create"
                    onClick={() => setMobileMenuOpen(false)}
                    className="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 transition focus:outline-none cursor-pointer"
                  >
                    <i className="fa-solid fa-circle-plus mr-1.5"></i> Đăng tin
                  </Link>
                </div>
              </div>

            </div>
          </div>
        </div>
      )}
    </header>
  )
}
