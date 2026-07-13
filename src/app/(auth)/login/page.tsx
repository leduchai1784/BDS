'use client'

import { useState, useEffect, useRef, Suspense } from 'react'
import { signIn } from 'next-auth/react'
import { useRouter, useSearchParams } from 'next/navigation'
import Link from 'next/link'

interface RememberedAccount {
  email: string
  name: string
  avatar: string
}

function LoginForm() {
  const router = useRouter()
  const searchParams = useSearchParams()
  
  // State variables
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [remember, setRemember] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [loading, setLoading] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')
  const [successMsg, setSuccessMsg] = useState('')
  
  // Remembered accounts states
  const [accounts, setAccounts] = useState<RememberedAccount[]>([])
  const [selectedAccount, setSelectedAccount] = useState<RememberedAccount | null>(null)
  const [openDropdown, setOpenDropdown] = useState(false)
  const [openSelector, setOpenSelector] = useState(false)

  const emailInputRef = useRef<HTMLInputElement>(null)
  const passwordInputRef = useRef<HTMLInputElement>(null)

  // Initialize and check search params/local storage
  useEffect(() => {
    // Show success message if registered successfully
    const registered = searchParams.get('registered')
    if (registered) {
      setSuccessMsg('Đăng ký tài khoản thành công! Vui lòng đăng nhập.')
    }

    // Load remembered accounts from localStorage
    const saved = localStorage.getItem('remembered_accounts')
    if (saved) {
      try {
        const parsed = JSON.parse(saved) as RememberedAccount[]
        setAccounts(parsed)
        if (parsed.length > 0) {
          setSelectedAccount(parsed[0])
          setEmail(parsed[0].email)
        }
      } catch (e) {
        console.error('Error parsing remembered accounts', e)
      }
    }
  }, [searchParams])

  const selectAccount = (acc: RememberedAccount) => {
    setSelectedAccount(acc)
    setEmail(acc.email)
    setOpenDropdown(false)
    setOpenSelector(false)
    setTimeout(() => {
      passwordInputRef.current?.focus()
    }, 50)
  }

  const useDifferentAccount = () => {
    setSelectedAccount(null)
    setEmail('')
    setOpenDropdown(false)
    setOpenSelector(false)
    setTimeout(() => {
      emailInputRef.current?.focus()
    }, 50)
  }

  const removeAccount = (emailToRemove: string) => {
    const updated = accounts.filter(a => a.email !== emailToRemove)
    setAccounts(updated)
    localStorage.setItem('remembered_accounts', JSON.stringify(updated))
    
    if (selectedAccount?.email === emailToRemove) {
      if (updated.length > 0) {
        selectAccount(updated[0])
      } else {
        useDifferentAccount()
      }
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setErrorMsg('')
    setSuccessMsg('')

    const result = await signIn('credentials', {
      email,
      password,
      redirect: false,
    })

    if (result?.error) {
      setLoading(false)
      if (result.error === 'ACCOUNT_LOCKED') {
        setErrorMsg('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.')
      } else if (result.error === 'INVALID_CREDENTIALS') {
        setErrorMsg('Thông tin đăng nhập không chính xác.')
      } else {
        setErrorMsg('Đăng nhập thất bại. Vui lòng thử lại.')
      }
    } else {
      // Login successful
      // 1. Save account to remembered accounts if remember is checked (or if selected account is already active)
      if (remember || selectedAccount) {
        // Fetch user profile from API to get name and avatar (or we can use temporary credentials)
        // Since we don't have user info yet on client, let's call our session/profile API
        try {
          const res = await fetch('/api/profile')
          if (res.ok) {
            const profile = await res.json()
            if (profile.success && profile.data) {
              const { name, email: userEmail, avatar } = profile.data
              const newAcc: RememberedAccount = {
                email: userEmail,
                name: name,
                avatar: avatar || 'https://res.cloudinary.com/dj8t18pke/image/upload/v1783582490/avatar_placeholder.png'
              }
              const filtered = accounts.filter(a => a.email !== userEmail)
              const updated = [newAcc, ...filtered].slice(0, 5) // limit to 5 accounts
              localStorage.setItem('remembered_accounts', JSON.stringify(updated))
            }
          }
        } catch (err) {
          console.error('Failed to save profile to remembered accounts', err)
        }
      }

      router.push('/profile')
      router.refresh()
    }
  }

  return (
    <div className="bg-slate-50 pt-24 min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col md:flex-row min-h-[550px]">
        
        {/* Left Side: Image Illustration */}
        <div className="md:w-1/2 relative bg-slate-900 overflow-hidden hidden md:block">
          <img 
            src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" 
            alt="Login illustration" 
            className="w-full h-full object-cover opacity-80 scale-105"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
          
          <div className="absolute bottom-10 left-10 right-10 text-left text-white z-10 space-y-3">
            <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-cyan-500/20 backdrop-blur-md text-cyan-300 border border-cyan-500/20">
              BDS Rental
            </span>
            <h2 className="text-2xl font-black leading-snug">Tìm kiếm không gian sống mơ ước nhanh chóng và tin cậy</h2>
            <p className="text-xs text-slate-300 font-medium">Hàng ngàn tin đăng chính chủ xác thực mỗi ngày đang chờ bạn khám phá.</p>
          </div>
        </div>

        {/* Right Side: Form Login */}
        <div className="md:w-1/2 p-8 sm:p-12 flex flex-col justify-center text-left">
          {/* Form Header */}
          <div className="mb-8">
            <div className="flex items-center space-x-2 mb-4">
              <div className="w-8 h-8 rounded-lg bg-cyan-600 flex items-center justify-center text-white shadow-md shadow-cyan-600/20">
                <i className="fa-solid fa-house-chimney text-sm"></i>
              </div>
              <span className="font-bold text-lg tracking-tight text-slate-800">
                BDS<span className="text-cyan-600">Rental</span>
              </span>
            </div>
            <h3 className="text-xl font-bold text-slate-800 leading-tight">Chào mừng quay trở lại!</h3>
            <p className="text-xs text-slate-400 mt-1 font-semibold">Đăng nhập tài khoản của bạn để tiếp tục sử dụng dịch vụ.</p>
          </div>

          {successMsg && (
            <div className="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-2xl text-emerald-700 text-xs font-bold flex items-start space-x-3 border border-emerald-100 shadow-sm">
              <i className="fa-solid fa-circle-check text-base text-emerald-500 mt-0.5 flex-shrink-0"></i>
              <div>
                <p className="font-black text-emerald-800">Thành công</p>
                <p className="text-[11px] font-semibold text-emerald-700/95 mt-0.5">{successMsg}</p>
              </div>
            </div>
          )}

          {errorMsg && (
            <div className="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-2xl text-rose-700 text-xs font-bold flex items-start space-x-3 border border-rose-100 shadow-sm">
              <i className="fa-solid fa-circle-exclamation text-base text-rose-500 mt-0.5 flex-shrink-0"></i>
              <div>
                <p className="font-black text-rose-800">Đăng nhập thất bại</p>
                <p className="text-[11px] font-semibold text-rose-700/95 mt-0.5">{errorMsg}</p>
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            
            {/* Selected Account Banner with Dropdown */}
            {selectedAccount && (
              <div className="relative mb-5">
                <div className="flex items-center space-x-3.5 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-left">
                  <img 
                    src={selectedAccount.avatar} 
                    alt={selectedAccount.name} 
                    className="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0"
                  />
                  <div className="flex-grow min-w-0">
                    <h4 className="text-xs font-extrabold text-slate-800 truncate">{selectedAccount.name}</h4>
                    <p className="text-[10px] font-semibold text-slate-400 truncate">{selectedAccount.email}</p>
                  </div>
                  <button 
                    type="button" 
                    onClick={() => setOpenDropdown(!openDropdown)} 
                    className="text-xs font-bold text-cyan-600 hover:underline cursor-pointer flex items-center gap-1 select-none"
                  >
                    Thay đổi <i className={`fa-solid fa-chevron-down text-[8px] transition-transform duration-200 ${openDropdown ? 'rotate-180' : ''}`}></i>
                  </button>
                </div>

                {/* Dropdown accounts list */}
                {openDropdown && (
                  <div className="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-2 space-y-1 max-h-[220px] overflow-y-auto">
                    {accounts.filter(a => a.email !== selectedAccount.email).map(acc => (
                      <div 
                        key={acc.email}
                        className="flex items-center justify-between p-2.5 hover:bg-slate-50 border border-transparent hover:border-slate-100 rounded-xl transition cursor-pointer" 
                        onClick={() => selectAccount(acc)}
                      >
                        <div className="flex items-center space-x-3 min-w-0">
                          <img src={acc.avatar} alt={acc.name} className="w-8 h-8 rounded-full object-cover border border-slate-100 flex-shrink-0" />
                          <div className="min-w-0 text-left">
                            <h5 className="text-[11px] font-extrabold text-slate-800 truncate">{acc.name}</h5>
                            <p className="text-[9px] font-semibold text-slate-400 truncate">{acc.email}</p>
                          </div>
                        </div>
                        <button 
                          type="button" 
                          onClick={(e) => { e.stopPropagation(); removeAccount(acc.email) }} 
                          className="flex items-center justify-center w-6 h-6 text-slate-400 hover:text-rose-650 hover:bg-rose-50 rounded-lg transition cursor-pointer" 
                          title="Xóa ghi nhớ"
                        >
                          <i className="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                      </div>
                    ))}
                    {accounts.filter(a => a.email !== selectedAccount.email).length > 0 && (
                      <div className="border-t border-slate-100 my-1"></div>
                    )}
                    <div 
                      className="flex items-center p-2.5 hover:bg-slate-50 border border-transparent hover:border-slate-100 rounded-xl transition cursor-pointer text-slate-600 hover:text-cyan-600 text-left" 
                      onClick={useDifferentAccount}
                    >
                      <i className="fa-solid fa-user-plus text-[10px] mr-2 text-cyan-600"></i>
                      <span className="text-[11px] font-bold">Sử dụng tài khoản khác</span>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Input Email (Hidden if selected account is active) */}
            {!selectedAccount && (
              <div className="space-y-1">
                <div className="flex justify-between items-center mb-1">
                  <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 px-1">Địa chỉ Email</label>
                  {accounts.length > 0 && (
                    <div className="relative">
                      <button 
                        type="button" 
                        onClick={() => setOpenSelector(!openSelector)} 
                        className="text-[10px] font-bold text-cyan-600 hover:underline cursor-pointer"
                      >
                        Chọn tài khoản đã lưu <i className={`fa-solid fa-chevron-down text-[8px] transition-transform duration-200 ${openSelector ? 'rotate-180' : ''}`}></i>
                      </button>
                      
                      {/* Popover account list */}
                      {openSelector && (
                        <div className="absolute right-0 top-full mt-1 w-64 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-2 space-y-1">
                          {accounts.map(acc => (
                            <div 
                              key={acc.email}
                              className="flex items-center justify-between p-2 hover:bg-slate-50 border border-transparent hover:border-slate-100 rounded-xl transition cursor-pointer" 
                              onClick={() => selectAccount(acc)}
                            >
                              <div className="flex items-center space-x-3 min-w-0">
                                <img src={acc.avatar} alt={acc.name} className="w-8 h-8 rounded-full object-cover border border-slate-100 flex-shrink-0" />
                                <div className="min-w-0 text-left">
                                  <h5 className="text-[11px] font-extrabold text-slate-800 truncate">{acc.name}</h5>
                                  <p className="text-[9px] font-semibold text-slate-400 truncate">{acc.email}</p>
                                </div>
                              </div>
                              <button 
                                type="button" 
                                onClick={(e) => { e.stopPropagation(); removeAccount(acc.email) }} 
                                className="flex items-center justify-center w-6 h-6 text-slate-400 hover:text-rose-650 hover:bg-rose-50 rounded-lg transition cursor-pointer" 
                                title="Xóa ghi nhớ"
                              >
                                <i className="fa-solid fa-xmark text-[10px]"></i>
                              </button>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  )}
                </div>
                <div className="relative">
                  <i className="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                  <input 
                    type="email" 
                    name="email" 
                    ref={emailInputRef}
                    required={!selectedAccount} 
                    placeholder="email@example.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>
            )}
     
            {/* Input Password */}
            <div className="space-y-1">
              <div className="flex justify-between items-center mb-1">
                <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 px-1">Mật khẩu</label>
                <Link href="#" className="text-[10px] font-bold text-cyan-600 hover:underline">Quên mật khẩu?</Link>
              </div>
              <div className="relative">
                <i className="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                  type={showPassword ? 'text' : 'password'} 
                  name="password" 
                  ref={passwordInputRef}
                  required 
                  placeholder="Nhập mật khẩu..."
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-cyan-600 focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
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
     
            {/* Remember Me & Submit */}
            {!selectedAccount && (
              <div className="flex items-center justify-between pt-1">
                <label className="inline-flex items-center cursor-pointer select-none">
                  <input 
                    type="checkbox" 
                    name="remember" 
                    checked={remember}
                    onChange={(e) => setRemember(e.target.checked)}
                    className="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-600 border-slate-200 cursor-pointer" 
                  />
                  <span className="ml-2 text-xs font-bold text-slate-500">Ghi nhớ đăng nhập</span>
                </label>
              </div>
            )}
     
            <button 
              type="submit" 
              disabled={loading}
              className="w-full bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold py-3.5 px-4 rounded-xl shadow-md shadow-cyan-600/20 hover:shadow-cyan-600/35 transition cursor-pointer active:scale-98 mt-2 flex items-center justify-center gap-2"
            >
              {loading ? (
                <>
                  <i className="fa-solid fa-spinner animate-spin"></i> Đang đăng nhập...
                </>
              ) : 'Đăng nhập tài khoản'}
            </button>
          </form>

          {/* Social Login Options */}
          <div className="mt-8">
            <div className="relative flex py-2 items-center">
              <div className="flex-grow border-t border-slate-100"></div>
              <span className="flex-shrink mx-4 text-[10px] text-slate-400 font-bold uppercase tracking-wider">Hoặc đăng nhập bằng</span>
              <div className="flex-grow border-t border-slate-100"></div>
            </div>

            <div className="grid grid-cols-2 gap-3 mt-4">
              <button className="inline-flex justify-center items-center px-4 py-2.5 border border-slate-200 hover:border-slate-350 bg-white rounded-xl text-xs font-bold text-slate-600 transition cursor-pointer">
                <i className="fa-brands fa-google text-rose-500 mr-2 text-sm"></i> Google
              </button>
              <button className="inline-flex justify-center items-center px-4 py-2.5 border border-slate-200 hover:border-slate-350 bg-white rounded-xl text-xs font-bold text-slate-600 transition cursor-pointer">
                <i className="fa-brands fa-facebook-f text-blue-600 mr-2 text-sm"></i> Facebook
              </button>
            </div>
          </div>

          {/* Redirect to Register */}
          <p className="text-xs text-slate-500 mt-8 font-semibold text-center">
            Bạn chưa có tài khoản? <Link href="/register" className="text-cyan-600 hover:underline font-bold">Đăng ký thành viên ngay</Link>
          </p>
        </div>

      </div>
    </div>
  )
}

export default function LoginPage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen bg-slate-900 flex items-center justify-center text-slate-400 font-semibold text-xs">
        Đang tải...
      </div>
    }>
      <LoginForm />
    </Suspense>
  )
}
