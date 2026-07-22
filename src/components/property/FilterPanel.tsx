'use client'

import { useState, useEffect } from 'react'
import { useRouter, useSearchParams } from 'next/navigation'

interface Ward {
  Id: string
  Name: string
}

interface District {
  Id: string
  Name: string
  Wards: Ward[]
}

interface Province {
  Id: string
  Name: string
  Districts: District[]
}

export default function FilterPanel() {
  const router = useRouter()
  const searchParams = useSearchParams()

  // Locations data
  const [provinces, setProvinces] = useState<Province[]>([])
  const [districts, setDistricts] = useState<District[]>([])
  const [wards, setWards] = useState<Ward[]>([])

  // Collapsible toggle
  const [showAdvanced, setShowAdvanced] = useState(false)

  // Filter form states
  const [keyword, setKeyword] = useState('')
  const [purpose, setPurpose] = useState('')
  const [propertyType, setPropertyType] = useState('')
  const [selectedProvince, setSelectedProvince] = useState('')
  const [selectedDistrict, setSelectedDistrict] = useState('')
  const [selectedWard, setSelectedWard] = useState('')
  const [price, setPrice] = useState('')
  const [area, setArea] = useState('')
  const [bedrooms, setBedrooms] = useState('')
  const [bathrooms, setBathrooms] = useState('')
  const [furniture, setFurniture] = useState('')
  const [direction, setDirection] = useState('')

  // Load NKS provinces API
  useEffect(() => {
    const loadLocations = async () => {
      try {
        const res = await fetch('/api/nks/provinces', { method: 'POST' })
        if (res.ok) {
          const data = await res.json()
          if (data && data.success && Array.isArray(data.data)) {
            const nksProvs = data.data.map((p: any) => ({
              Id: String(p.id),
              Name: p.title,
              Districts: []
            }))
            setProvinces(nksProvs)
          }
        }
      } catch (err) {
        console.error('Failed to load NKS provinces', err)
      }
    }
    loadLocations()
  }, [])

  // Sync state with URL search params on mount or param changes
  useEffect(() => {
    setKeyword(searchParams.get('keyword') || searchParams.get('search') || '')
    setPurpose(searchParams.get('purpose') || '')
    setPropertyType(searchParams.get('property_type') || searchParams.get('type') || '')
    setPrice(searchParams.get('price') || '')
    setArea(searchParams.get('area') || '')
    setBedrooms(searchParams.get('bedrooms') || searchParams.get('bedroom') || '')
    setBathrooms(searchParams.get('bathrooms') || searchParams.get('bathroom') || '')
    setFurniture(searchParams.get('furniture') || '')
    setDirection(searchParams.get('direction') || '')

    const urlProvince = searchParams.get('province') || searchParams.get('city') || ''
    const urlDistrict = searchParams.get('district') || ''
    const urlWard = searchParams.get('ward') || ''

    if (urlProvince) setSelectedProvince(urlProvince)
    if (urlDistrict) setSelectedDistrict(urlDistrict)
    if (urlWard) setSelectedWard(urlWard)

    // Open advanced filter if any advanced parameters are active
    if (
      urlProvince || urlDistrict || urlWard || 
      searchParams.get('price') || searchParams.get('area') || 
      searchParams.get('bedrooms') || searchParams.get('bathrooms') || 
      searchParams.get('furniture') || searchParams.get('direction')
    ) {
      setShowAdvanced(true)
    }
  }, [searchParams])

  // Sync Districts & Wards dropdown arrays based on selections
  useEffect(() => {
    if (!selectedProvince || provinces.length === 0) {
      setDistricts([])
      setWards([])
      return
    }

    const provObj = provinces.find(p => p.Name.includes(selectedProvince) || selectedProvince.includes(p.Name))
    if (provObj) {
      setDistricts(provObj.Districts)
      
      if (selectedDistrict) {
        const distObj = provObj.Districts.find(d => d.Name.includes(selectedDistrict) || selectedDistrict.includes(d.Name) || d.Id === selectedDistrict)
        if (distObj) {
          setWards(distObj.Wards)
        } else {
          setWards([])
        }
      } else {
        setWards([])
      }
    }
  }, [selectedProvince, selectedDistrict, provinces])

  const handleProvinceChange = (name: string) => {
    setSelectedProvince(name)
    setSelectedDistrict('')
    setSelectedWard('')
    setDistricts([])
    setWards([])
  }

  const handleDistrictChange = (name: string) => {
    setSelectedDistrict(name)
    setSelectedWard('')
    setWards([])
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    
    const params = new URLSearchParams()
    if (keyword) params.set('keyword', keyword)
    if (purpose) params.set('purpose', purpose)
    if (propertyType) params.set('property_type', propertyType)
    if (selectedProvince) params.set('province', selectedProvince)
    if (selectedDistrict) params.set('district', selectedDistrict)
    if (selectedWard) params.set('ward', selectedWard)
    if (price) params.set('price', price)
    if (area) params.set('area', area)
    if (bedrooms) params.set('bedrooms', bedrooms)
    if (bathrooms) params.set('bathrooms', bathrooms)
    if (furniture) params.set('furniture', furniture)
    if (direction) params.set('direction', direction)

    router.push(`/listings?${params.toString()}`)
  }

  const handleReset = () => {
    setKeyword('')
    setPurpose('')
    setPropertyType('')
    setSelectedProvince('')
    setSelectedDistrict('')
    setSelectedWard('')
    setPrice('')
    setArea('')
    setBedrooms('')
    setBathrooms('')
    setFurniture('')
    setDirection('')
    router.push('/listings')
  }

  return (
    <form onSubmit={handleSubmit} className="bg-white rounded-3xl p-6 shadow-md border border-slate-100 mb-8 block text-left">
      {/* Filter Top Bar (Always Visible) */}
      <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        {/* Heading & Quick Results */}
        <div>
          <h2 className="text-xl font-black text-slate-800 flex items-center gap-2">
            <i className="fa-solid fa-sliders text-primary"></i>
            <span>Bộ Lọc Tìm Kiếm</span>
          </h2>
          <p className="text-xs text-slate-500 mt-1">Tìm kiếm chi tiết bất động sản phù hợp với nhu cầu của bạn</p>
        </div>

        {/* Right Quick Filters & Advanced Toggle */}
        <div className="flex flex-wrap items-center gap-3">
          {/* Toggle Advanced Filters Button */}
          <button 
            type="button" 
            onClick={() => setShowAdvanced(!showAdvanced)}
            className={`flex items-center space-x-2 px-4 py-2.5 rounded-xl border text-sm font-bold transition duration-200 cursor-pointer ${
              showAdvanced ? 'bg-primary text-white border-transparent' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'
            }`}
          >
            <i className={`fa-solid fa-chevron-down transform transition duration-300 ${showAdvanced ? 'rotate-180' : ''}`}></i>
            <span>Bộ lọc chi tiết</span>
          </button>

          {/* Reset Button */}
          <button 
            type="button"
            onClick={handleReset}
            className="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-xl border border-slate-200 text-sm font-semibold transition text-center cursor-pointer"
          >
            Đặt lại
          </button>
        </div>
      </div>

      {/* Collapsible Advanced Search Fields */}
      {showAdvanced && (
        <div className="mt-6 pt-6 border-t border-slate-100 transition-all duration-300">
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            
            {/* Keyword */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Từ khóa tìm kiếm</label>
              <input 
                type="text" 
                name="keyword" 
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
                placeholder="Địa điểm, tên dự án..." 
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition"
              />
            </div>

            {/* Transaction Type */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Loại giao dịch</label>
              <select 
                name="purpose" 
                value={purpose}
                onChange={(e) => { setPurpose(e.target.value); setPrice('') }}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả</option>
                <option value="rent">Cho thuê</option>
                <option value="sale">Bán</option>
              </select>
            </div>

            {/* Property Type */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Loại bất động sản</label>
              <select 
                name="property_type" 
                value={propertyType}
                onChange={(e) => setPropertyType(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả loại hình</option>
                <option value="apartment">Căn hộ</option>
                <option value="house">Nhà riêng</option>
                <option value="room">Phòng trọ</option>
                <option value="land">Đất nền</option>
                <option value="premises">Mặt bằng</option>
                <option value="office">Văn phòng</option>
                <option value="warehouse">Kho xưởng</option>
              </select>
            </div>

            {/* Province */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Tỉnh / Thành phố</label>
              <select 
                name="province" 
                value={selectedProvince}
                onChange={(e) => handleProvinceChange(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Chọn Tỉnh/Thành phố</option>
                {provinces.map(p => (
                  <option key={p.Id} value={p.Name}>{p.Name}</option>
                ))}
              </select>
            </div>

            {/* District */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Quận / Huyện</label>
              <select 
                name="district" 
                value={selectedDistrict}
                onChange={(e) => handleDistrictChange(e.target.value)}
                disabled={!selectedProvince}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer disabled:opacity-60"
              >
                <option value="">Chọn Quận/Huyện</option>
                {districts.map(d => (
                  <option key={d.Id} value={d.Name}>{d.Name}</option>
                ))}
              </select>
            </div>

            {/* Ward */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phường / Xã</label>
              <select 
                name="ward" 
                value={selectedWard}
                onChange={(e) => setSelectedWard(e.target.value)}
                disabled={!selectedDistrict}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer disabled:opacity-60"
              >
                <option value="">Chọn Phường/Xã</option>
                {wards.map(w => (
                  <option key={w.Id} value={w.Name}>{w.Name}</option>
                ))}
              </select>
            </div>

            {/* Price */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Khoảng giá</label>
              <select 
                name="price" 
                value={price}
                onChange={(e) => setPrice(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả mức giá</option>
                {/* Rent Options */}
                {(purpose === 'rent' || purpose === '') && (
                  <>
                    <option value="under_3">Dưới 3 triệu</option>
                    <option value="3_5">3 - 5 triệu</option>
                    <option value="5_10">5 - 10 triệu</option>
                    <option value="10_20">10 - 20 triệu</option>
                    <option value="above_20">Trên 20 triệu</option>
                  </>
                )}
                {/* Sale Options */}
                {purpose === 'sale' && (
                  <>
                    <option value="under_1b">Dưới 1 tỷ</option>
                    <option value="1b_3b">1 - 3 tỷ</option>
                    <option value="3b_5b">3 - 5 tỷ</option>
                    <option value="5b_10b">5 - 10 tỷ</option>
                    <option value="above_10b">Trên 10 tỷ</option>
                  </>
                )}
              </select>
            </div>

            {/* Area */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Diện tích</label>
              <select 
                name="area" 
                value={area}
                onChange={(e) => setArea(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả diện tích</option>
                <option value="under_30">Dưới 30 m²</option>
                <option value="30_50">30 - 50 m²</option>
                <option value="50_80">50 - 80 m²</option>
                <option value="80_120">80 - 120 m²</option>
                <option value="above_120">Trên 120 m²</option>
              </select>
            </div>

            {/* Bedrooms */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phòng ngủ</label>
              <select 
                name="bedrooms" 
                value={bedrooms}
                onChange={(e) => setBedrooms(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả</option>
                <option value="1">1+ phòng ngủ</option>
                <option value="2">2+ phòng ngủ</option>
                <option value="3">3+ phòng ngủ</option>
                <option value="4">4+ phòng ngủ</option>
              </select>
            </div>

            {/* Bathrooms */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phòng vệ sinh</label>
              <select 
                name="bathrooms" 
                value={bathrooms}
                onChange={(e) => setBathrooms(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả</option>
                <option value="1">1+ phòng vệ sinh</option>
                <option value="2">2+ phòng vệ sinh</option>
                <option value="3">3+ phòng vệ sinh</option>
              </select>
            </div>

            {/* Furniture */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Nội thất</label>
              <select 
                name="furniture" 
                value={furniture}
                onChange={(e) => setFurniture(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả</option>
                <option value="full">Đầy đủ nội thất</option>
                <option value="basic">Nội thất cơ bản</option>
                <option value="none">Không nội thất</option>
              </select>
            </div>

            {/* Direction */}
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Hướng</label>
              <select 
                name="direction" 
                value={direction}
                onChange={(e) => setDirection(e.target.value)}
                className="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
              >
                <option value="">Tất cả hướng</option>
                <option value="east">Đông</option>
                <option value="west">Tây</option>
                <option value="south">Nam</option>
                <option value="north">Bắc</option>
                <option value="southeast">Đông Nam</option>
                <option value="southwest">Tây Nam</option>
                <option value="northeast">Đông Bắc</option>
                <option value="northwest">Tây Bắc</option>
              </select>
            </div>
          </div>

          {/* Filter Action Buttons */}
          <div className="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
            <button 
              type="button" 
              onClick={() => setShowAdvanced(false)}
              className="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition cursor-pointer"
            >
              Hủy
            </button>
            <button 
              type="submit" 
              className="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl text-sm shadow-md shadow-primary/20 hover:shadow-primary/30 transition cursor-pointer"
            >
              Tìm kiếm
            </button>
          </div>
        </div>
      )}
    </form>
  )
}
