'use client'

import { useState, useEffect, useRef, FormEvent, KeyboardEvent } from 'react'
import { useRouter } from 'next/navigation'

interface Suggestion {
  type: 'city' | 'district' | 'ward' | 'address' | 'property'
  label: string
  sublabel: string
  value: string
  id?: string
}

export default function Hero() {
  const router = useRouter()
  
  const [query, setQuery] = useState('')
  const [suggestions, setSuggestions] = useState<Suggestion[]>([])
  const [isOpen, setIsOpen] = useState(false)
  const [activeIndex, setActiveIndex] = useState(-1)
  const [locating, setLocating] = useState(false)
  
  const searchFormRef = useRef<HTMLFormElement>(null)
  const debounceTimeoutRef = useRef<NodeJS.Timeout | null>(null)

  // Fetch suggestions with debounce
  useEffect(() => {
    if (debounceTimeoutRef.current) {
      clearTimeout(debounceTimeoutRef.current)
    }

    if (query.trim().length < 2) {
      setSuggestions([])
      setIsOpen(false)
      return
    }

    debounceTimeoutRef.current = setTimeout(async () => {
      try {
        const res = await fetch(`/api/properties/autocomplete?q=${encodeURIComponent(query)}`)
        if (res.ok) {
          const data = await res.json()
          setSuggestions(data)
          setIsOpen(data.length > 0)
          setActiveIndex(-1)
        }
      } catch (error) {
        console.error('Error fetching suggestions:', error)
      }
    }, 250)

    return () => {
      if (debounceTimeoutRef.current) {
        clearTimeout(debounceTimeoutRef.current)
      }
    }
  }, [query])

  const selectSuggestion = (sug: Suggestion) => {
    setQuery(sug.label)
    setIsOpen(false)
    if (sug.type === 'property' && sug.id) {
      router.push(`/property/${sug.id}`)
    } else if (sug.type === 'city') {
      router.push(`/map?city=${encodeURIComponent(sug.value)}`)
    } else if (sug.type === 'district') {
      router.push(`/map?district=${encodeURIComponent(sug.value)}`)
    } else if (sug.type === 'ward') {
      router.push(`/map?ward=${encodeURIComponent(sug.value)}`)
    } else {
      router.push(`/map?keyword=${encodeURIComponent(sug.value)}`)
    }
  }

  const handleKeyDown = (e: KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault()
      setActiveIndex(prev => (prev + 1) % suggestions.length)
    } else if (e.key === 'ArrowUp') {
      e.preventDefault()
      setActiveIndex(prev => (prev - 1 + suggestions.length) % suggestions.length)
    } else if (e.key === 'Enter') {
      e.preventDefault()
      if (activeIndex >= 0 && activeIndex < suggestions.length) {
        selectSuggestion(suggestions[activeIndex])
      } else {
        router.push(`/map?keyword=${encodeURIComponent(query)}`)
      }
    } else if (e.key === 'Escape') {
      setIsOpen(false)
    }
  }

  const handleSearchSubmit = (e: FormEvent) => {
    e.preventDefault()
    router.push(`/map?keyword=${encodeURIComponent(query)}`)
  }

  const getUserLocation = () => {
    if (!navigator.geolocation) {
      alert('Trình duyệt của bạn không hỗ trợ định vị vị trí.')
      return
    }
    setLocating(true)
    navigator.geolocation.getCurrentPosition(
      (position) => {
        setLocating(false)
        const lat = position.coords.latitude
        const lng = position.coords.longitude
        router.push(`/map?lat=${lat}&lng=${lng}`)
      },
      (error) => {
        setLocating(false)
        console.error('Error getting location:', error)
        alert('Không thể lấy vị trí hiện tại của bạn. Vui lòng cấp quyền truy cập vị trí cho trang web.')
      },
      { enableHighAccuracy: true, timeout: 6000 }
    )
  }

  const getSuggestionTypeLabel = (type: string) => {
    switch (type) {
      case 'city': return 'Tỉnh thành'
      case 'district': return 'Quận huyện'
      case 'ward': return 'Phường xã'
      case 'address': return 'Địa chỉ'
      default: return 'Bất động sản'
    }
  }

  const getSuggestionTypeClass = (type: string) => {
    switch (type) {
      case 'city': return 'bg-blue-50 text-blue-600'
      case 'district': return 'bg-indigo-50 text-indigo-600'
      case 'ward': return 'bg-purple-50 text-purple-600'
      case 'address': return 'bg-teal-50 text-teal-600'
      default: return 'bg-amber-50 text-amber-600'
    }
  }

  return (
    <section className="relative z-30 min-h-[90vh] flex items-center justify-center bg-slate-900 text-white pt-24 pb-16 overflow-visible">
      {/* Background Wrapper */}
      <div className="absolute inset-0 z-0 overflow-hidden">
        <img 
          src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" 
          alt="Real estate banner" 
          className="w-full h-full object-cover object-center opacity-50 transform scale-105"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-slate-950/40"></div>

        {/* Decorative Circles */}
        <div className="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-cyan-600/20 blur-3xl z-0"></div>
        <div className="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-cyan-600/10 blur-3xl z-0"></div>
      </div>

      <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        {/* Animated Badge */}
        <div className="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/20 mb-6 animate-pulse select-none">
          <span className="w-2.5 h-2.5 rounded-full bg-green-500"></span>
          <span className="text-xs font-semibold uppercase tracking-wider text-slate-200">Hơn 50,000+ Bất động sản đang cho thuê</span>
        </div>

        {/* Heading H1 */}
        <h1 className="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight select-none">
          Tìm Kiếm Không Gian Sống <br className="hidden sm:inline" />
          <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-primary font-black">Lý Tưởng Cho Bạn</span>
        </h1>

        {/* Subheading */}
        <p className="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto mb-10 font-normal leading-relaxed select-none">
          Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn và mặt bằng kinh doanh cho thuê uy tín, cập nhật liên tục mỗi ngày với bộ lọc thông minh.
        </p>

        {/* Search Bar Widget */}
        <div className="bg-white rounded-full p-2 pl-6 pr-2 shadow-2xl text-slate-800 max-w-4xl mx-auto border border-slate-100/50 backdrop-blur-md relative">
          <form onSubmit={handleSearchSubmit} ref={searchFormRef}>
            <div className="flex flex-col sm:flex-row items-center w-full gap-2">
              
              {/* Location Icon and Input */}
              <div className="relative flex-grow w-full text-left flex items-center">
                <button 
                  type="button" 
                  onClick={getUserLocation} 
                  disabled={locating}
                  className="hover:scale-110 active:scale-95 transition text-primary hover:text-primary-hover flex-shrink-0 cursor-pointer focus:outline-none relative"
                  title="Định vị vị trí hiện tại"
                >
                  {locating ? (
                    <svg className="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  ) : (
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path strokeLinecap="round" strokeLinejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                  )}
                </button>
                
                <div className="h-6 w-px bg-slate-200 ml-2.5 mr-3 flex-shrink-0"></div>
                
                <input 
                  type="text" 
                  name="keyword" 
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  onFocus={() => setIsOpen(suggestions.length > 0)}
                  onBlur={() => setTimeout(() => setIsOpen(false), 200)}
                  onKeyDown={handleKeyDown}
                  placeholder="Nhập địa chỉ hoặc khu vực tìm kiếm" 
                  className="w-full py-3 bg-transparent text-sm font-semibold outline-none appearance-none transition h-12"
                  autoComplete="off"
                />

                {query.length > 0 && (
                  <button 
                    type="button" 
                    onClick={() => { setQuery(''); setSuggestions([]); setIsOpen(false) }}
                    className="absolute right-2 text-slate-400 hover:text-slate-650 transition cursor-pointer"
                  >
                    <i className="fa-solid fa-circle-xmark text-sm"></i>
                  </button>
                )}
              </div>

              {/* Submit Button */}
              <div className="w-full sm:w-auto flex-shrink-0">
                <button 
                  type="submit" 
                  className="w-full sm:w-40 bg-primary hover:bg-primary-hover text-white font-extrabold py-3 px-6 rounded-full flex items-center justify-center space-x-2 shadow-lg shadow-primary/20 hover:shadow-primary/35 transition duration-200 h-12 cursor-pointer text-sm"
                >
                  <i className="fa-solid fa-magnifying-glass"></i>
                  <span>Tìm kiếm</span>
                </button>
              </div>
            </div>
          </form>

          {/* Autocomplete Suggestion Dropdown */}
          {isOpen && suggestions.length > 0 && (
            <div className="absolute left-4 right-4 md:left-5 md:right-5 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden text-left">
              <div className="max-h-[350px] overflow-y-auto py-2">
                {suggestions.map((sug, index) => (
                  <div 
                    key={index}
                    onClick={() => selectSuggestion(sug)}
                    onMouseEnter={() => setActiveIndex(index)}
                    className={`px-4 py-3 cursor-pointer flex items-center justify-between border-b border-slate-50 last:border-0 hover:bg-slate-50 transition duration-150 ${
                      activeIndex === index ? 'bg-slate-50 text-primary' : ''
                    }`}
                  >
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 transition">
                        {sug.type === 'property' ? (
                          <i className="fa-solid fa-building text-sm text-amber-500"></i>
                        ) : (
                          <i className="fa-solid fa-location-dot text-sm text-cyan-600"></i>
                        )}
                      </div>
                      <div>
                        <div className="text-xs font-bold text-slate-800">{sug.label}</div>
                        <div className="text-[10px] text-slate-400">{sug.sublabel}</div>
                      </div>
                    </div>
                    <span className={`text-[9px] font-bold uppercase px-2 py-0.5 rounded-full ${getSuggestionTypeClass(sug.type)}`}>
                      {getSuggestionTypeLabel(sug.type)}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </section>
  )
}
