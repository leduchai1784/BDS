'use client'

import { useRef, useEffect } from 'react'
import PropertyCard from '@/components/property/PropertyCard'

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
    <div className="flex flex-col h-full bg-white border-r border-slate-100 w-full lg:w-96 flex-shrink-0 z-20">
      {/* Search Result Statistics Summary */}
      <div className="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between text-left">
        <div>
          <span className="text-[10px] font-black text-primary uppercase tracking-wider">Kết quả tìm kiếm</span>
          <p className="text-xs text-slate-500 font-semibold mt-0.5">
            Tìm thấy <span className="text-primary font-bold">{properties.length}</span> tin đăng phù hợp
          </p>
        </div>
      </div>

      {/* Cards List container */}
      <div 
        ref={sidebarContainerRef}
        className="flex-grow overflow-y-auto p-4 space-y-4 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-slate-200 [&::-webkit-scrollbar-thumb]:rounded-full"
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
                className={`transition-all duration-300 rounded-[24px] cursor-pointer ${
                  isSelected 
                    ? 'ring-2 ring-primary ring-offset-2 scale-[0.98] shadow-md shadow-primary/10' 
                    : isHovered 
                      ? 'scale-[0.99] shadow-sm'
                      : ''
                }`}
              >
                <PropertyCard 
                  property={property} 
                  isFavoriteInitial={false} // Wishlist toggler handles internal ajax/storage
                />
              </div>
            )
          })
        ) : (
          <div className="py-16 text-center text-slate-400">
            <i className="fa-solid fa-folder-open text-4xl text-slate-300 mb-4 block"></i>
            <h3 className="text-sm font-bold text-slate-800 mb-1">Không tìm thấy kết quả</h3>
            <p className="text-xs text-slate-400 max-w-[220px] mx-auto">Vui lòng thay đổi bộ lọc để xem thêm bất động sản.</p>
          </div>
        )}
      </div>
    </div>
  )
}
