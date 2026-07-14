'use client'

import { useState, useEffect } from 'react'

interface UserProfile {
  name: string
  email: string
  phone?: string | null
  firstname?: string | null
  lastname?: string | null
  gender?: number | null
  dob?: string | null
  permanentAddress?: string | null
  intro?: string | null
  website?: string | null
  companyName?: string | null
  province?: string | null
  district?: string | null
  ward?: string | null
}

interface ProfileInfoFormProps {
  user: UserProfile
  onSuccess: (message: string) => void
}

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

export default function ProfileInfoForm({ user, onSuccess }: ProfileInfoFormProps) {
  const [isEditing, setIsEditing] = useState(false)

  // Basic Info States
  const [name, setName] = useState(user.name || '')
  const [phone, setPhone] = useState(user.phone || '')
  const [firstname, setFirstname] = useState(user.firstname || '')
  const [lastname, setLastname] = useState(user.lastname || '')
  const [gender, setGender] = useState(user.gender !== undefined && user.gender !== null ? String(user.gender) : '0')
  const [dob, setDob] = useState(user.dob || '')
  const [permanentAddress, setPermanentAddress] = useState(user.permanentAddress || '')
  const [intro, setIntro] = useState(user.intro || '')
  const [website, setWebsite] = useState(user.website || '')
  const [companyName, setCompanyName] = useState(user.companyName || '')

  // Administrative Bound States
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

  const [isSaving, setIsSaving] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  // Load Administrative divisions
  useEffect(() => {
    fetch('/vietnam_provinces.json')
      .then(res => res.json())
      .then((data: Province[]) => {
        setProvinces(data)
        
        // Initial match for user's province
        if (user.province) {
          const matchedProv = data.find(p => p.Name.toLowerCase() === user.province?.toLowerCase())
          if (matchedProv) {
            setSelectedProvince(matchedProv)
            setProvinceSearch(matchedProv.Name)
            
            // Match user's district
            if (user.district) {
              const matchedDist = matchedProv.Districts.find(d => d.Name.toLowerCase() === user.district?.toLowerCase())
              if (matchedDist) {
                setSelectedDistrict(matchedDist)
                setDistrictSearch(matchedDist.Name)
                
                // Match user's ward
                if (user.ward) {
                  const matchedWard = matchedDist.Wards.find(w => w.Name.toLowerCase() === user.ward?.toLowerCase())
                  if (matchedWard) {
                    setSelectedWard(matchedWard)
                    setWardSearch(matchedWard.Name)
                  }
                }
              }
            }
          }
        }
      })
      .catch(err => console.error('Failed to load provinces list:', err))
  }, [user.province, user.district, user.ward])

  const handleCancel = () => {
    setIsEditing(false)
    setName(user.name || '')
    setPhone(user.phone || '')
    setFirstname(user.firstname || '')
    setLastname(user.lastname || '')
    setGender(user.gender !== undefined && user.gender !== null ? String(user.gender) : '0')
    setDob(user.dob || '')
    setPermanentAddress(user.permanentAddress || '')
    setIntro(user.intro || '')
    setWebsite(user.website || '')
    setCompanyName(user.companyName || '')

    // Re-match initial location selections
    if (user.province && provinces.length > 0) {
      const matchedProv = provinces.find(p => p.Name.toLowerCase() === user.province?.toLowerCase())
      if (matchedProv) {
        setSelectedProvince(matchedProv)
        setProvinceSearch(matchedProv.Name)
        const matchedDist = matchedProv.Districts.find(d => d.Name.toLowerCase() === user.district?.toLowerCase())
        if (matchedDist) {
          setSelectedDistrict(matchedDist)
          setDistrictSearch(matchedDist.Name)
          const matchedWard = matchedDist.Wards.find(w => w.Name.toLowerCase() === user.ward?.toLowerCase())
          if (matchedWard) {
            setSelectedWard(matchedWard)
            setWardSearch(matchedWard.Name)
          } else {
            setSelectedWard(null)
            setWardSearch('')
          }
        } else {
          setSelectedDistrict(null)
          setDistrictSearch('')
          setSelectedWard(null)
          setWardSearch('')
        }
      }
    } else {
      setSelectedProvince(null)
      setProvinceSearch('')
      setSelectedDistrict(null)
      setDistrictSearch('')
      setSelectedWard(null)
      setWardSearch('')
    }
    setErrorMsg('')
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSaving(true)
    setErrorMsg('')

    try {
      const res = await fetch('/api/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name,
          phone,
          firstname,
          lastname,
          gender: Number(gender),
          dob,
          permanent_address: permanentAddress,
          intro,
          website,
          company_name: companyName,
          province: selectedProvince?.Name || '',
          district: selectedDistrict?.Name || '',
          ward: selectedWard?.Name || ''
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setIsEditing(false)
        onSuccess('Cập nhật thông tin hồ sơ cá nhân thành công!')
      } else {
        setErrorMsg(data.message || 'Lỗi cập nhật thông tin.')
      }
    } catch (err) {
      setErrorMsg('Lỗi kết nối mạng, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsSaving(false)
    }
  }

  // Filters for searches
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

  return (
    <form onSubmit={handleSave} className="space-y-6 text-left">
      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* Grid 1: Basic user Info */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        {/* Email (Readonly) */}
        <div className="space-y-1.5">
          <div className="flex justify-between items-center px-1">
            <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Địa chỉ Email</label>
            <span className="text-[9px] text-amber-500 font-bold"><i className="fa-solid fa-lock mr-1"></i> Email đăng nhập</span>
          </div>
          <div className="relative">
            <i className="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="email" 
              value={user.email} 
              disabled 
              className="w-full pl-10 pr-4 py-2.5 bg-slate-100 border border-slate-200 text-slate-400 rounded-xl text-xs font-semibold outline-none cursor-not-allowed"
            />
          </div>
        </div>

        {/* Display Name */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Họ và tên hiển thị</label>
          <div className="relative">
            <i className="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              value={name} 
              onChange={(e) => setName(e.target.value)} 
              required
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>

        {/* Phone */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Số điện thoại liên hệ</label>
          <div className="relative">
            <i className="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="tel" 
              value={phone} 
              onChange={(e) => setPhone(e.target.value)} 
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>

        {/* Date of Birth */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Ngày sinh</label>
          <div className="relative">
            <i className="fa-solid fa-cake-candles absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs z-10"></i>
            <input 
              type="date" 
              value={dob} 
              onChange={(e) => setDob(e.target.value)} 
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>

        {/* Gender */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Giới tính</label>
          <div className="relative">
            <i className="fa-solid fa-venus-mars absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <select 
              value={gender} 
              onChange={(e) => setGender(e.target.value)} 
              disabled={!isEditing}
              className="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none transition cursor-pointer disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            >
              <option value="0">Nam</option>
              <option value="1">Nữ</option>
              <option value="2">Khác</option>
            </select>
            <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
          </div>
        </div>

        {/* Website */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Website</label>
          <div className="relative">
            <i className="fa-solid fa-globe absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              value={website} 
              placeholder="https://..."
              onChange={(e) => setWebsite(e.target.value)} 
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>

        {/* Company Name */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Tên công ty (nếu có)</label>
          <div className="relative">
            <i className="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              value={companyName} 
              onChange={(e) => setCompanyName(e.target.value)} 
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>
      </div>

      {/* Grid 2: Administrative Boundaries Dropdowns */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        {/* Province/City Selector */}
        <div className="space-y-1.5 relative">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Tỉnh / Thành phố</label>
          <div className="relative">
            <i className="fa-solid fa-map-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              placeholder="Chọn Tỉnh/Thành phố..."
              value={provinceSearch}
              onChange={(e) => {
                setProvinceSearch(e.target.value)
                setProvinceOpen(true)
              }}
              onFocus={() => isEditing && setProvinceOpen(true)}
              disabled={!isEditing}
              className="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
            <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
          </div>

          {provinceOpen && filteredProvinces.length > 0 && (
            <div className="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
              {filteredProvinces.map(p => (
                <button
                  key={p.Id}
                  type="button"
                  onClick={() => {
                    setSelectedProvince(p)
                    setProvinceSearch(p.Name)
                    setProvinceOpen(false)
                    // Reset child selects
                    setSelectedDistrict(null)
                    setDistrictSearch('')
                    setSelectedWard(null)
                    setWardSearch('')
                  }}
                  className="w-full text-left px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary transition"
                >
                  {p.Name}
                </button>
              ))}
            </div>
          )}
        </div>

        {/* District Selector */}
        <div className="space-y-1.5 relative">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Quận / Huyện</label>
          <div className="relative">
            <i className="fa-solid fa-tree-city absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              placeholder="Chọn Quận/Huyện..."
              value={districtSearch}
              onChange={(e) => {
                setDistrictSearch(e.target.value)
                setDistrictOpen(true)
              }}
              onFocus={() => isEditing && selectedProvince && setDistrictOpen(true)}
              disabled={!isEditing || !selectedProvince}
              className="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
            <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
          </div>

          {districtOpen && selectedProvince && filteredDistricts.length > 0 && (
            <div className="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
              {filteredDistricts.map(d => (
                <button
                  key={d.Id}
                  type="button"
                  onClick={() => {
                    setSelectedDistrict(d)
                    setDistrictSearch(d.Name)
                    setDistrictOpen(false)
                    // Reset ward select
                    setSelectedWard(null)
                    setWardSearch('')
                  }}
                  className="w-full text-left px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary transition"
                >
                  {d.Name}
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Ward Selector */}
        <div className="space-y-1.5 relative">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Phường / Xã</label>
          <div className="relative">
            <i className="fa-solid fa-road absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              placeholder="Chọn Phường/Xã..."
              value={wardSearch}
              onChange={(e) => {
                setWardSearch(e.target.value)
                setWardOpen(true)
              }}
              onFocus={() => isEditing && selectedDistrict && setWardOpen(true)}
              disabled={!isEditing || !selectedDistrict}
              className="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
            <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
          </div>

          {wardOpen && selectedDistrict && filteredWards.length > 0 && (
            <div className="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
              {filteredWards.map(w => (
                <button
                  key={w.Id}
                  type="button"
                  onClick={() => {
                    setSelectedWard(w)
                    setWardSearch(w.Name)
                    setWardOpen(false)
                  }}
                  className="w-full text-left px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary transition"
                >
                  {w.Name}
                </button>
              ))}
            </div>
          )}
        </div>

      </div>

      {/* Permanent Address */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Địa chỉ thường trú</label>
        <div className="relative">
          <i className="fa-solid fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type="text" 
            value={permanentAddress} 
            onChange={(e) => setPermanentAddress(e.target.value)} 
            disabled={!isEditing}
            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
          />
        </div>
      </div>

      {/* Intro */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Giới thiệu ngắn</label>
        <textarea 
          value={intro} 
          onChange={(e) => setIntro(e.target.value)} 
          rows={3}
          disabled={!isEditing}
          placeholder="Mô tả bản thân, kinh nghiệm bất động sản..."
          className="w-full px-4 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition resize-none disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
        />
      </div>

      {/* Action Buttons */}
      <div className="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
        {!isEditing ? (
          <button 
            type="button" 
            onClick={() => setIsEditing(true)}
            className="inline-flex items-center justify-center px-6 py-2.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition cursor-pointer active:scale-98 min-w-[130px]"
          >
            <i className="fa-solid fa-pen-to-square mr-2 text-xs"></i>Chỉnh sửa
          </button>
        ) : (
          <>
            <button 
              type="button" 
              onClick={handleCancel}
              className="inline-flex items-center justify-center px-6 py-2.5 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer active:scale-98 min-w-[100px]"
            >
              Hủy
            </button>

            <button 
              type="submit" 
              disabled={isSaving}
              className="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
            >
              {isSaving ? (
                <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Đang lưu...</span>
              ) : (
                <span><i className="fa-solid fa-floppy-disk mr-2"></i>Lưu thay đổi</span>
              )}
            </button>
          </>
        )}
      </div>
    </form>
  )
}
