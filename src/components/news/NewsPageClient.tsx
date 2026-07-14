'use client'

import { useState, useEffect } from 'react'
import { useRouter, useSearchParams, usePathname } from 'next/navigation'
import Link from 'next/link'
import { newsData, Article } from '@/lib/newsData'

export default function NewsPageClient() {
  const router = useRouter()
  const pathname = usePathname()
  const searchParams = useSearchParams()

  // Get initial category from URL or default to 'report'
  const initialCategory = searchParams.get('category') || 'report'
  const [activeTab, setActiveTab] = useState<string>(initialCategory)
  const [searchQuery, setSearchQuery] = useState<string>('')

  // Sync state if URL changes (e.g. browser back/forward)
  useEffect(() => {
    const category = searchParams.get('category') || 'report'
    setActiveTab(category)
  }, [searchParams])

  const changeTab = (tab: string) => {
    setActiveTab(tab)
    const params = new URLSearchParams(searchParams.toString())
    params.set('category', tab)
    router.push(`${pathname}?${params.toString()}`, { scroll: false })
  }

  // Get articles for the active category
  const activeArticles: Article[] = (newsData as any)[activeTab] || []

  // Filter articles based on search query
  const filteredArticles = activeArticles.filter(a => {
    if (!searchQuery.trim()) return true
    const q = searchQuery.toLowerCase()
    return (
      a.title.toLowerCase().includes(q) ||
      a.excerpt.toLowerCase().includes(q)
    )
  })

  // Popular articles list
  const popularArticles = [
    {
      slug: 'quy-trinh-thu-tuc-chuyen-nhuong-hop-dong-thue-nha',
      title: 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
      image: 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&q=80&w=200',
      date: '02/07/2026'
    },
    {
      slug: 'kinh-nghiem-vang-phan-biet-so-hong-that-gia',
      title: 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
      image: 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&q=80&w=200',
      date: '26/06/2026'
    },
    {
      slug: 'cac-loai-thue-phi-phai-nop-khi-mua-ban-nha-dat',
      title: 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
      image: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=200',
      date: '17/06/2026'
    },
    {
      slug: 'kinh-nghiem-quan-ly-tai-chinh-mua-nha-tra-gop-gia-dinh-tre',
      title: 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
      image: 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&q=80&w=200',
      date: '26/06/2026'
    }
  ]

  return (
    <div className="bg-white min-h-screen text-slate-800 text-left">
      
      {/* Hero / Header Title Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-400 mb-4 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-500">Tin tức & Cẩm nang</span>
        </nav>

        <div className="space-y-2 border-b border-slate-100 pb-6 text-left">
          <span className="text-xs font-black text-primary uppercase tracking-widest">NKS WIKI TIN TỨC</span>
          <h1 className="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">
            Tin Tức Bất Động Sản
          </h1>
          <p className="text-slate-400 text-xs sm:text-sm font-semibold">
            Cập nhật nhanh chóng xu hướng thị trường, kiến thức đầu tư, thiết kế nội thất và cẩm nang phong thủy.
          </p>
        </div>
      </section>

      {/* Main Container */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pb-20">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16 items-start">
          
          {/* LEFT COLUMN: Sidebar (33% width) */}
          <div className="space-y-8 lg:col-span-1 lg:sticky lg:top-28 lg:self-start lg:border-r lg:border-slate-100 lg:pr-12">
            
            {/* Search Box Widget */}
            <div className="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
              <h3 className="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
              <div className="flex gap-2">
                <div className="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3 py-2.5 flex items-center gap-2 shadow-2xs">
                  <i className="fa-solid fa-magnifying-glass text-slate-400 text-xs pl-1"></i>
                  <input 
                    type="text" 
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Nhập từ khóa tìm kiếm..." 
                    className="w-full bg-transparent border-0 p-0 text-slate-700 placeholder-slate-400 font-bold focus:outline-none focus:ring-0 text-xs"
                  />
                </div>
                <button type="button" className="bg-primary hover:bg-primary-hover text-white font-bold px-4 py-2.5 rounded-2xl transition-all text-xs cursor-pointer">
                  Tìm
                </button>
              </div>
            </div>

            {/* Popular Articles Widget */}
            <div className="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
              <h3 className="text-xs font-black text-slate-800 uppercase tracking-widest pb-3 border-b border-slate-50">
                Tin đọc nhiều
              </h3>
              
              <div className="space-y-4">
                {popularArticles.map(art => (
                  <Link 
                    key={art.slug} 
                    href={`/news/${art.slug}`} 
                    className="flex gap-3 items-start group cursor-pointer"
                  >
                    <div className="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                      <img 
                        src={art.image} 
                        alt={art.title} 
                        className="w-full h-full object-cover group-hover:scale-103 transition-transform"
                      />
                    </div>
                    <div className="space-y-1">
                      <h4 className="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                        {art.title}
                      </h4>
                      <span className="text-[9px] text-slate-400 font-bold block">{art.date}</span>
                    </div>
                  </Link>
                ))}
              </div>
            </div>

          </div>

          {/* RIGHT COLUMN: Articles Grid */}
          <div className="lg:col-span-2 space-y-8">
            
            {/* Category Filtering Navigation Menu */}
            <div className="flex flex-wrap gap-2 border-b border-slate-100 pb-4">
              {[
                { key: 'report', label: 'Báo cáo Thị trường BĐS' },
                { key: 'view', label: 'Góc Nhìn NKS' },
                { key: 'interior', label: 'Nội Thất' },
                { key: 'fengshui', label: 'Phong Thủy' },
                { key: 'news', label: 'Tin Tức' },
                { key: 'knowledge', label: 'Kiến Thức' }
              ].map(tab => (
                <button
                  key={tab.key}
                  onClick={() => changeTab(tab.key)}
                  className={`px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none ${
                    activeTab === tab.key
                      ? 'bg-primary text-white shadow-sm'
                      : 'bg-slate-50 text-slate-500 hover:bg-slate-100'
                  }`}
                >
                  {tab.label}
                </button>
              ))}
            </div>

            {/* Articles Grid */}
            {filteredArticles.length > 0 ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                {filteredArticles.map((article, index) => (
                  <Link 
                    key={index} 
                    href={`/news/${article.slug}`}
                    className="space-y-4 group cursor-pointer block"
                  >
                    <div className="h-48 rounded-[24px] overflow-hidden shadow-2xs relative border border-slate-100/60">
                      <img 
                        src={article.image} 
                        alt={article.title} 
                        className="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                    </div>
                    
                    <div className="space-y-2">
                      <span className="text-[9px] font-black text-slate-400 uppercase tracking-widest block">
                        {article.category_label}
                      </span>
                      <h4 className="text-sm font-extrabold text-slate-800 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                        {article.title}
                      </h4>
                      <p className="text-xs text-slate-400 font-semibold line-clamp-2 leading-relaxed">
                        {article.excerpt}
                      </p>
                      <p className="text-[10px] text-slate-400 font-bold pt-1">
                        {article.date}
                      </p>
                    </div>
                  </Link>
                ))}
              </div>
            ) : (
              /* Empty State */
              <div className="text-center py-16 bg-slate-50 rounded-3xl border border-slate-100">
                <i className="fa-solid fa-folder-open text-slate-300 text-4xl mb-3 block"></i>
                <span className="text-slate-500 font-bold text-sm">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</span>
              </div>
            )}
            
          </div>

        </div>
      </div>
    </div>
  )
}
