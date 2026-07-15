'use client'

import { useState, useEffect, useRef } from 'react'
import { useSession } from 'next-auth/react'
import { useRouter, useParams } from 'next/navigation'
import Link from 'next/link'
import { toast } from 'sonner'
import MapPicker from '@/components/property/MapPicker'

interface Ward {
  Id: string
  Name: string
  Level: string
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

export default function PropertyEditPage() {
  const { data: session, status } = useSession()
  const user = session?.user as any
  const router = useRouter()
  const params = useParams()
  const propertyId = params.id as string

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
      router.push(`/login?callbackUrl=/property/${propertyId}/edit`)
    } else if (status === 'authenticated' && user?.role !== 'owner') {
      toast.error('Chỉ dành cho Đối tác Chủ nhà. Vui lòng nâng cấp tài khoản trước.')
      router.push('/profile?tab=register_owner')
    }
  }, [status, user, router, propertyId])

  // Form fields states
  const [purpose, setPurpose] = useState<'rent' | 'sale'>('rent')
  const [title, setTitle] = useState('')
  const [propertyType, setPropertyType] = useState('Căn hộ chung cư')
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
  const [isLoading, setIsLoading] = useState(true)
  const [errorMsg, setErrorMsg] = useState('')

  // Temporary holding variables for database text values to match dropdowns
  const [initialCity, setInitialCity] = useState('')
  const [initialDistrict, setInitialDistrict] = useState('')
  const [initialWard, setInitialWard] = useState('')

  // Load Administrative divisions
  useEffect(() => {
    fetch('/vietnam_provinces.json')
      .then(res => res.json())
      .then(data => setProvinces(data))
      .catch(err => console.error('Failed to load provinces list:', err))
  }, [])

  // Fetch existing property details
  useEffect(() => {
    if (propertyId) {
      setIsLoading(true)
      fetch(`/api/properties/${propertyId}`)
        .then(res => {
          if (!res.ok) throw new Error('Không thể lấy thông tin tin đăng')
          return res.json()
        })
        .then(data => {
          if (data.success && data.property) {
            const p = data.property
            setPurpose(p.transactionType === 'sale' ? 'sale' : 'rent')
            setTitle(p.title)
            setPropertyType(p.propertyType)
            setPrice(String(p.price))
            setArea(String(p.area))
            setBedroom(String(p.bedroom || 0))
            setBathroom(String(p.bathroom || 0))
            setFrontage(p.frontage ? String(p.frontage) : '')
            setRoadWidth(p.roadWidth ? String(p.roadWidth) : '')
            setFloors(p.floors ? String(p.floors) : '')
            setDeposit(p.deposit ? String(p.deposit) : '')
            setLeaseTerm(p.leaseTerm || '')
            setDirection(p.direction || '')
            setLegal(p.legal || '')
            setFurniture(p.furniture || '')
            setDescription(p.description || '')
            setAddress(p.address || '')
            setLatitude(p.latitude || 21.0285)
            setLongitude(p.longitude || 105.8521)

            setInitialCity(p.city)
            setInitialDistrict(p.district)
            setInitialWard(p.ward)

            // Extract images
            const primaryImg = p.propertyImages?.find((img: any) => img.isPrimary)
            if (primaryImg) setImageUrl(primaryImg.imagePath)

            const galleryImgs = p.propertyImages
              ?.filter((img: any) => !img.isPrimary)
              .map((img: any) => img.imagePath)
              .join('\n') || ''
            setGalleryUrlsText(galleryImgs)
          }
        })
        .catch(err => {
          console.error(err)
          setErrorMsg(err.message || 'Lỗi tải dữ liệu.')
        })
        .finally(() => {
          setIsLoading(false)
        })
    }
  }, [propertyId])

  // Match administrative boundary names when provinces and property details load
  useEffect(() => {
    if (provinces.length > 0 && initialCity) {
      const prov = provinces.find(p => p.Name.toLowerCase() === initialCity.toLowerCase() || p.Name.toLowerCase().replace('tỉnh ', '').replace('thành phố ', '') === initialCity.toLowerCase().replace('tỉnh ', '').replace('thành phố ', ''))
      if (prov) {
        setSelectedProvince(prov)
        if (initialDistrict) {
          const dist = prov.Districts.find(d => d.Name.toLowerCase() === initialDistrict.toLowerCase() || d.Name.toLowerCase().replace('quận ', '').replace('huyện ', '').replace('thị xã ', '') === initialDistrict.toLowerCase().replace('quận ', '').replace('huyện ', '').replace('thị xã ', ''))
          if (dist) {
            setSelectedDistrict(dist)
            if (initialWard) {
              const wrd = dist.Wards.find(w => w.Name.toLowerCase() === initialWard.toLowerCase() || w.Name.toLowerCase().replace('phường ', '').replace('xã ', '').replace('thị trấn ', '') === initialWard.toLowerCase().replace('phường ', '').replace('xã ', '').replace('thị trấn ', ''))
              if (wrd) {
                setSelectedWard(wrd)
              }
            }
          }
        }
      }
    }
  }, [provinces, initialCity, initialDistrict, initialWard])

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
    }, 1500)
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
      const res = await fetch(`/api/properties/${propertyId}`, {
        method: 'PUT',
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
        toast.success('Cập nhật tin đăng bất động sản thành công!')
        router.push('/profile?tab=properties')
      } else {
        setErrorMsg(data.error || data.message || 'Lỗi cập nhật tin đăng.')
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

  if (isLoading) {
    return (
      <div className="min-h-screen bg-slate-50 flex items-center justify-center">
        <div className="text-center space-y-3">
          <i className="fa-solid fa-circle-notch fa-spin text-3xl text-primary" />
          <p className="text-sm font-semibold text-slate-500">Đang tải thông tin tin đăng...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl mx-auto">
        {/* Header Breadcrumbs */}
        <div className="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-6">
          <Link href="/profile?tab=properties" className="hover:text-primary transition">Quản lý tin đăng</Link>
          <i className="fa-solid fa-chevron-right text-[9px]" />
          <span className="text-slate-800">Chỉnh sửa tin đăng</span>
        </div>

        <div className="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 sm:p-10">
          <div className="mb-8 border-b border-slate-100 pb-5 text-left">
            <h2 className="text-2xl font-black text-slate-900">Chỉnh sửa tin đăng</h2>
            <p className="text-xs text-slate-500 mt-1 font-semibold">Cập nhật thông tin chi tiết về bất động sản của bạn.</p>
          </div>

          {errorMsg && (
            <div className="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start space-x-3 text-left">
              <i className="fa-solid fa-triangle-exclamation text-rose-500 mt-0.5" />
              <p className="text-xs font-bold text-rose-700">{errorMsg}</p>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-8">
            {/* Section 1: Basic Info */}
            <div className="space-y-6 text-left">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">1. Thông tin cơ bản</h3>

              {/* Purpose Selector */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mục đích giao dịch <span className="text-red-500">*</span></label>
                <div className="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    onClick={() => setPurpose('rent')}
                    className={`py-3 px-4 border rounded-xl text-xs font-bold flex items-center justify-center space-x-2 transition cursor-pointer ${
                      purpose === 'rent'
                        ? 'border-primary bg-cyan-50 text-primary shadow-xs shadow-cyan-100'
                        : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600'
                    }`}
                  >
                    <i className="fa-solid fa-key" />
                    <span>Cho thuê</span>
                  </button>
                  <button
                    type="button"
                    onClick={() => setPurpose('sale')}
                    className={`py-3 px-4 border rounded-xl text-xs font-bold flex items-center justify-center space-x-2 transition cursor-pointer ${
                      purpose === 'sale'
                        ? 'border-primary bg-cyan-50 text-primary shadow-xs shadow-cyan-100'
                        : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600'
                    }`}
                  >
                    <i className="fa-solid fa-tags" />
                    <span>Cần bán</span>
                  </button>
                </div>
              </div>

              {/* Title */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tiêu đề tin đăng <span className="text-red-500">*</span></label>
                <input
                  type="text"
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  required
                  placeholder="VD: Căn hộ studio full nội thất trung tâm Quận 1"
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
                  <option value="Căn hộ chung cư">Căn hộ chung cư</option>
                  <option value="Nhà riêng">Nhà riêng</option>
                  <option value="Nhà phố">Nhà phố</option>
                  <option value="Nhà trọ / Phòng trọ">Nhà trọ / Phòng trọ</option>
                  <option value="Văn phòng">Văn phòng</option>
                  <option value="Mặt bằng kinh doanh">Mặt bằng kinh doanh</option>
                </select>
              </div>

              {/* Price and Area */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giá yêu cầu (đ) <span className="text-red-500">*</span></label>
                  <input
                    type="number"
                    value={price}
                    onChange={(e) => setPrice(e.target.value)}
                    required
                    placeholder="Nhập giá bằng số (VD: 8500000)"
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Diện tích (m²) <span className="text-red-500">*</span></label>
                  <input
                    type="number"
                    value={area}
                    onChange={(e) => setArea(e.target.value)}
                    required
                    placeholder="Diện tích sử dụng (VD: 45)"
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>

              {/* Bedroom and Bathroom */}
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng ngủ</label>
                  <select
                    value={bedroom}
                    onChange={(e) => setBedroom(e.target.value)}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
                  >
                    <option value="0">0 (Không ngăn phòng)</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5+</option>
                  </select>
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng vệ sinh</label>
                  <select
                    value={bathroom}
                    onChange={(e) => setBathroom(e.target.value)}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
                  >
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4+</option>
                  </select>
                </div>
              </div>

              {/* Purpose Specific Info */}
              {purpose === 'sale' ? (
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                  <div className="space-y-1">
                    <label className="block text-[9px] font-bold uppercase text-slate-500">Mặt tiền (m)</label>
                    <input
                      type="number"
                      value={frontage}
                      onChange={(e) => setFrontage(e.target.value)}
                      placeholder="Chiều ngang mặt tiền"
                      className="w-full px-3 py-2 bg-white border border-slate-200 focus:border-primary rounded-lg text-xs font-semibold outline-none transition"
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="block text-[9px] font-bold uppercase text-slate-500">Đường vào rộng (m)</label>
                    <input
                      type="number"
                      value={roadWidth}
                      onChange={(e) => setRoadWidth(e.target.value)}
                      placeholder="Chiều rộng đường trước nhà"
                      className="w-full px-3 py-2 bg-white border border-slate-200 focus:border-primary rounded-lg text-xs font-semibold outline-none transition"
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="block text-[9px] font-bold uppercase text-slate-500">Số tầng</label>
                    <input
                      type="number"
                      value={floors}
                      onChange={(e) => setFloors(e.target.value)}
                      placeholder="Số tầng nhà"
                      className="w-full px-3 py-2 bg-white border border-slate-200 focus:border-primary rounded-lg text-xs font-semibold outline-none transition"
                    />
                  </div>
                </div>
              ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                  <div className="space-y-1">
                    <label className="block text-[9px] font-bold uppercase text-slate-500">Tiền đặt cọc (đ)</label>
                    <input
                      type="number"
                      value={deposit}
                      onChange={(e) => setDeposit(e.target.value)}
                      placeholder="VD: 15000000"
                      className="w-full px-3 py-2 bg-white border border-slate-200 focus:border-primary rounded-lg text-xs font-semibold outline-none transition"
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="block text-[9px] font-bold uppercase text-slate-500">Thời hạn thuê tối thiểu</label>
                    <input
                      type="text"
                      value={leaseTerm}
                      onChange={(e) => setLeaseTerm(e.target.value)}
                      placeholder="VD: 1 năm, 6 tháng"
                      className="w-full px-3 py-2 bg-white border border-slate-200 focus:border-primary rounded-lg text-xs font-semibold outline-none transition"
                    />
                  </div>
                </div>
              )}

              {/* Specs */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Hướng nhà</label>
                  <select
                    value={direction}
                    onChange={(e) => setDirection(e.target.value)}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
                  >
                    <option value="">Chọn hướng</option>
                    <option value="Đông">Đông</option>
                    <option value="Tây">Tây</option>
                    <option value="Nam">Nam</option>
                    <option value="Bắc">Bắc</option>
                    <option value="Đông Nam">Đông Nam</option>
                    <option value="Đông Bắc">Đông Bắc</option>
                    <option value="Tây Nam">Tây Nam</option>
                    <option value="Tây Bắc">Tây Bắc</option>
                  </select>
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tình trạng pháp lý</label>
                  <select
                    value={legal}
                    onChange={(e) => setLegal(e.target.value)}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
                  >
                    <option value="">Chọn tình trạng pháp lý</option>
                    <option value="Đã có sổ hồng/sổ đỏ">Đã có sổ hồng/sổ đỏ</option>
                    <option value="Đang chờ sổ">Đang chờ sổ</option>
                    <option value="Hợp đồng mua bán">Hợp đồng mua bán</option>
                    <option value="Giấy tờ viết tay">Giấy tờ viết tay</option>
                  </select>
                </div>

                <div className="space-y-1">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tình trạng nội thất</label>
                  <select
                    value={furniture}
                    onChange={(e) => setFurniture(e.target.value)}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
                  >
                    <option value="">Chọn tình trạng nội thất</option>
                    <option value="Đầy đủ nội thất">Đầy đủ nội thất</option>
                    <option value="Nội thất cơ bản">Nội thất cơ bản</option>
                    <option value="Nhà trống (Không nội thất)">Nhà trống (Không nội thất)</option>
                  </select>
                </div>
              </div>

              {/* Description */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mô tả chi tiết <span className="text-red-500">*</span></label>
                <textarea
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  required
                  rows={6}
                  placeholder="Mô tả cụ thể về vị trí, các tiện ích xung quanh, chi phí phát sinh khác..."
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition resize-y"
                />
              </div>
            </div>

            {/* Section 2: Address and Location */}
            <div className="space-y-6 text-left">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">2. Vị trí &amp; Bản đồ</h3>

              {/* Dropdowns for Address selectors */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {/* Province Dropdown */}
                <div className="space-y-1 relative" ref={provinceRef}>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh / Thành phố <span className="text-red-500">*</span></label>
                  <button
                    type="button"
                    onClick={() => {
                      setProvinceOpen(!provinceOpen)
                      setDistrictOpen(false)
                      setWardOpen(false)
                    }}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 flex items-center justify-between cursor-pointer"
                  >
                    <span className="truncate">{selectedProvince?.Name || 'Chọn Tỉnh/Thành'}</span>
                    <i className="fa-solid fa-chevron-down text-[10px] text-slate-400" />
                  </button>
                  {provinceOpen && (
                    <div className="absolute left-0 right-0 mt-1 bg-white border border-slate-100 rounded-xl shadow-lg z-50 p-2 text-left">
                      <input
                        type="text"
                        placeholder="Tìm kiếm..."
                        value={provinceSearch}
                        onChange={(e) => setProvinceSearch(e.target.value)}
                        className="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs outline-none mb-2"
                      />
                      <div className="max-h-48 overflow-y-auto space-y-0.5">
                        {filteredProvinces.map(p => (
                          <button
                            key={p.Id}
                            type="button"
                            onClick={() => {
                              setSelectedProvince(p)
                              setSelectedDistrict(null)
                              setSelectedWard(null)
                              setProvinceOpen(false)
                            }}
                            className="w-full px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 rounded-lg text-left font-semibold"
                          >
                            {p.Name}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}
                </div>

                {/* District Dropdown */}
                <div className="space-y-1 relative" ref={districtRef}>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quận / Huyện <span className="text-red-500">*</span></label>
                  <button
                    type="button"
                    disabled={!selectedProvince}
                    onClick={() => {
                      setDistrictOpen(!districtOpen)
                      setProvinceOpen(false)
                      setWardOpen(false)
                    }}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 flex items-center justify-between cursor-pointer disabled:opacity-50"
                  >
                    <span className="truncate">{selectedDistrict?.Name || 'Chọn Quận/Huyện'}</span>
                    <i className="fa-solid fa-chevron-down text-[10px] text-slate-400" />
                  </button>
                  {districtOpen && selectedProvince && (
                    <div className="absolute left-0 right-0 mt-1 bg-white border border-slate-100 rounded-xl shadow-lg z-50 p-2 text-left">
                      <input
                        type="text"
                        placeholder="Tìm kiếm..."
                        value={districtSearch}
                        onChange={(e) => setDistrictSearch(e.target.value)}
                        className="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs outline-none mb-2"
                      />
                      <div className="max-h-48 overflow-y-auto space-y-0.5">
                        {filteredDistricts.map(d => (
                          <button
                            key={d.Id}
                            type="button"
                            onClick={() => {
                              setSelectedDistrict(d)
                              setSelectedWard(null)
                              setDistrictOpen(false)
                            }}
                            className="w-full px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 rounded-lg text-left font-semibold"
                          >
                            {d.Name}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}
                </div>

                {/* Ward Dropdown */}
                <div className="space-y-1 relative" ref={wardRef}>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường / Xã <span className="text-red-500">*</span></label>
                  <button
                    type="button"
                    disabled={!selectedDistrict}
                    onClick={() => {
                      setWardOpen(!wardOpen)
                      setProvinceOpen(false)
                      setDistrictOpen(false)
                    }}
                    className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 flex items-center justify-between cursor-pointer disabled:opacity-50"
                  >
                    <span className="truncate">{selectedWard?.Name || 'Chọn Phường/Xã'}</span>
                    <i className="fa-solid fa-chevron-down text-[10px] text-slate-400" />
                  </button>
                  {wardOpen && selectedDistrict && (
                    <div className="absolute left-0 right-0 mt-1 bg-white border border-slate-100 rounded-xl shadow-lg z-50 p-2 text-left">
                      <input
                        type="text"
                        placeholder="Tìm kiếm..."
                        value={wardSearch}
                        onChange={(e) => setWardSearch(e.target.value)}
                        className="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs outline-none mb-2"
                      />
                      <div className="max-h-48 overflow-y-auto space-y-0.5">
                        {filteredWards.map(w => (
                          <button
                            key={w.Id}
                            type="button"
                            onClick={() => {
                              setSelectedWard(w)
                              setWardOpen(false)
                            }}
                            className="w-full px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 rounded-lg text-left font-semibold"
                          >
                            {w.Name}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>

              {/* Address details */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số nhà, tên đường <span className="text-red-500">*</span></label>
                <input
                  type="text"
                  value={address}
                  onChange={(e) => setAddress(e.target.value)}
                  required
                  placeholder="VD: 222 Lê Văn Sỹ"
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>

              {/* Map coordinates picker */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Vị trí bản đồ (Kéo thả ghim để chọn chính xác)</label>
                <div className="w-full h-[280px] rounded-2xl overflow-hidden border border-slate-200 relative">
                  <MapPicker
                    latitude={latitude}
                    longitude={longitude}
                    onCoordinatesChange={handleCoordinatesChange}
                  />
                </div>
                <div className="flex justify-between items-center text-[10px] text-slate-400 font-bold px-1 mt-1.5">
                  <span>Kinh độ: {longitude.toFixed(6)}</span>
                  <span>Vĩ độ: {latitude.toFixed(6)}</span>
                </div>
              </div>
            </div>

            {/* Section 3: Media & Images */}
            <div className="space-y-6 text-left">
              <h3 className="text-xs font-black uppercase tracking-wider text-primary">3. Hình ảnh &amp; Thư viện</h3>

              {/* Cover Image URL */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Liên kết ảnh bìa đại diện <span className="text-red-500">*</span></label>
                <input
                  type="url"
                  value={imageUrl}
                  onChange={(e) => setImageUrl(e.target.value)}
                  required
                  placeholder="Nhập URL ảnh bìa (VD: https://images.unsplash.com/photo-...)"
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                />
                {imageUrl && (
                  <div className="mt-2.5 w-32 h-20 rounded-xl overflow-hidden border border-slate-100">
                    <img src={imageUrl} alt="Cover Preview" className="w-full h-full object-cover" />
                  </div>
                )}
              </div>

              {/* Gallery Images URLs */}
              <div className="space-y-1">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Thư viện ảnh phụ (Mỗi dòng là một liên kết URL)</label>
                <textarea
                  value={galleryUrlsText}
                  onChange={(e) => setGalleryUrlsText(e.target.value)}
                  rows={4}
                  placeholder="Nhập mỗi liên kết hình ảnh phụ trên 1 dòng..."
                  className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition resize-y font-mono"
                />
              </div>
            </div>

            {/* Submit buttons */}
            <div className="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3 text-xs">
              <Link
                href="/profile?tab=properties"
                className="px-5 py-3 border border-slate-200 hover:bg-slate-50 text-slate-650 hover:text-slate-800 font-bold rounded-xl transition cursor-pointer"
              >
                Hủy bỏ
              </Link>
              <button
                type="submit"
                disabled={isSubmitting}
                className="px-7 py-3 bg-primary hover:bg-primary-hover text-white font-extrabold rounded-xl shadow-lg shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-50 flex items-center space-x-2"
              >
                {isSubmitting && <i className="fa-solid fa-circle-notch fa-spin text-xs" />}
                <span>Lưu thay đổi</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}
