'use client'

import { useState, useMemo } from 'react'
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

  return (
    <div className="flex flex-col h-[calc(100vh-70px)] pt-[70px] overflow-hidden bg-slate-50">
      
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
      <div className="flex flex-col lg:flex-row flex-grow w-full overflow-hidden">
        {/* Sidebar */}
        <MapSidebar 
          properties={filteredList}
          activeId={activeId}
          setActiveId={setActiveId}
          hoveredId={hoveredId}
          setHoveredId={setHoveredId}
        />

        {/* Map Canvas */}
        <div className="flex-grow h-full relative z-10">
          <MapLibreMap 
            properties={filteredList}
            activeId={activeId}
            setActiveId={setActiveId}
            hoveredId={hoveredId}
          />
        </div>
      </div>
    </div>
  )
}
