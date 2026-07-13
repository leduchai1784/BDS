'use client'

import { useState } from 'react'
import { useSession } from 'next-auth/react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'

interface BookingFormProps {
  propertyId: string
  propertyTitle: string
  propertyOwnerId: number
  agentName: string
}

export default function BookingForm({
  propertyId,
  propertyTitle,
  propertyOwnerId,
  agentName
}: BookingFormProps) {
  const { data: session, status } = useSession()
  const user = session?.user as any
  const router = useRouter()

  // Form states
  const [name, setName] = useState(user?.name || '')
  const [phone, setPhone] = useState(user?.phone || '')
  const [email, setEmail] = useState(user?.email || '')
  
  const todayString = new Date().toLocaleDateString('en-CA') // YYYY-MM-DD format
  const [date, setDate] = useState('')
  const [time, setTime] = useState('')
  const [message, setMessage] = useState('')

  const [isTimeDropdownOpen, setIsTimeDropdownOpen] = useState(false)
  const [isProcessing, setIsProcessing] = useState(false)
  const [submitted, setSubmitted] = useState(false)
  const [errorMessage, setErrorMessage] = useState('')

  const timeSlots = [
    '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', 
    '11:00', '11:30', '13:30', '14:00', '14:30', '15:00', 
    '15:30', '16:00', '16:30', '17:00', '17:30'
  ]

  const handleReset = () => {
    setDate('')
    setTime('')
    setMessage('')
    setErrorMessage('')
    setName(user?.name || '')
    setPhone(user?.phone || '')
    setEmail(user?.email || '')
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (status !== 'authenticated') {
      router.push(`/login?callbackUrl=${window.location.pathname}`)
      return
    }

    if (session?.user?.id && Number(session.user.id) === propertyOwnerId) {
      alert('Bạn không thể tự đặt lịch xem nhà trên tin đăng của chính mình.')
      return
    }

    if (!date) {
      setErrorMessage('Vui lòng chọn ngày hẹn.')
      return
    }
    if (!time) {
      setErrorMessage('Vui lòng chọn giờ hẹn.')
      return
    }

    setIsProcessing(true)
    setErrorMessage('')

    try {
      const res = await fetch('/api/appointments', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          property_id: propertyId,
          name,
          phone,
          email,
          date,
          time,
          message
        })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setSubmitted(true)
      } else {
        setErrorMessage(data.message || 'Có lỗi xảy ra, vui lòng thử lại.')
      }
    } catch (err) {
      setErrorMessage('Lỗi kết nối mạng, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsProcessing(false)
    }
  }

  return (
    <div className="border-t border-slate-100 pt-5">
      <div className="flex justify-between items-center mb-4">
        <h5 className="text-sm font-bold text-slate-800 flex items-center gap-2">
          <i className="fa-solid fa-calendar-days text-primary"></i>
          <span>Đặt lịch xem nhà</span>
        </h5>
        <button 
          type="button"
          onClick={handleReset}
          className="text-[10px] font-bold text-slate-400 hover:text-primary transition cursor-pointer flex items-center gap-1 focus:outline-none"
        >
          <i className="fa-solid fa-arrow-rotate-left"></i>
          <span>Đặt lại</span>
        </button>
      </div>

      {/* Success State */}
      {submitted ? (
        <div className="bg-green-50 border border-green-150 rounded-2xl p-4 text-center">
          <i className="fa-solid fa-circle-check text-green-500 text-2xl mb-2"></i>
          <h6 className="text-xs font-bold text-green-800 mb-1">Gửi lịch hẹn thành công!</h6>
          <p className="text-[10px] text-green-600 leading-normal font-medium">
            Môi giới <span className="font-bold">{agentName}</span> sẽ liên hệ lại qua SĐT của bạn trong ít phút để xác nhận.
          </p>
          <div className="flex items-center justify-center gap-2 mt-4">
            <Link 
              href="/profile?tab=appointments" 
              className="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg shadow-sm hover:bg-primary-hover transition cursor-pointer"
            >
              <i className="fa-regular fa-calendar-check mr-1"></i> Xem lịch hẹn
            </Link>
            <button 
              onClick={() => setSubmitted(false)} 
              className="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 text-[10px] font-bold rounded-lg text-slate-650 hover:bg-slate-50 transition cursor-pointer"
            >
              Đặt lịch hẹn khác
            </button>
          </div>
        </div>
      ) : (
        /* Form State */
        <form onSubmit={handleSubmit} className="space-y-3.5">
          {errorMessage && (
            <div className="p-3 bg-red-50 text-red-500 rounded-xl text-[11px] font-bold text-left">
              <i className="fa-solid fa-circle-exclamation mr-1"></i>
              <span>{errorMessage}</span>
            </div>
          )}

          {/* Input Name */}
          <div>
            <input 
              type="text" 
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="Họ và tên của bạn..." 
              required
              className="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition"
            />
          </div>
          
          {/* Input Phone */}
          <div>
            <input 
              type="tel" 
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
              placeholder="Số điện thoại liên hệ..." 
              required
              className="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition"
            />
          </div>

          {/* Input Email */}
          <div>
            <input 
              type="email" 
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="Địa chỉ email liên hệ..." 
              required
              className="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition"
            />
          </div>

          {/* Date and Time Picker */}
          <div className="grid grid-cols-2 gap-2 text-left">
            <div>
              <input 
                type="date" 
                value={date}
                onChange={(e) => setDate(e.target.value)}
                required
                min={todayString}
                className="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-xs font-medium outline-none transition cursor-pointer"
              />
            </div>
            
            <div className="relative" onMouseLeave={() => setIsTimeDropdownOpen(false)}>
              <button 
                type="button"
                onClick={() => setIsTimeDropdownOpen(!isTimeDropdownOpen)}
                className={`w-full border rounded-xl px-3 py-2.5 text-xs font-semibold outline-none transition cursor-pointer text-left flex items-center justify-between ${
                  isTimeDropdownOpen ? 'border-primary bg-white ring-2 ring-primary/10' : 'border-slate-200 bg-slate-50 hover:bg-slate-100/70'
                }`}
              >
                <div className="flex items-center gap-1.5">
                  <i className={`fa-regular fa-clock text-[11px] ${time ? 'text-slate-650' : 'text-slate-450'}`} />
                  <span className={time ? 'text-slate-800' : 'text-slate-400'}>{time || 'Chọn giờ'}</span>
                </div>
                <i className={`fa-solid fa-chevron-down text-slate-400 text-[10px] transition duration-200 ${isTimeDropdownOpen ? 'text-primary rotate-180' : ''}`} />
              </button>
              
              {/* Dropdown Panel */}
              {isTimeDropdownOpen && (
                <div className="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-150 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.08)] z-50 p-1 max-h-[180px] overflow-y-auto">
                  <div className="space-y-0.5">
                    {timeSlots.map(t => (
                      <button 
                        key={t}
                        type="button"
                        onClick={() => { setTime(t); setIsTimeDropdownOpen(false) }}
                        className={`w-full text-left px-3 py-2 text-[11px] rounded-lg transition cursor-pointer flex items-center justify-between ${
                          time === t ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-semibold'
                        }`}
                      >
                        <span>{t}</span>
                        {time === t && <i className="fa-solid fa-check text-primary text-[10px]" />}
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Note / Message */}
          <div>
            <textarea 
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              placeholder="Ghi chú thêm cho chủ nhà (nếu có)..." 
              rows={2}
              maxLength={1000}
              className="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2 text-xs font-medium outline-none transition resize-none"
            />
          </div>

          {/* Form Submit Button */}
          <button 
            type="submit" 
            disabled={isProcessing}
            className="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-3.5 px-4 rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 disabled:opacity-55"
          >
            {isProcessing ? (
              <span><i className="fa-solid fa-spinner animate-spin mr-1"></i> Đang xử lý...</span>
            ) : (
              <span>Gửi yêu cầu đặt lịch</span>
            )}
          </button>
        </form>
      )}
    </div>
  )
}
