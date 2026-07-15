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
    <div className="grid grid-cols-2 gap-3 mb-5 border-t border-slate-100 pt-4">
      {/* Favorite Button */}
      <button
        type="button"
        onClick={toggleLike}
        disabled={isProcessing}
        className={`inline-flex items-center justify-center gap-2 py-2.5 rounded-2xl border text-xs font-bold transition cursor-pointer active:scale-97 disabled:opacity-50 ${
          liked
            ? 'bg-rose-50 border-rose-100 text-rose-600 hover:bg-rose-100/70'
            : 'bg-slate-50 border-slate-100 text-slate-600 hover:bg-slate-100/70'
        }`}
      >
        {liked ? (
          <i className="fa-solid fa-heart text-[13px] text-rose-500 animate-pulse-once"></i>
        ) : (
          <i className="fa-regular fa-heart text-[13px]"></i>
        )}
        <span>{liked ? 'Đã thích' : 'Yêu thích'}</span>
      </button>

      {/* Share Button */}
      <button
        type="button"
        onClick={handleShare}
        className="inline-flex items-center justify-center gap-2 py-2.5 rounded-2xl bg-slate-50 hover:bg-slate-100/70 border border-slate-100 text-slate-600 transition font-bold text-xs cursor-pointer active:scale-97"
      >
        <i className="fa-solid fa-share-nodes text-[13px] text-slate-500"></i>
        <span>Chia sẻ</span>
      </button>
    </div>
  )
}
