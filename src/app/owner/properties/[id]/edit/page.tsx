'use client'

import { useState, useEffect, useRef } from 'react'
import { useRouter, useParams } from 'next/navigation'
import Link from 'next/link'
import dynamic from 'next/dynamic'
import { Toaster, toast } from 'sonner'
import { useSession } from 'next-auth/react'

// Dynamically import MapLibre picker map to bypass SSR
const MapPicker = dynamic(() => import('@/components/property/MapPicker'), {
  ssr: false,
  loading: () => (
    <div className="w-full h-[250px] bg-slate-100 dark:bg-gray-800 flex items-center justify-center rounded-2xl border border-slate-200 dark:border-gray-800">
      <span className="text-xs text-slate-500 font-bold animate-pulse">Đang tải bản đồ vị trí...</span>
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

export default function OwnerPropertyEditPage() {
  const { data: session, status } = useSession()
  const user = session?.user as any
  const router = useRouter()
  const params = useParams()
  const propertyId = params.id as string

  // Dropdown ref hooks
  const provinceRef = useRef<HTMLDivElement>(null)
  const districtRef = useRef<HTMLDivElement>(null)
  const wardRef = useRef<HTMLDivElement>(null)

  // Form fields states
  const [purpose, setPurpose] = useState<'rent' | 'sale'>('rent')
  const [title, setTitle] = useState('')
  const [propertyType, setPropertyType] = useState('Căn hộ')
  const [price, setPrice] = useState('')
  const [area, setArea] = useState('')
  const [bedroom, setBedroom] = useState('0')
  const [bathroom, setBathroom] = useState('0')

  // Sale specific specs
  const [frontage, setFrontage] = useState('')
  const [roadWidth, setRoadWidth] = useState('')
  const [floors, setFloors] = useState('')

  // Rent specific specs
  const [deposit, setDeposit] = useState('')
  const [leaseTerm, setLeaseTerm] = useState('')

  // General Specs
  const [direction, setDirection] = useState('')
  const [legal, setLegal] = useState('')
  const [furniture, setFurniture] = useState('')
  const [description, setDescription] = useState('')

  // Address divisions
  const [provinces, setProvinces] = useState<Province[]>([])
  const [provinceSearch, setProvinceSearch] = useState('')
  const [selectedProvince, setSelectedProvince] = useState<Province | null>(null)
  const [provinceOpen, setProvinceOpen] = useState(false)

  const [districtSearch, setDistrictSearch] = useState('')
  const [selectedDistrict, setSelectedDistrict] = useState<District | null>(null)
  const [districtOpen, setDistrictOpen] = useState(false)

  const [nksAdministratives, setNksAdministratives] = useState<Ward[]>([])
  const [wardSearch, setWardSearch] = useState('')
  const [selectedWard, setSelectedWard] = useState<Ward | null>(null)
  const [wardOpen, setWardOpen] = useState(false)

  const [address, setAddress] = useState('')
  const [latitude, setLatitude] = useState(10.7769)
  const [longitude, setLongitude] = useState(106.7009)

  // Contact Info states
  const [contactName, setContactName] = useState('')
  const [contactPhone, setContactPhone] = useState('')
  const [contactEmail, setContactEmail] = useState('')

  // Images states
  const [imageUrl, setImageUrl] = useState('')
  const [galleryUrlsText, setGalleryUrlsText] = useState('')

  const [isSubmitting, setIsSubmitting] = useState(false)
  const [isLoading, setIsLoading] = useState(true)
  const [errorMsg, setErrorMsg] = useState('')

  // Temporary holding variables for initial matching
  const [initialCity, setInitialCity] = useState('')
  const [initialWard, setInitialWard] = useState('')

  // Click outside listener for dropdowns
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
    return () => document.removeEventListener('mousedown', handleClickOutside)
  }, [])

  // Check auth
  useEffect(() => {
    if (status === 'unauthenticated') {
      router.push(`/login?callbackUrl=/owner/properties/${propertyId}/edit`)
    } else if (status === 'authenticated' && !['owner', 'agent', 'admin'].includes(user?.role)) {
      toast.error('Chỉ dành cho Đối tác Chủ nhà hoặc Môi giới. Vui lòng nâng cấp tài khoản.')
      router.push('/profile?tab=register_owner')
    }
  }, [status, user, router, propertyId])

  // Fetch NKS Provinces
  useEffect(() => {
    fetch('/api/nks/provinces', { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data && data.success && Array.isArray(data.data)) {
          const nksProvList: Province[] = data.data.map((item: any) => ({
            Id: String(item.id),
            Name: item.title,
            Districts: []
          }))
          setProvinces(nksProvList)
        }
      })
      .catch(err => console.error('Error loading NKS provinces:', err))
  }, [])

  // Fetch property data
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
            setTitle(p.title || '')
            setPropertyType(p.propertyType || 'Căn hộ')
            setPrice(String(p.price || ''))
            setArea(String(p.area || ''))
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
            setLatitude(p.latitude || 10.7769)
            setLongitude(p.longitude || 106.7009)
            setContactName(p.phone ? p.title.split(' ')[0] || user?.name || '' : user?.name || '')
            setContactPhone(p.phone || user?.phone || '')
            setContactEmail(user?.email || '')

            setInitialCity(p.city || '')
            setInitialWard(p.ward || '')

            // Primary image & gallery
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
  }, [propertyId, user])

  // Match initial province & fetch wards
  useEffect(() => {
    if (provinces.length > 0 && initialCity && !selectedProvince) {
      const match = provinces.find(
        p => p.Name.toLowerCase().includes(initialCity.toLowerCase()) || initialCity.toLowerCase().includes(p.Name.toLowerCase())
      )
      if (match) {
        setSelectedProvince(match)
        setProvinceSearch(match.Name)

        fetch(`/api/nks/administratives?province_id=${match.Id}`, { method: 'POST' })
          .then(res => res.json())
          .then(data => {
            if (data && data.success && Array.isArray(data.data)) {
              const adminWards: Ward[] = data.data.map((item: any) => ({
                Id: String(item.id),
                Name: item.title
              }))
              setNksAdministratives(adminWards)
              if (initialWard) {
                const wardMatch = adminWards.find(
                  w => w.Name.toLowerCase().includes(initialWard.toLowerCase()) || initialWard.toLowerCase().includes(w.Name.toLowerCase())
                )
                if (wardMatch) {
                  setSelectedWard(wardMatch)
                  setWardSearch(wardMatch.Name)
                }
              }
            }
          })
      }
    }
  }, [provinces, initialCity, initialWard, selectedProvince])

  // Province selection handler
  const handleProvinceSelect = (province: Province) => {
    setSelectedProvince(province)
    setProvinceSearch(province.Name)
    setProvinceOpen(false)

    setSelectedDistrict(null)
    setDistrictSearch('')
    setSelectedWard(null)
    setWardSearch('')
    setNksAdministratives([])

    fetch(`/api/nks/administratives?province_id=${province.Id}`, { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data && data.success && Array.isArray(data.data)) {
          const adminWards: Ward[] = data.data.map((item: any) => ({
            Id: String(item.id),
            Name: item.title
          }))
          setNksAdministratives(adminWards)
        }
      })
      .catch(err => console.error('Error loading NKS administratives:', err))
  }

  // Handle Photo uploading & Conversion to Base64
  const handlePhotoUpload = (e: React.ChangeEvent<HTMLInputElement>, isPrimary: boolean) => {
    const files = e.target.files
    if (!files || files.length === 0) return

    const convertToBase64 = (file: File): Promise<string> => {
      return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = () => resolve(reader.result as string)
        reader.onerror = error => reject(error)
      })
    }

    if (isPrimary) {
      convertToBase64(files[0])
        .then(base64 => {
          setImageUrl(base64)
          toast.success('Đã thay đổi ảnh đại diện thành công!')
        })
        .catch(err => toast.error('Lỗi khi xử lý ảnh đại diện.'))
    } else {
      const promises = Array.from(files).map(file => convertToBase64(file))
      Promise.all(promises)
        .then(base64Array => {
          const currentText = galleryUrlsText ? galleryUrlsText.trim() + '\n' : ''
          setGalleryUrlsText(currentText + base64Array.join('\n'))
          toast.success(`Đã tải lên thêm ${base64Array.length} ảnh phụ thành công!`)
        })
        .catch(err => toast.error('Lỗi khi xử lý danh sách ảnh phụ.'))
    }
  }

  // Form Submission for PUT update
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)
    setErrorMsg('')

    if (!selectedProvince || !selectedWard || !address) {
      setErrorMsg('Vui lòng chọn đầy đủ thông tin Tỉnh/Thành phố, Phường/Xã và Địa chỉ chi tiết.')
      setIsSubmitting(false)
      return
    }

    if (!imageUrl) {
      setErrorMsg('Vui lòng chọn hoặc tải lên ảnh đại diện chính.')
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
          district: selectedDistrict?.Name || selectedProvince.Name,
          ward: selectedWard.Name,
          address,
          latitude,
          longitude,
          image_url: imageUrl,
          gallery_urls: galleryUrls,
          nksProvinceId: selectedProvince.Id,
          nksAdministrativeId: selectedWard.Id
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        toast.success('Cập nhật thông tin bất động sản thành công!')
        router.push('/owner/properties')
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

  // Dropdown list filters
  const filteredProvinces = provinces.filter(p =>
    !provinceSearch || p.Name.toLowerCase().includes(provinceSearch.toLowerCase())
  )

  const filteredWards = selectedProvince && nksAdministratives.length > 0
    ? nksAdministratives.filter(w =>
        !wardSearch || w.Name.toLowerCase().includes(wardSearch.toLowerCase())
      )
    : []

  if (status === 'loading' || isLoading) {
    return (
      <div className="flex items-center justify-center min-h-[50vh]">
        <div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6 text-left max-w-4xl mx-auto">
      <Toaster position="top-right" richColors />

      {/* Header Title & Purpose Switcher */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-gray-800 pb-5">
        <div>
          <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
            Chỉnh sửa tin đăng
          </h1>
          <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
            Cập nhật chi tiết thông tin và đồng bộ trực tiếp lên hệ thống NKS.
          </p>
        </div>

        {/* Transaction Purpose Switcher */}
        <div className="flex bg-slate-100 dark:bg-gray-850 p-1.5 rounded-2xl w-max self-start sm:self-center">
          <button
            type="button"
            onClick={() => setPurpose('rent')}
            className={`px-5 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${
              purpose === 'rent' ? 'bg-white dark:bg-gray-800 text-primary shadow-sm' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            Tin Cho Thuê
          </button>
          <button
            type="button"
            onClick={() => setPurpose('sale')}
            className={`px-5 py-2 rounded-xl text-xs font-bold transition cursor-pointer ${
              purpose === 'sale' ? 'bg-white dark:bg-gray-800 text-primary shadow-sm' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            Tin Mua Bán
          </button>
        </div>
      </div>

      {/* Edit Form Card */}
      <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        {errorMsg && (
          <div className="mb-6 p-4 bg-rose-50 dark:bg-rose-955/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-bold flex items-center gap-2">
            <i className="fa-solid fa-circle-exclamation text-sm" />
            {errorMsg}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">

          {/* Section 1: Basic Info */}
          <div className="space-y-5">
            <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
              1. Thông tin cơ bản bất động sản
            </h3>

            {/* Dropdown for Property Type */}
            <div className="space-y-1">
              <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Loại hình bất động sản <span className="text-rose-500">*</span></label>
              <select 
                value={propertyType} 
                onChange={(e) => setPropertyType(e.target.value)}
                className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-2xl text-xs font-bold outline-none transition cursor-pointer"
              >
                <option value="Căn hộ">Căn hộ</option>
                <option value="Nhà phố">Nhà phố</option>
                <option value="Biệt thự">Biệt thự</option>
                <option value="Mặt bằng">Mặt bằng</option>
              </select>
            </div>

            {/* Title */}
            <div className="space-y-1">
              <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Tiêu đề tin đăng <span className="text-rose-500">*</span></label>
              <input 
                type="text" 
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                required 
                placeholder="Ví dụ: Căn hộ Studio Vinhomes Ocean Park Full Nội Thất..." 
                className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
              />
            </div>

            {/* Price & Area row */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">
                  {purpose === 'rent' ? 'Giá thuê (đ/tháng)' : 'Giá bán (VND)'} <span className="text-rose-500">*</span>
                </label>
                <input 
                  type="number" 
                  value={price}
                  onChange={(e) => setPrice(e.target.value)}
                  required
                  placeholder="Ví dụ: 12000000" 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                />
                {price && Number(price) > 0 && (
                  <span className="block text-[10px] text-primary font-bold mt-1 px-1">
                    Hiển thị: {Number(price) >= 1000000000 ? `${(Number(price) / 1000000000).toFixed(1).replace(/\.0$/, '')} tỷ` : Number(price) >= 1000000 ? `${(Number(price) / 1000000).toFixed(1).replace(/\.0$/, '')} triệu` : `${Number(price).toLocaleString('vi-VN')} đ`}{purpose === 'rent' ? '/tháng' : ''}
                  </span>
                )}
              </div>
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Diện tích (m²) <span className="text-rose-500">*</span></label>
                <input 
                  type="number" 
                  value={area}
                  onChange={(e) => setArea(e.target.value)}
                  required
                  placeholder="Ví dụ: 65" 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>
            </div>

            {/* Room selections */}
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Số phòng ngủ</label>
                <select 
                  value={bedroom} 
                  onChange={(e) => setBedroom(e.target.value)}
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-2xl text-xs font-bold outline-none transition cursor-pointer"
                >
                  <option value="0">0 (Studio / Mặt bằng)</option>
                  <option value="1">1 phòng ngủ</option>
                  <option value="2">2 phòng ngủ</option>
                  <option value="3">3 phòng ngủ</option>
                  <option value="4">4+ phòng ngủ</option>
                </select>
              </div>
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Số phòng tắm</label>
                <select 
                  value={bathroom} 
                  onChange={(e) => setBathroom(e.target.value)}
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-2xl text-xs font-bold outline-none transition cursor-pointer"
                >
                  <option value="0">0 phòng tắm</option>
                  <option value="1">1 phòng tắm</option>
                  <option value="2">2 phòng tắm</option>
                  <option value="3">3 phòng tắm</option>
                  <option value="4">4+ phòng tắm</option>
                </select>
              </div>
            </div>

            {/* Detailed description */}
            <div className="space-y-1">
              <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Mô tả chi tiết <span className="text-rose-500">*</span></label>
              <textarea 
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                required
                rows={6}
                placeholder="Nhập thông tin giới thiệu chi tiết về phòng ốc, tiện ích..."
                className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition resize-y"
              />
            </div>
          </div>

          {/* Section 2: Specifications */}
          <div className="space-y-4 pt-4 border-t border-slate-100 dark:border-gray-850">
            <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
              2. Thông số kỹ thuật bất động sản
            </h3>

            {purpose === 'rent' ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Tiền đặt cọc (VND)</label>
                  <input 
                    type="number" 
                    value={deposit}
                    onChange={(e) => setDeposit(e.target.value)}
                    placeholder="Ví dụ: 24000000" 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Thời hạn hợp đồng</label>
                  <input 
                    type="text" 
                    value={leaseTerm}
                    onChange={(e) => setLeaseTerm(e.target.value)}
                    placeholder="Ví dụ: Tối thiểu 1 năm" 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>
            ) : (
              <div className="grid grid-cols-3 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Mặt tiền (m)</label>
                  <input 
                    type="number" 
                    value={frontage}
                    onChange={(e) => setFrontage(e.target.value)}
                    placeholder="Mặt tiền ngang" 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Đường trước nhà (m)</label>
                  <input 
                    type="number" 
                    value={roadWidth}
                    onChange={(e) => setRoadWidth(e.target.value)}
                    placeholder="Độ rộng đường" 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Số tầng</label>
                  <input 
                    type="number" 
                    value={floors}
                    onChange={(e) => setFloors(e.target.value)}
                    placeholder="Số tầng lầu" 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Hướng nhà/ban công</label>
                <select 
                  value={direction}
                  onChange={(e) => setDirection(e.target.value)}
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-2xl text-xs font-bold outline-none transition cursor-pointer"
                >
                  <option value="">-- Chọn hướng nhà / ban công --</option>
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
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Tình trạng pháp lý</label>
                <input 
                  type="text" 
                  value={legal}
                  onChange={(e) => setLegal(e.target.value)}
                  placeholder="Ví dụ: Sổ hồng, Hợp đồng" 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Tình trạng nội thất</label>
                <input 
                  type="text" 
                  value={furniture}
                  onChange={(e) => setFurniture(e.target.value)}
                  placeholder="Ví dụ: Full nội thất cao cấp" 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>
            </div>
          </div>

          {/* Section 3: Location */}
          <div className="space-y-5 pt-4 border-t border-slate-100 dark:border-gray-850">
            <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
              3. Vị trí địa lý
            </h3>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {/* Provinces */}
              <div ref={provinceRef} className="space-y-1 relative">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Tỉnh / Thành phố <span className="text-rose-500">*</span></label>
                <button
                  type="button"
                  onClick={() => setProvinceOpen(!provinceOpen)}
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 text-left rounded-2xl text-xs font-semibold flex items-center justify-between cursor-pointer"
                >
                  <span>{selectedProvince ? selectedProvince.Name : 'Chọn Tỉnh / Thành phố'}</span>
                  <i className={`fa-solid fa-chevron-down text-[10px] transition ${provinceOpen ? 'rotate-180' : ''}`} />
                </button>
                {provinceOpen && (
                  <div className="absolute left-0 top-full pt-1.5 w-full z-50 animate-dropdown">
                    <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-855 rounded-2xl shadow-xl p-2 max-h-[220px] overflow-y-auto thin-scrollbar">
                      <input
                        type="text"
                        value={provinceSearch}
                        onChange={(e) => setProvinceSearch(e.target.value)}
                        placeholder="Tìm kiếm..."
                        className="w-full px-3 py-2 border border-slate-200 dark:border-gray-850 rounded-xl text-[11px] font-semibold outline-none mb-2"
                      />
                      {filteredProvinces.map((p) => (
                        <button
                          key={p.Id}
                          type="button"
                          onClick={() => handleProvinceSelect(p)}
                          className="w-full text-left px-3 py-2 text-xs font-semibold rounded-lg hover:bg-slate-55 dark:hover:bg-gray-850 transition cursor-pointer"
                        >
                          {p.Name}
                        </button>
                      ))}
                    </div>
                  </div>
                )}
              </div>

              {/* Wards */}
              <div ref={wardRef} className="space-y-1 relative">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Phường / Xã <span className="text-rose-500">*</span></label>
                <button
                  type="button"
                  onClick={() => {
                    if (!selectedProvince) {
                      toast.warning('Vui lòng chọn Tỉnh/Thành phố trước!')
                      return
                    }
                    setWardOpen(!wardOpen)
                  }}
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 text-left rounded-2xl text-xs font-semibold flex items-center justify-between cursor-pointer"
                >
                  <span>{selectedWard ? selectedWard.Name : 'Chọn Phường / Xã'}</span>
                  <i className={`fa-solid fa-chevron-down text-[10px] transition ${wardOpen ? 'rotate-180' : ''}`} />
                </button>
                {wardOpen && (
                  <div className="absolute left-0 top-full pt-1.5 w-full z-50 animate-dropdown">
                    <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-855 rounded-2xl shadow-xl p-2 max-h-[220px] overflow-y-auto thin-scrollbar">
                      <input
                        type="text"
                        value={wardSearch}
                        onChange={(e) => setWardSearch(e.target.value)}
                        placeholder="Tìm kiếm..."
                        className="w-full px-3 py-2 border border-slate-200 dark:border-gray-850 rounded-xl text-[11px] font-semibold outline-none mb-2"
                      />
                      {filteredWards.map((w) => (
                        <button
                          key={w.Id}
                          type="button"
                          onClick={() => {
                            setSelectedWard(w)
                            setWardSearch(w.Name)
                            setWardOpen(false)
                          }}
                          className="w-full text-left px-3 py-2 text-xs font-semibold rounded-lg hover:bg-slate-55 dark:hover:bg-gray-850 transition cursor-pointer"
                        >
                          {w.Name}
                        </button>
                      ))}
                      {filteredWards.length === 0 && (
                        <div className="py-4 text-center text-[10px] text-slate-400 font-bold">Không tìm thấy phường/xã</div>
                      )}
                    </div>
                  </div>
                )}
              </div>
            </div>

            {/* Address */}
            <div className="space-y-1">
              <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Địa chỉ chi tiết (Số nhà, tên đường...) <span className="text-rose-500">*</span></label>
              <input 
                type="text" 
                value={address}
                onChange={(e) => setAddress(e.target.value)}
                required 
                placeholder="Ví dụ: 208 Nguyễn Hữu Cảnh" 
                className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
              />
            </div>

            {/* Map Coordinates & MapPicker display */}
            <div className="space-y-3 pt-2">
              <span className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-1">Định vị & Ô nhập tọa độ bất động sản</span>

              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">
                  Tọa độ vị trí (Vĩ độ, Kinh độ) <span className="text-rose-500">*</span>
                </label>
                <input 
                  type="text" 
                  value={`${latitude}, ${longitude}`}
                  onChange={(e) => {
                    const val = e.target.value
                    const parts = val.split(',').map(s => s.trim())
                    if (parts.length >= 2) {
                      const lat = parseFloat(parts[0])
                      const lng = parseFloat(parts[1])
                      if (!isNaN(lat)) setLatitude(lat)
                      if (!isNaN(lng)) setLongitude(lng)
                    }
                  }}
                  placeholder="Ví dụ: 10.7769, 106.7009" 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-2xl text-xs font-semibold outline-none transition"
                />
                <span className="block text-[9px] text-slate-400 font-semibold italic mt-0.5 px-1">
                  Nhập theo định dạng "Vĩ độ, Kinh độ" (Ví dụ: 10.7769, 106.7009) hoặc di chuyển ghim trên bản đồ bên dưới.
                </span>
              </div>

              <MapPicker 
                latitude={latitude} 
                longitude={longitude} 
                onCoordinatesChange={(lat, lng) => { setLatitude(lat); setLongitude(lng); }} 
              />
            </div>
          </div>

          {/* Section 4: Images */}
          <div className="space-y-5 pt-4 border-t border-slate-100 dark:border-gray-850">
            <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
              4. Hình ảnh bất động sản
            </h3>

            {/* Cover image upload container */}
            <div className="space-y-2">
              <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Ảnh đại diện chính <span className="text-rose-500">*</span></label>
              <div className="flex flex-col sm:flex-row gap-5 items-center">
                <div className="w-full sm:w-1/2">
                  <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 hover:border-primary rounded-2xl cursor-pointer hover:bg-slate-50/50 dark:hover:bg-gray-850 transition">
                    <div className="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                      <i className="fa-solid fa-cloud-arrow-up text-xl text-slate-400 mb-2 group-hover:text-primary" />
                      <p className="text-[10px] text-slate-500 font-bold">KÉO THẢ HOẶC NHẤP ĐỂ ĐỔI ẢNH BÌA</p>
                    </div>
                    <input 
                      type="file" 
                      accept="image/*"
                      onChange={(e) => handlePhotoUpload(e, true)}
                      className="hidden" 
                    />
                  </label>
                </div>
                <div className="w-full sm:w-1/2 flex items-center justify-center border border-slate-100 dark:border-gray-855 rounded-2xl p-2 h-32 bg-slate-55 dark:bg-gray-955 relative overflow-hidden">
                  {imageUrl ? (
                    <img 
                      src={imageUrl} 
                      alt="Cover Preview" 
                      className="max-w-full max-h-full object-contain rounded-lg"
                    />
                  ) : (
                    <span className="text-[10px] text-slate-400 font-bold">Chưa có ảnh bìa</span>
                  )}
                </div>
              </div>
            </div>

            {/* Gallery images container */}
            <div className="space-y-2 pt-2">
              <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Album ảnh phụ</label>
              <div className="flex flex-col sm:flex-row gap-5 items-center">
                <div className="w-full sm:w-1/2">
                  <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 hover:border-primary rounded-2xl cursor-pointer hover:bg-slate-50/50 dark:hover:bg-gray-855 transition">
                    <div className="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                      <i className="fa-solid fa-photo-film text-xl text-slate-400 mb-2 group-hover:text-primary" />
                      <p className="text-[10px] text-slate-500 font-bold">TẢI THÊM ALBUM ẢNH PHỤ</p>
                    </div>
                    <input 
                      type="file" 
                      multiple
                      accept="image/*"
                      onChange={(e) => handlePhotoUpload(e, false)}
                      className="hidden" 
                    />
                  </label>
                </div>
                <div className="w-full sm:w-1/2 border border-slate-100 dark:border-gray-855 rounded-2xl p-3 h-32 bg-slate-55 dark:bg-gray-955 overflow-y-auto scrollbar-thin">
                  {galleryUrlsText.trim().split('\n').filter(Boolean).length > 0 ? (
                    <div className="grid grid-cols-3 gap-2">
                      {galleryUrlsText.split('\n').map((url, i) => {
                        if (!url) return null
                        return (
                          <div key={i} className="relative aspect-video rounded-lg overflow-hidden bg-white border border-slate-150 flex items-center justify-center group">
                            <img 
                              src={url} 
                              alt="Gallery Preview" 
                              className="w-full h-full object-cover"
                            />
                            <button
                              type="button"
                              onClick={() => {
                                const list = galleryUrlsText.split('\n').filter((_, idx) => idx !== i)
                                setGalleryUrlsText(list.join('\n'))
                              }}
                              className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition cursor-pointer text-white text-[10px]"
                            >
                              <i className="fa-solid fa-trash-can mr-1"></i> Xóa
                            </button>
                          </div>
                        )
                      })}
                    </div>
                  ) : (
                    <div className="w-full h-full flex items-center justify-center text-[10px] text-slate-400 font-bold">Chưa có album ảnh phụ</div>
                  )}
                </div>
              </div>
            </div>
          </div>

          {/* Submit buttons */}
          <div className="flex items-center justify-end gap-3 border-t border-slate-100 dark:border-gray-850 pt-5 mt-6">
            <Link
              href="/owner/properties"
              className="px-5 py-3 rounded-xl border border-slate-200 dark:border-gray-850 text-slate-655 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-850 text-xs font-bold transition cursor-pointer"
            >
              Hủy bỏ
            </Link>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-6 py-3 rounded-xl bg-primary hover:bg-primary-hover disabled:bg-primary/50 text-white text-xs font-bold transition cursor-pointer active:scale-95 shadow-md shadow-primary/10 flex items-center gap-2"
            >
              {isSubmitting ? (
                <>
                  <i className="fa-solid fa-spinner animate-spin" /> Đang cập nhật...
                </>
              ) : (
                <>
                  <i className="fa-solid fa-floppy-disk" /> Lưu cập nhật
                </>
              )}
            </button>
          </div>

        </form>
      </div>
    </div>
  )
}
