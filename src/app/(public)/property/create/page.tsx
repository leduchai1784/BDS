'use client'

import { useState, useEffect, useRef } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import dynamic from 'next/dynamic'
import { Toaster, toast } from 'sonner'
import { useSession } from 'next-auth/react'

// Dynamically import MapLibre picker map to bypass SSR
const MapPicker = dynamic(() => import('@/components/property/MapPicker'), {
  ssr: false,
  loading: () => (
    <div className="w-full h-[300px] bg-slate-100 flex items-center justify-center rounded-2xl border border-slate-200">
      <span className="text-xs text-slate-500 font-bold">Đang tải bản đồ vị trí...</span>
    </div>
  )
})

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

export default function PropertyCreatePage() {
  const { data: session, status } = useSession()
  const user = session?.user as any
  const router = useRouter()
  const provinceRef = useRef<HTMLDivElement>(null)
  const districtRef = useRef<HTMLDivElement>(null)
  const wardRef = useRef<HTMLDivElement>(null)

  // Close dropdowns when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (provinceRef.current && !provinceRef.current.contains(event.target as Node)) {
        setProvinceOpen(false)
      }
      if (districtRef.current && !districtRef.current.contains(event.target as Node)) {
        setDistrictOpen(false)
      }
      if (wardRef.current && !wardRef.current.contains(event.target as Node)) {
        setWardOpen(false)
      }
    }
    document.addEventListener('mousedown', handleClickOutside)
    return () => {
      document.removeEventListener('mousedown', handleClickOutside)
    }
  }, [])

  // Redirect if not authenticated or not owner
  useEffect(() => {
    if (status === 'unauthenticated') {
      router.push('/login?callbackUrl=/property/create')
    } else if (status === 'authenticated' && user?.role !== 'owner') {
      toast.error('Chỉ dành cho Đối tác Chủ nhà. Vui lòng nâng cấp tài khoản trước.')
      router.push('/profile?tab=register_owner')
    }
  }, [status, user, router])

  // Form fields states
  const [purpose, setPurpose] = useState<'rent' | 'sale'>('rent')
  const [title, setTitle] = useState('')
  const [propertyType, setPropertyType] = useState('Căn hộ')
  const [price, setPrice] = useState('')
  const [area, setArea] = useState('')
  const [bedroom, setBedroom] = useState('0')
  const [bathroom, setBathroom] = useState('0')

  // Sale specific
  const [frontage, setFrontage] = useState('')
  const [roadWidth, setRoadWidth] = useState('')
  const [floors, setFloors] = useState('')

  // Rent specific
  const [deposit, setDeposit] = useState('')
  const [leaseTerm, setLeaseTerm] = useState('')

  // General Specs
  const [direction, setDirection] = useState('')
  const [legal, setLegal] = useState('')
  const [furniture, setFurniture] = useState('')
  const [description, setDescription] = useState('')

  // Address Selector States
  const [provinces, setProvinces] = useState<Province[]>([])
  const [provinceSearch, setProvinceSearch] = useState('')
  const [selectedProvince, setSelectedProvince] = useState<Province | null>(null)
  const [provinceOpen, setProvinceOpen] = useState(false)

  const [districtSearch, setDistrictSearch] = useState('')
  const [selectedDistrict, setSelectedDistrict] = useState<District | null>(null)
  const [districtOpen, setDistrictOpen] = useState(false)

  const [wardSearch, setWardSearch] = useState('')
  const [selectedWard, setSelectedWard] = useState<Ward | null>(null)
  const [wardOpen, setWardOpen] = useState(false)

  const [address, setAddress] = useState('')
  const [latitude, setLatitude] = useState(21.0285)
  const [longitude, setLongitude] = useState(105.8521)

  // Images
  const [imageUrl, setImageUrl] = useState('')
  const [galleryUrlsText, setGalleryUrlsText] = useState('')

  const [isSubmitting, setIsSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const fillSampleData = () => {
    setTitle('Căn hộ chung cư cao cấp view Vinhomes Riverside cực đẹp')
    setPurpose('rent')
    setPropertyType('Căn hộ chung cư')
    setPrice('12000000')
    setArea('75')
    setBedroom('2')
    setBathroom('2')
    setDeposit('24000000')
    setLeaseTerm('Tối thiểu 1 năm')
    setDirection('Đông Nam')
    setLegal('Sổ hồng lâu dài')
    setFurniture('Đầy đủ đồ cao cấp chỉ việc xách vali vào ở')
    setDescription('Cho thuê căn hộ chung cư cao cấp thiết kế cực kỳ hiện đại và sang trọng. Căn hộ gồm 2 phòng ngủ, 2 nhà vệ sinh, ban công rộng hướng Đông Nam đón gió mát mẻ, tầm nhìn trực diện sang khu biệt thự Vinhomes Riverside. Đã trang bị đầy đủ nội thất tivi, tủ lạnh, máy giặt, điều hòa các phòng, giường đệm cao cấp, sofa da bếp từ xịn xò. Cư dân miễn phí gửi xe, sử dụng bể bơi bốn mùa.')
    
    // Auto-locate Hà Nội -> Long Biên -> Phúc Lợi
    const hn = provinces.find(p => p.Name.includes('Hà Nội'))
    if (hn) {
      setSelectedProvince(hn)
      const lb = hn.Districts.find(d => d.Name.includes('Long Biên'))
      if (lb) {
        setSelectedDistrict(lb)
        const pl = lb.Wards.find(w => w.Name.includes('Phúc Lợi'))
        if (pl) {
          setSelectedWard(pl)
        }
      }
    }
    setAddress('Căn hộ 1506, Tòa Park 2, Vinhomes Symphony')
    setLatitude(21.0435)
    setLongitude(105.9123)
    setImageUrl('https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg')
    setGalleryUrlsText('https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg\nhttps://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg')
    
    toast.success('Đã điền tự động dữ liệu đăng tin mẫu!')
  }

  // Load Administrative divisions
  useEffect(() => {
    fetch('/vietnam_provinces.json')
      .then(res => res.json())
      .then(data => setProvinces(data))
      .catch(err => console.error('Failed to load provinces list:', err))
  }, [])

  // Geocoding function using proxy geocode API
  const performGeocode = async () => {
    if (!address || !selectedProvince || !selectedDistrict) return

    const fullAddr = `${address}, ${selectedWard?.Name || ''}, ${selectedDistrict.Name}, ${selectedProvince.Name}, Vietnam`
    try {
      const res = await fetch(`/api/geocode?q=${encodeURIComponent(fullAddr)}`)
      const data = await res.json()
      if (res.ok && data.lat && data.lon) {
        setLatitude(Number(data.lat))
        setLongitude(Number(data.lon))
      }
    } catch (err) {
      console.warn('Geocoding error:', err)
    }
  }

  // Trigger geocode on address change / boundary change
  useEffect(() => {
    const timer = setTimeout(() => {
      performGeocode()
    }, 1200)
    return () => clearTimeout(timer)
  }, [address, selectedProvince, selectedDistrict, selectedWard])

  const handleCoordinatesChange = (lat: number, lng: number) => {
    setLatitude(lat)
    setLongitude(lng)
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)
    setErrorMsg('')

    if (!selectedProvince || !selectedDistrict || !selectedWard || !address) {
      setErrorMsg('Vui lòng chọn đầy đủ thông tin Tỉnh/Thành, Quận/Huyện, Phường/Xã và Địa chỉ chi tiết.')
      setIsSubmitting(false)
      return
    }

    if (!imageUrl) {
      setErrorMsg('Vui lòng nhập liên kết ảnh đại diện chính của bất động sản.')
      setIsSubmitting(false)
      return
    }

    const galleryUrls = galleryUrlsText
      .split('\n')
      .map(line => line.trim())
      .filter(line => line.length > 0)

    try {
      const res = await fetch('/api/properties/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          purpose,
          title,
          property_type: propertyType,
          price: Number(price),
          area: Number(area),
          bedroom: Number(bedroom),
          bathroom: Number(bathroom),
          frontage: frontage ? Number(frontage) : null,
          road_width: roadWidth ? Number(roadWidth) : null,
          floors: floors ? Number(floors) : null,
          deposit: deposit ? Number(deposit) : null,
          lease_term: leaseTerm,
          direction,
          legal,
          furniture,
          description,
          city: selectedProvince.Name,
          district: selectedDistrict.Name,
          ward: selectedWard.Name,
          address,
          latitude,
          longitude,
          image_url: imageUrl,
          gallery_urls: galleryUrls
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        toast.success('Đăng tin bất động sản thành công!')
        router.push('/profile?tab=properties')
      } else {
        setErrorMsg(data.error || data.message || 'Lỗi đăng tin bất động sản.')
      }
    } catch (err) {
      setErrorMsg('Lỗi kết nối mạng, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsSubmitting(false)
    }
  }

  const filteredProvinces = provinces.filter(p => 
    !provinceSearch || p.Name.toLowerCase().includes(provinceSearch.toLowerCase())
  )

  const filteredDistricts = selectedProvince
    ? selectedProvince.Districts.filter(d => 
        !districtSearch || d.Name.toLowerCase().includes(districtSearch.toLowerCase())
      )
    : []

  const filteredWards = selectedDistrict
    ? selectedDistrict.Wards.filter(w => 
        !wardSearch || w.Name.toLowerCase().includes(wardSearch.toLowerCase())
      )
    : []

  if (status === 'loading') {
    return (
      <div className="pt-32 pb-16 text-center text-slate-500 font-bold text-xs">
        Đang xác thực thông tin...
      </div>
    )
  }

  return (
    <div className="bg-slate-50 pt-28 pb-16 min-h-screen text-slate-800">
      <Toaster position="top-right" richColors />

      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        
        {/* Breadcrumbs */}
        <nav className="flex text-xs font-semibold text-slate-500 mb-6 space-x-2 text-left" aria-label="Breadcrumb">
          <Link href="/" className="hover:text-primary transition">Trang chủ</Link>
          <span>/</span>
          <span className="text-slate-800">Đăng tin mới</span>
        </nav>

        <div className="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-6 sm:p-8 text-left">
          
          <div className="pb-5 border-b border-slate-100 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div className="space-y-2">
              <div className="flex items-center gap-3">
                <h1 className="text-xl font-bold text-slate-800">Đăng tin mới</h1>
                <button
                  type="button"
                  onClick={fillSampleData}
                  className="inline-flex items-center justify-center px-2.5 py-1 border border-primary/20 text-[9px] font-black rounded-lg text-primary bg-primary/5 hover:bg-primary/10 transition cursor-pointer active:scale-98"
                >
                  <i className="fa-solid fa-wand-magic-sparkles mr-1"></i> Nhập dữ liệu mẫu
                </button>
              </div>
              <p className="text-xs text-slate-400 mt-1 font-semibold">Nhập đầy đủ thông tin để thu hút khách hàng tiềm năng.</p>
            </div>
            
            {/* Segment Switcher */}
            <div className="flex bg-slate-100 p-1 rounded-xl self-start">
              <button
                type="button"
                onClick={() => setPurpose('rent')}
                className={`px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer ${
                  purpose === 'rent' ? 'bg-white text-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'
                }`}
              >
                Tin Cho Thuê
              </button>
              <button
                type="button"
                onClick={() => setPurpose('sale')}
                className={`px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer ${
                  purpose === 'sale' ? 'bg-white text-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'
                }`}
              >
                Tin Bán
              </button>
            </div>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6 text-left">
            {errorMsg && (
              <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
                <i className="fa-solid fa-circle-exclamation mr-1.5" />
                {errorMsg}
              </div>
            )}

            {/* Section 1: Basic Info */}
            <div className="space-y-4">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">1. Thông tin cơ bản</h3>
              
              {/* Title */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tiêu đề tin đăng <span className="text-red-500">*</span></label>
                <input 
                  type="text" 
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  required 
                  placeholder="Ví dụ: Căn hộ Studio Vinhomes Ocean Park Full Nội Thất..." 
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>

              {/* Property Type Selector */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Loại hình nhà đất <span className="text-red-500">*</span></label>
                <select 
                  value={propertyType}
                  onChange={(e) => setPropertyType(e.target.value)}
                  required
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
                >
                  <option value="Căn hộ">Căn hộ (Chung cư / Căn hộ dịch vụ)</option>
                  <option value="Nhà phố">Nhà phố (Nhà riêng / Nhà nguyên căn)</option>
                  <option value="Biệt thự">Biệt thự (Villa)</option>
                  <option value="Mặt bằng">Mặt bằng (Kinh doanh / Văn phòng)</option>
                </select>
              </div>

              {/* Price & Area Specs Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                {/* Price */}
                <div className="space-y-1 sm:col-span-2">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">
                    {purpose === 'sale' ? 'Giá bán (VND)' : 'Giá thuê (VND / Tháng)'} <span className="text-red-500">*</span>
                  </label>
                  <input 
                    type="number" 
                    value={price}
                    onChange={(e) => setPrice(e.target.value)}
                    required 
                    min="0" 
                    placeholder="Ví dụ: 6500000" 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>

                {/* Area */}
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Diện tích (m²) <span className="text-red-500">*</span></label>
                  <input 
                    type="number" 
                    value={area}
                    onChange={(e) => setArea(e.target.value)}
                    required 
                    min="0" 
                    placeholder="Ví dụ: 35" 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>

                {/* Bedrooms */}
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng ngủ</label>
                  <input 
                    type="number" 
                    value={bedroom}
                    onChange={(e) => setBedroom(e.target.value)}
                    min="0" 
                    placeholder="Ví dụ: 1" 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>

              {/* Dynamic Sale Spec fields */}
              {purpose === 'sale' ? (
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div className="space-y-1">
                    <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mặt tiền (m)</label>
                    <input 
                      type="number" 
                      step="0.01"
                      value={frontage}
                      onChange={(e) => setFrontage(e.target.value)}
                      placeholder="Ví dụ: 5.5" 
                      className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Đường rộng (m)</label>
                    <input 
                      type="number" 
                      step="0.01"
                      value={roadWidth}
                      onChange={(e) => setRoadWidth(e.target.value)}
                      placeholder="Ví dụ: 12.0" 
                      className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số tầng</label>
                    <input 
                      type="number" 
                      value={floors}
                      onChange={(e) => setFloors(e.target.value)}
                      min="0"
                      placeholder="Ví dụ: 3" 
                      className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    />
                  </div>
                </div>
              ) : (
                /* Rent Specs fields */
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="space-y-1">
                    <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tiền đặt cọc (VND)</label>
                    <input 
                      type="number" 
                      value={deposit}
                      onChange={(e) => setDeposit(e.target.value)}
                      min="0"
                      placeholder="Ví dụ: 10000000 (10 triệu)" 
                      className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Thời hạn thuê hợp đồng</label>
                    <input 
                      type="text" 
                      value={leaseTerm}
                      onChange={(e) => setLeaseTerm(e.target.value)}
                      placeholder="Ví dụ: Tối thiểu 1 năm, 6 tháng..." 
                      className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    />
                  </div>
                </div>
              )}

              {/* General Specs Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng tắm</label>
                  <input 
                    type="number" 
                    value={bathroom}
                    onChange={(e) => setBathroom(e.target.value)}
                    min="0" 
                    placeholder="Ví dụ: 1" 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Hướng nhà</label>
                  <input 
                    type="text" 
                    value={direction}
                    onChange={(e) => setDirection(e.target.value)}
                    placeholder="Ví dụ: Đông Nam, Tây Bắc..." 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Pháp lý / Giấy tờ</label>
                  <input 
                    type="text" 
                    value={legal}
                    onChange={(e) => setLegal(e.target.value)}
                    placeholder="Ví dụ: Sổ đỏ chính chủ, Sổ hồng riêng..." 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>

              {/* Furniture */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tình trạng nội thất</label>
                <input 
                  type="text" 
                  value={furniture}
                  onChange={(e) => setFurniture(e.target.value)}
                  placeholder="Ví dụ: Full nội thất (Tivi, Tủ lạnh, Sofa...)" 
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>

              {/* Description */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mô tả chi tiết <span className="text-red-500">*</span></label>
                <textarea 
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  required 
                  rows={5} 
                  placeholder="Nhập thông tin chi tiết về tiện ích căn hộ, khu dân cư, giao thông..." 
                  className="w-full p-4 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition resize-none"
                />
              </div>
            </div>

            <div className="border-t border-slate-100 my-6" />

            {/* Section 2: Position & Coordinates picker */}
            <div className="space-y-4">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">2. Vị trí bất động sản</h3>
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {/* Province Selector */}
                <div className="space-y-1 relative" ref={provinceRef}>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh/Thành phố <span className="text-red-500">*</span></label>
                  <div className="relative">
                    <input 
                      type="text" 
                      placeholder="-- Chọn Tỉnh/Thành phố --"
                      value={provinceSearch}
                      onChange={(e) => { setProvinceSearch(e.target.value); setProvinceOpen(true) }}
                      onFocus={() => setProvinceOpen(true)}
                      required 
                      className="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition text-left"
                    />
                    <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs" />
                  </div>
                  {provinceOpen && (
                    <div className="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                      {filteredProvinces.map(p => (
                        <div 
                          key={p.Id}
                          onClick={() => {
                            setSelectedProvince(p)
                            setProvinceSearch(p.Name)
                            setSelectedDistrict(null)
                            setDistrictSearch('')
                            setSelectedWard(null)
                            setWardSearch('')
                            setProvinceOpen(false)
                          }}
                          className="px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition text-left"
                        >
                          {p.Name}
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                {/* District Selector */}
                <div className="space-y-1 relative" ref={districtRef}>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quận/Huyện <span className="text-red-500">*</span></label>
                  <div className="relative">
                    <input 
                      type="text" 
                      placeholder="-- Chọn Quận/Huyện --"
                      value={districtSearch}
                      onChange={(e) => { setDistrictSearch(e.target.value); setDistrictOpen(true) }}
                      onFocus={() => setDistrictOpen(true)}
                      disabled={!selectedProvince}
                      required 
                      className="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition text-left disabled:opacity-60 disabled:cursor-not-allowed"
                    />
                    <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs" />
                  </div>
                  {districtOpen && selectedProvince && (
                    <div className="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                      {filteredDistricts.map(d => (
                        <div 
                          key={d.Id}
                          onClick={() => {
                            setSelectedDistrict(d)
                            setDistrictSearch(d.Name)
                            setSelectedWard(null)
                            setWardSearch('')
                            setDistrictOpen(false)
                          }}
                          className="px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition text-left"
                        >
                          {d.Name}
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                {/* Ward Selector */}
                <div className="space-y-1 relative" ref={wardRef}>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường/Xã <span className="text-red-500">*</span></label>
                  <div className="relative">
                    <input 
                      type="text" 
                      placeholder="-- Chọn Phường/Xã --"
                      value={wardSearch}
                      onChange={(e) => { setWardSearch(e.target.value); setWardOpen(true) }}
                      onFocus={() => setWardOpen(true)}
                      disabled={!selectedDistrict}
                      required 
                      className="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition text-left disabled:opacity-60 disabled:cursor-not-allowed"
                    />
                    <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs" />
                  </div>
                  {wardOpen && selectedDistrict && (
                    <div className="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                      {filteredWards.map(w => (
                        <div 
                          key={w.Id}
                          onClick={() => {
                            setSelectedWard(w)
                            setWardSearch(w.Name)
                            setWardOpen(false)
                          }}
                          className="px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition text-left"
                        >
                          {w.Name}
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                {/* Street Address */}
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Địa chỉ chi tiết <span className="text-red-500">*</span></label>
                  <input 
                    type="text" 
                    value={address}
                    onChange={(e) => setAddress(e.target.value)}
                    required 
                    placeholder="Ví dụ: Số 15, Ngõ 44, Đường Duy Tân" 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition text-left"
                  />
                </div>
              </div>

              {/* Coordinates display */}
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Vĩ độ (Latitude)</label>
                  <input 
                    type="number" 
                    step="any"
                    value={latitude}
                    onChange={(e) => setLatitude(Number(e.target.value))}
                    required 
                    className="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                  />
                </div>
                
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Kinh độ (Longitude)</label>
                  <input 
                    type="number" 
                    step="any"
                    value={longitude}
                    onChange={(e) => setLongitude(Number(e.target.value))}
                    required 
                    className="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                  />
                </div>
              </div>

              {/* Map Canvas Location Picker */}
              <div className="space-y-2">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Chọn vị trí trên bản đồ</label>
                <MapPicker 
                  latitude={latitude}
                  longitude={longitude}
                  onCoordinatesChange={handleCoordinatesChange}
                />
              </div>
            </div>

            <div className="border-t border-slate-100 my-6" />

            {/* Section 3: Contact Info */}
            <div className="space-y-4">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">3. Thông tin liên hệ</h3>
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại liên hệ <span className="text-red-500">*</span></label>
                  <input 
                    type="text" 
                    defaultValue={user?.phone || ''}
                    required 
                    placeholder="Số điện thoại của bạn..." 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Link Zalo (Tùy chọn)</label>
                  <input 
                    type="text" 
                    placeholder="Ví dụ: https://zalo.me/0987654321" 
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>
            </div>

            <div className="border-t border-slate-100 my-6" />

            {/* Section 4: Images urls */}
            <div className="space-y-4">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">4. Hình ảnh bất động sản</h3>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {/* Primary cover image */}
                <div className="space-y-2">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1 font-bold">Liên kết ảnh chính <span className="text-red-500">*</span></label>
                  <div className="flex items-start space-x-4">
                    <div className="w-24 h-20 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-inner flex items-center justify-center flex-shrink-0">
                      {imageUrl ? (
                        <img src={imageUrl} className="w-full h-full object-cover" />
                      ) : (
                        <i className="fa-regular fa-image text-slate-300 text-2xl" />
                      )}
                    </div>
                    <div className="flex-grow text-left">
                      <input 
                        type="text" 
                        value={imageUrl} 
                        onChange={(e) => setImageUrl(e.target.value)}
                        required
                        placeholder="https://example.com/cover.jpg" 
                        className="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                      />
                    </div>
                  </div>
                </div>

                {/* Sub Gallery */}
                <div className="space-y-2">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Danh sách liên kết ảnh phụ (Gallery)</label>
                  <textarea 
                    value={galleryUrlsText}
                    onChange={(e) => setGalleryUrlsText(e.target.value)}
                    rows={3}
                    placeholder="Mỗi dòng một liên kết ảnh..." 
                    className="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition resize-none"
                  />
                </div>
              </div>
            </div>

            {/* Actions footer */}
            <div className="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-8">
              <Link 
                href="/profile?tab=properties" 
                className="inline-flex items-center justify-center px-5 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer"
              >
                Hủy bỏ
              </Link>
              <button 
                type="submit" 
                disabled={isSubmitting}
                className="px-6 py-3 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-60"
              >
                {isSubmitting ? (
                  <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Đang tạo...</span>
                ) : (
                  <span>Đăng tin</span>
                )}
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  )
}
