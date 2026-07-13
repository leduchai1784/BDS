'use client'

import { useRef } from 'react'

export default function DemandSlider() {
  const containerRef = useRef<HTMLDivElement>(null)

  const slideNext = () => {
    containerRef.current?.scrollBy({ left: 300, behavior: 'smooth' })
  }

  const slidePrev = () => {
    containerRef.current?.scrollBy({ left: -300, behavior: 'smooth' })
  }

  const mocks = [
    {
      initials: 'HT',
      purpose: 'Cho thuê',
      title: 'Cho thuê nhà hẻm Thanh Huy 1 Quận Thanh Khê, TP. Đà Nẵng',
      time: '5 giờ trước',
      location: 'Quận Thanh Khê, Đà Nẵng'
    },
    {
      initials: 'HT',
      purpose: 'Cho thuê',
      title: 'Cho thuê nhà hẻm Mai Thúc Lân Quận Ngũ Hành Sơn, Đà Nẵng',
      time: '6 giờ trước',
      location: 'Quận Ngũ Hành Sơn, Đà Nẵng'
    },
    {
      initials: 'KL',
      purpose: 'Cho thuê',
      title: 'Cho thuê phòng trọ Trần Cao Vân Quận Thanh Khê, Đà Nẵng',
      time: '7 giờ trước',
      location: 'Quận Thanh Khê, Đà Nẵng'
    },
    {
      initials: 'LV',
      purpose: 'Cho thuê',
      title: 'Cho thuê phòng trọ Hải Phòng Quận Hải Châu, Đà Nẵng',
      time: '8 giờ trước',
      location: 'Quận Hải Châu, Đà Nẵng'
    },
    {
      avatar: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg',
      purpose: 'Cần bán',
      title: 'NHÀ ĐẸP – HẺM XE HƠI 6M – TRUNG TÂM TÂN BÌNH',
      time: '1 ngày trước',
      location: 'Quận Tân Bình, TP. HCM',
      isSale: true
    },
    {
      avatar: 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg',
      purpose: 'Cần bán',
      title: 'Bán nhà mặt tiền Nguyễn Đình Chính Phú Nhuận, TP. HCM',
      time: '1 ngày trước',
      location: 'Phú Nhuận, TP. HCM',
      isSale: true
    }
  ]

  const handleCreateDemand = () => {
    alert('Tính năng Tạo nhu cầu sẽ khả dụng sau khi bạn đăng nhập!')
  }

  return (
    <div className="bg-white rounded-[32px] p-8 sm:p-10 border border-slate-100 shadow-xs text-left relative">
      {/* Header row */}
      <div className="flex items-center justify-between mb-8">
        <div>
          <h2 className="text-2xl font-bold text-slate-900">Nhu cầu</h2>
          <p className="text-sm text-slate-500 mt-1">Khám phá nhu cầu mua, bán, thuê mới nhất từ cộng đồng</p>
        </div>
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

      {/* Slides Container */}
      <div 
        ref={containerRef} 
        className="flex space-x-6 overflow-x-auto [&::-webkit-scrollbar]:hidden scrollbar-none scroll-smooth pb-3"
        style={{ msOverflowStyle: 'none', scrollbarWidth: 'none' }}
      >
        {/* Card 1: Tạo nhu cầu */}
        <div 
          onClick={handleCreateDemand}
          className="w-64 h-48 rounded-[24px] border-2 border-dashed border-primary-light hover:border-primary bg-white flex flex-col items-center justify-center p-6 flex-shrink-0 cursor-pointer group transition duration-300"
        >
          <div className="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-115 transition duration-300 mb-3">
            <i className="fa-solid fa-plus text-base"></i>
          </div>
          <h4 className="text-sm font-bold text-slate-800 group-hover:text-primary transition duration-150 mb-0.5">Tạo nhu cầu</h4>
          <p className="text-xs text-slate-400 font-medium">Chia sẻ điều bạn đang tìm</p>
        </div>

        {/* Mock demands list */}
        {mocks.map((item, idx) => (
          <div 
            key={idx}
            className="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300"
          >
            <div className="flex items-center justify-between">
              {item.avatar ? (
                <img src={item.avatar} alt="User Avatar" className="w-8 h-8 rounded-full object-cover" />
              ) : (
                <div className="w-8 h-8 rounded-full bg-primary-light text-primary font-bold text-xs flex items-center justify-center">
                  {item.initials}
                </div>
              )}
              <span className={`px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold uppercase tracking-wider ${
                item.isSale ? 'bg-orange-50 text-orange-600' : 'bg-primary-light text-primary'
              }`}>
                {item.purpose}
              </span>
            </div>
            
            <h4 className="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
              {item.title}
            </h4>
            
            <div className="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
              <span>{item.time}</span>
              <span className="truncate max-w-[130px]">{item.location}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
