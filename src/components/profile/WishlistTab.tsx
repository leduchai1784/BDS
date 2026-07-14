'use client'

import { useState, useEffect } from 'react'
import PropertyCard from '@/components/property/PropertyCard'

interface WishlistTabProps {
  initialProperties: any[]
}

export default function WishlistTab({ initialProperties }: WishlistTabProps) {
  const [list, setList] = useState(initialProperties)

  useEffect(() => {
    const handleWishlistUpdate = (e: Event) => {
      const customEvent = e as CustomEvent<{ id: string; isFavorite: boolean }>
      if (customEvent.detail && !customEvent.detail.isFavorite) {
        setList(prev => prev.filter(item => item.id !== customEvent.detail.id))
      }
    }
    
    window.addEventListener('wishlist-updated', handleWishlistUpdate)
    return () => {
      window.removeEventListener('wishlist-updated', handleWishlistUpdate)
    }
  }, [])

  return (
    <div className="space-y-6 text-left">
      <div>
        <h3 className="text-base font-black text-slate-800">Bất động sản yêu thích</h3>
        <p className="text-[11px] text-slate-500 font-semibold">Danh sách bất động sản bạn đã lưu quan tâm.</p>
      </div>

      {list.length > 0 ? (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          {list.map(p => (
            <div key={p.id} className="relative group">
              <PropertyCard property={p} isFavoriteInitial={true} />
            </div>
          ))}
        </div>
      ) : (
        <div className="text-center py-12 bg-white border border-slate-100 rounded-3xl text-slate-400 font-semibold text-xs">
          Bạn chưa lưu bất kỳ bất động sản nào.
        </div>
      )}
    </div>
  )
}
