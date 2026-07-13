import { redirect } from 'next/navigation'
import Link from 'next/link'
import { auth } from '@/lib/auth'

export const dynamic = 'force-dynamic'

interface AdminLayoutProps {
  children: React.ReactNode
}

export default async function AdminLayout({ children }: AdminLayoutProps) {
  const session = await auth()

  if (!session?.user?.id) {
    redirect('/login?callbackUrl=/admin')
  }

  if (session.user.role !== 'admin') {
    redirect('/')
  }

  const user = session.user as any

  return (
    <div className="flex h-screen bg-slate-50 overflow-hidden font-sans text-slate-800">
      
      {/* Admin Left Sidebar */}
      <aside className="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shadow-2xl z-30 flex-shrink-0">
        <div>
          {/* Logo Brand */}
          <div className="p-6 border-b border-slate-800 flex items-center space-x-3">
            <div className="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/35">
              <i className="fa-solid fa-hotel text-white text-sm" />
            </div>
            <div>
              <span className="font-black text-white text-base tracking-wider block">BDS RENTAL</span>
              <span className="text-[10px] text-slate-500 font-bold uppercase tracking-widest block">Admin Portal</span>
            </div>
          </div>

          {/* Navigation Links */}
          <nav className="p-4 space-y-1.5 text-left">
            <Link 
              href="/admin" 
              className="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-slate-800 hover:text-white"
            >
              <i className="fa-solid fa-chart-pie text-sm w-5" />
              <span>Bảng điều khiển</span>
            </Link>

            <Link 
              href="/admin/users" 
              className="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-slate-800 hover:text-white"
            >
              <i className="fa-solid fa-users text-sm w-5" />
              <span>Quản lý thành viên</span>
            </Link>

            <Link 
              href="/admin/properties" 
              className="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-slate-800 hover:text-white"
            >
              <i className="fa-solid fa-house-chimney text-sm w-5" />
              <span>Quản lý tin đăng</span>
            </Link>

            <Link 
              href="/admin/appointments" 
              className="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-slate-800 hover:text-white"
            >
              <i className="fa-regular fa-calendar-check text-sm w-5" />
              <span>Quản lý lịch hẹn</span>
            </Link>

            <Link 
              href="/admin/categories" 
              className="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-slate-800 hover:text-white"
            >
              <i className="fa-solid fa-tags text-sm w-5" />
              <span>Quản lý danh mục</span>
            </Link>

            <Link 
              href="/admin/reports" 
              className="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-slate-800 hover:text-white"
            >
              <i className="fa-solid fa-chart-line text-sm w-5" />
              <span>Báo cáo thống kê</span>
            </Link>
          </nav>
        </div>

        {/* User Footer Profile */}
        <div className="p-4 border-t border-slate-800">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-3">
              <div className="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 overflow-hidden flex items-center justify-center">
                {user.avatar ? (
                  <img src={user.avatar} className="w-full h-full object-cover" />
                ) : (
                  <i className="fa-regular fa-user text-slate-400 text-sm" />
                )}
              </div>
              <div className="text-left">
                <span className="text-xs font-bold text-white block truncate w-32">{user.name}</span>
                <span className="text-[9px] font-black text-slate-500 uppercase tracking-wider block">Administrator</span>
              </div>
            </div>
            <Link 
              href="/"
              className="w-8 h-8 rounded-lg hover:bg-red-500/10 hover:text-red-400 flex items-center justify-center text-slate-500 transition cursor-pointer"
              title="Quay lại trang chính"
            >
              <i className="fa-solid fa-arrow-right-from-bracket text-xs" />
            </Link>
          </div>
        </div>
      </aside>

      {/* Main Container */}
      <div className="flex-grow flex flex-col overflow-hidden">
        
        {/* Header bar */}
        <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0 z-20">
          <div className="flex items-center space-x-3">
            <span className="text-xs font-bold text-slate-450 uppercase tracking-widest">Hệ thống quản trị</span>
            <span className="text-slate-350">|</span>
            <span className="text-xs font-black text-primary">BDS Rental Portal</span>
          </div>

          <div className="flex items-center space-x-4">
            <div className="relative">
              <button className="w-9 h-9 bg-slate-50 hover:bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 transition cursor-pointer">
                <i className="fa-regular fa-bell text-sm" />
              </button>
              <span className="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border border-white rounded-full" />
            </div>
            
            <Link 
              href="/" 
              className="text-xs font-bold text-slate-600 hover:text-primary transition"
            >
              Xem Website
            </Link>
          </div>
        </header>

        {/* Content canvas */}
        <main className="flex-grow overflow-y-auto bg-slate-50 p-8">
          {children}
        </main>
      </div>

    </div>
  )
}
