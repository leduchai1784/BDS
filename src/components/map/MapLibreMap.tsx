'use client'

import { useEffect, useRef } from 'react'
import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'

interface MapLibreMapProps {
  properties: any[]
  activeId: string | null
  setActiveId: (id: string | null | ((prev: string | null) => string | null)) => void
  hoveredId: string | null
  initialLat?: number
  initialLng?: number
}

function updateMarkerStyle(
  el: HTMLElement,
  isActive: boolean,
  isHovered: boolean,
  priceLabel: string
) {
  el.style.whiteSpace = 'nowrap'
  el.style.width = 'max-content'
  el.style.display = 'inline-flex'
  
  // Safe base styling classes (blue capsule)
  el.classList.add('custom-price-marker', 'text-[11px]', 'font-black', 'px-2.5', 'py-1.5', 'rounded-full', 'shadow-lg', 'border-2', 'cursor-pointer', 'flex', 'items-center', 'justify-center', 'transition-colors', 'duration-200', 'text-white', 'border-white', 'bg-cyan-600', 'hover:bg-cyan-700')
  
  // Remove any active/hover styles if they were previously added
  el.classList.remove('bg-white', 'text-slate-800', 'border-cyan-600', 'scale-110', 'bg-cyan-700', 'scale-105')
  
  // Manage only z-index layers for active/hovered markers
  if (isActive) {
    el.classList.add('z-30')
    el.classList.remove('z-20')
  } else if (isHovered) {
    el.classList.add('z-20')
    el.classList.remove('z-30')
  } else {
    el.classList.remove('z-20', 'z-30')
  }
  
  // Keep innerHTML strictly to the price label
  el.innerHTML = priceLabel
}

