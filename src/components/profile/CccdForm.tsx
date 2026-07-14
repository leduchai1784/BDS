'use client'

import { useState, useRef } from 'react'

interface UserCccd {
  idNumber?: string | null
  idDate?: string | null
  idPlace?: string | null
  pob?: string | null
  dob?: string | null
  permanentAddress?: string | null
  cccdFront?: string | null
  cccdBack?: string | null
}

interface CccdFormProps {
  user: UserCccd
  onSuccess: (message: string) => void
}

export default function CccdForm({ user, onSuccess }: CccdFormProps) {
  const [idNumber, setIdNumber] = useState(user.idNumber || '')
  const [idDate, setIdDate] = useState(user.idDate || '')
  const [idPlace, setIdPlace] = useState(user.idPlace || '')
  const [pob, setPob] = useState(user.pob || '')
  const [dob, setDob] = useState(user.dob || '')
  const [permanentAddress, setPermanentAddress] = useState(user.permanentAddress || '')

  const [cccdFront, setCccdFront] = useState(user.cccdFront || '')
  const [cccdBack, setCccdBack] = useState(user.cccdBack || '')

  const [isScanningFront, setIsScanningFront] = useState(false)
  const [isScanningBack, setIsScanningBack] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const frontInputRef = useRef<HTMLInputElement>(null)
  const backInputRef = useRef<HTMLInputElement>(null)

  const handleImageChange = async (e: React.ChangeEvent<HTMLInputElement>, side: 'front' | 'back') => {
    const file = e.target.files?.[0]
    if (!file) return

    if (side === 'front') {
      setIsScanningFront(true)
    } else {
      setIsScanningBack(true)
    }
    setErrorMsg('')

    const reader = new FileReader()
    reader.onload = async () => {
      if (typeof reader.result !== 'string') {
        setIsScanningFront(false)
        setIsScanningBack(false)
        return
      }

      const base64Data = reader.result

      // Update UI preview
      if (side === 'front') {
        setCccdFront(base64Data)
      } else {
        setCccdBack(base64Data)
      }

      // Triggers OCR scan API
      try {
        const res = await fetch('/api/profile/scan-cccd', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ image: base64Data, side })
        })

        const resData = await res.json()
        if (res.ok && resData.success && resData.data) {
          const parsed = resData.data
          if (side === 'front') {
            if (parsed.number) setIdNumber(parsed.number)
            if (parsed.dob) setDob(parsed.dob)
            if (parsed.pob) setPob(parsed.pob)
            if (parsed.permanent_address) setPermanentAddress(parsed.permanent_address)
          } else {
            if (parsed.issue_date) setIdDate(parsed.issue_date)
            if (parsed.issue_place) setIdPlace(parsed.issue_place)
            if (parsed.permanent_address) setPermanentAddress(parsed.permanent_address)
          }
        } else {
          setErrorMsg(resData.message || 'Không thể nhận dạng hình ảnh tự động. Vui lòng nhập thủ công.')
        }
      } catch (err) {
        console.error(err)
        setErrorMsg('Không thể quét ảnh tự động do sự cố kết nối OCR. Vui lòng nhập thủ công.')
      } finally {
        setIsScanningFront(false)
        setIsScanningBack(false)
      }
    }
    reader.readAsDataURL(file)
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSaving(true)
    setErrorMsg('')

    try {
      const res = await fetch('/api/profile/cccd', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id_number: idNumber,
          id_date: idDate,
          id_place: idPlace,
          pob,
          dob,
          permanent_address: permanentAddress,
          cccd_front: cccdFront.startsWith('data:image') ? cccdFront : null,
          cccd_back: cccdBack.startsWith('data:image') ? cccdBack : null
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        onSuccess('Thông tin xác thực CCCD đã được cập nhật thành công!')
      } else {
        setErrorMsg(data.error || data.message || 'Lỗi cập nhật CCCD.')
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

      {/* OCR Scanner Image Upload Panel */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Front Photo */}
        <div className="space-y-2">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Ảnh CCCD Mặt trước</label>
          <div 
            onClick={() => !isScanningFront && frontInputRef.current?.click()}
            className="h-44 w-full rounded-2xl border-2 border-dashed border-slate-200 hover:border-primary bg-slate-50 flex items-center justify-center cursor-pointer overflow-hidden relative group transition"
          >
            {cccdFront ? (
              <img src={cccdFront} alt="CCCD Front" className="w-full h-full object-cover" />
            ) : (
              <div className="text-center p-4">
                <i className="fa-solid fa-id-card text-2xl text-slate-350 mb-2" />
                <span className="block text-xs font-bold text-slate-500">Tải ảnh mặt trước</span>
                <span className="block text-[10px] text-slate-400">Hỗ trợ quét OCR tự động</span>
              </div>
            )}

            {isScanningFront && (
              <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center text-white">
                <div className="text-center">
                  <i className="fa-solid fa-spinner animate-spin text-xl mb-1.5" />
                  <span className="block text-[10px] font-bold">AI Đang quét CCCD...</span>
                </div>
              </div>
            )}
          </div>
          <input 
            type="file" 
            ref={frontInputRef} 
            onChange={(e) => handleImageChange(e, 'front')} 
            accept="image/*" 
            className="hidden" 
          />
        </div>

        {/* Back Photo */}
        <div className="space-y-2">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Ảnh CCCD Mặt sau</label>
          <div 
            onClick={() => !isScanningBack && backInputRef.current?.click()}
            className="h-44 w-full rounded-2xl border-2 border-dashed border-slate-200 hover:border-primary bg-slate-50 flex items-center justify-center cursor-pointer overflow-hidden relative group transition"
          >
            {cccdBack ? (
              <img src={cccdBack} alt="CCCD Back" className="w-full h-full object-cover" />
            ) : (
              <div className="text-center p-4">
                <i className="fa-solid fa-id-card text-2xl text-slate-350 mb-2" />
                <span className="block text-xs font-bold text-slate-500">Tải ảnh mặt sau</span>
                <span className="block text-[10px] text-slate-400">Hỗ trợ quét OCR tự động</span>
              </div>
            )}

            {isScanningBack && (
              <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center text-white">
                <div className="text-center">
                  <i className="fa-solid fa-spinner animate-spin text-xl mb-1.5" />
                  <span className="block text-[10px] font-bold">AI Đang quét CCCD...</span>
                </div>
              </div>
            )}
          </div>
          <input 
            type="file" 
            ref={backInputRef} 
            onChange={(e) => handleImageChange(e, 'back')} 
            accept="image/*" 
            className="hidden" 
          />
        </div>
      </div>

      {/* Manual Input Fields */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4">
        {/* ID Number */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Số CCCD (12 số)</label>
          <input 
            type="text" 
            value={idNumber} 
            onChange={(e) => setIdNumber(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Date of birth */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Ngày sinh</label>
          <input 
            type="date" 
            value={dob} 
            onChange={(e) => setDob(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
          />
        </div>

        {/* Place of Birth */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Quê quán</label>
          <input 
            type="text" 
            value={pob} 
            onChange={(e) => setPob(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Issue Date */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Ngày cấp</label>
          <input 
            type="date" 
            value={idDate} 
            onChange={(e) => setIdDate(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer"
          />
        </div>

        {/* Issue Place */}
        <div className="space-y-1.5">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nơi cấp</label>
          <input 
            type="text" 
            value={idPlace} 
            onChange={(e) => setIdPlace(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Permanent Address */}
        <div className="space-y-1.5 md:col-span-2">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nơi thường trú</label>
          <input 
            type="text" 
            value={permanentAddress} 
            onChange={(e) => setPermanentAddress(e.target.value)} 
            required
            className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>
      </div>

      <div className="flex justify-end pt-2">
        <button 
          type="submit" 
          disabled={isSaving}
          className="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-60"
        >
          {isSaving ? (
            <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Đang cập nhật...</span>
          ) : (
            <span>Cập nhật xác thực</span>
          )}
        </button>
      </div>
    </form>
  )
}
