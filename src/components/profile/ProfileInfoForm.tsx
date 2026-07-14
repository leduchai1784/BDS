'use client'

import { useState, useEffect } from 'react'

interface UserProfile {
  name: string
  email: string
  phone?: string | null
  gender?: number | null
  dob?: string | null
  addStreet?: string | null
  province?: string | null
  district?: string | null
  ward?: string | null
  addProvince?: string | null
  addDistrict?: string | null
  addWard?: string | null
  intro?: string | null
  website?: string | null
}

interface ProfileInfoFormProps {
  user: UserProfile
  onSuccess: (message: string) => void
}

interface Ward {
  Id: string
  Name: string
}

interface WardWithDistrict extends Ward {
  DistrictName: string
  DistrictId: string
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

  // Form States
  const [name, setName] = useState(user.name || '')
  const [gender, setGender] = useState(user.gender !== undefined && user.gender !== null ? String(user.gender) : '0')
  const [phone, setPhone] = useState(user.phone || '')
  const [email, setEmail] = useState(user.email || '')
  const [dob, setDob] = useState('') // HTML date format: yyyy-mm-dd
  const [website, setWebsite] = useState(user.website || '')
  const [addStreet, setAddStreet] = useState(user.addStreet || '')
  const [intro, setIntro] = useState(user.intro || '')
  const [districtName, setDistrictName] = useState(user.district || '')

  // Administrative Bound States
  const [provinces, setProvinces] = useState<Province[]>([])
  
  const [provinceSearch, setProvinceSearch] = useState('')
  const [selectedProvince, setSelectedProvince] = useState<Province | null>(null)
  const [provinceOpen, setProvinceOpen] = useState(false)

  const [wardSearch, setWardSearch] = useState('')
  const [selectedWard, setSelectedWard] = useState<WardWithDistrict | null>(null)
  const [wardOpen, setWardOpen] = useState(false)

  const [isSaving, setIsSaving] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  // Convert dd/mm/yyyy from DB to yyyy-mm-dd for input[type="date"]
  useEffect(() => {
    if (user.dob) {
      if (user.dob.includes('/')) {
        const parts = user.dob.split('/')
        if (parts.length === 3) {
          setDob(`${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`)
          return
        }
      }
      setDob(user.dob)
    } else {
      setDob('')
    }
  }, [user.dob])

  // Load Vietnam Administrative divisions
  useEffect(() => {
    fetch('/vietnam_provinces.json')
      .then(res => res.json())
      .then((data: Province[]) => {
        setProvinces(data)
        
        // Initial match for user's province (by ID or by Name)
        const savedProvince = user.addProvince || user.province
        if (savedProvince) {
          const searchVal = savedProvince.toLowerCase()
          const matchedProv = data.find(p => p.Id.toLowerCase() === searchVal || p.Name.toLowerCase() === searchVal)
          if (matchedProv) {
            setSelectedProvince(matchedProv)
            setProvinceSearch(matchedProv.Name)
            
            // Match user's ward (by ID or by Name)
            const savedWard = user.addWard || user.ward
            if (savedWard) {
              const searchWardVal = savedWard.toLowerCase()
              for (const dist of matchedProv.Districts) {
                const matchedW = dist.Wards.find(w => w.Id.toLowerCase() === searchWardVal || w.Name.toLowerCase() === searchWardVal)
                if (matchedW) {
                  setSelectedWard({
                    ...matchedW,
                    DistrictName: dist.Name,
                    DistrictId: dist.Id
                  })
                  setWardSearch(matchedW.Name)
                  break
                }
              }
            }
          }
        }
      })
      .catch(err => console.error('Failed to load provinces list:', err))
  }, [user.province, user.addProvince, user.ward, user.addWard])

