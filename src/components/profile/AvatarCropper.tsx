'use client'

import { useState, useRef } from 'react'

interface AvatarCropperProps {
  currentAvatar: string
  onSuccess: (message: string, newAvatar: string) => void
}

export default function AvatarCropper({ currentAvatar, onSuccess }: AvatarCropperProps) {
  const [previewUrl, setPreviewUrl] = useState(currentAvatar)
  const [isUploading, setIsUploading] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')
  const fileInputRef = useRef<HTMLInputElement>(null)

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (!file) return

    if (!file.type.startsWith('image/')) {
      setErrorMsg('Vui lòng chọn tệp hình ảnh hợp lệ (jpg, png, jpeg).')
      return
    }

    if (file.size > 2 * 1024 * 1024) {
      setErrorMsg('Dung lượng ảnh tối đa là 2MB.')
      return
    }

    setErrorMsg('')
    const reader = new FileReader()
    reader.onload = () => {
      if (typeof reader.result === 'string') {
        setPreviewUrl(reader.result)
      }
    }
    reader.readAsDataURL(file)
  }

  const handleUpload = async () => {
    if (previewUrl === currentAvatar) {
      setErrorMsg('Vui lòng chọn ảnh mới trước khi tải lên.')
      return
    }

    setIsUploading(true)
    setErrorMsg('')

    try {
      const res = await fetch('/api/profile/avatar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ avatar: previewUrl })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        onSuccess('Cập nhật ảnh đại diện thành công!', data.avatar)
      } else {
        setErrorMsg(data.message || 'Lỗi tải ảnh lên.')
      }
    } catch (err) {
      setErrorMsg('Lỗi kết nối mạng, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsUploading(false)
    }
  }

  return (
    <div className="space-y-6 text-left max-w-md">
      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      <div className="flex flex-col items-center space-y-4">
        {/* Avatar Viewbox */}
        <div className="relative w-36 h-36 rounded-full overflow-hidden border-4 border-white shadow-xl bg-slate-100">
          <img 
            src={previewUrl} 
            alt="Avatar Preview" 
            className="w-full h-full object-cover"
            onError={(e) => {
              e.currentTarget.src = 'https://ui-avatars.com/api/?name=BDS&background=0077bb&color=fff'
            }}
          />
        </div>

        {/* Input Trigger */}
        <input 
          type="file" 
          ref={fileInputRef} 
          onChange={handleFileChange} 
          accept="image/*" 
          className="hidden"
        />

        <div className="flex gap-3">
          <button 
            type="button" 
            onClick={() => fileInputRef.current?.click()}
            className="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-xs transition cursor-pointer"
          >
            <i className="fa-solid fa-camera mr-1.5" />
            Chọn ảnh từ máy
          </button>

          {previewUrl !== currentAvatar && (
            <button 
              type="button" 
              onClick={handleUpload}
              disabled={isUploading}
              className="px-5 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 transition cursor-pointer disabled:opacity-60"
            >
              {isUploading ? (
                <span><i className="fa-solid fa-spinner animate-spin mr-1" /> Tải lên...</span>
              ) : (
                <span>Cập nhật ảnh</span>
              )}
            </button>
          )}
        </div>
      </div>
    </div>
  )
}
