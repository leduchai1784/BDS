'use client'

import { useState } from 'react'

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

export default function ProfileInfoForm({ user, onSuccess }: ProfileInfoFormProps) {
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
  
  const [province, setProvince] = useState(user.province || '')
  const [district, setDistrict] = useState(user.district || '')
  const [ward, setWard] = useState(user.ward || '')

  const [isSaving, setIsSaving] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

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
          province,
          district,
          ward
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
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

  return (
    <form onSubmit={handleSave} className="space-y-6 text-left">
      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        {/* Email (Readonly) */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Email đăng nhập</label>
          <input 
            type="email" 
            value={user.email} 
            disabled 
            className="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 text-slate-400 rounded-xl text-xs font-semibold outline-none cursor-not-allowed"
          />
        </div>

        {/* Display Name */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Họ và tên hiển thị</label>
          <input 
            type="text" 
            value={name} 
            onChange={(e) => setName(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Phone */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Số điện thoại liên hệ</label>
          <input 
            type="tel" 
            value={phone} 
            onChange={(e) => setPhone(e.target.value)} 
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Date of Birth */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Ngày sinh</label>
          <input 
            type="date" 
            value={dob} 
            onChange={(e) => setDob(e.target.value)} 
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Gender */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Giới tính</label>
          <select 
            value={gender} 
            onChange={(e) => setGender(e.target.value)} 
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
          >
            <option value="0">Nam</option>
            <option value="1">Nữ</option>
            <option value="2">Khác</option>
          </select>
        </div>

        {/* Firstname */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tên (NKS Sync)</label>
          <input 
            type="text" 
            value={firstname} 
            onChange={(e) => setFirstname(e.target.value)} 
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Lastname */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Họ lót (NKS Sync)</label>
          <input 
            type="text" 
            value={lastname} 
            onChange={(e) => setLastname(e.target.value)} 
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Company Name */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tên công ty (nếu có)</label>
          <input 
            type="text" 
            value={companyName} 
            onChange={(e) => setCompanyName(e.target.value)} 
            className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>
      </div>

      {/* Permanent Address */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Địa chỉ thường trú</label>
        <input 
          type="text" 
          value={permanentAddress} 
          onChange={(e) => setPermanentAddress(e.target.value)} 
          className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
        />
      </div>

      {/* Intro */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Giới thiệu ngắn</label>
        <textarea 
          value={intro} 
          onChange={(e) => setIntro(e.target.value)} 
          rows={3}
          placeholder="Mô tả bản thân, kinh nghiệm bất động sản..."
          className="w-full px-4 py-2 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition resize-none"
        />
      </div>

      <div className="flex justify-end pt-2">
        <button 
          type="submit" 
          disabled={isSaving}
          className="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-60"
        >
          {isSaving ? (
            <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Đang lưu...</span>
          ) : (
            <span>Lưu thay đổi</span>
          )}
        </button>
      </div>
    </form>
  )
}
