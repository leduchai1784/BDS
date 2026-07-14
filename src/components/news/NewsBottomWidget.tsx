'use client'

import Link from 'next/link'

interface NewsBottomWidgetProps {
  articleTitle: string
}

export default function NewsBottomWidget({ articleTitle }: NewsBottomWidgetProps) {
  const handleShare = () => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(window.location.href)
      alert('Đã sao chép liên kết bài viết vào bộ nhớ tạm!')
    }
  }

  return (
    <div className="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
      <Link href="/news" className="inline-flex items-center text-xs font-bold text-slate-500 hover:text-primary transition">
        <i className="fa-solid fa-arrow-left-long mr-2"></i> Quay lại trang tin tức
      </Link>
      
      <button 
        onClick={handleShare}
        className="inline-flex items-center gap-1.5 px-4.5 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/60 rounded-xl text-xs font-extrabold text-slate-700 transition cursor-pointer"
      >
        <i className="fa-solid fa-share-nodes text-primary"></i> Chia sẻ bài viết
      </button>
    </div>
  )
}
