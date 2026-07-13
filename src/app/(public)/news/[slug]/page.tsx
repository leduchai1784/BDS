import { getArticleBySlug } from '@/lib/newsData'
import { notFound } from 'next/navigation'
import Link from 'next/link'

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

  return (
    <div className="bg-slate-50 min-h-screen pt-28 pb-16 text-slate-800 text-left">
      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <Link href="/news" className="hover:text-primary transition">Tin tức & Cẩm nang</Link>
          <span>/</span>
          <span className="text-slate-850 truncate max-w-xs">{article.title}</span>
        </nav>

        {/* Article Container Card */}
        <article className="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-6 sm:p-10 space-y-6">
          
          {/* Metadata */}
          <div className="space-y-3">
            <span className="inline-block px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-md text-[10px] font-bold uppercase">
              {article.category_label}
            </span>
            <h1 className="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
              {article.title}
            </h1>
            <div className="flex items-center text-xs text-slate-450 font-bold space-x-3">
              <span>Đăng ngày: {article.date}</span>
              <span>•</span>
              <span>Tác giả: BDS Rental Editor</span>
            </div>
          </div>

          {/* Featured Image */}
          <div className="w-full h-64 sm:h-[400px] rounded-2xl overflow-hidden bg-slate-100">
            <img src={article.image} className="w-full h-full object-cover" alt={article.title} />
          </div>

          {/* Excerpt */}
          <p className="text-xs sm:text-sm text-slate-500 font-bold border-l-4 border-primary pl-4 py-1 italic leading-relaxed">
            {article.excerpt}
          </p>

          <div className="border-t border-slate-100 my-6" />

          {/* Rendered HTML content */}
          <div 
            className="prose prose-sm max-w-none text-slate-700 text-xs sm:text-sm leading-relaxed space-y-4 font-medium"
            dangerouslySetInnerHTML={{ __html: article.content }}
          />

        </article>

      </div>
    </div>
  )
}
