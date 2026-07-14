import { Suspense } from 'react'
import NewsPageClient from '@/components/news/NewsPageClient'

export const dynamic = 'force-dynamic'

export default async function NewsPage() {
  return (
    <Suspense fallback={
      <div className="bg-white min-h-screen text-slate-800 text-left pt-28 pb-16 flex items-center justify-center">
        <div className="text-center space-y-4">
          <div className="w-12 h-12 rounded-full border-4 border-primary border-t-transparent animate-spin mx-auto"></div>
          <span className="text-sm font-bold text-slate-500">Đang tải tin tức...</span>
        </div>
      </div>
    }>
      <NewsPageClient />
    </Suspense>
  )
}
