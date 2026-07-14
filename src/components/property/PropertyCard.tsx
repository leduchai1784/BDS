'use client'

import { useState, useEffect } from 'react'
import Link from 'next/link'
import { useSession } from 'next-auth/react'

export interface PropertyCardProps {
  property: {
    id: string
    title: string
    transactionType?: string | null
    priceLabel: string
    price: number | bigint | string
    area: number
    bedroom: number
    bathroom: number
    floors?: number | null
    address: string
    ward?: string | null
    district: string
    city: string
    isVip?: boolean
    isNew?: boolean
    propertyType?: string | null
    imagePath?: string | null
  }
  isFavoriteInitial?: boolean
}

export default function PropertyCard({ property, isFavoriteInitial = false }: PropertyCardProps) {
  const { status } = useSession()
  
  const [liked, setLiked] = useState(isFavoriteInitial)
  const [isProcessing, setIsProcessing] = useState(false)

  // Sync initial favorite status
  useEffect(() => {
    setLiked(isFavoriteInitial)
  }, [isFavoriteInitial])

  const getImageUrl = () => {
    if (property.imagePath) {
      if (property.imagePath.startsWith('http://') || property.imagePath.startsWith('https://')) {
        return property.imagePath
      }
      return `/${property.imagePath}`
    }
    return '/images/apartment_placeholder.png' // Default placeholder
  }

  const getPropertyTypeLabel = () => {
    const type = property.propertyType || ''
    switch (type) {
      case 'apartment': return 'Chung cư'
      case 'house': return 'Nhà nguyên căn'
      case 'room': return 'Phòng trọ'
      case 'land': return 'Đất nền'
      case 'premises': return 'Mặt bằng'
      case 'office': return 'Văn phòng'
      case 'warehouse': return 'Nhà kho'
      default: return 'Căn hộ dịch vụ'
    }
  }

  const isSale = () => {
    const label = property.priceLabel.toLowerCase()
    return !label.includes('tháng') && !label.includes('triệu/m') && !label.includes('thang')
  }

  const toggleLike = async () => {
    if (isProcessing) return
    setIsProcessing(true)

    // Check if not logged in, we save to local storage
    if (status !== 'authenticated') {
      try {
        const saved = localStorage.getItem('guest_wishlist')
        let wishlist: string[] = []
        if (saved) {
          wishlist = JSON.parse(saved)
        }
        
        let newLiked = false
        if (wishlist.includes(property.id)) {
          wishlist = wishlist.filter(id => id !== property.id)
          newLiked = false
        } else {
          wishlist.push(property.id)
          newLiked = true
        }
        
        localStorage.setItem('guest_wishlist', JSON.stringify(wishlist))
        setLiked(newLiked)
        setIsProcessing(false)
        
        // Dispatch custom event to notify components
        window.dispatchEvent(new CustomEvent('wishlist-updated', {
          detail: { id: property.id, isFavorite: newLiked }
        }))
        return
      } catch (e) {
        console.error(e)
        setIsProcessing(false)
        return
      }
    }

    try {
      const res = await fetch('/api/wishlist/toggle', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ property_id: property.id })
      })
      const data = await res.json()
      if (res.ok && data.success) {
        setLiked(data.is_favorite)
        window.dispatchEvent(new CustomEvent('wishlist-updated', {
          detail: { id: property.id, isFavorite: data.is_favorite }
        }))
      }
    } catch (err) {
      console.error('Error toggling wishlist:', err)
    } finally {
      setIsProcessing(false)
    }
  }

  const handleShare = () => {
    const shareUrl = `${window.location.origin}/property/${property.id}`
    window.dispatchEvent(
      new CustomEvent('open-share-modal', {
        detail: {
          url: shareUrl,
          title: property.title
        }
      })
    )
  }

  return (
    <div className="group bg-white rounded-[24px] overflow-hidden border border-slate-100 hover:shadow-2xl hover:shadow-slate-200/80 transform hover:-translate-y-1.5 transition-all duration-350 flex flex-col h-full text-left">
      
      {/* 1. Hình ảnh (Image) */}
      <div className="relative h-48 w-full overflow-hidden bg-slate-100 flex-shrink-0">
        <Link href={`/property/${property.id}`} className="absolute inset-0 block">
          <img 
            src={getImageUrl()} 
            alt={property.title} 
            className="w-full h-full object-cover object-center group-hover:scale-108 transition-transform duration-500 ease-out"
            onError={(e) => {
              e.currentTarget.src = '/images/apartment_placeholder.png'
            }}
          />
        </Link>

        {/* VIP/NEW/Sale/Rent Badges Overlay */}
        <div className="absolute top-4 left-4 flex flex-col gap-1.5 z-10">
          {isSale() ? (
            <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-orange-500 text-white shadow-md shadow-orange-500/20">
              <i className="fa-solid fa-tags mr-1"></i> BÁN
            </span>
          ) : (
            <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-primary text-white shadow-md shadow-blue-500/20">
              <i className="fa-solid fa-key mr-1"></i> THUÊ
            </span>
          )}
          {property.isVip && (
            <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-red-500 text-white shadow-md shadow-red-500/20">
              <i className="fa-solid fa-crown mr-1"></i> VIP
            </span>
          )}
          {property.isNew && (
            <span className="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-green-500 text-white shadow-md shadow-green-500/20">
              <i className="fa-solid fa-sparkles mr-1"></i> MỚI
            </span>
          )}
        </div>

        {/* Buttons Overlay (Wishlist & Share) */}
        <div className="absolute top-4 right-4 z-10 flex items-center gap-2">
          {/* Share Button */}
          <button 
            onClick={handleShare}
            type="button" 
            className="w-9 h-9 rounded-full flex items-center justify-center border border-slate-100 bg-white/80 hover:bg-white text-slate-400 hover:text-primary shadow-sm transition active:scale-90 cursor-pointer"
            title="Chia sẻ tin đăng"
          >
            <i className="fa-solid fa-share-nodes text-xs"></i>
          </button>

          {/* Wishlist Button */}
          <button 
            onClick={toggleLike}
            disabled={isProcessing}
            type="button" 
            className={`w-9 h-9 rounded-full flex items-center justify-center border shadow-sm transition active:scale-90 cursor-pointer ${
              liked ? 'bg-red-50 text-red-500 border-red-100' : 'bg-white/80 hover:bg-white text-slate-400 border-slate-100'
            }`}
          >
            <i className={`fa-solid fa-heart transition ${liked ? 'text-red-500' : 'text-slate-400'}`}></i>
          </button>
        </div>

        {/* Property Type Overlay Tag */}
        <div className="absolute bottom-4 left-4 z-10">
          <span className="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-900/85 backdrop-blur-md text-white">
            {getPropertyTypeLabel()}
          </span>
        </div>
      </div>

      {/* Nội dung thông tin card */}
      <div className="p-4 flex flex-col flex-grow">
        {/* 2. Giá thuê (Price) */}
        <div className="flex items-center justify-between mb-2">
          <span className="text-xl font-extrabold text-primary tracking-tight">
            {property.priceLabel}
          </span>
          <span className="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
            {property.area} m²
          </span>
        </div>

        {/* 3. Tiêu đề (Title) */}
        <h3 className="text-sm font-bold text-slate-800 line-clamp-2 hover:text-primary transition duration-150 mb-2 leading-snug flex-grow">
          <Link href={`/property/${property.id}`}>{property.title}</Link>
        </h3>

        {/* 4. Địa chỉ (Address) */}
        <div className="flex items-center text-slate-500 text-xs font-semibold mb-3">
          <i className="fa-solid fa-location-dot text-slate-400 mr-2 text-base"></i>
          <span className="truncate">{property.address || `${property.district}, ${property.city}`}</span>
        </div>

        {/* 5. Thông tin chi tiết thu gọn (Property Specs) */}
        <div className="pt-3 border-t border-slate-100/80 mt-auto flex-shrink-0">
          <div className="flex items-center justify-between text-slate-600 text-xs px-1">
            {property.bedroom > 0 && (
              <div className="flex items-center space-x-1.5" title={`${property.bedroom} phòng ngủ`}>
                <i className="fa-solid fa-bed text-[15px] text-slate-400"></i>
                <span className="font-extrabold text-slate-700">{property.bedroom}</span>
              </div>
            )}
            
            {property.bathroom > 0 && (
              <div className="flex items-center space-x-1.5" title={`${property.bathroom} phòng tắm`}>
                <i className="fa-solid fa-bath text-[15px] text-slate-400"></i>
                <span className="font-extrabold text-slate-700">{property.bathroom}</span>
              </div>
            )}

            {property.floors && property.floors > 0 ? (
              <div className="flex items-center space-x-1.5" title={`${property.floors} tầng`}>
                <i className="fa-solid fa-layer-group text-[15px] text-slate-400"></i>
                <span className="font-extrabold text-slate-700">{property.floors}</span>
              </div>
            ) : null}

            {property.area > 0 && (
              <div className="flex items-center space-x-1.5" title={`Diện tích ${property.area} m²`}>
                <i className="fa-solid fa-crop-simple text-[15px] text-slate-400"></i>
                <span className="font-extrabold text-slate-700">{property.area}m²</span>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
