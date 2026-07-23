'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

interface Property {
  id: string
  title: string
  address: string
}

interface Campaign {
  id: string
  title: string
  type: string
  goal: string | null
  tone: string
  content: string
  createdAt: string
}

export default function AiMarketingPage() {
  const [properties, setProperties] = useState<Property[]>([])
  const [campaigns, setCampaigns] = useState<Campaign[]>([])
  const [loading, setLoading] = useState(true)
  
  const [selectedPropertyId, setSelectedPropertyId] = useState('')
  const [campaignType, setCampaignType] = useState('zalo') // zalo | sms | facebook
  const [title, setTitle] = useState('')
  const [goal, setGoal] = useState('rent_fast')
  const [tone, setTone] = useState('friendly')
  const [content, setContent] = useState('')
  
  const [saving, setSaving] = useState(false)
  const [message, setMessage] = useState('')
  const [error, setError] = useState('')

  useEffect(() => {
    async function loadInitialData() {
      try {
        // Fetch properties
        const profileRes = await fetch('/api/profile')
        if (profileRes.ok) {
          const profileJson = await profileRes.json()
          if (profileJson?.success && profileJson?.data) {
            const list = profileJson.data.properties || []
            setProperties(list)
            if (list.length > 0) {
              setSelectedPropertyId(list[0].id)
            }
          }
        }

        // Fetch campaign history
        const campaignsRes = await fetch('/api/marketing/campaigns')
        if (campaignsRes.ok) {
          const campaignsJson = await campaignsRes.json()
          if (campaignsJson?.success) {
            setCampaigns(campaignsJson.campaigns || [])
          }
        }
      } catch (err) {
        console.error('Failed to load initial data:', err)
      } finally {
        setLoading(false)
      }
    }
    loadInitialData()
  }, [])

  const handleSaveCampaign = async () => {
    if (!title.trim() || !content.trim()) {
      setError('Vui lòng nhập đầy đủ tiêu đề và nội dung chiến dịch.')
      return
    }

    setSaving(true)
    setMessage('')
    setError('')
    try {
      const payload = {
        type: campaignType,
        property_id: selectedPropertyId || null,
        title,
        goal,
        tone,
        content
      }

      const res = await fetch('/api/marketing/campaigns', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })

      const data = await res.json()
      if (res.ok && data?.success) {
        setMessage('Lưu chiến dịch thành công!')
        // Refresh history list
        setCampaigns([data.campaign, ...campaigns])
        // Reset form
        setTitle('')
        setContent('')
      } else {
        setError(data.error || 'Lỗi khi lưu chiến dịch.')
      }
    } catch (err) {
      console.error(err)
      setError('Lỗi kết nối tới máy chủ.')
    } finally {
      setSaving(false)
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
          <i className="fa-solid fa-bolt text-primary"></i> AI Marketing Campaigns
        </h1>
        <p className="text-sm text-slate-500 mt-1">
          Tạo và quản lý các chiến dịch quảng cáo, tiếp cận khách hàng qua tin nhắn Zalo, SMS hoặc mạng xã hội.
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Left Column: Create Campaign Form */}
        <div className="lg:col-span-6 bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
          <h2 className="text-sm font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-gray-800 pb-2">
            Tạo chiến dịch mới
          </h2>

          {message && (
            <div className="bg-green-50 dark:bg-green-950/20 text-green-600 dark:text-green-500 p-3 rounded-xl border border-green-100 dark:border-green-900/30 text-xs">
              {message}
            </div>
          )}

          {error && (
            <div className="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-500 p-3 rounded-xl border border-red-100 dark:border-red-900/30 text-xs">
              {error}
            </div>
          )}

          {/* Campaign Type */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
              Hình thức chiến dịch
            </label>
            <div className="grid grid-cols-3 gap-2">
              {[
                { id: 'zalo', label: 'Zalo OA', icon: 'fa-regular fa-comment-dots' },
                { id: 'sms', label: 'SMS Brand', icon: 'fa-solid fa-mobile-screen' },
                { id: 'facebook', label: 'Social Ads', icon: 'fa-brands fa-facebook-f' },
              ].map((opt) => (
                <button
                  key={opt.id}
                  onClick={() => setCampaignType(opt.id)}
                  type="button"
                  className={`flex flex-col items-center justify-center gap-1.5 py-3 rounded-xl border text-xs font-bold transition cursor-pointer ${
                    campaignType === opt.id
                      ? 'border-primary bg-primary/5 text-primary'
                      : 'border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-gray-800 text-slate-600 dark:text-slate-400'
                  }`}
                >
                  <i className={`${opt.icon} text-base`}></i>
                  {opt.label}
                </button>
              ))}
            </div>
          </div>

          {/* Campaign Title */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">
              Tên chiến dịch
            </label>
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="Ví dụ: Chiến dịch tri ân khách thuê tháng 7 Quận 10"
              className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2.5 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white"
            />
          </div>

          {/* Select Property */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">
              Liên kết tin đăng (Không bắt buộc)
            </label>
            <select
              value={selectedPropertyId}
              onChange={(e) => setSelectedPropertyId(e.target.value)}
              className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2.5 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white"
            >
              <option value="">-- Chọn tin đăng liên kết --</option>
              {properties.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.title}
                </option>
              ))}
            </select>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">
                Mục tiêu
              </label>
              <select
                value={goal}
                onChange={(e) => setGoal(e.target.value)}
                className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white"
              >
                <option value="rent_fast">Cho thuê nhanh</option>
                <option value="luxury_brand">Quảng bá thương hiệu</option>
                <option value="price_deal">Ưu đãi / Giảm giá sâu</option>
                <option value="review_detail">Review chi tiết</option>
              </select>
            </div>
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">
                Tông giọng
              </label>
              <select
                value={tone}
                onChange={(e) => setTone(e.target.value)}
                className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white"
              >
                <option value="friendly">Thân thiện, gần gũi</option>
                <option value="professional">Chuyên nghiệp, tin cậy</option>
                <option value="funny">Hài hước, dí dỏm</option>
                <option value="emotional">Gợi mở cảm xúc</option>
              </select>
            </div>
          </div>

          {/* Campaign Content */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">
              Nội dung truyền thông chiến dịch
            </label>
            <textarea
              value={content}
              onChange={(e) => setContent(e.target.value)}
              placeholder="Nhập nội dung tin nhắn, kịch bản hoặc thông tin chiến dịch đã chuẩn bị..."
              rows={4}
              className="w-full text-sm border border-slate-200 dark:border-gray-800 rounded-xl px-3 py-2.5 bg-slate-50 dark:bg-gray-800 focus:outline-none focus:border-primary text-slate-800 dark:text-white resize-none"
            ></textarea>
          </div>

          <button
            onClick={handleSaveCampaign}
            disabled={saving}
            type="button"
            className="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 disabled:bg-slate-250 text-white font-bold py-2.5 px-4 rounded-xl cursor-pointer transition shadow-md shadow-primary/20 text-sm"
          >
            {saving ? (
              <>
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Đang lưu trữ...
              </>
            ) : (
              <>
                <i className="fa-regular fa-paper-plane"></i> Lưu & Kích hoạt chiến dịch
              </>
            )}
          </button>
        </div>

        {/* Right Column: Campaign History */}
        <div className="lg:col-span-6 bg-white dark:bg-gray-900 border border-slate-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
          <h2 className="text-sm font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-gray-800 pb-2">
            Lịch sử chiến dịch của bạn ({campaigns.length})
          </h2>

          {campaigns.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-slate-655 text-center">
              <i className="fa-solid fa-list-check text-4xl mb-3 text-slate-200 dark:text-gray-800"></i>
              <p className="text-xs font-semibold">Chưa có chiến dịch nào được lưu.</p>
              <p className="text-[10px] text-slate-400 mt-0.5">Các chiến dịch mới tạo sẽ lưu lịch sử trực tiếp tại đây.</p>
            </div>
          ) : (
            <div className="space-y-4 max-h-[500px] overflow-y-auto pr-1">
              {campaigns.map((c) => (
                <div
                  key={c.id}
                  className="p-4 border border-slate-100 dark:border-gray-800 rounded-xl hover:border-slate-200 dark:hover:border-gray-700 transition space-y-2 bg-slate-50/50 dark:bg-gray-900/50"
                >
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-bold text-slate-800 dark:text-white truncate max-w-[200px]">
                      {c.title}
                    </span>
                    <span className={`text-[10px] px-2 py-0.5 rounded font-bold uppercase ${
                      c.type === 'zalo' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600' :
                      c.type === 'sms' ? 'bg-orange-50 dark:bg-orange-950/20 text-orange-600' :
                      'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600'
                    }`}>
                      {c.type === 'zalo' ? 'Zalo OA' : c.type === 'sms' ? 'SMS' : 'Facebook'}
                    </span>
                  </div>

                  <div className="flex items-center gap-4 text-[10px] text-slate-400">
                    {c.goal && (
                      <span>
                        <i className="fa-solid fa-bullseye mr-1"></i> {c.goal}
                      </span>
                    )}
                    <span>
                      <i className="fa-regular fa-comment mr-1"></i> {c.tone}
                    </span>
                    <span>
                      <i className="fa-regular fa-clock mr-1"></i> {new Date(c.createdAt).toLocaleDateString('vi-VN')}
                    </span>
                  </div>

                  <div className="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 bg-white dark:bg-gray-850 p-2.5 rounded-lg border border-slate-100/50 dark:border-gray-800/50">
                    {c.content}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
