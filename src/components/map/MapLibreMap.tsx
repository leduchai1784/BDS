'use client'

import { useEffect, useRef } from 'react'
import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'

interface MapLibreMapProps {
  properties: any[]
  activeId: string | null
  setActiveId: (id: string | null) => void
  hoveredId: string | null
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
  
  // Preserve MapLibre's internal classes so they are not wiped out by className assignment
  const isMapLibre = el.classList.contains('maplibregl-marker') || el.className.includes('maplibregl-marker')
  const anchorClass = Array.from(el.classList).find(c => c.startsWith('maplibregl-marker-anchor-'))
  
  const classes = ['custom-price-marker', 'text-[11px]', 'font-black', 'px-2.5', 'py-1.5', 'rounded-full', 'shadow-lg', 'border-2', 'cursor-pointer', 'flex', 'items-center', 'justify-center', 'transition-colors', 'duration-200']
  
  if (isMapLibre) classes.push('maplibregl-marker')
  if (anchorClass) classes.push(anchorClass)
  
  if (isActive) {
    classes.push('bg-white', 'text-slate-800', 'border-cyan-600', 'scale-110', 'z-30')
    el.innerHTML = `<span class="flex items-center text-xs font-black"><i class="fa-solid fa-circle-check text-emerald-500 mr-1 text-[13px]"></i>${priceLabel}</span>`
  } else if (isHovered) {
    classes.push('text-white', 'border-white', 'bg-cyan-700', 'scale-105', 'z-20')
    el.innerHTML = priceLabel
  } else {
    classes.push('text-white', 'border-white', 'bg-cyan-600', 'hover:bg-cyan-700')
    el.innerHTML = priceLabel
  }
  
  el.className = classes.join(' ')
}

export default function MapLibreMap({
  properties,
  activeId,
  setActiveId,
  hoveredId
}: MapLibreMapProps) {
  const mapContainerRef = useRef<HTMLDivElement>(null)
  const mapInstanceRef = useRef<maplibregl.Map | null>(null)
  const markersRef = useRef<Record<string, maplibregl.Marker>>({})
  const geolocateRef = useRef<maplibregl.GeolocateControl | null>(null)

  // 1. Initialize MapLibre instance
  useEffect(() => {
    if (!mapContainerRef.current || mapInstanceRef.current) return

    const map = new maplibregl.Map({
      container: mapContainerRef.current,
      style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
      center: [106.6704, 10.7822], // Default center to HCMC
      zoom: 12.5
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

      const imgUrl = p.imagePath || '/images/apartment_placeholder.png'

      // Detailed popup HTML
      const popupHTML = `
        <div class="w-[240px] text-left relative bg-white">
          <div class="relative w-full h-28 overflow-hidden rounded-t-2xl">
            <a href="/property/${p.id}" class="block w-full h-full">
              <img src="${imgUrl}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
            </a>
          </div>
          
          <div class="p-3.5 text-slate-800">
            <h4 class="text-[13px] font-black text-slate-850 line-clamp-1 hover:text-cyan-600 transition mb-1 text-left">
              <a href="/property/${p.id}">${p.title}</a>
            </h4>
            <p class="text-[10px] font-semibold text-slate-400 truncate mb-3 text-left">
              ${p.address}
            </p>
            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
              <span class="text-[13px] font-black text-cyan-600">${p.priceLabel}</span>
              <a href="/property/${p.id}" class="text-[10px] font-black text-cyan-600 hover:underline flex items-center gap-0.5">
                Chi tiết <i class="fa-solid fa-arrow-right text-[8px]"></i>
              </a>
            </div>
          </div>
        </div>
      `

      const popup = new maplibregl.Popup({
        offset: 35,
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
        // Close all other popups
        Object.entries(markersRef.current).forEach(([id, m]) => {
          if (id !== p.id && m.getPopup().isOpen()) {
            m.getPopup().remove()
          }
        })
      })

      // Click on marker bubble triggers centering and popup
      el.addEventListener('click', (e) => {
        e.stopPropagation()
        setActiveId(p.id)
        map.flyTo({ center: [lng, lat], zoom: 14.5, duration: 600 })
      })

      markersRef.current[p.id] = marker
    })

    // Fit bounds to show all markers
    map.fitBounds(bounds, {
      padding: { top: 80, bottom: 80, left: 50, right: 50 },
      maxZoom: 14,
      duration: 800
    })
  }, [properties])

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
      
      // Force MapLibre to recalculate positioning offset based on new element dimensions
      marker.setLngLat(marker.getLngLat())
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
