'use client'

import { useState } from 'react'

interface PasswordFormProps {
  onSuccess: (message: string) => void
}

export default function PasswordForm({ onSuccess }: PasswordFormProps) {
  const [currentPassword, setCurrentPassword] = useState('')
  const [newPassword, setNewPassword] = useState('')
  const [confirmPassword, setConfirmPassword] = useState('')

  const [isSaving, setIsSaving] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSaving(true)
    setErrorMsg('')

    if (newPassword !== confirmPassword) {
      setErrorMsg('Mật khẩu mới và xác nhận mật khẩu không trùng khớp.')
      setIsSaving(false)
      return
    }

    if (newPassword.length < 6) {
      setErrorMsg('Mật khẩu mới phải có tối thiểu 6 ký tự.')
      setIsSaving(false)
      return
    }

    try {
      const res = await fetch('/api/profile/password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          current_password: currentPassword,
          new_password: newPassword
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        onSuccess('Thay đổi mật khẩu tài khoản thành công!')
        setCurrentPassword('')
        setNewPassword('')
        setConfirmPassword('')
      } else {
        setErrorMsg(data.message || 'Mật khẩu cũ không chính xác.')
      }
    } catch (err) {
      setErrorMsg('Lỗi kết nối mạng, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsSaving(false)
    }
  }

  return (
    <form onSubmit={handleSave} className="space-y-5 text-left max-w-lg">
      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* Current Password */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Mật khẩu hiện tại</label>
        <input 
          type="password" 
          value={currentPassword} 
          onChange={(e) => setCurrentPassword(e.target.value)} 
          required
          placeholder="••••••••"
          className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
        />
      </div>

      {/* New Password */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Mật khẩu mới</label>
        <input 
          type="password" 
          value={newPassword} 
          onChange={(e) => setNewPassword(e.target.value)} 
          required
          placeholder="Tối thiểu 6 ký tự..."
          className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
        />
      </div>

      {/* Confirm Password */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Xác nhận mật khẩu mới</label>
        <input 
          type="password" 
          value={confirmPassword} 
          onChange={(e) => setConfirmPassword(e.target.value)} 
          required
          placeholder="Nhập lại mật khẩu mới..."
          className="w-full px-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
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
            <span>Đổi mật khẩu</span>
          )}
        </button>
      </div>
    </form>
  )
}
