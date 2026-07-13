'use client'

import { useEffect, useRef } from 'react'
import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'

interface MapPickerProps {
  latitude: number
  longitude: number
  onCoordinatesChange: (latitude: number, longitude: number) => void
}

export default function MapPicker({ latitude, longitude, onCoordinatesChange }: MapPickerProps) {
  const containerRef = useRef<HTMLDivElement>(null)
  const mapRef = useRef<maplibregl.Map | null>(null)
  const markerRef = useRef<maplibregl.Marker | null>(null)

  // 1. Initial setup
  useEffect(() => {
    if (!containerRef.current || mapRef.current) return

    const map = new maplibregl.Map({
      container: containerRef.current,
      style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
      center: [longitude, latitude],
      zoom: 14,
      scrollZoom: true
    })

    map.addControl(new maplibregl.NavigationControl(), 'top-right')

    // Create a draggable marker
    const marker = new maplibregl.Marker({
      draggable: true,
      color: '#ef4444' // Red color for pin
    })
      .setLngLat([longitude, latitude])
      .addTo(map)

    marker.on('dragend', () => {
      const lngLat = marker.getLngLat()
      onCoordinatesChange(lngLat.lat, lngLat.lng)
    })

    // Click map to reposition marker
    map.on('click', (e) => {
      marker.setLngLat(e.lngLat)
      onCoordinatesChange(e.lngLat.lat, e.lngLat.lng)
    })

    mapRef.current = map
    markerRef.current = marker

    return () => {
      map.remove()
      mapRef.current = null
      markerRef.current = null
    }
  }, [])

  // 2. Sync marker and center position if latitude/longitude props change from parent
  useEffect(() => {
    if (mapRef.current && markerRef.current) {
      const markerLngLat = markerRef.current.getLngLat()
      
      // Prevent infinite triggering loops
      if (Math.abs(markerLngLat.lat - latitude) > 0.0001 || Math.abs(markerLngLat.lng - longitude) > 0.0001) {
        markerRef.current.setLngLat([longitude, latitude])
        mapRef.current.flyTo({
          center: [longitude, latitude],
          zoom: 14,
          speed: 1.2
        })
      }
    }
  }, [latitude, longitude])

  return (
    <div className="relative rounded-2xl overflow-hidden border border-slate-150 shadow-sm w-full h-[300px]">
      <div ref={containerRef} className="w-full h-full bg-slate-50" />
    </div>
  )
}
