'use client'

import { useState, useMemo, useEffect, useRef } from 'react'
import MapSidebar from './MapSidebar'
import MapFilterBar from './MapFilterBar'
import dynamic from 'next/dynamic'

// Dynamically import MapLibre map to avoid SSR errors
const MapLibreMap = dynamic(() => import('./MapLibreMap'), {
  ssr: false,
  loading: () => (
    <div className="w-full h-full flex items-center justify-center bg-slate-50">
      <div className="text-center">
        <svg className="animate-spin h-8 w-8 text-primary mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span className="text-xs text-slate-500 font-bold">Đang tải bản đồ...</span>
      </div>
    </div>
  )
})

interface MapPageClientProps {
  initialProperties: any[]
}

export default function MapPageClient({ initialProperties }: MapPageClientProps) {
  // Sync map states
  const [activeId, setActiveId] = useState<string | null>(null)
  const [hoveredId, setHoveredId] = useState<string | null>(null)

  // Filter states
  const [keyword, setKeyword] = useState('')
  const [purpose, setPurpose] = useState('')
  const [propertyType, setPropertyType] = useState('')
  const [price, setPrice] = useState('')
  const [bedrooms, setBedrooms] = useState('')
  const [area, setArea] = useState('')

  // Advanced filter states
  const [bathrooms, setBathrooms] = useState('')
  const [furniture, setFurniture] = useState('')
  const [direction, setDirection] = useState('')

  // Mobile scroll synchronization controls
  const [ignoreMobileScroll, setIgnoreMobileScroll] = useState(false)
  const mobileScrollTimeoutRef = useRef<NodeJS.Timeout | null>(null)

  // Sync initial state values with URL parameters
  useState(() => {
    if (typeof window !== 'undefined') {
      const params = new URLSearchParams(window.location.search)
      setKeyword(params.get('keyword') || params.get('search') || '')
      setPurpose(params.get('purpose') || '')
      setPropertyType(params.get('property_type') || '')
      setPrice(params.get('price') || '')
      setBedrooms(params.get('bedrooms') || '')
      setArea(params.get('area') || '')
      setBathrooms(params.get('bathrooms') || '')
      setFurniture(params.get('furniture') || '')
      setDirection(params.get('direction') || '')
    }
  })

  // Filter list in memory for maximum speed and interactivity
  const filteredList = useMemo(() => {
    return initialProperties.filter(item => {
      // 1. Keyword search
      if (keyword) {
        const kw = keyword.toLowerCase()
        const match = 
          item.title?.toLowerCase().includes(kw) ||
          item.address?.toLowerCase().includes(kw) ||
          item.district?.toLowerCase().includes(kw) ||
          item.city?.toLowerCase().includes(kw)
        if (!match) return false
      }

      // 2. Purpose (rent / sale)
      if (purpose) {
        const isRent = item.priceLabel.toLowerCase().includes('tháng') || item.priceLabel.toLowerCase().includes('thang')
        const itemPurpose = isRent ? 'rent' : 'sale'
        if (itemPurpose !== purpose) return false
      }

      // 3. Property Type
      if (propertyType) {
        if (item.propertyType !== propertyType) return false
      }

      // 4. Price
      if (price) {
        const pVal = item.price
        if (price === 'under_3' && pVal >= 3000000) return false
        if (price === '3_5' && (pVal < 3000000 || pVal > 5000000)) return false
        if (price === '5_10' && (pVal < 5000000 || pVal > 10000000)) return false
        if (price === '10_20' && (pVal < 10000000 || pVal > 20000000)) return false
        if (price === 'above_20' && pVal <= 20000000) return false
        if (price === 'under_1b' && pVal >= 1000000000) return false
        if (price === '1b_3b' && (pVal < 1000000000 || pVal > 3000000000)) return false
        if (price === '3b_5b' && (pVal < 3000000000 || pVal > 5000000000)) return false
        if (price === '5b_10b' && (pVal < 5000000000 || pVal > 10000000000)) return false
        if (price === 'above_10b' && pVal <= 10000000000) return false
      }

      // 5. Bedrooms
      if (bedrooms) {
        const bCount = parseInt(bedrooms, 10)
        if (bedrooms === '4_plus') {
          if (item.bedroom < 4) return false
        } else if (!isNaN(bCount) && item.bedroom !== bCount) {
          return false
        }
      }

      // 6. Area
      if (area) {
        const aVal = item.area
        if (area === 'under_30' && aVal >= 30) return false
        if (area === '30_50' && (aVal < 30 || aVal > 50)) return false
        if (area === '50_80' && (aVal < 50 || aVal > 80)) return false
        if (area === '80_120' && (aVal < 80 || aVal > 120)) return false
        if (area === 'above_120' && aVal <= 120) return false
      }

      // 7. Bathrooms
      if (bathrooms) {
        const btCount = parseInt(bathrooms, 10)
        if (bathrooms === '3_plus') {
          if (item.bathroom < 3) return false
        } else if (!isNaN(btCount) && item.bathroom !== btCount) {
          return false
        }
      }

      // 8. Furniture
      if (furniture) {
        if (item.furniture !== furniture) return false
      }

      // 9. Direction
      if (direction) {
        if (item.direction !== direction) return false
      }

      return true
    })
  }, [initialProperties, keyword, purpose, propertyType, price, bedrooms, area, bathrooms, furniture, direction])

  const handleReset = () => {
    setKeyword('')
    setPurpose('')
    setPropertyType('')
    setPrice('')
    setBedrooms('')
    setArea('')
    setBathrooms('')
    setFurniture('')
    setDirection('')
    setActiveId(null)
  }

  // Handle activeId change to scroll mobile card slider element
  useEffect(() => {
    if (!activeId) return

    // Mobile card scroll synchronization
    const mobileCard = document.getElementById(`card-mobile-${activeId}`)
    if (mobileCard) {
      setIgnoreMobileScroll(true)
      mobileCard.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' })
      
      const timeout = setTimeout(() => {
        setIgnoreMobileScroll(false)
      }, 800)
      return () => clearTimeout(timeout)
    }
  }, [activeId])

  // Listen to mobile sliding container scroll events to snap active markers
  const handleMobileScroll = () => {
    if (ignoreMobileScroll) return

    if (mobileScrollTimeoutRef.current) {
      clearTimeout(mobileScrollTimeoutRef.current)
    }

    mobileScrollTimeoutRef.current = setTimeout(() => {
      const container = document.getElementById('mobile-cards-slider')
      if (!container) return

      const containerCenter = container.scrollLeft + (container.offsetWidth / 2)
      const cards = container.children
      let closestCard: HTMLElement | null = null
      let minDistance = Infinity

      for (let i = 0; i < cards.length; i++) {
        const card = cards[i] as HTMLElement
        if (card.style.display === 'none') continue

        const cardCenter = card.offsetLeft + (card.offsetWidth / 2)
        const distance = Math.abs(containerCenter - cardCenter)

        if (distance < minDistance) {
          minDistance = distance
          closestCard = card
        }
      }

      if (closestCard) {
        const cardId = closestCard.id.replace('card-mobile-', '')
        if (cardId && cardId !== activeId) {
          setActiveId(cardId)
        }
      }
    }, 100)
  }

  return (
    <div className="flex flex-col h-screen pt-[72px] overflow-hidden bg-slate-50">
      
      {/* 1. Filter Capsule Bar (always floating on top) */}
      <div className="px-4 py-2 flex-shrink-0 z-30">
        <MapFilterBar 
          keyword={keyword}
          setKeyword={setKeyword}
          purpose={purpose}
          setPurpose={setPurpose}
          propertyType={propertyType}
          setPropertyType={setPropertyType}
          price={price}
          setPrice={setPrice}
          bedrooms={bedrooms}
          setBedrooms={setBedrooms}
          area={area}
          setArea={setArea}
          bathrooms={bathrooms}
          setBathrooms={setBathrooms}
          furniture={furniture}
          setFurniture={setFurniture}
          direction={direction}
          setDirection={setDirection}
          onReset={handleReset}
        />
      </div>

      {/* 2. Main Flex Workspace (Sidebar + Map) */}
      <div className="flex flex-row flex-grow w-full overflow-hidden relative">
        {/* Sidebar */}
        <MapSidebar 
          properties={filteredList}
          activeId={activeId}
          setActiveId={setActiveId}
          hoveredId={hoveredId}
          setHoveredId={setHoveredId}
        />

        {/* Map Canvas & Mobile Slider Container */}
        <div className="flex-grow h-full relative z-10">
          <MapLibreMap 
            properties={filteredList}
            activeId={activeId}
            setActiveId={setActiveId}
            hoveredId={hoveredId}
          />

          {/* Floating Bottom Mobile Card Slider */}
          {filteredList.length > 0 && (
            <div className="absolute bottom-6 left-0 right-0 z-20 md:hidden px-4">
              <div 
                id="mobile-cards-slider"
                className="flex space-x-3.5 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-none scroll-smooth [&::-webkit-scrollbar]:hidden"
                onScroll={handleMobileScroll}
              >
                {filteredList.map(property => {
                  const isSelected = activeId === property.id
                  return (
                    <div 
                      key={property.id}
                      id={`card-mobile-${property.id}`}
                      onClick={() => {
                        setActiveId(property.id)
                      }}
                      className={`flex-shrink-0 w-[285px] bg-white rounded-2xl p-3 border flex gap-3 text-left snap-center scroll-ml-4 transition-all duration-300 ${
                        isSelected ? 'border-primary ring-1 ring-primary shadow-2xl scale-[0.99]' : 'border-slate-100/80 shadow-xl'
                      }`}
                    >
                      {/* Mobile Thumbnail */}
                      <div className="w-24 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-slate-100 relative">
                        <img 
                          src={property.imagePath || '/images/apartment_placeholder.png'} 
                          alt={property.title} 
                          className="w-full h-full object-cover"
                        />
                        {property.isVip && (
                          <span className="absolute top-1 left-1 inline-flex items-center px-1 rounded text-[7px] font-black uppercase bg-red-500 text-white">
                            VIP
                          </span>
                        )}
                      </div>

                      {/* Details */}
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
                          <h4 className="text-[12px] font-bold line-clamp-2 leading-snug text-slate-800">
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
                })}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
