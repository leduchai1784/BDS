'use client'

import { useState } from 'react'

interface ImageGalleryProps {
  images: string[]
  isVip?: boolean
}

export default function ImageGallery({ images, isVip = false }: ImageGalleryProps) {
  const [activeIndex, setActiveIndex] = useState(0)

  const list = images.length > 0 ? images : ['/images/apartment_placeholder.png']

  const handlePrev = () => {
    setActiveIndex(prev => (prev - 1 + list.length) % list.length)
  }

  const handleNext = () => {
    setActiveIndex(prev => (prev + 1) % list.length)
  }

  const getImageUrl = (img: string) => {
    if (img.startsWith('http://') || img.startsWith('https://')) {
      return img
    }
    return `/${img}`
  }

  return (
    <div className="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm p-4 sm:p-6 space-y-6">
      {/* Large Primary Image */}
      <div className="relative h-[280px] sm:h-[450px] w-full rounded-2xl overflow-hidden bg-slate-100 group">
        <img 
          src={getImageUrl(list[activeIndex])} 
          alt="Property view" 
          className="w-full h-full object-cover object-center transition-all duration-300"
          onError={(e) => {
            e.currentTarget.src = '/images/apartment_placeholder.png'
          }}
        />
        
        {/* Badges Overlay */}
        <div className="absolute top-4 left-4 flex flex-col gap-2 z-10">
          {isVip && (
            <span className="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black tracking-wider uppercase bg-red-500 text-white shadow-lg shadow-red-500/30">
              <i className="fa-solid fa-crown mr-1.5"></i> VIP NỔI BẬT
            </span>
          )}
        </div>

        {/* Navigation arrows (Only if multiple images) */}
        {list.length > 1 && (
          <>
            <button 
              type="button"
              onClick={handlePrev}
              className="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/45 hover:bg-black/60 text-white flex items-center justify-center transition shadow-md backdrop-blur-sm z-10 cursor-pointer active:scale-90 opacity-0 group-hover:opacity-100 duration-200"
            >
              <i className="fa-solid fa-chevron-left text-sm"></i>
            </button>
            <button 
              type="button"
              onClick={handleNext}
              className="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/45 hover:bg-black/60 text-white flex items-center justify-center transition shadow-md backdrop-blur-sm z-10 cursor-pointer active:scale-90 opacity-0 group-hover:opacity-100 duration-200"
            >
              <i className="fa-solid fa-chevron-right text-sm"></i>
            </button>

            {/* Slide Count Indicator */}
            <span className="absolute bottom-4 right-4 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full z-10 select-none">
              {activeIndex + 1} / {list.length}
            </span>
          </>
        )}
      </div>

      {/* Thumbnail selection list (if multiple images) */}
      {list.length > 1 && (
        <div className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none" style={{ msOverflowStyle: 'none', scrollbarWidth: 'none' }}>
          {list.map((img, idx) => (
            <button
              key={idx}
              type="button"
              onClick={() => setActiveIndex(idx)}
              className={`w-16 h-12 rounded-lg overflow-hidden border-2 flex-shrink-0 cursor-pointer transition ${
                activeIndex === idx ? 'border-primary' : 'border-transparent opacity-60'
              }`}
            >
              <img 
                src={getImageUrl(img)} 
                alt="Thumbnail" 
                className="w-full h-full object-cover" 
                onError={(e) => {
                  e.currentTarget.src = '/images/apartment_placeholder.png'
                }}
              />
            </button>
          ))}
        </div>
      )}
    </div>
  )
}
