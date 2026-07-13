'use client'

import { useState, useRef } from 'react'

export default function VideoShowcase() {
  const containerRef = useRef<HTMLDivElement>(null)
  
  const [videoModalOpen, setVideoModalOpen] = useState(false)
  const [activeVideoUrl, setActiveVideoUrl] = useState('')

  const slideNext = () => {
    containerRef.current?.scrollBy({ left: 384, behavior: 'smooth' })
  }

  const slidePrev = () => {
    containerRef.current?.scrollBy({ left: -384, behavior: 'smooth' })
  }

  const openVideo = (youtubeId: string) => {
    setActiveVideoUrl(`https://www.youtube.com/embed/${youtubeId}?autoplay=1`)
    setVideoModalOpen(true)
  }

  const closeVideo = () => {
    setVideoModalOpen(false)
    setActiveVideoUrl('')
  }

  const videos = [
    {
      youtube_id: 'dQw4w9WgXcQ',
      title: 'Căn hộ Studio dịch vụ tách bếp Phú Nhuận',
      location: 'Phú Nhuận, TPHCM',
      badge: 'CHO THUÊ',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
    },
    {
      youtube_id: 'dQw4w9WgXcQ',
      title: 'Biệt thự song lập compound Thảo Điền Quận 2',
      location: 'Quận 2, TPHCM',
      badge: 'ĐANG BÁN',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg'
    },
    {
      youtube_id: 'dQw4w9WgXcQ',
      title: 'Nhà phố mặt tiền kinh doanh 222 Lê Văn Sỹ',
      location: 'Phú Nhuận, TPHCM',
      badge: 'ĐANG BÁN',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg'
    },
    {
      youtube_id: 'dQw4w9WgXcQ',
      title: 'Căn hộ Landmark 81 Full nội thất view sông',
      location: 'Bình Thạnh, TPHCM',
      badge: 'CHO THUÊ',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/mvt1mwpuj5vo4qm538rb.jpg'
    }
  ]

  return (
    <section className="py-16 bg-white text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Section Header */}
        <div className="mb-8 flex items-center justify-between">
          <h2 className="text-2xl font-bold text-slate-900">Video nhà đất</h2>
          {/* Slider Navigation arrows */}
          <div className="flex items-center space-x-2.5">
            <button 
              onClick={slidePrev} 
              className="w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-primary hover:border-primary transition flex items-center justify-center shadow-xs cursor-pointer active:scale-95"
            >
              <i className="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button 
              onClick={slideNext} 
              className="w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-primary hover:border-primary transition flex items-center justify-center shadow-xs cursor-pointer active:scale-95"
            >
              <i className="fa-solid fa-chevron-right text-xs"></i>
            </button>
          </div>
        </div>

        {/* Videos Slides Container */}
        <div 
          ref={containerRef} 
          className="flex space-x-6 overflow-x-auto [&::-webkit-scrollbar]:hidden scrollbar-none scroll-smooth pb-4"
          style={{ msOverflowStyle: 'none', scrollbarWidth: 'none' }}
        >
          {videos.map((video, idx) => (
            <div 
              key={idx}
              onClick={() => openVideo(video.youtube_id)} 
              className="w-96 h-64 rounded-[24px] overflow-hidden relative flex-shrink-0 group shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
            >
              {/* Background Image */}
              <img 
                src={video.image} 
                alt={video.title} 
                className="absolute inset-0 w-full h-full object-cover group-hover:scale-103 transition duration-500"
              />
              {/* Dark Overlay */}
              <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-transparent z-1"></div>
              
              {/* Top-left category badge */}
              <div className="absolute top-4 left-4 z-10">
                <span className="px-2.5 py-1 rounded-lg bg-primary text-white text-[9px] font-black uppercase tracking-wider">
                  {video.badge}
                </span>
              </div>

              {/* Center Play Button icon */}
              <div className="absolute inset-0 flex items-center justify-center z-10">
                <div className="w-12 h-12 rounded-full border border-white/40 bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition duration-300 transform group-hover:scale-110 shadow-lg">
                  <i className="fa-solid fa-play text-sm ml-0.5"></i>
                </div>
              </div>

              {/* Bottom Title and Location */}
              <div className="absolute bottom-5 left-6 right-6 z-10 text-left">
                <h4 className="text-sm font-bold text-white leading-snug line-clamp-2 mb-1.5">
                  {video.title}
                </h4>
                <p className="text-[10px] text-white/80 font-medium block">
                  {video.location}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Video Modal Overlay */}
      {videoModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 p-4 sm:p-6 transition-opacity duration-300">
          <div className="relative w-full max-w-4xl bg-black rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
            {/* Close button */}
            <button 
              onClick={closeVideo} 
              className="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center transition border border-white/10 cursor-pointer"
            >
              <i className="fa-solid fa-xmark text-lg"></i>
            </button>

            {/* Iframe Container */}
            <div className="aspect-video w-full bg-black">
              <iframe 
                src={activeVideoUrl} 
                className="w-full h-full border-0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowFullScreen
              ></iframe>
            </div>
          </div>
        </div>
      )}
    </section>
  )
}
