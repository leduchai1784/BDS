import { newsData } from '@/lib/newsData'
import Link from 'next/link'

export const dynamic = 'force-dynamic'

export default async function NewsPage() {
  return (
    <div className="bg-slate-50 min-h-screen pt-28 pb-16 text-slate-800 text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-850">Tin tức & Cẩm nang</span>
        </nav>

        {/* Page Title */}
        <div className="mb-10">
          <span className="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Blog & Reports</span>
          <h1 className="text-3xl font-extrabold text-slate-900 leading-tight">Tin tức & Cẩm nang thuê nhà</h1>
        </div>

        {/* 1. Báo cáo thị trường Section */}
        <section className="mb-12">
          <div className="border-b border-slate-200 pb-3 mb-6 flex items-center justify-between">
            <h2 className="text-lg font-black text-slate-850">Báo cáo thị trường</h2>
            <span className="text-xs text-slate-400 font-bold">Cập nhật xu hướng & dữ liệu</span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {newsData.report.map(art => (
              <div key={art.slug} className="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                  <div className="w-full h-44 overflow-hidden bg-slate-100 relative">
                    <img src={art.image} className="w-full h-full object-cover hover:scale-102 transition duration-500" alt={art.title} />
                  </div>
                  <div className="p-5 space-y-2">
                    <span className="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md font-bold uppercase">{art.category_label}</span>
                    <h3 className="text-sm font-bold text-slate-800 line-clamp-2 leading-snug">{art.title}</h3>
                    <p className="text-[11px] text-slate-500 leading-relaxed line-clamp-3 font-semibold">{art.excerpt}</p>
                  </div>
                </div>
                <div className="px-5 pb-5 pt-2 flex items-center justify-between text-[11px] font-bold border-t border-slate-50 mt-2">
                  <span className="text-slate-400">{art.date}</span>
                  <Link href={`/news/${art.slug}`} className="text-primary hover:underline">Đọc bài viết →</Link>
                </div>
              </div>
            ))}
          </div>
        </section>

        {/* 2. Góc nhìn NKS & Cẩm nang Section */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          {/* Góc nhìn NKS */}
          <section className="space-y-4">
            <div className="border-b border-slate-200 pb-3">
              <h2 className="text-lg font-black text-slate-850">Góc nhìn NKS</h2>
            </div>
            
            <div className="space-y-4">
              {newsData.view.map(art => (
                <div key={art.slug} className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex gap-4 hover:shadow-md transition">
                  <div className="w-24 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-slate-100">
                    <img src={art.image} className="w-full h-full object-cover" alt={art.title} />
                  </div>
                  <div className="flex-grow space-y-1 min-w-0">
                    <span className="inline-block px-1.5 py-0.5 bg-emerald-50 text-emerald-650 rounded-md text-[8px] font-bold uppercase">{art.category_label}</span>
                    <h3 className="text-xs font-bold text-slate-800 truncate">{art.title}</h3>
                    <p className="text-[10px] text-slate-400 font-semibold line-clamp-2 leading-normal">{art.excerpt}</p>
                    <Link href={`/news/${art.slug}`} className="text-[10px] text-primary hover:underline font-bold block pt-1">Chi tiết →</Link>
                  </div>
                </div>
              ))}
            </div>
          </section>

          {/* Cẩm nang thuê nhà */}
          <section className="space-y-4">
            <div className="border-b border-slate-200 pb-3">
              <h2 className="text-lg font-black text-slate-850">Cẩm nang thuê nhà</h2>
            </div>

            <div className="space-y-4">
              {newsData.guide.map(art => (
                <div key={art.slug} className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex gap-4 hover:shadow-md transition">
                  <div className="w-24 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-slate-100">
                    <img src={art.image} className="w-full h-full object-cover" alt={art.title} />
                  </div>
                  <div className="flex-grow space-y-1 min-w-0">
                    <span className="inline-block px-1.5 py-0.5 bg-amber-50 text-amber-650 rounded-md text-[8px] font-bold uppercase">{art.category_label}</span>
                    <h3 className="text-xs font-bold text-slate-800 truncate">{art.title}</h3>
                    <p className="text-[10px] text-slate-400 font-semibold line-clamp-2 leading-normal">{art.excerpt}</p>
                    <Link href={`/news/${art.slug}`} className="text-[10px] text-primary hover:underline font-bold block pt-1">Chi tiết →</Link>
                  </div>
                </div>
              ))}
            </div>
          </section>

        </div>

      </div>
    </div>
  )
}
