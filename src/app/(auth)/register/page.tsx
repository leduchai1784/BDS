'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'

export default function RegisterPage() {
  const router = useRouter()
  
  // State variables
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [phone, setPhone] = useState('')
  const [role, setRole] = useState<'tenant' | 'owner'>('tenant')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [agreeTerms, setAgreeTerms] = useState(false)
  
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)
  
  const [openRoleDropdown, setOpenRoleDropdown] = useState(false)
  const [loading, setLoading] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const getRoleLabel = () => {
    return role === 'owner' ? 'Chủ nhà' : 'Khách mua / thuê'
  }

  const getRoleIcon = () => {
    return role === 'owner' ? 'fa-solid fa-house-chimney-user' : 'fa-solid fa-user-tag'
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setErrorMsg('')

    // Basic password validation
    if (password !== passwordConfirmation) {
      setErrorMsg('Xác nhận mật khẩu không khớp.')
      setLoading(false)
      return
    }

    if (!agreeTerms) {
      setErrorMsg('Bạn phải đồng ý với Điều khoản sử dụng.')
      setLoading(false)
      return
    }

    try {
      const response = await fetch('/api/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          name,
          email,
          phone: phone || null,
          role,
          password
        })
      })

      const data = await response.json()
      setLoading(false)

      if (response.ok && data.success) {
        // Redirect to login with query param
        router.push('/login?registered=true')
      } else {
        setErrorMsg(data.message || 'Có lỗi xảy ra trong quá trình đăng ký.')
      }
    } catch (err: any) {
      console.error('Registration failed:', err)
      setLoading(false)
      setErrorMsg('Không thể kết nối đến máy chủ. Vui lòng thử lại sau.')
    }
  }

  return (
    <div className="bg-slate-50 pt-24 min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col md:flex-row min-h-[620px]">
        
        {/* Left Side: Image Illustration */}
        <div className="md:w-1/2 relative bg-slate-900 overflow-hidden hidden md:block">
          <img 
            src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/rxws6x6gxuewnt10nhgg.jpg" 
            alt="Register illustration" 
            className="w-full h-full object-cover opacity-80 scale-105"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
          
          <div className="absolute bottom-10 left-10 right-10 text-left text-white z-10 space-y-3">
            <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-cyan-500/20 backdrop-blur-md text-cyan-300 border border-cyan-500/20">
              BDS Rental
            </span>
            <h2 className="text-2xl font-black leading-snug">Gia nhập cộng đồng cho thuê nhà lớn nhất Việt Nam</h2>
            <p className="text-xs text-slate-300 font-medium">Bắt đầu đăng tin miễn phí hoặc lưu lại những ngôi nhà bạn quan tâm một cách dễ dàng.</p>
          </div>
        </div>

        {/* Right Side: Form Register */}
        <div className="md:w-1/2 p-8 sm:p-12 flex flex-col justify-center text-left">
          <div className="mb-6">
            <div className="flex items-center space-x-2 mb-3">
              <div className="w-8 h-8 rounded-lg bg-cyan-600 flex items-center justify-center text-white shadow-md shadow-cyan-600/20">
                <i className="fa-solid fa-house-chimney text-sm"></i>
              </div>
              <span className="font-bold text-lg tracking-tight text-slate-800">
                BDS<span className="text-cyan-600">Rental</span>
              </span>
            </div>
            <h3 className="text-xl font-bold text-slate-800 leading-tight">Đăng ký thành viên mới</h3>
            <p className="text-xs text-slate-400 mt-1 font-semibold">Tạo tài khoản miễn phí và trải nghiệm tính năng tìm kiếm thông minh.</p>
          </div>

          {errorMsg && (
            <div className="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-2xl text-rose-700 text-xs font-bold flex items-start space-x-3 border border-rose-100 shadow-sm">
              <i className="fa-solid fa-circle-exclamation text-base text-rose-500 mt-0.5 flex-shrink-0"></i>
              <div>
                <p className="font-black text-rose-800">Đăng ký thất bại</p>
                <p className="text-[11px] font-semibold text-rose-700/95 mt-0.5">{errorMsg}</p>
              </div>
            </div>
          )}

          {/* Register Form */}
          <form onSubmit={handleSubmit} className="space-y-3.5">
            {/* Input Full Name */}
            <div className="space-y-1">
              <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên</label>
              <div className="relative">
                <i className="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                  type="text" 
                  name="name" 
                  required 
                  placeholder="Nhập họ và tên..."
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>
            </div>

            {/* Input Email */}
            <div className="space-y-1">
              <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Địa chỉ Email</label>
              <div className="relative">
                <i className="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                  type="email" 
                  name="email" 
                  required 
                  placeholder="email@example.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>
            </div>

            {/* Input Phone */}
            <div className="space-y-1">
              <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại</label>
              <div className="relative">
                <i className="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                  type="tel" 
                  name="phone" 
                  required 
                  placeholder="09xx.xxx.xxx"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>
            </div>

            {/* Choose Role */}
            <div className="space-y-1">
              <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Loại tài khoản</label>
              <div className="relative">
                <i className={`absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs transition-colors duration-200 pointer-events-none z-10 ${getRoleIcon()}`}></i>

                <button 
                  type="button"
                  onClick={() => setOpenRoleDropdown(!openRoleDropdown)}
                  className={`w-full flex items-center justify-between pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition text-left cursor-pointer select-none ${openRoleDropdown ? 'border-cyan-600 bg-white shadow-sm ring-1 ring-cyan-600/10' : ''}`}
                >
                  <span className="text-slate-700">{getRoleLabel()}</span>
                  <i className={`fa-solid fa-chevron-down text-slate-400 text-[10px] transition duration-200 ${openRoleDropdown ? 'rotate-180 text-cyan-600' : ''}`}></i>
                </button>

                {openRoleDropdown && (
                  <div className="absolute left-0 right-0 mt-2 rounded-2xl overflow-hidden bg-white border border-slate-150/70 shadow-xl py-1.5 z-50 text-left">
                    <button 
                      type="button"
                      onClick={() => { setRole('tenant'); setOpenRoleDropdown(false) }}
                      className={`w-full flex items-center justify-between px-4 py-2.5 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer text-left ${role === 'tenant' ? 'text-cyan-600 bg-cyan-50/30' : 'text-slate-700'}`}
                    >
                      <span className="flex items-center space-x-2.5">
                        <i className="fa-solid fa-user-tag text-slate-400 text-xs w-4"></i>
                        <span>Khách mua / thuê</span>
                      </span>
                      {role === 'tenant' && <i className="fa-solid fa-check text-[10px] text-cyan-600"></i>}
                    </button>

                    <button 
                      type="button"
                      onClick={() => { setRole('owner'); setOpenRoleDropdown(false) }}
                      className={`w-full flex items-center justify-between px-4 py-2.5 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer text-left ${role === 'owner' ? 'text-cyan-600 bg-cyan-50/30' : 'text-slate-700'}`}
                    >
                      <span className="flex items-center space-x-2.5">
                        <i className="fa-solid fa-house-chimney-user text-slate-400 text-xs w-4"></i>
                        <span>Chủ nhà</span>
                      </span>
                      {role === 'owner' && <i className="fa-solid fa-check text-[10px] text-cyan-600"></i>}
                    </button>
                  </div>
                )}
              </div>
            </div>

            {/* Input Password & Confirm */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
              <div className="space-y-1">
                <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mật khẩu</label>
                <div className="relative">
                  <i className="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                  <input 
                    type={showPassword ? 'text' : 'password'} 
                    name="password" 
                    required 
                    placeholder="Tối thiểu 8 ký tự..."
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                  <button 
                    type="button" 
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-cyan-600 transition focus:outline-none cursor-pointer"
                  >
                    <i className={`text-xs ${showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'}`}></i>
                  </button>
                </div>
              </div>
              
              <div className="space-y-1">
                <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Xác nhận mật khẩu</label>
                <div className="relative">
                  <i className="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                  <input 
                    type={showConfirm ? 'text' : 'password'} 
                    name="password_confirmation" 
                    required 
                    placeholder="Nhập lại mật khẩu..."
                    value={passwordConfirmation}
                    onChange={(e) => setPasswordConfirmation(e.target.value)}
                    className="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                  <button 
                    type="button" 
                    onClick={() => setShowConfirm(!showConfirm)}
                    className="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-cyan-600 transition focus:outline-none cursor-pointer"
                  >
                    <i className={`text-xs ${showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'}`}></i>
                  </button>
                </div>
              </div>
            </div>

            {/* Accept Terms */}
            <div className="flex items-center pt-1">
              <label className="inline-flex items-center cursor-pointer select-none">
                <input 
                  type="checkbox" 
                  checked={agreeTerms}
                  onChange={(e) => setAgreeTerms(e.target.checked)}
                  required 
                  className="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-600 border-slate-200 cursor-pointer" 
                />
                <span className="ml-2 text-xs font-bold text-slate-500 leading-normal">Tôi đồng ý với <Link href="#" className="text-cyan-600 hover:underline">Điều khoản sử dụng</Link> và <Link href="#" className="text-cyan-600 hover:underline">Quy chế hoạt động</Link></span>
              </label>
            </div>

            <button 
              type="submit" 
              disabled={loading}
              className="w-full bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-md shadow-cyan-600/20 hover:shadow-cyan-600/35 transition cursor-pointer active:scale-98 mt-2 flex items-center justify-center gap-2"
            >
              {loading ? (
                <>
                  <i className="fa-solid fa-spinner animate-spin"></i> Đang đăng ký...
                </>
              ) : 'Đăng ký tài khoản mới'}
            </button>
          </form>

          {/* Redirect to Login */}
          <p className="text-xs text-slate-500 mt-6 font-semibold text-center">
            Bạn đã có tài khoản thành viên? <Link href="/login" className="text-cyan-600 hover:underline font-bold">Đăng nhập ngay</Link>
          </p>
        </div>

      </div>
    </div>
  )
}
