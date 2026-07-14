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

function toInputDate(val?: string | null): string {
  if (!val) return ''
  if (val.includes('/')) {
    const parts = val.split('/')
    if (parts.length === 3) {
      return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`
    }
  }
  return val
}

function toDisplayDate(val?: string | null): string {
  if (!val) return 'Chưa cập nhật'
  if (val.includes('-')) {
    const parts = val.split('-')
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`
    }
  }
  return val
}

function getCccdUrl(path?: string | null): string {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('data:image')) {
    return path
  }
  const cleanPath = path.replace(/^\/+/, '')
  if (cleanPath.startsWith('uploads/')) {
    return `/${cleanPath}`
  }
  return `https://data.nks.vn/storage/${cleanPath.replace('storage/', '')}`
}

const compressImage = (file: File): Promise<string> => {
  return new Promise((resolve) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (e) => {
      const img = new Image()
      img.src = e.target?.result as string
      img.onload = () => {
        const canvas = document.createElement('canvas')
        const maxW = 1000
        const maxH = 1000
        let w = img.width
        let h = img.height
        if (w > h) {
          if (w > maxW) {
            h = Math.round((h * maxW) / w)
            w = maxW
          }
        } else {
          if (h > maxH) {
            w = Math.round((w * maxH) / h)
            h = maxH
          }
        }
        canvas.width = w
        canvas.height = h
        const ctx = canvas.getContext('2d')
        ctx?.drawImage(img, 0, 0, w, h)
        resolve(canvas.toDataURL('image/jpeg', 0.85))
      }
    }
  })
}

