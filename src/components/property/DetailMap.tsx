'use client'

import { useEffect, useRef } from 'react'
import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'

interface DetailMapProps {
  latitude: number
  longitude: number
  title: string
}

export default function DetailMap({ latitude, longitude, title }: DetailMapProps) {
  const containerRef = useRef<HTMLDivElement>(null)
  const mapRef = useRef<maplibregl.Map | null>(null)

  useEffect(() => {
    if (!containerRef.current || mapRef.current) return

    const lat = Number(latitude)
    const lng = Number(longitude)
    if (isNaN(lat) || isNaN(lng)) return

    const map = new maplibregl.Map({
      container: containerRef.current,
      style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
      center: [lng, lat],
      zoom: 14.5,
      scrollZoom: true
    })

    map.addControl(new maplibregl.NavigationControl(), 'top-right')

    // Custom HTML marker
    const el = document.createElement('div')
    el.className = 'w-9 h-9 rounded-full bg-primary border-4 border-white shadow-xl flex items-center justify-center cursor-pointer transition duration-200'
    el.innerHTML = '<i class="fa-solid fa-house-chimney text-xs text-white"></i>'

    const popup = new maplibregl.Popup({
      offset: 15,
      closeOnClick: false,
      focusAfterOpen: false
    }).setHTML(`
      <div class="text-[11px] font-extrabold text-slate-850 p-1 leading-snug text-left">
        ${title}
      </div>
    `)

    const marker = new maplibregl.Marker({ element: el })
      .setLngLat([lng, lat])
      .setPopup(popup)
      .addTo(map)

    // Automatically open popup
    marker.togglePopup()

    mapRef.current = map

    return () => {
      map.remove()
      mapRef.current = null
    }
  }, [latitude, longitude, title])

  return (
    <div className="relative rounded-2xl overflow-hidden border border-slate-150 shadow-sm w-full h-[250px] sm:h-[320px]">
      <div ref={containerRef} className="w-full h-full bg-slate-50" />
    </div>
  )
}
