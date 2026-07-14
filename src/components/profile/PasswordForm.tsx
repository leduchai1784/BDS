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

  // Show/Hide States
  const [showCurrentPassword, setShowCurrentPassword] = useState(false)
  const [showNewPassword, setShowNewPassword] = useState(false)
  const [copiedNewPassword, setCopiedNewPassword] = useState(false)

  // Password Generator States
  const [openGen, setOpenGen] = useState(false)
  const [passwordLength, setPasswordLength] = useState(12)
  const [useUpper, setUseUpper] = useState(true)
  const [useLower, setUseLower] = useState(true)
  const [useNumbers, setUseNumbers] = useState(true)
  const [useSpecial, setUseSpecial] = useState(true)
  const [generatedPassText, setGeneratedPassText] = useState('')
  const [genCopied, setGenCopied] = useState(false)
  const [showGenPass, setShowGenPass] = useState(true)
  const [savedConfirm, setSavedConfirm] = useState(false)

  const generateRandomPassword = (
    length: number = passwordLength,
    upper: boolean = useUpper,
    lower: boolean = useLower,
    num: boolean = useNumbers,
    spec: boolean = useSpecial
  ) => {
    let charset = ''
    if (upper) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    if (lower) charset += 'abcdefghijklmnopqrstuvwxyz'
    if (num) charset += '0123456789'
    if (spec) charset += '!@#$%^&*()_+~|{}[]:;?'

    let activeLower = lower
    let activeNumbers = num
    if (charset.length === 0) {
      activeLower = true
      activeNumbers = true
      charset = 'abcdefghijklmnopqrstuvwxyz0123456789'
    }

    let password = ''
    let guaranteed: string[] = []
    if (upper) guaranteed.push('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.charAt(Math.floor(Math.random() * 26)))
    if (activeLower) guaranteed.push('abcdefghijklmnopqrstuvwxyz'.charAt(Math.floor(Math.random() * 26)))
    if (activeNumbers) guaranteed.push('0123456789'.charAt(Math.floor(Math.random() * 10)))
    if (spec) {
      const specials = '!@#$%^&*()_+~|{}[]:;?'
      guaranteed.push(specials.charAt(Math.floor(Math.random() * specials.length)))
    }

    let remainingLength = length - guaranteed.length
    if (remainingLength < 0) {
      guaranteed = guaranteed.slice(0, length)
      remainingLength = 0
    }

    for (let i = 0; i < remainingLength; i++) {
      password += charset.charAt(Math.floor(Math.random() * charset.length))
    }

    let finalPasswordArray = guaranteed.concat(password.split(''))
    for (let i = finalPasswordArray.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1))
      const temp = finalPasswordArray[i]
      finalPasswordArray[i] = finalPasswordArray[j]
      finalPasswordArray[j] = temp
    }

    const generated = finalPasswordArray.join('')
    setGeneratedPassText(generated)
    setSavedConfirm(false) // Reset confirmation
  }

  const copyGenPass = () => {
    navigator.clipboard.writeText(generatedPassText)
    setGenCopied(true)
    setTimeout(() => setGenCopied(false), 2000)
  }

  const applyGeneratedPassword = () => {
    if (savedConfirm && generatedPassText) {
      setNewPassword(generatedPassText)
      setConfirmPassword(generatedPassText)
      setShowNewPassword(true)
      setOpenGen(false)
    }
  }

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
        setShowCurrentPassword(false)
        setShowNewPassword(false)
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
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Mật khẩu hiện tại</label>
        <div className="relative">
          <i className="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type={showCurrentPassword ? 'text' : 'password'} 
            value={currentPassword} 
            onChange={(e) => setCurrentPassword(e.target.value)} 
            required
            placeholder="••••••••"
            className="w-full pl-10 pr-12 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
          <button 
            type="button" 
            onClick={() => setShowCurrentPassword(!showCurrentPassword)}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-650 transition cursor-pointer"
            title="Hiện/Ẩn mật khẩu"
          >
            <i className={`fa-solid ${showCurrentPassword ? 'fa-eye-slash' : 'fa-eye'} text-xs`} />
          </button>
        </div>
      </div>

      {/* New Password */}
      <div className="space-y-1.5 relative">
        <div className="flex justify-between items-center px-1">
          <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Mật khẩu mới</label>
          
          {/* Popover Dropdown for Generating Password */}
          <div className="relative">
            <button 
              type="button" 
              onClick={() => {
                const nextState = !openGen
                setOpenGen(nextState)
                if(nextState) generateRandomPassword(passwordLength, useUpper, useLower, useNumbers, useSpecial)
              }}
              className="text-[10px] font-bold text-primary hover:text-primary-hover flex items-center gap-1 transition cursor-pointer"
            >
              <i className="fa-solid fa-wand-magic-sparkles"></i> Mật khẩu ngẫu nhiên
            </button>
            
            {openGen && (
              <div className="absolute right-0 mt-2 z-50 w-64 rounded-2xl bg-white border border-slate-200 shadow-2xl p-3.5 text-left space-y-2.5 select-none">
                <div className="flex justify-between items-center pb-1.5 border-b border-slate-100">
                  <span className="text-xs font-bold text-primary flex items-center gap-1.5">
                    <i className="fa-solid fa-wand-magic-sparkles"></i> Generate Password
                  </span>
                  <button type="button" onClick={() => setOpenGen(false)} className="text-slate-400 hover:text-slate-650 text-xs">
                    <i className="fa-solid fa-xmark"></i>
                  </button>
                </div>
                
                {/* Password Display Area */}
                <div className="relative bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 flex items-center justify-between min-h-[38px]">
                  <span className="text-xs font-mono font-bold text-slate-700 tracking-wide break-all select-all">
                    {generatedPassText ? (showGenPass ? generatedPassText : '•'.repeat(generatedPassText.length)) : '***'}
                  </span>
                  <div className="flex items-center space-x-1.5 flex-shrink-0 ml-2">
                    <button type="button" onClick={() => setShowGenPass(!showGenPass)} className="text-slate-400 hover:text-slate-605 p-0.5 cursor-pointer" title="Hiện/Ẩn">
                      <i className={`fa-solid text-[10px] ${showGenPass ? 'fa-eye-slash' : 'fa-eye'}`}></i>
                    </button>
                    {generatedPassText && (
                      <button type="button" onClick={copyGenPass} className="text-slate-400 hover:text-slate-655 p-0.5 cursor-pointer" title="Sao chép">
                        <i className={`fa-solid text-[10px] ${genCopied ? 'fa-check text-green-500' : 'fa-copy'}`}></i>
                      </button>
                    )}
                  </div>
                </div>

                {/* Length control slider */}
                <div className="space-y-1">
                  <div className="flex justify-between items-center text-[9px] font-extrabold uppercase text-slate-400">
                    <span>Số lượng ký tự</span>
                    <span className="text-xs font-bold text-slate-700">{passwordLength}</span>
                  </div>
                  <input 
                    type="range" 
                    min="8" 
                    max="32" 
                    value={passwordLength}
                    onChange={(e) => {
                      const len = Number(e.target.value)
                      setPasswordLength(len)
                      generateRandomPassword(len, useUpper, useLower, useNumbers, useSpecial)
                    }}
                    className="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary" 
                  />
                </div>
                
                {/* Checkboxes */}
                <div className="space-y-1.5 text-[10px] font-bold text-slate-600 pt-0.5">
                  <label className="flex items-center space-x-2 cursor-pointer">
                    <input 
                      type="checkbox" 
                      checked={useNumbers} 
                      onChange={(e) => {
                        setUseNumbers(e.target.checked)
                        generateRandomPassword(passwordLength, useUpper, useLower, e.target.checked, useSpecial)
                      }} 
                      className="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5" 
                    />
                    <span>Có ký tự số</span>
                  </label>
                  <label className="flex items-center space-x-2 cursor-pointer">
                    <input 
                      type="checkbox" 
                      checked={useLower} 
                      onChange={(e) => {
                        setUseLower(e.target.checked)
                        generateRandomPassword(passwordLength, useUpper, e.target.checked, useNumbers, useSpecial)
                      }} 
                      className="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5" 
                    />
                    <span>Có ký tự thường</span>
                  </label>
                  <label className="flex items-center space-x-2 cursor-pointer">
                    <input 
                      type="checkbox" 
                      checked={useUpper} 
                      onChange={(e) => {
                        setUseUpper(e.target.checked)
                        generateRandomPassword(passwordLength, e.target.checked, useLower, useNumbers, useSpecial)
                      }} 
                      className="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5" 
                    />
                    <span>Có ký tự hoa</span>
                  </label>
                  <label className="flex items-center space-x-2 cursor-pointer">
                    <input 
                      type="checkbox" 
                      checked={useSpecial} 
                      onChange={(e) => {
                        setUseSpecial(e.target.checked)
                        generateRandomPassword(passwordLength, useUpper, useLower, useNumbers, e.target.checked)
                      }} 
                      className="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5" 
                    />
                    <span>Có ký tự đặc biệt</span>
                  </label>
                </div>

                {/* Save check checkbox */}
                <div className="pt-1.5 border-t border-slate-100">
                  <label className="flex items-start space-x-2 cursor-pointer text-[10px] font-bold text-slate-600">
                    <input 
                      type="checkbox" 
                      checked={savedConfirm} 
                      onChange={(e) => setSavedConfirm(e.target.checked)} 
                      className="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5 mt-0.5" 
                    />
                    <span className="leading-tight">Tôi đã lưu lại mật khẩu mới</span>
                  </label>
                </div>

                {/* Submit (Apply) Button */}
                <button 
                  type="button" 
                  disabled={!savedConfirm || !generatedPassText}
                  onClick={applyGeneratedPassword}
                  className={`w-full py-2.5 text-xs font-bold rounded-full transition cursor-pointer text-center ${
                    (!savedConfirm || !generatedPassText) ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-primary text-white hover:bg-primary-hover active:scale-98 shadow-md'
                  }`}
                >
                  Xác nhận
                </button>
              </div>
            )}
          </div>
        </div>

        <div className="relative">
          <i className="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type={showNewPassword ? 'text' : 'password'} 
            value={newPassword} 
            onChange={(e) => setNewPassword(e.target.value)} 
            required
            placeholder="Tối thiểu 6 ký tự..."
            className="w-full pl-10 pr-20 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
          <div className="absolute right-2 top-1/2 -translate-y-1/2 flex items-center space-x-1">
            <button 
              type="button" 
              onClick={() => setShowNewPassword(!showNewPassword)}
              className="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-650 transition cursor-pointer"
              title="Hiện/Ẩn mật khẩu"
            >
              <i className={`fa-solid ${showNewPassword ? 'fa-eye-slash' : 'fa-eye'} text-xs`} />
            </button>
            {newPassword.length > 0 && (
              <button 
                type="button" 
                onClick={() => {
                  navigator.clipboard.writeText(newPassword)
                  setCopiedNewPassword(true)
                  setTimeout(() => setCopiedNewPassword(false), 2000)
                }}
                className="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-650 transition cursor-pointer"
                title="Sao chép"
              >
                <i className={`fa-solid ${copiedNewPassword ? 'fa-check text-green-500' : 'fa-copy'} text-xs`} />
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Confirm Password */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Xác nhận mật khẩu mới</label>
        <div className="relative">
          <i className="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type={showNewPassword ? 'text' : 'password'} 
            value={confirmPassword} 
            onChange={(e) => setConfirmPassword(e.target.value)} 
            required
            placeholder="Nhập lại mật khẩu mới..."
            className="w-full pl-10 pr-12 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
          <button 
            type="button" 
            onClick={() => setShowNewPassword(!showNewPassword)}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-650 transition cursor-pointer"
            title="Hiện/Ẩn mật khẩu"
          >
            <i className={`fa-solid ${showNewPassword ? 'fa-eye-slash' : 'fa-eye'} text-xs`} />
          </button>
        </div>
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
