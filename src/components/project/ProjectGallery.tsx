'use client'

import { useState } from 'react'

interface ProjectGalleryProps {
  images: string[]
  title: string
}

export default function ProjectGallery({ images, title }: ProjectGalleryProps) {
  const [activeImage, setActiveImage] = useState(0)

  if (!images || images.length === 0) {
    return (
      <div className="w-full aspect-[16/9] flex items-center justify-center bg-slate-100 rounded-3xl text-slate-300 border border-slate-200">
        <i className="fa-regular fa-image text-5xl"></i>
      </div>
    )
  }

  return (
    <div className="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm p-4 text-left">
      {/* Main Large Image */}
      <div className="relative aspect-[16/9] bg-slate-100 rounded-2xl overflow-hidden mb-4">
        <img 
          src={images[activeImage]} 
          alt={title} 
          className="w-full h-full object-cover transition-all duration-300"
        />
      </div>

      {/* Thumbnail list */}
      <div className="flex gap-3 overflow-x-auto pb-1 scrollbar-thin">
        {images.map((img, idx) => (
          <button 
            key={idx}
            type="button"
            onClick={() => setActiveImage(idx)}
            className={`w-24 aspect-[16/10] rounded-xl overflow-hidden border-2 transition-all flex-shrink-0 cursor-pointer ${
              activeImage === idx ? 'border-primary shadow-md' : 'border-transparent opacity-70 hover:opacity-100'
            }`}
          >
            <img src={img} className="w-full h-full object-cover" alt={`${title} thumb ${idx}`} />
          </button>
        ))}
      </div>
    </div>
  )
}