export default function CccdForm({ user, onSuccess }: CccdFormProps) {
  // Mode Selection
  const [isEditingCccd, setIsEditingCccd] = useState(!user.idNumber)

  // Input States
  const [idNumber, setIdNumber] = useState(user.idNumber || '')
  const [idDate, setIdDate] = useState(toInputDate(user.idDate))
  const [idPlace, setIdPlace] = useState(user.idPlace || '')
  const [pob, setPob] = useState(user.pob || '')
  const [dob, setDob] = useState(toInputDate(user.dob))
  const [permanentAddress, setPermanentAddress] = useState(user.permanentAddress || '')

  const [cccdFront, setCccdFront] = useState(getCccdUrl(user.cccdFront))
  const [cccdBack, setCccdBack] = useState(getCccdUrl(user.cccdBack))

  // OCR scanning highlight classes
  const [highlightNum, setHighlightNum] = useState(false)
  const [highlightDob, setHighlightDob] = useState(false)
  const [highlightPob, setHighlightPob] = useState(false)
  const [highlightAddress, setHighlightAddress] = useState(false)
  const [highlightIdDate, setHighlightIdDate] = useState(false)
  const [highlightIdPlace, setHighlightIdPlace] = useState(false)

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

    try {
      const base64Data = await compressImage(file)
      
      // Update UI preview
      if (side === 'front') {
        setCccdFront(base64Data)
      } else {
        setCccdBack(base64Data)
      }

      // Triggers Tesseract OCR scan API
      const res = await fetch('/api/profile/scan-cccd', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image: base64Data, side })
      })

      const resData = await res.json()
      if (res.ok && resData.success && resData.data) {
        const parsed = resData.data
        if (side === 'front') {
          if (parsed.number) {
            setIdNumber(parsed.number)
            setHighlightNum(true)
            setTimeout(() => setHighlightNum(false), 1500)
          }
          if (parsed.dob) {
            setDob(parsed.dob)
            setHighlightDob(true)
            setTimeout(() => setHighlightDob(false), 1500)
          }
          if (parsed.pob) {
            setPob(parsed.pob)
            setHighlightPob(true)
            setTimeout(() => setHighlightPob(false), 1500)
          }
          if (parsed.permanent_address) {
            setPermanentAddress(parsed.permanent_address)
            setHighlightAddress(true)
            setTimeout(() => setHighlightAddress(false), 1500)
          }
        } else {
          if (parsed.issue_date) {
            setIdDate(parsed.issue_date)
            setHighlightIdDate(true)
            setTimeout(() => setHighlightIdDate(false), 1500)
          }
          if (parsed.issue_place) {
            setIdPlace(parsed.issue_place)
            setHighlightIdPlace(true)
            setTimeout(() => setHighlightIdPlace(false), 1500)
          }
          if (parsed.permanent_address && !permanentAddress) {
            setPermanentAddress(parsed.permanent_address)
            setHighlightAddress(true)
            setTimeout(() => setHighlightAddress(false), 1500)
          }
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

  const handleCancel = () => {
    setIsEditingCccd(false)
    setIdNumber(user.idNumber || '')
    setIdDate(toInputDate(user.idDate))
    setIdPlace(user.idPlace || '')
    setPob(user.pob || '')
    setDob(toInputDate(user.dob))
    setPermanentAddress(user.permanentAddress || '')
    setCccdFront(getCccdUrl(user.cccdFront))
    setCccdBack(getCccdUrl(user.cccdBack))
    setErrorMsg('')
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSaving(true)
    setErrorMsg('')

    // Format HTML dates (yyyy-mm-dd) back to dd/mm/yyyy for database
    let formattedDob = dob
    if (dob && dob.includes('-')) {
      const parts = dob.split('-')
      if (parts.length === 3) {
        formattedDob = `${parts[2]}/${parts[1]}/${parts[0]}`
      }
    }

    let formattedIdDate = idDate
    if (idDate && idDate.includes('-')) {
      const parts = idDate.split('-')
      if (parts.length === 3) {
        formattedIdDate = `${parts[2]}/${parts[1]}/${parts[0]}`
      }
    }

    try {
      const res = await fetch('/api/profile/cccd', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id_number: idNumber,
          id_date: formattedIdDate,
          id_place: idPlace,
          pob,
          dob: formattedDob,
          permanent_address: permanentAddress,
          cccd_front: cccdFront.startsWith('data:image') ? cccdFront : null,
          cccd_back: cccdBack.startsWith('data:image') ? cccdBack : null
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setIsEditingCccd(false)
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
    <div className="space-y-6">
      {/* Dynamic Keyframes Animation Injection */}
      <style dangerouslySetInnerHTML={{__html: `
        @keyframes scan {
          0% { top: 0%; }
          50% { top: 100%; }
          100% { top: 0%; }
        }
        .scanner-line {
          position: absolute;
          left: 0;
          right: 0;
          height: 3px;
          background-color: #10b981;
          box-shadow: 0 0 12px #10b981;
          animation: scan 2.5s linear infinite;
        }
        @keyframes ocrHighlight {
          0% { background-color: #d1fae5; border-color: #10b981; }
          100% { background-color: #f8fafc; border-color: #e2e8f0; }
        }
        .ocr-highlight {
          animation: ocrHighlight 1.8s ease-out;
        }
      `}} />

      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold text-left">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* READ-ONLY VERIFIED VIEW */}
      {!isEditingCccd && (
        <div className="space-y-6 text-left">
          <div>
            <h2 className="text-xl font-bold text-slate-800">Xác thực CCCD / CMND</h2>
            <p className="text-xs text-slate-400 mt-1 font-semibold">Thông tin xác thực danh tính của bạn</p>
          </div>

          {/* Read-only images */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {/* Mặt trước */}
            <div className="space-y-2 text-left">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt trước CCCD</label>
              <div className="border border-slate-150 rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] overflow-hidden">
                {cccdFront ? (
                  <div className="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                    <img src={cccdFront} className="w-full h-full object-cover" alt="Mặt trước CCCD" />
                  </div>
                ) : (
                  <div className="text-center py-6 flex flex-col items-center justify-center text-slate-400">
                    <i className="fa-solid fa-image text-3xl mb-2"></i>
                    <p className="text-xs font-bold">Chưa có ảnh mặt trước</p>
                  </div>
                )}
              </div>
            </div>

            {/* Mặt sau */}
            <div className="space-y-2 text-left">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt sau CCCD</label>
              <div className="border border-slate-150 rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] overflow-hidden">
                {cccdBack ? (
                  <div className="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                    <img src={cccdBack} className="w-full h-full object-cover" alt="Mặt sau CCCD" />
                  </div>
                ) : (
                  <div className="text-center py-6 flex flex-col items-center justify-center text-slate-400">
                    <i className="fa-solid fa-image text-3xl mb-2"></i>
                    <p className="text-xs font-bold">Chưa có ảnh mặt sau</p>
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* Info Summary Card */}
          <div className="bg-gradient-to-br from-slate-50 to-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div className="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
              <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs">
                <i className="fa-solid fa-address-card"></i>
              </div>
              <div>
                <h4 className="text-xs font-bold text-slate-800">Thông tin Căn cước công dân đã lưu</h4>
                <p className="text-[9px] text-slate-400 font-semibold">Dữ liệu hiện tại trong hệ thống</p>
              </div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-left">
              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-hashtag"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Số CCCD / CMND</span>
                  <span className="text-xs font-black text-slate-800">{user.idNumber || 'Chưa cập nhật'}</span>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày sinh</span>
                  <span className="text-xs font-black text-slate-800">{toDisplayDate(user.dob)}</span>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày cấp</span>
                  <span className="text-xs font-black text-slate-800">{toDisplayDate(user.idDate)}</span>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-building-columns"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi cấp</span>
                  <span className="text-xs font-black text-slate-800 leading-relaxed">{user.idPlace || 'Chưa cập nhật'}</span>
                </div>
              </div>

              <div className="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5 mt-1">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Quê quán (Nơi sinh)</span>
                  <span className="text-xs font-black text-slate-800 leading-relaxed">{user.pob || 'Chưa cập nhật'}</span>
                </div>
              </div>

              <div className="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-house-user"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi thường trú</span>
                  <span className="text-xs font-black text-slate-800 leading-relaxed">{user.permanentAddress || 'Chưa cập nhật'}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Edit Action Button */}
          <div className="flex justify-end pt-4 border-t border-slate-100">
            <button 
              type="button" 
              onClick={() => setIsEditingCccd(true)} 
              className="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer active:scale-98"
            >
              <i className="fa-solid fa-pen-to-square mr-2"></i> Chỉnh sửa thông tin
            </button>
          </div>
        </div>
      )}

      {/* EDITABLE FORM VIEW */}
      {isEditingCccd && (
        <form onSubmit={handleSave} className="space-y-6 text-left">
          <div>
            <h2 className="text-xl font-bold text-slate-800">Cập nhật thông tin CCCD</h2>
            <p className="text-xs text-slate-400 mt-1 font-semibold">Tải lên hình ảnh CCCD 2 mặt và cập nhật thông tin giấy tờ</p>
          </div>

          {/* Image Upload Policy Warning Card */}
          <div className="bg-amber-50 border border-amber-200 rounded-3xl p-4 flex items-start space-x-3 text-left">
            <div className="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
              <i className="fa-solid fa-triangle-exclamation text-base"></i>
            </div>
            <div>
              <h4 className="text-xs font-black text-amber-800 mb-0.5">Yêu cầu hình ảnh:</h4>
              <p className="text-[11px] text-amber-700 font-semibold leading-relaxed">
                Ảnh chụp rõ nét, không bị lóa sáng, không mất góc và không bị che khuất các thông tin cá nhân quan trọng.
              </p>
            </div>
          </div>

          {/* Image upload inputs */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {/* Front Image Upload */}
            <div className="space-y-2 text-left">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt trước CCCD</label>
              <div className="relative border-2 border-dashed border-slate-200 hover:border-primary rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] transition group overflow-hidden cursor-pointer">
                {/* Scanning Overlay */}
                {isScanningFront && (
                  <div className="absolute inset-0 bg-slate-950/65 flex flex-col items-center justify-center text-white z-20">
                    <div className="scanner-line"></div>
                    <i className="fa-solid fa-circle-notch animate-spin text-2xl mb-2 text-emerald-400"></i>
                    <span className="text-[10px] font-black uppercase tracking-wider text-emerald-400 animate-pulse">Đang quét mặt trước...</span>
                  </div>
                )}

                {cccdFront ? (
                  <div className="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                    <img src={cccdFront} className="w-full h-full object-cover" alt="Mặt trước CCCD" />
                    <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                      <span className="text-white text-xs font-bold"><i className="fa-solid fa-camera mr-1"></i> Thay đổi</span>
                    </div>
                  </div>
                ) : (
                  <div className="text-center py-6 flex flex-col items-center justify-center">
                    <div className="w-12 h-12 bg-blue-50 text-primary flex items-center justify-center rounded-full mb-3">
                      <i className="fa-solid fa-camera text-lg"></i>
                    </div>
                    <p className="text-xs font-bold text-slate-700">Chọn ảnh mặt trước</p>
                    <p className="text-[10px] text-slate-400 mt-1">Nhấp để tải lên</p>
                  </div>
                )}
                <input 
                  type="file" 
                  accept="image/*" 
                  ref={frontInputRef}
                  onChange={(e) => handleImageChange(e, 'front')} 
                  className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                />
              </div>
            </div>

            {/* Back Image Upload */}
            <div className="space-y-2 text-left">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt sau CCCD</label>
              <div className="relative border-2 border-dashed border-slate-200 hover:border-primary rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] transition group overflow-hidden cursor-pointer">
                {/* Scanning Overlay */}
                {isScanningBack && (
                  <div className="absolute inset-0 bg-slate-950/65 flex flex-col items-center justify-center text-white z-20">
                    <div className="scanner-line"></div>
                    <i className="fa-solid fa-circle-notch animate-spin text-2xl mb-2 text-emerald-400"></i>
                    <span className="text-[10px] font-black uppercase tracking-wider text-emerald-400 animate-pulse">Đang quét mặt sau...</span>
                  </div>
                )}

                {cccdBack ? (
                  <div className="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                    <img src={cccdBack} className="w-full h-full object-cover" alt="Mặt sau CCCD" />
                    <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                      <span className="text-white text-xs font-bold"><i className="fa-solid fa-camera mr-1"></i> Thay đổi</span>
                    </div>
                  </div>
                ) : (
                  <div className="text-center py-6 flex flex-col items-center justify-center">
                    <div className="w-12 h-12 bg-blue-50 text-primary flex items-center justify-center rounded-full mb-3">
                      <i className="fa-solid fa-camera text-lg"></i>
                    </div>
                    <p className="text-xs font-bold text-slate-700">Chọn ảnh mặt sau</p>
                    <p className="text-[10px] text-slate-400 mt-1">Nhấp để tải lên</p>
                  </div>
                )}
                <input 
                  type="file" 
                  accept="image/*" 
                  ref={backInputRef}
                  onChange={(e) => handleImageChange(e, 'back')} 
                  className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                />
              </div>
            </div>
          </div>

          {/* Form Fields Group */}
          <div className="space-y-4 pt-2">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
              {/* Số CCCD */}
              <div className="space-y-1 text-left">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số CCCD / CMND (12 số)</label>
                <input 
                  type="text" 
                  value={idNumber} 
                  onChange={(e) => setIdNumber(e.target.value)} 
                  required
                  placeholder="Ví dụ: 012345678901"
                  className={`w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition ${
                    highlightNum ? 'ocr-highlight' : ''
                  }`}
                />
              </div>

              {/* Ngày sinh */}
              <div className="space-y-1 text-left">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày sinh</label>
                <input 
                  type="date" 
                  value={dob} 
                  onChange={(e) => setDob(e.target.value)} 
                  required
                  className={`w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer ${
                    highlightDob ? 'ocr-highlight' : ''
                  }`}
                />
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
              {/* Ngày cấp */}
              <div className="space-y-1 text-left">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày cấp</label>
                <input 
                  type="date" 
                  value={idDate} 
                  onChange={(e) => setIdDate(e.target.value)} 
                  required
                  className={`w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer ${
                    highlightIdDate ? 'ocr-highlight' : ''
                  }`}
                />
              </div>

              {/* Nơi cấp */}
              <div className="space-y-1 text-left">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Nơi cấp</label>
                <input 
                  type="text" 
                  value={idPlace} 
                  onChange={(e) => setIdPlace(e.target.value)} 
                  required
                  placeholder="Ví dụ: Cục Cảnh sát QLHC về TTXH"
                  className={`w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition ${
                    highlightIdPlace ? 'ocr-highlight' : ''
                  }`}
                />
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
              {/* Quê quán */}
              <div className="space-y-1 text-left">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quê quán (Nơi sinh)</label>
                <input 
                  type="text" 
                  value={pob} 
                  onChange={(e) => setPob(e.target.value)} 
                  required
                  placeholder="Ví dụ: Ba Đình, Hà Nội"
                  className={`w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition ${
                    highlightPob ? 'ocr-highlight' : ''
                  }`}
                />
              </div>

              {/* Nơi thường trú */}
              <div className="space-y-1 text-left">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Nơi thường trú</label>
                <input 
                  type="text" 
                  value={permanentAddress} 
                  onChange={(e) => setPermanentAddress(e.target.value)} 
                  required
                  placeholder="Ví dụ: 123 Nguyễn Huệ, Quận 1, TP.HCM"
                  className={`w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition ${
                    highlightAddress ? 'ocr-highlight' : ''
                  }`}
                />
              </div>
            </div>
          </div>

          {/* Action buttons */}
          <div className="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            {user.idNumber && (
              <button 
                type="button" 
                onClick={handleCancel}
                className="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer active:scale-98 min-w-[100px]"
              >
                Hủy
              </button>
            )}
            <button 
              type="submit" 
              disabled={isSaving}
              className="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
            >
              {isSaving ? (
                <>
                  <i className="fa-solid fa-spinner animate-spin mr-2" />
                  Đang cập nhật...
                </>
              ) : (
                <>
                  <i className="fa-solid fa-floppy-disk mr-2" />
                  Cập nhật xác thực
                </>
              )}
            </button>
          </div>

          {/* Summary card at bottom of editable view */}
          <div className="bg-gradient-to-br from-slate-50 to-white border border-slate-100 rounded-3xl p-6 mt-6 shadow-sm">
            <div className="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
              <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs">
                <i className="fa-solid fa-address-card"></i>
              </div>
              <div>
                <h4 className="text-xs font-bold text-slate-800">Thông tin Căn cước công dân đã lưu</h4>
                <p className="text-[9px] text-slate-400 font-semibold">Dữ liệu hiện tại trong hệ thống</p>
              </div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-left">
              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-hashtag"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Số CCCD / CMND</span>
                  <span className="text-xs font-black text-slate-800">{user.idNumber || 'Chưa cập nhật'}</span>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày sinh</span>
                  <span className="text-xs font-black text-slate-800">{toDisplayDate(user.dob)}</span>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày cấp</span>
                  <span className="text-xs font-black text-slate-800">{toDisplayDate(user.idDate)}</span>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-building-columns"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi cấp</span>
                  <span className="text-xs font-black text-slate-800 leading-relaxed">{user.idPlace || 'Chưa cập nhật'}</span>
                </div>
              </div>

              <div className="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5 mt-1">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Quê quán (Nơi sinh)</span>
                  <span className="text-xs font-black text-slate-800 leading-relaxed">{user.pob || 'Chưa cập nhật'}</span>
                </div>
              </div>

              <div className="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5">
                <div className="mt-1 text-slate-400 text-xs w-4 text-center">
                  <i className="fa-solid fa-house-user"></i>
                </div>
                <div>
                  <span className="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi thường trú</span>
                  <span className="text-xs font-black text-slate-800 leading-relaxed">{user.permanentAddress || 'Chưa cập nhật'}</span>
                </div>
              </div>
            </div>
          </div>
        </form>
      )}
    </div>
  )
}
