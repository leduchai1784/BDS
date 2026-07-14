'use client'

import { useState, useEffect } from 'react'

interface ShareEventDetail {
  url: string
  title: string
}

export default function ShareModal() {
  const [open, setOpen] = useState(false)
  const [url, setUrl] = useState('')
  const [title, setTitle] = useState('')
  const [copied, setCopied] = useState(false)

  useEffect(() => {
    const handleOpenShare = (event: Event) => {
      const customEvent = event as CustomEvent<ShareEventDetail>
      if (customEvent.detail) {
        setUrl(customEvent.detail.url)
        setTitle(customEvent.detail.title)
        setOpen(true)
        setCopied(false)
      }
    }

    window.addEventListener('open-share-modal', handleOpenShare)
    return () => {
      window.removeEventListener('open-share-modal', handleOpenShare)
    }
  }, [])

  const copyLink = () => {
    navigator.clipboard.writeText(url)
    setCopied(true)
    setTimeout(() => setCopied(false), 2000)
  }

  if (!open) return null

  return (
    <div 
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
      onClick={() => setOpen(false)}
    >
      <div 
        onClick={(e) => e.stopPropagation()}
        className="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative space-y-5 text-left border border-slate-100 animate-dropdown"
      >
        {/* Close Button */}
        <button 
          type="button" 
          onClick={() => setOpen(false)} 
          className="absolute top-4 right-4 text-slate-400 hover:text-slate-650 transition cursor-pointer text-sm"
        >
          <i className="fa-solid fa-xmark"></i>
        </button>

        {/* Title */}
        <div className="space-y-1 pr-6 text-left">
          <h3 className="text-base font-extrabold text-slate-900 flex items-center gap-2">
            <i className="fa-solid fa-share-nodes text-primary"></i> Chia sẻ tin đăng này
          </h3>
          <p className="text-xs text-slate-400 font-semibold leading-normal">
            Chia sẻ bất động sản này với bạn bè và người thân của bạn qua các ứng dụng sau:
          </p>
        </div>

        {/* Social Links Grid */}
        <div className="grid grid-cols-3 gap-3">
          {/* Facebook Share */}
          <a 
            href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`}
            target="_blank"
            rel="noopener noreferrer"
            className="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-blue-500 bg-slate-50/50 hover:bg-blue-50/10 text-slate-600 hover:text-blue-600 transition cursor-pointer space-y-1.5"
          >
            <i className="fa-brands fa-facebook text-2xl text-[#1877f2]"></i>
            <span className="text-[10px] font-bold">Facebook</span>
          </a>
          
          {/* Zalo Share */}
          <a 
            href={`https://sp.zalo.me/share_to_zalo?url=${encodeURIComponent(url)}`}
            target="_blank"
            rel="noopener noreferrer"
            className="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-sky-500 bg-slate-50/50 hover:bg-sky-50/10 text-slate-600 hover:text-sky-600 transition cursor-pointer space-y-1.5"
          >
            <img 
              src="https://sp.zalo.me/favicon.ico" 
              alt="Zalo" 
              className="w-6 h-6 object-contain"
              onError={(e) => {
                e.currentTarget.src = 'https://res.cloudinary.com/dj8t18pke/image/upload/v1700000000/zalo-icon.png'
              }}
            />
            <span className="text-[10px] font-bold">Zalo</span>
          </a>
          
          {/* Telegram Share */}
          <a 
            href={`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`}
            target="_blank"
            rel="noopener noreferrer"
            className="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-cyan-500 bg-slate-50/50 hover:bg-cyan-50/10 text-slate-600 hover:text-cyan-605 transition cursor-pointer space-y-1.5"
          >
            <i className="fa-brands fa-telegram text-2xl text-[#0088cc]"></i>
            <span className="text-[10px] font-bold">Telegram</span>
          </a>
        </div>

        {/* Copy Link Section */}
        <div className="space-y-1.5 pt-2 border-t border-slate-100">
          <label className="block text-[9px] font-extrabold uppercase text-slate-400 mb-1 px-1">Sao chép liên kết</label>
          <div className="relative flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
            <input 
              type="text" 
              readOnly 
              value={url} 
              className="w-full bg-transparent text-xs font-mono font-bold text-slate-600 outline-none pr-10 select-all"
            />
            <button 
              type="button" 
              onClick={copyLink}
              className="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition cursor-pointer"
              title="Sao chép"
            >
              <i className={`fa-solid text-xs ${copied ? 'fa-check text-green-500' : 'fa-copy'}`}></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