  const handleCancel = () => {
    setIsEditing(false)
    setName(user.name || '')
    setGender(user.gender !== undefined && user.gender !== null ? String(user.gender) : '0')
    setPhone(user.phone || '')
    setEmail(user.email || '')
    setWebsite(user.website || '')
    setAddStreet(user.addStreet || '')
    setIntro(user.intro || '')
    setDistrictName(user.district || '')

    if (user.dob) {
      if (user.dob.includes('/')) {
        const parts = user.dob.split('/')
        if (parts.length === 3) {
          setDob(`${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`)
        }
      } else {
        setDob(user.dob)
      }
    } else {
      setDob('')
    }

    // Re-match initial location selections
    const savedProvince = user.addProvince || user.province
    if (savedProvince && provinces.length > 0) {
      const searchVal = savedProvince.toLowerCase()
      const matchedProv = provinces.find(p => p.Id.toLowerCase() === searchVal || p.Name.toLowerCase() === searchVal)
      if (matchedProv) {
        setSelectedProvince(matchedProv)
        setProvinceSearch(matchedProv.Name)
        let found = false
        const savedWard = user.addWard || user.ward
        if (savedWard) {
          const searchWardVal = savedWard.toLowerCase()
          for (const dist of matchedProv.Districts) {
            const matchedW = dist.Wards.find(w => w.Id.toLowerCase() === searchWardVal || w.Name.toLowerCase() === searchWardVal)
            if (matchedW) {
              setSelectedWard({
                ...matchedW,
                DistrictName: dist.Name,
                DistrictId: dist.Id
              })
              setWardSearch(matchedW.Name)
              found = true
              break
            }
          }
        }
        if (!found) {
          setSelectedWard(null)
          setWardSearch('')
        }
      }
    } else {
      setSelectedProvince(null)
      setProvinceSearch('')
      setSelectedWard(null)
      setWardSearch('')
    }
    setErrorMsg('')
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSaving(true)
    setErrorMsg('')

    // Format DOB back to dd/mm/yyyy for database
    let formattedDob = dob
    if (dob && dob.includes('-')) {
      const parts = dob.split('-')
      if (parts.length === 3) {
        formattedDob = `${parts[2]}/${parts[1]}/${parts[0]}`
      }
    }

    try {
      const res = await fetch('/api/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name,
          phone,
          email,
          gender: Number(gender),
          dob: formattedDob,
          add_street: addStreet,
          province: selectedProvince?.Name || '',
          district: districtName || '',
          ward: selectedWard?.Name || '',
          add_province: selectedProvince?.Id || '',
          add_district: selectedWard?.DistrictId || '',
          add_ward: selectedWard?.Id || '',
          intro,
          website
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setIsEditing(false)
        onSuccess('Hồ sơ cá nhân đã được cập nhật thành công!')
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

  // Get flat list of wards under the selected province
  const flatWardsOfProvince: WardWithDistrict[] = selectedProvince
    ? selectedProvince.Districts.flatMap(d => 
        d.Wards.map(w => ({
          ...w,
          DistrictName: d.Name,
          DistrictId: d.Id
        }))
      )
    : []

  // Filters for searches
  const filteredProvinces = provinces.filter(p => 
    !provinceSearch || p.Name.toLowerCase().includes(provinceSearch.toLowerCase())
  )

  const filteredWards = flatWardsOfProvince.filter(w => 
    !wardSearch || w.Name.toLowerCase().includes(wardSearch.toLowerCase())
  )

  return (
    <form onSubmit={handleSave} className="space-y-6 text-left">
      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* Grid 1: Name & Gender */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {/* Họ và tên */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên</label>
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

        {/* Giới tính */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giới tính</label>
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
      </div>

      {/* Grid 2: Phone & Email */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {/* Số điện thoại */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại</label>
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

        {/* Email */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1 flex justify-between items-center">
            <span>Địa chỉ Email</span>
            <span className="text-[9px] text-amber-500 normal-case font-bold"><i className="fa-solid fa-lock mr-1"></i> Email đăng nhập</span>
          </label>
          <div className="relative">
            <i className="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="email" 
              value={email} 
              onChange={(e) => setEmail(e.target.value)} 
              required
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>
      </div>

      {/* Grid 3: Dob & Website */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {/* Ngày sinh */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày sinh</label>
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

        {/* Website */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Website</label>
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
      </div>

      {/* Grid 4: Address Details */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        {/* Tỉnh / Thành phố */}
        <div className="space-y-1 relative">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh / Thành phố</label>
          <div className="relative">
            <i className="fa-solid fa-map-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs z-10"></i>
            <input 
              type="text" 
              placeholder="Chọn Tỉnh/Thành phố..."
              value={provinceSearch}
              onFocus={() => {
                if (isEditing) {
                  setProvinceOpen(true)
                  setWardOpen(false)
                }
              }}
              onChange={(e) => {
                setProvinceSearch(e.target.value)
                setProvinceOpen(true)
              }}
              disabled={!isEditing}
              className="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer text-left disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
            <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>

            {/* Dropdown Panel */}
            {provinceOpen && (
              <div className="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left">
                {filteredProvinces.map(p => (
                  <div 
                    key={p.Id}
                    onClick={() => {
                      setSelectedProvince(p)
                      setProvinceSearch(p.Name)
                      setProvinceOpen(false)
                      
                      // Reset ward selection
                      setSelectedWard(null)
                      setWardSearch('')
                      setDistrictName('')
                    }}
                    className="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                  >
                    {p.Name}
                  </div>
                ))}
                {filteredProvinces.length === 0 && (
                  <div className="px-4 py-2.5 text-xs text-slate-400 font-semibold">Không tìm thấy kết quả</div>
                )}
              </div>
            )}
          </div>
        </div>

        {/* Phường / Xã */}
        <div className="space-y-1 relative">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường / Xã</label>
          <div className="relative">
            <i className="fa-solid fa-tree-city absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs z-10"></i>
            <input 
              type="text" 
              placeholder="Chọn Phường/Xã..."
              value={wardSearch}
              onFocus={() => {
                if (isEditing && selectedProvince) {
                  setWardOpen(true)
                  setProvinceOpen(false)
                }
              }}
              onChange={(e) => {
                setWardSearch(e.target.value)
                setWardOpen(true)
              }}
              disabled={!isEditing || !selectedProvince}
              className="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer text-left disabled:opacity-60 disabled:cursor-not-allowed"
            />
            <i className="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>

            {/* Dropdown Panel */}
            {wardOpen && selectedProvince && (
              <div className="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left">
                {filteredWards.map(w => (
                  <div 
                    key={w.Id}
                    onClick={() => {
                      setSelectedWard(w)
                      setWardSearch(w.Name)
                      setWardOpen(false)
                      setDistrictName(w.DistrictName) // Auto map district name
                    }}
                    className="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                  >
                    {w.Name}
                  </div>
                ))}
                {filteredWards.length === 0 && (
                  <div className="px-4 py-2.5 text-xs text-slate-400 font-semibold">Không tìm thấy kết quả</div>
                )}
              </div>
            )}
          </div>
        </div>

        {/* Đường / Số nhà */}
        <div className="space-y-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Đường / Số nhà</label>
          <div className="relative">
            <i className="fa-solid fa-road absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
              type="text" 
              value={addStreet} 
              placeholder="Số 10 Duy Tân..."
              onChange={(e) => setAddStreet(e.target.value)} 
              disabled={!isEditing}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
            />
          </div>
        </div>
      </div>

      {/* Giới thiệu bản thân */}
      <div className="space-y-1">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giới thiệu bản thân</label>
        <textarea 
          value={intro} 
          rows={3}
          placeholder="Chia sẻ một chút về bản thân bạn..."
          onChange={(e) => setIntro(e.target.value)} 
          disabled={!isEditing}
          className="w-full p-3.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
        />
      </div>

      {/* Action Buttons Footer */}
      <div className="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
        {!isEditing ? (
          <button 
            type="button" 
            onClick={() => setIsEditing(true)}
            className="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition cursor-pointer active:scale-98 min-w-[130px]"
          >
            <i className="fa-solid fa-pen-to-square mr-2 text-xs"></i>Chỉnh sửa
          </button>
        ) : (
          <>
            <button 
              type="button" 
              onClick={handleCancel}
              className="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer active:scale-98 min-w-[100px]"
            >
              Hủy
            </button>
            <button 
              type="submit" 
              disabled={isSaving}
              className="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
            >
              {isSaving ? (
                <>
                  <i className="fa-solid fa-spinner animate-spin mr-2" />
                  Đang lưu...
                </>
              ) : (
                <>
                  <i className="fa-solid fa-floppy-disk mr-2" />
                  Lưu thay đổi
                </>
              )}
            </button>
          </>
        )}
      </div>
    </form>
  )
}
