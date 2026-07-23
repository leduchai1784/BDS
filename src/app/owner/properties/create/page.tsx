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

export default function OwnerPropertyCreatePage() {
  const { data: session, status } = useSession()
  const user = session?.user as any
  const router = useRouter()

  // Dropdown ref hooks
  const provinceRef = useRef<HTMLDivElement>(null)
  const districtRef = useRef<HTMLDivElement>(null)
  const wardRef = useRef<HTMLDivElement>(null)

  // Stepper State
  const [currentStep, setCurrentStep] = useState(1)

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

  // Contact Info states (Pre-filled from session)
  const [contactName, setContactName] = useState('')
  const [contactPhone, setContactPhone] = useState('')
  const [contactEmail, setContactEmail] = useState('')

  // Images states
  const [imageUrl, setImageUrl] = useState('')
  const [galleryUrlsText, setGalleryUrlsText] = useState('')

  const [isSubmitting, setIsSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  // Draft info modal state
  const [hasDraft, setHasDraft] = useState(false)
  const [draftTime, setDraftTime] = useState('')

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
      router.push('/login?callbackUrl=/owner/properties/create')
    } else if (status === 'authenticated') {
      if (!['owner', 'agent', 'admin'].includes(user?.role)) {
        toast.error('Chỉ dành cho Đối tác Chủ nhà hoặc Môi giới. Vui lòng nâng cấp tài khoản.')
        router.push('/profile?tab=register_owner')
      } else {
        // Pre-fill contact details from user session
        setContactName(user.name || '')
        setContactEmail(user.email || '')
        setContactPhone(user.phone || '')
      }
    }
  }, [status, user, router])

  // Fetch provinces
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

  // Check if draft exists in localStorage
  useEffect(() => {
    const savedDraft = localStorage.getItem('bds_property_draft')
    if (savedDraft) {
      try {
        const parsed = JSON.parse(savedDraft)
        if (parsed.title || parsed.description || parsed.address) {
          setHasDraft(true)
          setDraftTime(parsed.savedAt || 'Vừa xong')
        }
      } catch (e) {
        console.error('Error loading draft header:', e)
      }
    }
  }, [])

  // Load NKS Wards when province changes
  const handleProvinceSelect = (province: Province) => {
    setSelectedProvince(province)
    setProvinceSearch(province.Name)
    setProvinceOpen(false)

    // Reset subordinate divisions
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

  // Geocoding effect
  useEffect(() => {
    if (!address || !selectedProvince) return
    const timer = setTimeout(async () => {
      const fullAddr = `${address}, ${selectedWard?.Name || ''}, ${selectedDistrict?.Name || ''}, ${selectedProvince.Name}, Vietnam`
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
    }, 1200)
    return () => clearTimeout(timer)
  }, [address, selectedProvince, selectedDistrict, selectedWard])

  // Draft Saving helper
  const saveDraft = () => {
    const draftData = {
      purpose,
      title,
      propertyType,
      price,
      area,
      bedroom,
      bathroom,
      frontage,
      roadWidth,
      floors,
      deposit,
      leaseTerm,
      direction,
      legal,
      furniture,
      description,
      address,
      latitude,
      longitude,
      selectedProvince: selectedProvince ? { Id: selectedProvince.Id, Name: selectedProvince.Name } : null,
      selectedDistrict: selectedDistrict ? { Id: selectedDistrict.Id, Name: selectedDistrict.Name } : null,
      selectedWard: selectedWard ? { Id: selectedWard.Id, Name: selectedWard.Name } : null,
      contactName,
      contactPhone,
      contactEmail,
      imageUrl,
      galleryUrlsText,
      savedAt: new Date().toLocaleString('vi-VN')
    }
    localStorage.setItem('bds_property_draft', JSON.stringify(draftData))
    toast.success('Đã lưu bản nháp tin đăng thành công!')
    setHasDraft(true)
    setDraftTime(draftData.savedAt)
  }

  const loadDraft = () => {
    const saved = localStorage.getItem('bds_property_draft')
    if (saved) {
      try {
        const d = JSON.parse(saved)
        if (d.purpose) setPurpose(d.purpose)
        if (d.title) setTitle(d.title)
        if (d.propertyType) setPropertyType(d.propertyType)
        if (d.price) setPrice(d.price)
        if (d.area) setArea(d.area)
        if (d.bedroom) setBedroom(String(d.bedroom))
        if (d.bathroom) setBathroom(String(d.bathroom))
        if (d.frontage) setFrontage(d.frontage)
        if (d.roadWidth) setRoadWidth(d.roadWidth)
        if (d.floors) setFloors(d.floors)
        if (d.deposit) setDeposit(d.deposit)
        if (d.leaseTerm) setLeaseTerm(d.leaseTerm)
        if (d.direction) setDirection(d.direction)
        if (d.legal) setLegal(d.legal)
        if (d.furniture) setFurniture(d.furniture)
        if (d.description) setDescription(d.description)
        if (d.address) setAddress(d.address)
        if (d.latitude) setLatitude(d.latitude)
        if (d.longitude) setLongitude(d.longitude)
        if (d.contactName) setContactName(d.contactName)
        if (d.contactPhone) setContactPhone(d.contactPhone)
        if (d.contactEmail) setContactEmail(d.contactEmail)
        if (d.imageUrl) setImageUrl(d.imageUrl)
        if (d.galleryUrlsText) setGalleryUrlsText(d.galleryUrlsText)

        if (d.selectedProvince) {
          setSelectedProvince(d.selectedProvince)
          setProvinceSearch(d.selectedProvince.Name)
          // Fetch wards for that province
          fetch(`/api/nks/administratives?province_id=${d.selectedProvince.Id}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
              if (data && data.success && Array.isArray(data.data)) {
                const adminWards: Ward[] = data.data.map((item: any) => ({
                  Id: String(item.id),
                  Name: item.title
                }))
                setNksAdministratives(adminWards)
                if (d.selectedWard) {
                  const matchedWard = adminWards.find(w => w.Id === d.selectedWard.Id)
                  if (matchedWard) {
                    setSelectedWard(matchedWard)
                    setWardSearch(matchedWard.Name)
                  }
                }
              }
            })
        }
        if (d.selectedDistrict) {
          setSelectedDistrict(d.selectedDistrict)
          setDistrictSearch(d.selectedDistrict.Name)
        }

        toast.success('Đã phục hồi bản nháp tin đăng thành công!')
      } catch (err) {
        console.error('Failed to load draft:', err)
        toast.error('Lỗi khi tải dữ liệu bản nháp.')
      } finally {
        setHasDraft(false)
      }
    }
  }

  const discardDraft = () => {
    localStorage.removeItem('bds_property_draft')
    setHasDraft(false)
    toast.info('Đã hủy bỏ bản nháp cũ.')
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
          toast.success('Đã tải lên ảnh đại diện thành công!')
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

  // Form Submission
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)
    setErrorMsg('')

    if (!selectedProvince || !selectedWard || !address) {
      setErrorMsg('Vui lòng điền đầy đủ thông tin địa chỉ vị trí ở Bước 2.')
      setCurrentStep(2)
      setIsSubmitting(false)
      return
    }

    if (!imageUrl) {
      setErrorMsg('Vui lòng chọn hoặc tải lên ảnh đại diện ở Bước 4.')
      setCurrentStep(4)
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
          district: selectedDistrict?.Name || selectedProvince.Name,
          ward: selectedWard.Name,
          address,
          latitude,
          longitude,
          image_url: imageUrl,
          gallery_urls: galleryUrls,
          nksProvinceId: selectedProvince.Id,
          nksAdministrativeId: selectedWard.Id,
          contactName,
          contactPhone,
          contactEmail
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        toast.success('Đăng tin bất động sản thành công!')
        // Clean draft
        localStorage.removeItem('bds_property_draft')
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

  // Dropdown list filters
  const filteredProvinces = provinces.filter(p =>
    !provinceSearch || p.Name.toLowerCase().includes(provinceSearch.toLowerCase())
  )

  const filteredWards = selectedProvince && nksAdministratives.length > 0
    ? nksAdministratives.filter(w =>
        !wardSearch || w.Name.toLowerCase().includes(wardSearch.toLowerCase())
      )
    : []

  // Steps validations
  const isStepValid = (step: number) => {
    if (step === 1) {
      return title.trim().length > 5 && Number(price) > 0 && Number(area) > 0 && description.trim().length > 10
    }
    if (step === 2) {
      return !!selectedProvince && !!selectedWard && address.trim().length > 5
    }
    if (step === 3) {
      return contactName.trim().length > 2 && contactPhone.trim().length > 8
    }
    return true
  }

  if (status === 'loading') {
    return (
      <div className="flex items-center justify-center min-h-[50vh]">
        <div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6 text-left max-w-4xl mx-auto">
      <Toaster position="top-right" richColors />

      {/* Header Title */}
      <div className="border-b border-slate-100 dark:border-gray-800 pb-5">
        <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
          Đăng tin mới
        </h1>
        <p className="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
          Giao diện tạo tin đăng từng bước chuyên nghiệp liên kết đồng bộ NKS.
        </p>
      </div>

      {/* Draft Notification Alert Banner */}
      {hasDraft && (
        <div className="bg-amber-50/80 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-fadeIn">
          <div className="flex gap-3">
            <div className="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-base flex-shrink-0">
              <i className="fa-solid fa-file-signature"></i>
            </div>
            <div>
              <h4 className="text-xs font-extrabold text-amber-850 dark:text-amber-400">Tìm thấy bản nháp tin đăng trước đó</h4>
              <p className="text-[11px] text-amber-600/90 dark:text-amber-500 font-medium mt-0.5">Được lưu vào lúc: <span className="font-bold">{draftTime}</span></p>
            </div>
          </div>
          <div className="flex items-center gap-2 self-end sm:self-center">
            <button
              type="button"
              onClick={discardDraft}
              className="px-3.5 py-1.5 rounded-lg border border-amber-300/40 hover:bg-amber-100/30 text-amber-700 dark:text-amber-500 text-[10px] font-bold transition cursor-pointer"
            >
              Hủy bỏ
            </button>
            <button
              type="button"
              onClick={loadDraft}
              className="px-4 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold transition cursor-pointer shadow-sm active:scale-95"
            >
              Tiếp tục điền
            </button>
          </div>
        </div>
      )}

      {/* Stepper Header Progress Indicator */}
      <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-5 shadow-sm">
        <div className="relative flex items-center justify-between w-full max-w-2xl mx-auto py-2">
          {/* Connecting line */}
          <div className="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[3px] bg-slate-100 dark:bg-gray-800 z-0">
            <div 
              className="h-full bg-primary transition-all duration-300"
              style={{ width: `${((currentStep - 1) / 3) * 100}%` }}
            />
          </div>

          {/* Step circles */}
          {[
            { step: 1, label: 'Thông tin cơ bản', icon: 'fa-file-invoice' },
            { step: 2, label: 'Vị trí', icon: 'fa-map-location-dot' },
            { step: 3, label: 'Liên hệ', icon: 'fa-address-book' },
            { step: 4, label: 'Hình ảnh', icon: 'fa-images' }
          ].map((item) => {
            const isActive = currentStep === item.step
            const isCompleted = currentStep > item.step

            return (
              <button
                key={item.step}
                type="button"
                onClick={() => {
                  if (isCompleted || isStepValid(item.step - 1) || item.step < currentStep) {
                    setCurrentStep(item.step)
                  }
                }}
                className="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none"
              >
                <div 
                  className={`w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 border-2 ${
                    isCompleted 
                      ? 'bg-emerald-500 border-emerald-500 text-white shadow-emerald-500/10' 
                      : isActive 
                      ? 'bg-primary border-primary text-white shadow-lg shadow-primary/25 scale-110' 
                      : 'bg-white dark:bg-gray-950 border-slate-200 dark:border-gray-800 text-slate-400 group-hover:border-slate-350'
                  }`}
                >
                  {isCompleted ? (
                    <i className="fa-solid fa-check" />
                  ) : (
                    <i className={`fa-solid ${item.icon}`} />
                  )}
                </div>
                <span 
                  className={`text-[9px] uppercase font-bold tracking-wider mt-2 transition-colors ${
                    isActive ? 'text-primary' : isCompleted ? 'text-emerald-500' : 'text-slate-400'
                  }`}
                >
                  {item.label}
                </span>
              </button>
            )
          })}
        </div>
      </div>

      {/* Stepper Content Form Card */}
      <div className="bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        {errorMsg && (
          <div className="mb-6 p-4 bg-rose-50 dark:bg-rose-955/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-bold flex items-center gap-2">
            <i className="fa-solid fa-circle-exclamation text-sm" />
            {errorMsg}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">

          {/* STEP 1: Thông tin cơ bản */}
          {currentStep === 1 && (
            <div className="space-y-5 animate-fadeIn">
              <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
                Bước 1: Thông tin cơ bản bất động sản
              </h3>

              {/* Segment switch */}
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Hình thức giao dịch</label>
                <div className="flex bg-slate-100 dark:bg-gray-850 p-1.5 rounded-2xl w-max">
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

              {/* Dropdown for Property Type */}
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Loại hình bất động sản <span className="text-rose-500">*</span></label>
                <select 
                  value={propertyType} 
                  onChange={(e) => setPropertyType(e.target.value)}
                  className="w-full px-4 py-3 bg-slate-55 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-bold outline-none transition"
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
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-bold outline-none transition"
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
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-bold outline-none transition"
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
                  placeholder="Nhập thông tin giới thiệu chi tiết về phòng ốc, tiện ích xung quanh, chính sách giờ giấc sinh hoạt..."
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition resize-y"
                />
              </div>

              {/* Specification fields */}
              <div className="space-y-4 pt-4 border-t border-slate-100 dark:border-gray-850">
                <span className="block text-[10px] font-black uppercase tracking-wider text-primary">Thông số kỹ thuật đi kèm</span>

                {/* Purpose specific specifications */}
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

                {/* Common specifications */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div className="space-y-1">
                    <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Hướng nhà/ban công</label>
                    <input 
                      type="text" 
                      value={direction}
                      onChange={(e) => setDirection(e.target.value)}
                      placeholder="Ví dụ: Đông Nam, Tây Bắc" 
                      className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                    />
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
            </div>
          )}

          {/* STEP 2: Vị trí bất động sản */}
          {currentStep === 2 && (
            <div className="space-y-5 animate-fadeIn">
              <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
                Bước 2: Địa điểm vị trí bất động sản
              </h3>

              {/* Administrative selections */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {/* Provinces */}
                <div ref={provinceRef} className="space-y-1 relative">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Tỉnh / Thành phố <span className="text-rose-500">*</span></label>
                  <button
                    type="button"
                    onClick={() => setProvinceOpen(!provinceOpen)}
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 text-left rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer"
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
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 text-left rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer"
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

              {/* Detailed street number address */}
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Địa chỉ chi tiết (Số nhà, tên đường...) <span className="text-rose-500">*</span></label>
                <input 
                  type="text" 
                  value={address}
                  onChange={(e) => setAddress(e.target.value)}
                  required 
                  placeholder="Ví dụ: Tòa Landmark 3, Vinhomes Central Park, 208 Nguyễn Hữu Cảnh" 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>

              {/* Map Coordinates & MapPicker display */}
              <div className="space-y-2 pt-2">
                <span className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-1">Định vị tọa độ bất động sản trên bản đồ</span>
                
                <div className="grid grid-cols-2 gap-4 mb-2">
                  <div className="px-3.5 py-2 bg-slate-55 dark:bg-gray-955 border border-slate-150/80 dark:border-gray-850 rounded-xl text-[10px] font-semibold text-slate-500">
                    Kinh độ (Longitude): <span className="font-extrabold text-slate-800 dark:text-white ml-1">{longitude.toFixed(6)}</span>
                  </div>
                  <div className="px-3.5 py-2 bg-slate-55 dark:bg-gray-955 border border-slate-150/80 dark:border-gray-850 rounded-xl text-[10px] font-semibold text-slate-500">
                    Vĩ độ (Latitude): <span className="font-extrabold text-slate-800 dark:text-white ml-1">{latitude.toFixed(6)}</span>
                  </div>
                </div>

                <MapPicker 
                  latitude={latitude} 
                  longitude={longitude} 
                  onCoordinatesChange={(lat, lng) => { setLatitude(lat); setLongitude(lng); }} 
                />
                <span className="block text-[10px] text-slate-400 font-semibold italic mt-1.5 pl-1">
                  * Hệ thống sẽ tự động chuyển đổi từ địa chỉ sang tọa độ. Bạn có thể tự do di chuyển ghim bản đồ để có tọa độ chính xác nhất.
                </span>
              </div>
            </div>
          )}

          {/* STEP 3: Thông tin liên hệ */}
          {currentStep === 3 && (
            <div className="space-y-5 animate-fadeIn">
              <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
                Bước 3: Thông tin liên hệ chính chủ / Môi giới
              </h3>

              {/* Contact name */}
              <div className="space-y-1">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Họ và tên người liên hệ <span className="text-rose-500">*</span></label>
                <input 
                  type="text" 
                  value={contactName}
                  onChange={(e) => setContactName(e.target.value)}
                  required 
                  placeholder="Nhập tên đầy đủ..." 
                  className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                />
              </div>

              {/* Phone and email rows */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Số điện thoại liên lạc <span className="text-rose-500">*</span></label>
                  <input 
                    type="text" 
                    value={contactPhone}
                    onChange={(e) => setContactPhone(e.target.value)}
                    required 
                    placeholder="Ví dụ: 0932030958" 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
                <div className="space-y-1">
                  <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Email</label>
                  <input 
                    type="email" 
                    value={contactEmail}
                    onChange={(e) => setContactEmail(e.target.value)}
                    placeholder="Nhập email người liên hệ..." 
                    className="w-full px-4 py-3 bg-slate-50 dark:bg-gray-955 border border-slate-200 dark:border-gray-850 focus:border-primary focus:bg-white dark:focus:bg-gray-900 rounded-xl text-xs font-semibold outline-none transition"
                  />
                </div>
              </div>
            </div>
          )}

          {/* STEP 4: Hình ảnh bất động sản & Đăng tin */}
          {currentStep === 4 && (
            <div className="space-y-5 animate-fadeIn">
              <h3 className="text-sm font-extrabold text-slate-800 dark:text-white border-l-4 border-primary pl-2.5">
                Bước 4: Upload hình ảnh bất động sản & Hoàn tất
              </h3>

              {/* Cover image upload container */}
              <div className="space-y-2">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Ảnh đại diện chính <span className="text-rose-500">*</span></label>
                
                <div className="flex flex-col sm:flex-row gap-5 items-center">
                  {/* File Selector Dropzone */}
                  <div className="w-full sm:w-1/2">
                    <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 hover:border-primary rounded-2xl cursor-pointer hover:bg-slate-50/50 dark:hover:bg-gray-850 transition">
                      <div className="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <i className="fa-solid fa-cloud-arrow-up text-xl text-slate-400 mb-2 group-hover:text-primary" />
                        <p className="text-[10px] text-slate-500 font-bold">KÉO THẢ HOẶC NHẤP ĐỂ TẢI ẢNH BÌA</p>
                        <p className="text-[8px] text-slate-400 font-semibold mt-1">Định dạng JPG, PNG dung lượng dưới 5MB</p>
                      </div>
                      <input 
                        type="file" 
                        accept="image/*"
                        onChange={(e) => handlePhotoUpload(e, true)}
                        className="hidden" 
                      />
                    </label>
                  </div>

                  {/* Preview Container */}
                  <div className="w-full sm:w-1/2 flex items-center justify-center border border-slate-100 dark:border-gray-855 rounded-2xl p-2 h-32 bg-slate-55 dark:bg-gray-955 relative overflow-hidden">
                    {imageUrl ? (
                      <>
                        <img 
                          src={imageUrl} 
                          alt="Cover Preview" 
                          className="max-w-full max-h-full object-contain rounded-lg"
                        />
                        <button
                          type="button"
                          onClick={() => setImageUrl('')}
                          className="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500/80 hover:bg-red-600 text-white flex items-center justify-center text-xs transition cursor-pointer"
                        >
                          <i className="fa-solid fa-trash-can" />
                        </button>
                      </>
                    ) : (
                      <span className="text-[10px] text-slate-400 font-bold">Chưa tải ảnh bìa</span>
                    )}
                  </div>
                </div>
              </div>

              {/* Gallery images upload container */}
              <div className="space-y-2 pt-2">
                <label className="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 px-1">Danh sách album ảnh phụ (Tối đa 6 ảnh)</label>

                <div className="flex flex-col sm:flex-row gap-5 items-center">
                  {/* File Selector Dropzone */}
                  <div className="w-full sm:w-1/2">
                    <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 hover:border-primary rounded-2xl cursor-pointer hover:bg-slate-50/50 dark:hover:bg-gray-855 transition">
                      <div className="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <i className="fa-solid fa-photo-film text-xl text-slate-400 mb-2 group-hover:text-primary" />
                        <p className="text-[10px] text-slate-500 font-bold">TẢI ALBUM ẢNH PHỤ</p>
                        <p className="text-[8px] text-slate-400 font-semibold mt-1">Chọn một hoặc nhiều file ảnh từ máy</p>
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

                  {/* Preview Container */}
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
                      <div className="w-full h-full flex items-center justify-center text-[10px] text-slate-400 font-bold">Chưa tải ảnh album phụ</div>
                    )}
                  </div>
                </div>
              </div>

              {/* Complete Info Review Box */}
              <div className="bg-slate-50 dark:bg-gray-955 border border-slate-100 dark:border-gray-850 rounded-3xl p-5 mt-6 space-y-4">
                <span className="block text-[10px] font-black uppercase tracking-wider text-slate-400 pl-1">Tóm tắt kiểm duyệt tin đăng</span>
                <div className="grid grid-cols-2 gap-4 text-xs">
                  <div className="space-y-1">
                    <span className="block text-[9px] uppercase font-bold text-slate-450">Tiêu đề:</span>
                    <span className="font-extrabold text-slate-800 dark:text-white line-clamp-1">{title || 'Chưa nhập'}</span>
                  </div>
                  <div className="space-y-1">
                    <span className="block text-[9px] uppercase font-bold text-slate-450">Giao dịch / Loại hình:</span>
                    <span className="font-extrabold text-slate-800 dark:text-white">
                      {purpose === 'rent' ? 'Cho thuê' : 'Bán'} - {propertyType}
                    </span>
                  </div>
                  <div className="space-y-1">
                    <span className="block text-[9px] uppercase font-bold text-slate-450">Giá trị & Diện tích:</span>
                    <span className="font-extrabold text-slate-800 dark:text-white">
                      {price ? Number(price).toLocaleString('vi-VN') + ' đ' : 'Chưa nhập'} | {area ? area + ' m²' : 'Chưa nhập'}
                    </span>
                  </div>
                  <div className="space-y-1">
                    <span className="block text-[9px] uppercase font-bold text-slate-450">Địa chỉ vị trí:</span>
                    <span className="font-extrabold text-slate-800 dark:text-white line-clamp-1">
                      {address ? `${address}, ${selectedWard?.Name || ''}, ${selectedProvince?.Name || ''}` : 'Chưa chọn'}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Stepper Navigation Control Buttons */}
          <div className="flex items-center justify-between border-t border-slate-100 dark:border-gray-850 pt-5 mt-6">
            <button
              type="button"
              disabled={currentStep === 1 || isSubmitting}
              onClick={() => setCurrentStep(prev => prev - 1)}
              className="px-5 py-3 rounded-xl border border-slate-200 dark:border-gray-850 text-slate-655 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-850 disabled:opacity-40 text-xs font-bold transition cursor-pointer active:scale-95 disabled:scale-100"
            >
              <i className="fa-solid fa-arrow-left-long mr-2" />
              Quay lại
            </button>

            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={saveDraft}
                className="px-5 py-3 rounded-xl border border-slate-200 dark:border-gray-850 hover:bg-slate-50 dark:hover:bg-gray-850 text-slate-655 dark:text-slate-300 text-xs font-bold transition cursor-pointer active:scale-95 flex items-center gap-1.5"
              >
                <i className="fa-solid fa-floppy-disk text-slate-400" />
                Lưu bản nháp
              </button>

              {currentStep < 4 ? (
                <button
                  type="button"
                  onClick={() => {
                    if (isStepValid(currentStep)) {
                      setCurrentStep(prev => prev + 1)
                    } else {
                      toast.warning('Vui lòng điền đầy đủ các thông tin bắt buộc trước khi tiếp tục!')
                    }
                  }}
                  className="px-5 py-3 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition cursor-pointer active:scale-95 shadow-md shadow-primary/10 hover:shadow-primary/20"
                >
                  Tiếp theo
                  <i className="fa-solid fa-arrow-right-long ml-2" />
                </button>
              ) : (
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 disabled:bg-emerald-400 text-white text-xs font-bold transition cursor-pointer active:scale-95 shadow-md shadow-emerald-500/10 hover:shadow-emerald-500/25 flex items-center gap-2"
                >
                  {isSubmitting ? (
                    <>
                      <i className="fa-solid fa-spinner animate-spin" /> Đang đăng tin...
                    </>
                  ) : (
                    <>
                      <i className="fa-solid fa-circle-check" /> Đăng tin ngay
                    </>
                  )}
                </button>
              )}
            </div>
          </div>

        </form>
      </div>
    </div>
  )
}
