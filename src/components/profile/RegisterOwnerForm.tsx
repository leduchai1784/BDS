'use client'

import { useState } from 'react'

interface RegisterOwnerFormProps {
  onSuccess: (message: string) => void
  initialPhone?: string
  initialCompany?: string
}

export default function RegisterOwnerForm({ onSuccess, initialPhone = '', initialCompany = '' }: RegisterOwnerFormProps) {
  const [phone, setPhone] = useState(initialPhone)
  const [companyName, setCompanyName] = useState(initialCompany)

  const [isSaving, setIsSaving] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSaving(true)
    setErrorMsg('')

    try {
      const res = await fetch('/api/profile/register-owner', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          phone,
          company_name: companyName
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        onSuccess('Nâng cấp tài khoản Chủ nhà/Đối tác thành công! Vui lòng tải lại trang.')
        // Reload to sync session role update
        setTimeout(() => {
          window.location.reload()
        }, 1500)
      } else {
        setErrorMsg(data.message || 'Lỗi đăng ký đối tác.')
      }
    } catch (err) {
      setErrorMsg('Lỗi kết nối mạng, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsSaving(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6 text-left max-w-lg">
      <div className="bg-slate-50 border border-slate-150 rounded-2xl p-5 mb-4">
        <h4 className="text-xs font-black text-slate-800 mb-2 flex items-center gap-1.5">
          <i className="fa-solid fa-circle-question text-primary" />
          <span>Quyền lợi của Đối tác Chủ nhà</span>
        </h4>
        <ul className="list-disc pl-4 text-[11px] text-slate-500 font-semibold space-y-1">
          <li>Đăng tin cho thuê/bán nhà đất không giới hạn số lượng.</li>
          <li>Quản lý yêu cầu đặt lịch xem nhà trực tuyến từ khách hàng.</li>
          <li>Tiếp cận hệ thống công cụ hỗ trợ tiếp thị AI Content Studio.</li>
        </ul>
      </div>

      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* Phone */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Số điện thoại liên hệ</label>
        <input 
          type="tel" 
          value={phone} 
          onChange={(e) => setPhone(e.target.value)} 
          required
          placeholder="Ví dụ: 0977.758.217"
          className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
        />
      </div>

      {/* Company Name */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tên văn phòng/công ty (nếu có)</label>
        <input 
          type="text" 
          value={companyName} 
          onChange={(e) => setCompanyName(e.target.value)} 
          placeholder="Tên công ty hoặc để trống..."
          className="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
        />
      </div>

      <div className="flex justify-end pt-2">
        <button 
          type="submit" 
          disabled={isSaving}
          className="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-60"
        >
          {isSaving ? (
            <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Đang nâng cấp...</span>
          ) : (
            <span>Nâng cấp ngay</span>
          )}
        </button>
      </div>
    </form>
  )
}