export default function MapLibreMap({
  properties,
  activeId,
  setActiveId,
  hoveredId,
  initialLat,
  initialLng
}: MapLibreMapProps) {
  const mapContainerRef = useRef<HTMLDivElement>(null)
  const mapInstanceRef = useRef<maplibregl.Map | null>(null)
  const markersRef = useRef<Record<string, maplibregl.Marker>>({})
  const geolocateRef = useRef<maplibregl.GeolocateControl | null>(null)

  // 1. Initialize MapLibre instance
  useEffect(() => {
    if (!mapContainerRef.current || mapInstanceRef.current) return

    const defaultCenter: [number, number] = initialLng && initialLat
      ? [initialLng, initialLat]
      : [106.6704, 10.7822]

    const map = new maplibregl.Map({
      container: mapContainerRef.current,
      style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
      center: defaultCenter,
      zoom: initialLng && initialLat ? 15.5 : 12.5
    })

    // Navigation and Geolocate Controls
    map.addControl(new maplibregl.NavigationControl(), 'top-right')

    const geolocate = new maplibregl.GeolocateControl({
      positionOptions: { enableHighAccuracy: true },
      trackUserLocation: true,
      showUserLocation: true
    })
    map.addControl(geolocate, 'top-right')

    mapInstanceRef.current = map
    geolocateRef.current = geolocate

    return () => {
      map.remove()
      mapInstanceRef.current = null
    }
  }, [])

  // 2. Render Markers on Property updates
  useEffect(() => {
    const map = mapInstanceRef.current
    if (!map) return

    // Clean existing markers
    Object.values(markersRef.current).forEach(m => m.remove())
    markersRef.current = {}

    if (properties.length === 0) return

    const bounds = new maplibregl.LngLatBounds()

    properties.forEach(p => {
      const lat = Number(p.latitude)
      const lng = Number(p.longitude)
      if (isNaN(lat) || isNaN(lng)) return

      bounds.extend([lng, lat])

      // Custom marker container
      const el = document.createElement('div')
      el.id = `map-marker-${p.id}`
      updateMarkerStyle(el, activeId === p.id, false, p.priceLabel)

      let imgUrl = p.imagePath || '/images/apartment_placeholder.png'
      if (imgUrl.startsWith('http://')) {
        imgUrl = imgUrl.replace('http://', 'https://')
      }
      const typeLabel = (p.propertyType === 'apartment' ? 'Căn hộ' :
                         p.propertyType === 'house' ? 'Nhà riêng' :
                         p.propertyType === 'office' ? 'Văn phòng' :
                         p.propertyType === 'premises' ? 'Mặt bằng' :
                         p.propertyType === 'room' ? 'Phòng trọ' : 'Bất động sản').toUpperCase()

      // Detailed popup HTML (Synchronized with Laravel markup)
      const popupHTML = `
        <div class="w-[240px] text-left relative bg-white">
          <!-- Image Container with Absolute Badges -->
          <div class="relative w-full h-28 overflow-hidden rounded-t-2xl">
            <a href="/property/${p.id}" class="block w-full h-full">
              <img src="${imgUrl}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
            </a>
            <!-- Property Type Badge -->
            <span class="absolute top-2 left-2 bg-[#0077bb] text-white text-[9px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">
              ${typeLabel}
            </span>
            <!-- Close Button Mock -->
            <button onclick="window.activeMapPopup?.remove()" class="absolute top-2 right-2 w-6 h-6 bg-white/95 hover:bg-white rounded-full flex items-center justify-center text-slate-600 hover:text-slate-900 shadow-md transition focus:outline-none z-10">
              <i class="fa-solid fa-xmark text-xs"></i>
            </button>
          </div>
          
          <!-- Body Info -->
          <div class="p-4 text-slate-800">
            <h4 class="text-[13px] font-black text-slate-800 line-clamp-1 hover:text-[#0077bb] transition mb-1 text-left">
              <a href="/property/${p.id}">${p.title}</a>
            </h4>
            <p class="text-[10px] font-semibold text-slate-400 truncate mb-3 text-left">
              ${p.address}
            </p>
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
              <span class="text-[13px] font-black text-[#0077bb]">${p.priceLabel}</span>
              <a href="/property/${p.id}" class="text-[10px] font-black text-[#0077bb] hover:underline flex items-center gap-0.5">
                Chi tiết <i class="fa-solid fa-arrow-right text-[8px]"></i>
              </a>
            </div>
          </div>
        </div>
      `

      const popup = new maplibregl.Popup({
        offset: 25,
        closeButton: false,
        closeOnClick: true,
        anchor: 'bottom'
      }).setHTML(popupHTML)

      // Create marker
      const marker = new maplibregl.Marker({ element: el })
        .setLngLat([lng, lat])
        .setPopup(popup)
        .addTo(map)

      // Listen for popup opening
      popup.on('open', () => {
        setActiveId(p.id)
        if (typeof window !== 'undefined') {
          ;(window as any).activeMapPopup = popup
        }
        // Close all other popups
        Object.entries(markersRef.current).forEach(([id, m]) => {
          if (id !== p.id && m.getPopup().isOpen()) {
            m.getPopup().remove()
          }
        })
      })

      // Listen for popup closing to reset active state
      popup.on('close', () => {
        setActiveId((current: string | null) => current === p.id ? null : current)
      })

      // Click on marker bubble triggers centering and popup, or toggle close if already active
      el.addEventListener('click', (e) => {
        e.stopPropagation()
        if (popup.isOpen()) {
          popup.remove()
          setActiveId(null)
        } else {
          setActiveId(p.id)
          map.flyTo({ center: [lng, lat], zoom: 14.5, duration: 600 })
        }
      })

      markersRef.current[p.id] = marker
    })

    // Fit bounds or focus active marker
    if (activeId && markersRef.current[activeId]) {
      const activeMarker = markersRef.current[activeId]
      const coords = activeMarker.getLngLat()
      map.flyTo({ center: coords, zoom: 15, duration: 800 })
      if (!activeMarker.getPopup().isOpen()) {
        activeMarker.togglePopup()
      }
    } else if (initialLng && initialLat) {
      map.flyTo({ center: [initialLng, initialLat], zoom: 15.5, duration: 800 })
    } else if (properties.length > 0) {
      map.fitBounds(bounds, {
        padding: { top: 80, bottom: 80, left: 50, right: 50 },
        maxZoom: 14,
        duration: 800
      })
    }
  }, [properties, initialLat, initialLng, activeId])

  // 3. Highlight marker on Hover change
  useEffect(() => {
    // Reset all markers styles
    Object.entries(markersRef.current).forEach(([id, marker]) => {
      const el = marker.getElement()
      const isActive = activeId === id
      const isHovered = hoveredId === id

      const property = properties.find(p => p.id === id)
      const priceLabel = property ? property.priceLabel : ''

      updateMarkerStyle(el, isActive, isHovered, priceLabel)
    })
  }, [hoveredId, activeId, properties])

  // 4. Center map and open popup on Active ID change (from sidebar click)
  useEffect(() => {
    if (!activeId) return
    const map = mapInstanceRef.current
    const marker = markersRef.current[activeId]
    if (!map || !marker) return

    const coords = marker.getLngLat()
    map.flyTo({ center: coords, zoom: 14.5, duration: 600 })
    
    // Close all other popups
    Object.entries(markersRef.current).forEach(([id, m]) => {
      if (id !== activeId && m.getPopup().isOpen()) {
        m.getPopup().remove()
      }
    })

    // Open the popup if not already opened
    if (!marker.getPopup().isOpen()) {
      marker.togglePopup()
    }
  }, [activeId])

  return (
    <div className="relative w-full h-full bg-slate-100">
      <div ref={mapContainerRef} className="w-full h-full" />
    </div>
  )
}
