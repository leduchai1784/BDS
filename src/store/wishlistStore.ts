import { create } from 'zustand'
import { persist } from 'zustand/middleware'

interface WishlistState {
  wishlist: string[]
  addWishlist: (id: string) => void
  removeWishlist: (id: string) => void
  toggleWishlist: (id: string) => void
  setWishlist: (ids: string[]) => void
}

export const useWishlistStore = create<WishlistState>()(
  persist(
    (set) => ({
      wishlist: [],
      addWishlist: (id) =>
        set((state) => ({
          wishlist: state.wishlist.includes(id) ? state.wishlist : [...state.wishlist, id],
        })),
      removeWishlist: (id) =>
        set((state) => ({
          wishlist: state.wishlist.filter((item) => item !== id),
        })),
      toggleWishlist: (id) =>
        set((state) => {
          const isLiked = state.wishlist.includes(id)
          const newWishlist = isLiked
            ? state.wishlist.filter((item) => item !== id)
            : [...state.wishlist, id]
          
          // Dispatch window event to synchronize other tabs / components
          setTimeout(() => {
            window.dispatchEvent(new CustomEvent('wishlist-updated'))
          }, 0)

          return { wishlist: newWishlist }
        }),
      setWishlist: (ids) => set({ wishlist: ids }),
    }),
    {
      name: 'guest_wishlist', // localStorage key
    }
  )
)
