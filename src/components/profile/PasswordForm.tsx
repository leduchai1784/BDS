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

  // Password Generator States
  const [openGen, setOpenGen] = useState(false)
  const [passwordLength, setPasswordLength] = useState(12)
  const [useUpper, setUseUpper] = useState(true)
  const [useLower, setUseLower] = useState(true)
  const [useNumbers, setUseNumbers] = useState(true)
  const [useSpecial, setUseSpecial] = useState(true)
  const [generatedPassText, setGeneratedPassText] = useState('')
  const [genCopied, setGenCopied] = useState(false)

  const generateRandomPassword = () => {
    let charset = ''
    if (useUpper) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    if (useLower) charset += 'abcdefghijklmnopqrstuvwxyz'
    if (useNumbers) charset += '0123456789'
    if (useSpecial) charset += '!@#$%^&*()_+~|{}[]:;?'

    if (charset.length === 0) {
      setUseLower(true)
      setUseNumbers(true)
      charset = 'abcdefghijklmnopqrstuvwxyz0123456789'
    }

    let password = ''
    let guaranteed: string[] = []
    if (useUpper) guaranteed.push('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.charAt(Math.floor(Math.random() * 26)))
    if (useLower) guaranteed.push('abcdefghijklmnopqrstuvwxyz'.charAt(Math.floor(Math.random() * 26)))
    if (useNumbers) guaranteed.push('0123456789'.charAt(Math.floor(Math.random() * 10)))
    if (useSpecial) {
      const specials = '!@#$%^&*()_+~|{}[]:;?'
      guaranteed.push(specials.charAt(Math.floor(Math.random() * specials.length)))
    }

    let remainingLength = passwordLength - guaranteed.length
    if (remainingLength < 0) {
      guaranteed = guaranteed.slice(0, passwordLength)
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
  }

  const copyGenPass = () => {
    navigator.clipboard.writeText(generatedPassText)
    setGenCopied(true)
    setTimeout(() => setGenCopied(false), 2000)
  }

  const applyGeneratedPassword = () => {
    setNewPassword(generatedPassText)
    setConfirmPassword(generatedPassText)
    setOpenGen(false)
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
            type="password" 
            value={currentPassword} 
            onChange={(e) => setCurrentPassword(e.target.value)} 
            required
            placeholder="••••••••"
            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
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
                setOpenGen(!openGen)
                if(!openGen) generateRandomPassword()
              }}
              className="text-[10px] font-bold text-primary hover:text-primary-hover flex items-center gap-1 transition cursor-pointer"
            >
              <i className="fa-solid fa-wand-magic-sparkles"></i> Mật khẩu ngẫu nhiên
            </button>
            
            {openGen && (
              <div className="absolute right-0 mt-2 z-50 w-72 bg-white border border-slate-200 rounded-2xl shadow-xl p-4 space-y-4">
                <div className="flex justify-between items-center border-b border-slate-100 pb-2">
                  <span className="text-[11px] font-black text-slate-800 uppercase tracking-wider">Tạo mật khẩu</span>
                  <button type="button" onClick={() => setOpenGen(false)} className="text-slate-400 hover:text-slate-650 text-xs font-bold">×</button>
                </div>
                
                {/* Result field */}
                <div className="flex items-center gap-1.5 bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                  <span className="text-xs font-mono font-bold select-all break-all flex-grow text-slate-700">{generatedPassText}</span>
                  <button type="button" onClick={copyGenPass} className="text-slate-400 hover:text-primary transition text-xs shrink-0" title="Sao chép">
                    <i className={genCopied ? "fa-solid fa-check text-green-500" : "fa-regular fa-copy"} />
                  </button>
                </div>
                
                {/* Config Controls */}
                <div className="space-y-2.5">
                  <div className="flex items-center justify-between">
                    <span className="text-[10px] text-slate-500 font-bold">Độ dài: <span className="text-primary font-black">{passwordLength}</span></span>
                    <input 
                      type="range" 
                      min="8" 
                      max="24" 
                      value={passwordLength}
                      onChange={(e) => {
                        setPasswordLength(Number(e.target.value))
                        setTimeout(generateRandomPassword, 50)
                      }}
                      className="w-36 h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary" 
                    />
                  </div>
                  
                  <div className="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-600">
                    <label className="flex items-center gap-1.5 cursor-pointer">
                      <input type="checkbox" checked={useUpper} onChange={(e) => { setUseUpper(e.target.checked); setTimeout(generateRandomPassword, 50); }} className="accent-primary w-3.5 h-3.5" />
                      <span>Ký tự HOA</span>
                    </label>
                    <label className="flex items-center gap-1.5 cursor-pointer">
                      <input type="checkbox" checked={useLower} onChange={(e) => { setUseLower(e.target.checked); setTimeout(generateRandomPassword, 50); }} className="accent-primary w-3.5 h-3.5" />
                      <span>Ký tự thường</span>
                    </label>
                    <label className="flex items-center gap-1.5 cursor-pointer">
                      <input type="checkbox" checked={useNumbers} onChange={(e) => { setUseNumbers(e.target.checked); setTimeout(generateRandomPassword, 50); }} className="accent-primary w-3.5 h-3.5" />
                      <span>Chữ số (0-9)</span>
                    </label>
                    <label className="flex items-center gap-1.5 cursor-pointer">
                      <input type="checkbox" checked={useSpecial} onChange={(e) => { setUseSpecial(e.target.checked); setTimeout(generateRandomPassword, 50); }} className="accent-primary w-3.5 h-3.5" />
                      <span>Đặc biệt</span>
                    </label>
                  </div>
                </div>
                
                {/* Apply Button */}
                <button 
                  type="button" 
                  onClick={applyGeneratedPassword}
                  className="w-full py-2 bg-primary hover:bg-primary-hover text-white text-[11px] font-bold rounded-xl shadow-md shadow-primary/10 transition cursor-pointer"
                >
                  Sử dụng mật khẩu này
                </button>
              </div>
            )}
          </div>
        </div>

        <div className="relative">
          <i className="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type="password" 
            value={newPassword} 
            onChange={(e) => setNewPassword(e.target.value)} 
            required
            placeholder="Tối thiểu 6 ký tự..."
            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>
      </div>

      {/* Confirm Password */}
      <div className="space-y-1.5">
        <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Xác nhận mật khẩu mới</label>
        <div className="relative">
          <i className="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type="password" 
            value={confirmPassword} 
            onChange={(e) => setConfirmPassword(e.target.value)} 
            required
            placeholder="Nhập lại mật khẩu mới..."
            className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
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
            <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Đang lưu...</span>
          ) : (
            <span>Đổi mật khẩu</span>
          )}
        </button>
      </div>
    </form>
  )
}
