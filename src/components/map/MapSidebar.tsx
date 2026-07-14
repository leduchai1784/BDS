'use client'

import { useRef, useEffect } from 'react'

interface MapSidebarProps {
  properties: any[]
  activeId: string | null
  setActiveId: (id: string | null) => void
  hoveredId: string | null
  setHoveredId: (id: string | null) => void
}

export default function MapSidebar({
  properties,
  activeId,
  setActiveId,
  hoveredId,
  setHoveredId
}: MapSidebarProps) {
  const sidebarContainerRef = useRef<HTMLDivElement>(null)

  // Scroll active property card into view inside sidebar if triggered from map marker click
  useEffect(() => {
    if (activeId) {
      const activeElement = document.getElementById(`sidebar-card-${activeId}`)
      if (activeElement && sidebarContainerRef.current) {
        activeElement.scrollIntoView({
          behavior: 'smooth',
          block: 'nearest'
        })
      }
    }
  }, [activeId])

  return (
    <aside className="hidden md:flex flex-col w-[380px] lg:w-[420px] bg-white border-r border-slate-100 h-full flex-shrink-0 z-20 shadow-sm text-left">
      {/* Sidebar Header */}
      <div className="p-5 border-b border-slate-100 flex-shrink-0 bg-white text-left">
        <div className="flex items-center justify-between">
          <span className="text-sm font-extrabold text-slate-700 flex items-center gap-1.5">
            <i className="fa-solid fa-map-location-dot text-primary"></i>
            <span>Bản đồ bất động sản</span>
          </span>
          <span className="text-[10px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">
            Tìm thấy <span className="text-primary">{properties.length}</span> tin đăng
          </span>
        </div>
      </div>

      {/* Cards List container */}
      <div 
        ref={sidebarContainerRef}
        className="flex-grow overflow-y-auto p-4 space-y-3.5 bg-slate-50/50 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-slate-200 [&::-webkit-scrollbar-thumb]:rounded-full"
      >
        {properties.length > 0 ? (
          properties.map(property => {
            const isSelected = activeId === property.id
            const isHovered = hoveredId === property.id

            return (
              <div 
                key={property.id}
                id={`sidebar-card-${property.id}`}
                onMouseEnter={() => setHoveredId(property.id)}
                onMouseLeave={() => setHoveredId(null)}
                onClick={() => setActiveId(property.id)}
                className={`group flex gap-3.5 p-3 rounded-2xl border transition-all duration-300 cursor-pointer text-left relative overflow-hidden ${
                  isSelected 
                    ? 'border-primary ring-1 ring-primary bg-primary/5 shadow-lg shadow-primary/5' 
                    : isHovered 
                      ? 'border-slate-300 shadow-md'
                      : 'border-slate-150/50 bg-white hover:border-slate-300'
                }`}
              >
                {/* Visual Tag Indicator */}
                <div 
                  className={`absolute left-0 top-0 bottom-0 w-1 transition-colors duration-300 ${
                    isSelected ? 'bg-primary' : 'bg-transparent'
                  }`}
                />

                {/* Image Thumbnail */}
                <div className="w-[110px] h-[95px] rounded-xl overflow-hidden flex-shrink-0 bg-slate-100 relative">
                  <img 
                    src={property.imagePath || '/images/apartment_placeholder.png'} 
                    alt={property.title} 
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                  />
                  {property.isVip && (
                    <span className="absolute top-1.5 left-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-red-500 text-white shadow-sm">
                      VIP
                    </span>
                  )}
                </div>

                {/* Details Content */}
                <div className="flex flex-col justify-between flex-grow min-w-0">
                  <div>
                    <div className="flex items-center justify-between gap-1 mb-0.5">
                      <span className="text-sm font-extrabold text-primary tracking-tight">
                        {property.priceLabel}
                      </span>
                      <span className="text-[9px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                        {property.area} m²
                      </span>
                    </div>
                    <h4 
                      className={`text-[12px] font-bold line-clamp-2 leading-snug transition duration-200 ${
                        isSelected ? 'text-primary' : 'text-slate-800 group-hover:text-primary'
                      }`}
                    >
                      {property.title}
                    </h4>
                  </div>
                  
                  <div className="flex items-center text-slate-400 text-[10px] font-medium mt-1">
                    <i className="fa-solid fa-location-dot text-[9px] mr-1 flex-shrink-0"></i>
                    <span className="truncate">{property.address}</span>
                  </div>
                </div>
              </div>
            )
          })
        ) : (
          <div className="py-16 text-center text-slate-400">
            <div className="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-450 mb-3">
              <i className="fa-solid fa-magnifying-glass text-xl"></i>
            </div>
            <h3 className="text-sm font-bold text-slate-800 mb-1">Không tìm thấy kết quả</h3>
            <p className="text-xs text-slate-450 max-w-[220px] mx-auto">Vui lòng thay đổi bộ lọc để xem thêm bất động sản.</p>
          </div>
        )}
      </div>
    </aside>
  )
}
