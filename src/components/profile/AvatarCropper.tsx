'use client'

import { useState, useRef } from 'react'
import Cropper, { ReactCropperElement } from 'react-cropper'
import 'cropperjs/dist/cropper.css'

interface AvatarCropperProps {
  currentAvatar: string
  onSuccess: (message: string, newAvatar: string) => void
}

export default function AvatarCropper({ currentAvatar, onSuccess }: AvatarCropperProps) {
  const [isEditingAvatar, setIsEditingAvatar] = useState(false)
  const [hasImage, setHasImage] = useState(false)
  const [isUploading, setIsUploading] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')
  const [imageSrc, setImageSrc] = useState('')

  const fileInputRef = useRef<HTMLInputElement>(null)
  const cropperRef = useRef<ReactCropperElement>(null)

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (!file) return

    if (!file.type.startsWith('image/')) {
      setErrorMsg('Vui lòng chọn tệp hình ảnh hợp lệ (jpg, png, jpeg).')
      return
    }

    if (file.size > 5 * 1024 * 1024) {
      setErrorMsg('Dung lượng ảnh tối đa là 5MB.')
      return
    }

    setErrorMsg('')
    const reader = new FileReader()
    reader.onload = () => {
      if (typeof reader.result === 'string') {
        setImageSrc(reader.result)
        setHasImage(true)
      }
    }
    reader.readAsDataURL(file)
  }

  const handleCancelCrop = () => {
    setHasImage(false)
    setImageSrc('')
    if (fileInputRef.current) {
      fileInputRef.current.value = ''
    }
  }

  const handleZoomIn = () => {
    const cropper = cropperRef.current?.cropper
    if (cropper) cropper.zoom(0.1)
  }

  const handleZoomOut = () => {
    const cropper = cropperRef.current?.cropper
    if (cropper) cropper.zoom(-0.1)
  }

  const handleRotateLeft = () => {
    const cropper = cropperRef.current?.cropper
    if (cropper) cropper.rotate(-90)
  }

  const handleRotateRight = () => {
    const cropper = cropperRef.current?.cropper
    if (cropper) cropper.rotate(90)
  }

  const handleReset = () => {
    const cropper = cropperRef.current?.cropper
    if (cropper) cropper.reset()
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()

    const cropper = cropperRef.current?.cropper
    if (!cropper) return

    setIsUploading(true)
    setErrorMsg('')

    try {
      const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
      })

      if (!canvas) {
        setErrorMsg('Có lỗi xảy ra khi xử lý ảnh đại diện.')
        setIsUploading(false)
        return
      }

      const base64Data = canvas.toDataURL('image/jpeg', 0.9)

      const res = await fetch('/api/profile/avatar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ avatar: base64Data })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        onSuccess('Cập nhật ảnh đại diện thành công!', data.avatar)
        setIsEditingAvatar(false)
        setHasImage(false)
        setImageSrc('')
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
    <div className="space-y-6 text-left">
      {/* CSS Override to make the CropperJS selection box circular */}
      <style>{`
        .avatar-crop-container .cropper-view-box,
        .avatar-crop-container .cropper-face {
          border-radius: 50% !important;
        }
      `}</style>

      {errorMsg && (
        <div className="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold max-w-lg">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* READ-ONLY AVATAR VIEW */}
      {!isEditingAvatar && (
        <div className="flex flex-col items-center justify-center space-y-6 py-10 bg-slate-50 rounded-3xl border border-slate-100 px-6">
          <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-200">
            <img src={currentAvatar} alt="Avatar preview" className="w-full h-full object-cover" />
          </div>
          <div className="text-center space-y-2">
            <h4 className="text-sm font-bold text-slate-800">Ảnh đại diện hiện tại</h4>
            <p className="text-xs text-slate-400 max-w-xs leading-normal">Hình ảnh được sử dụng để nhận diện tài khoản của bạn trên hệ thống.</p>
            <button 
              type="button" 
              onClick={() => setIsEditingAvatar(true)} 
              className="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer active:scale-98"
            >
              <i className="fa-solid fa-pen-to-square mr-2"></i> Thay đổi ảnh đại diện
            </button>
          </div>
        </div>
      )}

      {/* EDITABLE AVATAR VIEW */}
      {isEditingAvatar && (
        <form onSubmit={handleSave} className="space-y-6">
          <div className="flex flex-col md:flex-row items-center justify-center gap-8 py-8 bg-slate-50 rounded-3xl border border-slate-100 px-6">
            
            {/* Left side: Interactive Area */}
            <div className="flex flex-col items-center space-y-4 w-full max-w-sm">
              {/* Current Avatar view (shows when no new image selected) */}
              {!hasImage && (
                <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-200 relative group">
                  <img src={currentAvatar} alt="Avatar preview" className="w-full h-full object-cover" />
                </div>
              )}

              {/* Cropper container (shows when a new image is selected) */}
              {hasImage && imageSrc && (
                <div className="w-full bg-slate-100 rounded-2xl overflow-hidden border border-slate-200">
                  <div className="avatar-crop-container flex justify-center items-center overflow-hidden h-64 w-full">
                    <Cropper
                      src={imageSrc}
                      style={{ height: '100%', width: '100%' }}
                      initialAspectRatio={1}
                      aspectRatio={1}
                      guides={true}
                      viewMode={1}
                      dragMode="move"
                      cropBoxMovable={false}
                      cropBoxResizable={false}
                      toggleDragModeOnDblclick={false}
                      preview=".img-preview"
                      ref={cropperRef}
                    />
                  </div>
                </div>
              )}

              {/* Action button for choosing files */}
              <div className="text-center space-y-2">
                <h4 className="text-sm font-bold text-slate-800">Chọn ảnh đại diện mới</h4>
                <p className="text-xs text-slate-400 max-w-xs leading-normal">Hỗ trợ định dạng JPG, PNG dung lượng dưới 5MB.</p>
                
                <div className="flex flex-wrap justify-center gap-3">
                  {/* Select Image Button */}
                  <label className="inline-flex items-center justify-center px-4 py-2 border border-slate-200 hover:border-primary text-xs font-bold rounded-xl text-slate-700 hover:text-white bg-white hover:bg-primary shadow-sm transition cursor-pointer">
                    <i className="fa-solid fa-camera mr-2 text-xs"></i> Chọn ảnh
                    <input 
                      type="file" 
                      ref={fileInputRef} 
                      onChange={handleFileChange} 
                      accept="image/*" 
                      className="hidden" 
                    />
                  </label>

                  {/* Cancel/Reset Selection Button */}
                  {hasImage && (
                    <button 
                      type="button" 
                      onClick={handleCancelCrop}
                      className="inline-flex items-center justify-center px-4 py-2 border border-rose-200 hover:border-rose-500 text-xs font-bold rounded-xl text-rose-600 hover:text-white bg-white hover:bg-rose-500 shadow-sm transition cursor-pointer"
                    >
                      Hủy chọn
                    </button>
                  )}
                </div>
              </div>
            </div>

            {/* Right side: Cropper Controls & Cropped Preview */}
            {hasImage && (
              <div className="flex flex-col items-center space-y-6 w-full max-w-xs border-t md:border-t-0 md:border-l border-slate-200/80 pt-6 md:pt-0 md:pl-8">
                {/* Cropped Preview Circle */}
                <div className="flex flex-col items-center space-y-2">
                  <span className="text-xs font-bold text-slate-500">Xem trước kết quả</span>
                  <div className="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-slate-100 relative">
                    {/* Preview element container for CropperJS */}
                    <div className="img-preview w-full h-full overflow-hidden rounded-full" style={{ width: '112px', height: '112px', overflow: 'hidden' }} />
                  </div>
                </div>

                {/* Navigation & Crop Controls */}
                <div className="flex flex-col space-y-3 w-full">
                  <span className="text-xs font-bold text-slate-500 text-center">Công cụ thu phóng & xoay</span>
                  
                  {/* Zoom & Pan Buttons */}
                  <div className="flex justify-center gap-2">
                    {/* Zoom In */}
                    <button 
                      type="button" 
                      onClick={handleZoomIn}
                      className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-600 flex items-center justify-center transition border border-slate-200 shadow-sm cursor-pointer" 
                      title="Phóng to"
                    >
                      <i className="fa-solid fa-magnifying-glass-plus text-sm"></i>
                    </button>
                    
                    {/* Zoom Out */}
                    <button 
                      type="button" 
                      onClick={handleZoomOut}
                      className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-600 flex items-center justify-center transition border border-slate-200 shadow-sm cursor-pointer" 
                      title="Thu nhỏ"
                    >
                      <i className="fa-solid fa-magnifying-glass-minus text-sm"></i>
                    </button>

                    {/* Rotate Left */}
                    <button 
                      type="button" 
                      onClick={handleRotateLeft}
                      className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-600 flex items-center justify-center transition border border-slate-200 shadow-sm cursor-pointer" 
                      title="Xoay trái"
                    >
                      <i className="fa-solid fa-rotate-left text-sm"></i>
                    </button>

                    {/* Rotate Right */}
                    <button 
                      type="button" 
                      onClick={handleRotateRight}
                      className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-600 flex items-center justify-center transition border border-slate-200 shadow-sm cursor-pointer" 
                      title="Xoay phải"
                    >
                      <i className="fa-solid fa-rotate-right text-sm"></i>
                    </button>

                    {/* Reset */}
                    <button 
                      type="button" 
                      onClick={handleReset}
                      className="w-10 h-10 rounded-xl bg-slate-100 hover:bg-rose-500 hover:text-white text-slate-600 flex items-center justify-center transition border border-slate-200 shadow-sm cursor-pointer" 
                      title="Đặt lại"
                    >
                      <i className="fa-solid fa-arrows-rotate text-sm"></i>
                    </button>
                  </div>
                </div>
              </div>
            )}
          </div>
          
          {/* Submit / Cancel Footer */}
          <div className="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
            <button 
              type="button" 
              onClick={() => {
                setIsEditingAvatar(false)
                handleCancelCrop()
              }} 
              className="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-slate-500 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition cursor-pointer active:scale-98"
            >
              Hủy bỏ
            </button>
            <button 
              type="submit" 
              disabled={isUploading || !hasImage}
              className="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isUploading ? (
                <>
                  <i className="fa-solid fa-spinner animate-spin mr-2" />
                  Đang lưu...
                </>
              ) : (
                <>
                  <i className="fa-solid fa-floppy-disk mr-2" />
                  Lưu ảnh đại diện
                </>
              )}
            </button>
          </div>
        </form>
      )}
    </div>
  )
}
