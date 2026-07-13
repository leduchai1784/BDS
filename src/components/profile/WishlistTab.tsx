'use client'

import { useState } from 'react'
import PropertyCard from '@/components/property/PropertyCard'

interface WishlistTabProps {
  initialProperties: any[]
}

export default function WishlistTab({ initialProperties }: WishlistTabProps) {
  const [list, setList] = useState(initialProperties)

  const handleRemove = async (propertyId: string) => {
    try {
      const res = await fetch('/api/wishlist/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: propertyId })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setList(prev => prev.filter(item => item.id !== propertyId))
      }
    } catch (err) {
      console.error('Error removing wishlist item:', err)
    }
  }

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
              <PropertyCard property={p} />
              
              {/* Quick Remove Button Overlay */}
              <button 
                type="button"
                onClick={() => handleRemove(p.id)}
                className="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/90 hover:bg-red-50 text-red-500 hover:text-red-600 border border-slate-200/50 flex items-center justify-center shadow-md transition cursor-pointer active:scale-95"
                title="Bỏ lưu"
              >
                <i className="fa-solid fa-heart-crack text-xs" />
              </button>
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
