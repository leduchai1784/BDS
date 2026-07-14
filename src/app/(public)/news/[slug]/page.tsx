import { getArticleBySlug } from '@/lib/newsData'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import NewsBottomWidget from '@/components/news/NewsBottomWidget'

export const dynamic = 'force-dynamic'

interface NewsDetailPageProps {
  params: Promise<{ slug: string }>
}

export default async function NewsDetailPage({ params }: NewsDetailPageProps) {
  const resolvedParams = await params
  const article = getArticleBySlug(resolvedParams.slug)

  if (!article) {
    notFound()
  }

  // Popular articles list matching Laravel news-detail
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
    }
  ]

  return (
    <div className="bg-white min-h-screen text-slate-800 text-left">
      
      {/* Header Section */}
      <div className="bg-slate-50 border-b border-slate-100 pt-28 pb-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Breadcrumbs */}
          <nav className="flex text-xs font-semibold text-slate-400 mb-4 space-x-2">
            <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
            <span>/</span>
            <Link href="/news" className="hover:text-primary transition">Tin tức</Link>
            <span>/</span>
            <span className="text-slate-600 truncate max-w-xs md:max-w-md">{article.title}</span>
          </nav>
          
          <div className="max-w-4xl">
            <span className="inline-block bg-primary/10 text-primary text-[10px] font-black px-2.5 py-1 rounded-[6px] uppercase tracking-wider mb-3">
              {article.category_label}
            </span>
            <h1 className="text-2xl md:text-3xl font-extrabold text-[#0f172a] leading-tight tracking-tight">
              {article.title}
            </h1>
            <p className="text-slate-400 text-xs font-bold mt-3">
              Ngày đăng: {article.date} • BDS Rental
            </p>
          </div>
        </div>
      </div>

      {/* Main Body Grid */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16 items-start">
          
          {/* LEFT COLUMN: Sidebar (33% width) */}
          <div className="space-y-8 lg:col-span-1 lg:sticky lg:top-28 lg:self-start lg:border-r lg:border-slate-100 lg:pr-12">
            
            {/* Search Box Widget */}
            <div className="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
              <h3 className="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
              <form action="/news" method="GET" className="flex gap-2">
                <div className="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3 py-2.5 flex items-center gap-2 shadow-2xs">
                  <input 
                    type="text" 
                    name="q"
                    placeholder="Nhập từ khóa..." 
                    className="w-full bg-transparent border-0 p-0 text-slate-700 placeholder-slate-400 font-bold focus:outline-none focus:ring-0 text-xs"
                  />
                </div>
                <button type="submit" className="bg-primary hover:bg-primary-hover text-white font-bold px-4 py-2.5 rounded-2xl transition-all text-xs cursor-pointer">
                  Tìm
                </button>
              </form>
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

          {/* RIGHT COLUMN: Article Detail Content (66% width) */}
          <div className="lg:col-span-2 space-y-8">
            
            {/* Article Main Image */}
            <div className="rounded-[32px] overflow-hidden aspect-[16/9] shadow-sm relative bg-slate-100">
              <img src={article.image} alt={article.title} className="w-full h-full object-cover" />
            </div>

            {/* Article Excerpt */}
            <div className="bg-slate-50 border-l-4 border-primary p-5 rounded-r-2xl">
              <p className="text-slate-600 font-semibold text-sm leading-relaxed italic">
                " {article.excerpt} "
              </p>
            </div>

            {/* Article HTML Body */}
            <article 
              className="prose prose-slate max-w-none text-slate-700 leading-relaxed text-sm md:text-base space-y-6"
              dangerouslySetInnerHTML={{ __html: article.content }}
            />

            {/* Bottom Share / Back Widget */}
            <NewsBottomWidget articleTitle={article.title} />

          </div>

        </div>
      </div>
    </div>
  )
}
