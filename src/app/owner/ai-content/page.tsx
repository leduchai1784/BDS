'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

interface Property {
  id: string
  title: string
  address: string
  price: number
}

export default function AiContentPage() {
  const [properties, setProperties] = useState<Property[]>([])
  const [loading, setLoading] = useState(true)
  const [selectedPropertyId, setSelectedPropertyId] = useState('')
  const [feature, setFeature] = useState('facebook') // facebook | tiktok | email | freeform
  const [campaignGoal, setCampaignGoal] = useState('rent_fast')
  const [campaignTone, setCampaignTone] = useState('friendly')
  const [freeformData, setFreeformData] = useState('')
  const [generating, setGenerating] = useState(false)
  const [result, setResult] = useState('')
  const [copied, setCopied] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    async function loadProperties() {
      try {
        const res = await fetch('/api/profile')
        if (res.ok) {
          const json = await res.json()
          if (json?.success && json?.data) {
            const list = json.data.properties || []
            setProperties(list)
            if (list.length > 0) {
              setSelectedPropertyId(list[0].id)
            }
          }
        }
      } catch (err) {
        console.error('Failed to load properties:', err)
      } finally {
        setLoading(false)
      }
    }
    loadProperties()
  }, [])

  const handleGenerate = async () => {
    setGenerating(true)
    setResult('')
    setError('')
    try {
      const payload = {
        feature,
        property_id: feature === 'freeform' ? undefined : selectedPropertyId,
        campaign_goal: feature === 'freeform' ? undefined : campaignGoal,
        campaign_tone: campaignTone,
        freeform_data: feature === 'freeform' ? freeformData : undefined,
      }

      const res = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      })

      const data = await res.json()
      if (res.ok && data?.success) {
        setResult(data.content || data.result || '')
      } else {
        setError(data.error || 'Có lỗi xảy ra khi tạo nội dung.')
      }
    } catch (err) {
      console.error(err)
      setError('Lỗi kết nối tới server.')
    } finally {
      setGenerating(false)
    }
  }

  const handleCopy = () => {
    if (result) {
      navigator.clipboard.writeText(result)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    }
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[50vh]">
        <div className="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      {/* Page Header */}
      <div>
        <h1 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-2">
          <i className="fa-regular fa-lightbulb text-primary"></i> AI Content Studio
        </h1>
        <p className="text-sm text-slate-500 mt-1">
          Tự động tạo mô tả bài viết mạng xã hội, kịch bản video hoặc email gửi khách dựa trên tin đăng bất động sản.
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Left Column: Form Controls */}
        <div className="lg:col-span-5 bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-5">
          {/* Feature Selection */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
              Kênh xuất bản
            </label>
            <div className="grid grid-cols-2 gap-2">
              {[
                { id: 'facebook', label: 'Bài viết Facebook', icon: 'fa-brands fa-facebook' },
                { id: 'tiktok', label: 'Kịch bản TikTok', icon: 'fa-brands fa-tiktok' },
                { id: 'email', label: 'Email gửi khách', icon: 'fa-regular fa-envelope' },
                { id: 'freeform', label: 'Viết tự do', icon: 'fa-solid fa-pen-nib' },
              ].map((opt) => (
                <button
                  key={opt.id}
                  onClick={() => setFeature(opt.id)}
                  type="button"
                  className={`flex items-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-semibold transition cursor-pointer ${
                    feature === opt.id
                      ? 'border-primary bg-primary/5 text-primary'
                      : 'border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-gray-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'
                  }`}
                >
                  <i className={`${opt.icon} text-sm`}></i>
                  {opt.label}
                </button>
              ))}
            </div>
          </div>

          {/* Conditional inputs */}
          {feature !== 'freeform' ? (
            <>
              {/* Select Property */}
              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
                  Chọn tin đăng bất động sản
                </label>
                {properties.length === 0 ? (
                  <div className="text-xs text-amber-600 dark:text-amber-500 bg-amber-50 dark:bg-amber-950/20 px-3 py-2.5 rounded-xl border border-amber-100 dark:border-amber-900/30">
                    Bạn chưa đăng tin bất động sản nào. <Link href="/property/create" className="underline font-bold">Đăng tin ngay</Link>
                  </div>
                ) : (
                  <select
                    value={selectedPropertyId}
                    onChange={(e) => setSelectedPropertyId(e.target.value)}
                    className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2.5 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white"
                  >
                    {properties.map((p) => (
                      <option key={p.id} value={p.id}>
                        {p.title}
                      </option>
                    ))}
                  </select>
                )}
              </div>

              {/* Goal selection */}
              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
                  Mục tiêu nội dung
                </label>
                <select
                  value={campaignGoal}
                  onChange={(e) => setCampaignGoal(e.target.value)}
                  className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2.5 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white"
                >
                  <option value="rent_fast">Cho thuê nhanh / Kêu gọi đặt hẹn</option>
                  <option value="luxury_brand">Quảng bá căn hộ dịch vụ cao cấp</option>
                  <option value="price_deal">Cắt lỗ gấp / Ưu đãi thanh toán tốt</option>
                  <option value="review_detail">Mô tả phòng chi tiết từ A-Z</option>
                </select>
              </div>
            </>
          ) : (
            /* Freeform prompt input */
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
                Yêu cầu viết tự do (Prompt)
              </label>
              <textarea
                value={freeformData}
                onChange={(e) => setFreeformData(e.target.value)}
                placeholder="Ví dụ: Viết một bài viết đăng Facebook hài hước để cho thuê phòng trọ Quận 10 giá rẻ, giờ giấc tự do..."
                rows={5}
                className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2.5 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white resize-none"
              ></textarea>
            </div>
          )}

          {/* Tone selection */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
              Tông giọng
            </label>
            <div className="grid grid-cols-2 gap-2">
              {[
                { id: 'friendly', label: 'Thân thiện, gần gũi' },
                { id: 'professional', label: 'Chuyên nghiệp, tin cậy' },
                { id: 'funny', label: 'Hài hước, dí dỏm' },
                { id: 'emotional', label: 'Cảm xúc, lôi cuốn' },
              ].map((tone) => (
                <button
                  key={tone.id}
                  onClick={() => setCampaignTone(tone.id)}
                  type="button"
                  className={`px-3 py-2 rounded-lg border text-xs font-medium transition cursor-pointer text-center ${
                    campaignTone === tone.id
                      ? 'border-primary bg-primary/5 text-primary'
                      : 'border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-gray-800 text-slate-600 dark:text-slate-400'
                  }`}
                >
                  {tone.label}
                </button>
              ))}
            </div>
          </div>

          <button
            onClick={handleGenerate}
            disabled={generating || (feature !== 'freeform' && properties.length === 0)}
            type="button"
            className="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 disabled:bg-slate-250 text-white font-bold py-3 px-4 rounded-xl cursor-pointer transition shadow-md shadow-primary/20"
          >
            {generating ? (
              <>
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Đang xử lý bằng AI...
              </>
            ) : (
              <>
                <i className="fa-solid fa-wand-magic-sparkles"></i> Sinh bài viết AI
              </>
            )}
          </button>
        </div>

        {/* Right Column: AI Output Results */}
        <div className="lg:col-span-7 bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm flex flex-col min-h-[500px]">
          <div className="flex items-center justify-between border-b border-slate-100 dark:border-gray-800 pb-3 mb-4">
            <span className="text-sm font-bold text-slate-800 dark:text-white">Nội dung đề xuất từ AI</span>
            {result && (
              <button
                onClick={handleCopy}
                type="button"
                className="flex items-center gap-1.5 text-xs text-primary font-bold bg-primary/5 hover:bg-primary/10 px-3 py-1.5 rounded-lg transition cursor-pointer"
              >
                {copied ? (
                  <>
                    <i className="fa-solid fa-check text-green-500"></i> Đã sao chép
                  </>
                ) : (
                  <>
                    <i className="fa-regular fa-copy"></i> Sao chép
                  </>
                )}
              </button>
            )}
          </div>

          {error && (
            <div className="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-500 p-4 rounded-xl border border-red-100 dark:border-red-900/30 text-sm mb-4">
              {error}
            </div>
          )}

          {result ? (
            <div className="flex-1 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-sans overflow-y-auto max-h-[500px] pr-2">
              {result}
            </div>
          ) : (
            <div className="flex-1 flex flex-col items-center justify-center text-slate-400 dark:text-slate-655 text-center p-8">
              <i className="fa-solid fa-brain text-5xl mb-4 text-slate-200 dark:text-gray-800 animate-pulse"></i>
              <p className="text-sm font-medium">Bấm &quot;Sinh bài viết AI&quot; để tạo bài đăng hấp dẫn.</p>
              <p className="text-xs text-slate-400 mt-1 max-w-xs">Nội dung sẽ được sinh tự động bởi trí tuệ nhân tạo và hiển thị trực tiếp tại đây.</p>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
