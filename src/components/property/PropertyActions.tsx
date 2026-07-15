'use client'

import { useState, useEffect } from 'react'
import { useSession } from 'next-auth/react'
import { toast } from 'sonner'

interface PropertyActionsProps {
  propertyId: string
  propertyTitle: string
  isFavoriteInitial: boolean
}

export default function PropertyActions({
  propertyId,
  propertyTitle,
  isFavoriteInitial
}: PropertyActionsProps) {
  const { status } = useSession()
  const [liked, setLiked] = useState(isFavoriteInitial)
  const [isProcessing, setIsProcessing] = useState(false)

  // Sync initial favorite status
  useEffect(() => {
    setLiked(isFavoriteInitial)
  }, [isFavoriteInitial])

  // Sync from localStorage if guest
  useEffect(() => {
    if (status === 'unauthenticated') {
      try {
        const saved = localStorage.getItem('guest_wishlist')
        if (saved) {
          const wishlist = JSON.parse(saved)
          setLiked(wishlist.includes(propertyId))
        }
      } catch (e) {
        console.error(e)
      }
    }
  }, [status, propertyId])

  const toggleLike = async () => {
    if (isProcessing) return
    setIsProcessing(true)

    // Guest wishlist
    if (status !== 'authenticated') {
      try {
        const saved = localStorage.getItem('guest_wishlist')
        let wishlist: string[] = []
        if (saved) {
          wishlist = JSON.parse(saved)
        }

        let newLiked = false
        if (wishlist.includes(propertyId)) {
          wishlist = wishlist.filter(id => id !== propertyId)
          newLiked = false
          toast.success('Đã xóa khỏi danh sách yêu thích')
        } else {
          wishlist.push(propertyId)
          newLiked = true
          toast.success('Đã thêm vào danh sách yêu thích')
        }

        localStorage.setItem('guest_wishlist', JSON.stringify(wishlist))
        setLiked(newLiked)

        // Dispatch sync event
        window.dispatchEvent(new CustomEvent('wishlist-updated', {
          detail: { id: propertyId, isFavorite: newLiked }
        }))
      } catch (e) {
        console.error(e)
      } finally {
        setIsProcessing(false)
      }
      return
    }

    // Authenticated user wishlist
    try {
      const res = await fetch('/api/wishlist/toggle', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ property_id: propertyId })
      })
      const data = await res.json()
      if (res.ok && data.success) {
        setLiked(data.is_favorite)
        if (data.is_favorite) {
          toast.success('Đã thêm vào danh sách yêu thích')
        } else {
          toast.success('Đã xóa khỏi danh sách yêu thích')
        }
        window.dispatchEvent(new CustomEvent('wishlist-updated', {
          detail: { id: propertyId, isFavorite: data.is_favorite }
        }))
      }
    } catch (err) {
      console.error('Error toggling wishlist:', err)
      toast.error('Có lỗi xảy ra, vui lòng thử lại sau')
    } finally {
      setIsProcessing(false)
    }
  }

  const handleShare = () => {
    const shareUrl = window.location.href
    window.dispatchEvent(
      new CustomEvent('open-share-modal', {
        detail: {
          url: shareUrl,
          title: propertyTitle
        }
      })
    )
  }

  return (
    <div className="absolute top-6 right-6 flex items-center gap-2 z-10">
      {/* Favorite Button */}
      <button
        type="button"
        onClick={toggleLike}
        disabled={isProcessing}
        title={liked ? 'Đã thích' : 'Yêu thích'}
        className={`w-9 h-9 rounded-xl border flex items-center justify-center transition cursor-pointer active:scale-95 disabled:opacity-50 ${
          liked
            ? 'bg-rose-50 border-rose-250 text-rose-600 hover:bg-rose-100'
            : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50'
        }`}
      >
        {liked ? (
          <i className="fa-solid fa-heart text-sm text-rose-500 animate-pulse-once"></i>
        ) : (
          <i className="fa-regular fa-heart text-sm"></i>
        )}
      </button>

      {/* Share Button */}
      <button
        type="button"
        onClick={handleShare}
        title="Chia sẻ"
        className="w-9 h-9 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer active:scale-95"
      >
        <i className="fa-solid fa-share-nodes text-sm"></i>
      </button>
    </div>
  )
}
