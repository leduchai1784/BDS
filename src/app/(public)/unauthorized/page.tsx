'use client'

import { useSearchParams } from 'next/navigation'
import Link from 'next/link'
import { signOut } from 'next-auth/react'
import { Suspense } from 'react'

function UnauthorizedContent() {
  const searchParams = useSearchParams()
  const requiredRole = searchParams.get('required')

  let message = 'Bạn không có quyền truy cập vào trang này.'
  if (requiredRole === 'admin') {
    message = 'Trang này chỉ dành riêng cho Quản trị viên hệ thống.'
  } else if (requiredRole === 'owner_agent') {
    message = 'Yêu cầu quyền Đối tác Chủ nhà hoặc Môi giới NKS để thực hiện thao tác này.'
  }

  const handleLogout = async () => {
    await signOut({ callbackUrl: '/login' })
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-gray-950 px-4 py-16 text-slate-800 dark:text-slate-200 font-sans transition-colors duration-200">
      <div className="relative max-w-md w-full bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl shadow-xl p-8 text-center overflow-hidden">
        {/* Glow effect backgrounds */}
        <div className="absolute -left-10 -top-10 w-24 h-24 rounded-full bg-red-500/10 blur-xl pointer-events-none" />
        <div className="absolute -right-10 -bottom-10 w-24 h-24 rounded-full bg-amber-500/10 blur-xl pointer-events-none" />

        {/* Animated warning shield */}
        <div className="relative w-20 h-20 mx-auto mb-6 bg-red-50 dark:bg-red-950/30 text-red-500 dark:text-red-400 rounded-full flex items-center justify-center border border-red-100 dark:border-red-900/30 shadow-md">
          <span className="absolute inline-flex h-full w-full rounded-full bg-red-400/20 opacity-75 animate-ping" />
          <i className="fa-solid fa-shield-halved text-4xl" />
        </div>

        {/* Header */}
        <h1 className="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight mb-3">
          403 - Từ chối truy cập
        </h1>
        <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-8 leading-relaxed max-w-sm mx-auto">
          {message}
        </p>

        {/* Action Buttons */}
        <div className="flex flex-col gap-3">
          <Link
            href="/"
            className="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-indigo-650 hover:from-primary-hover hover:to-indigo-700 text-white text-xs font-bold transition-all hover:shadow-lg shadow-primary/20 transform hover:-translate-y-0.5 cursor-pointer"
          >
            <i className="fa-solid fa-house mr-2" />
            Quay lại trang chủ
          </Link>
          
          <button
            onClick={handleLogout}
            className="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl border border-slate-200 dark:border-gray-800 hover:bg-slate-50 dark:hover:bg-gray-800 text-slate-655 dark:text-slate-300 text-xs font-bold transition cursor-pointer"
          >
            <i className="fa-solid fa-right-from-bracket mr-2" />
            Đăng xuất & Đổi tài khoản
          </button>
        </div>
      </div>
    </div>
  )
}

export default function UnauthorizedPage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-gray-950 text-xs font-bold text-slate-400">
        Đang tải...
      </div>
    }>
      <UnauthorizedContent />
    </Suspense>
  )
}
