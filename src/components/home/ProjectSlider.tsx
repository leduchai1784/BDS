'use client'

import { useRef } from 'react'
import Link from 'next/link'

interface Project {
  id: number
  title: string
  slug: string
  description?: string | null
  location?: string | null
  priceRange?: string | null
  status: string
  images?: any
}

interface ProjectSliderProps {
  projects: Project[]
}

export default function ProjectSlider({ projects }: ProjectSliderProps) {
  const containerRef = useRef<HTMLDivElement>(null)

  const slideNext = () => {
    containerRef.current?.scrollBy({ left: 384, behavior: 'smooth' })
  }

  const slidePrev = () => {
    containerRef.current?.scrollBy({ left: -384, behavior: 'smooth' })
  }

  // Fallback mocks if DB has no projects
  const mocks = [
    {
      slug: 'the-prive',
      title: 'THE PRIVÉ',
      location: 'An Phú, Quận Thủ Đức, Hồ Chí Minh',
      price: '4,9 tỷ - 15 tỷ',
      status_dot: 'bg-blue-500',
      status_text: 'Đang mở bán',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
    },
    {
      slug: 'the-emerald-garden-view',
      title: 'THE EMERALD GARDEN VIEW',
      location: 'Hưng Định, Quận Thuận An, Bình Dương',
      price: '1,3 tỷ - 3,2 tỷ',
      status_dot: 'bg-blue-500',
      status_text: 'Đang mở bán',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg'
    },
    {
      slug: 'ansana-by-kita',
      title: 'Ansana by Kita',
      location: 'An Lạc, Quận Bình Tân, Hồ Chí Minh',
      price: '90 triệu - 100 triệu',
      status_dot: 'bg-emerald-500',
      status_text: 'Đang nhận booking',
      image: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg'
    }
  ]

  const displayList = projects.length > 0 
    ? projects.map(p => {
        const images = Array.isArray(p.images) ? p.images : []
        const imgUrl = images.length > 0 ? images[0] : 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
        
        let statusDotColor = 'bg-blue-500'
        let statusText = 'Đang mở bán'
        if (p.status === 'upcoming') {
          statusDotColor = 'bg-orange-500'
          statusText = 'Sắp mở bán'
        } else if (p.status === 'handed_over' || p.status === 'completed') {
          statusDotColor = 'bg-emerald-500'
          statusText = 'Đã bàn giao'
        }

        return {
          slug: p.slug,
          title: p.title,
          location: p.location || '',
          price: p.priceRange || 'Liên hệ',
          status_dot: statusDotColor,
          status_text: statusText,
          image: imgUrl
        }
      })
    : mocks

  return (
    <div className="text-left">
      {/* Slider Header */}
      <div className="mb-8 flex items-center justify-between">
        <h2 className="text-2xl font-bold text-slate-900">Kho dự án nổi bật</h2>
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

      {/* Projects Slides Container */}
      <div 
        ref={containerRef} 
        className="flex space-x-6 overflow-x-auto [&::-webkit-scrollbar]:hidden scrollbar-none scroll-smooth pb-4"
        style={{ msOverflowStyle: 'none', scrollbarWidth: 'none' }}
      >
        {displayList.map((item, idx) => (
          <div 
            key={idx}
            className="w-96 h-64 rounded-[24px] overflow-hidden relative flex-shrink-0 group shadow-sm hover:shadow-lg transition-all duration-300"
          >
            {/* Background Image */}
            <img 
              src={item.image} 
              alt={item.title} 
              className="absolute inset-0 w-full h-full object-cover group-hover:scale-103 transition duration-500"
            />
            {/* Dark Overlay at the bottom */}
            <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-transparent z-1"></div>
            
            {/* Top Badges */}
            <div className="absolute top-4 left-4 z-10">
              <div className="px-3 py-1.5 rounded-full bg-black/40 backdrop-blur-xs flex items-center">
                <span className={`w-2 h-2 rounded-full ${item.status_dot} mr-1.5`}></span>
                <span className="text-[10px] text-white font-extrabold uppercase tracking-wider">{item.status_text}</span>
              </div>
            </div>

            {/* Bottom Info */}
            <div className="absolute bottom-5 left-6 right-6 z-10 text-left">
              <h3 className="text-lg font-bold text-white uppercase tracking-wide line-clamp-1 mb-1">
                <Link href={`/projects/${item.slug}`} className="text-white hover:text-white hover:underline">{item.title}</Link>
              </h3>
              <p className="text-xs text-white/90 font-medium line-clamp-1 mb-1.5">
                {item.location}
              </p>
              <span className="text-sm font-extrabold text-white block">
                {item.price}
              </span>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
