'use client'

import { useState } from 'react'

interface PropertySelectOption {
  id: string
  title: string
  address: string
}

interface AiMarketingStudioProps {
  properties: PropertySelectOption[]
}

export default function AiMarketingStudio({ properties }: AiMarketingStudioProps) {
  const [selectedPropertyId, setSelectedPropertyId] = useState('mock_prop_1')
  const [feature, setFeature] = useState<'facebook' | 'tiktok' | 'seo' | 'emailsms' | 'freeform'>('facebook')
  const [goal, setGoal] = useState('rent_fast')
  const [tone, setTone] = useState('friendly')

  // Freeform data
  const [freeformTitle, setFreeformTitle] = useState('')
  const [freeformTxType, setFreeformTxType] = useState('rent')
  const [freeformPropType, setFreeformPropType] = useState('Căn hộ chung cư')
  const [freeformPrice, setFreeformPrice] = useState('')
  const [freeformArea, setFreeformArea] = useState('')
  const [freeformAddress, setFreeformAddress] = useState('')
  const [freeformHighlights, setFreeformHighlights] = useState('')

  const [isGenerating, setIsGenerating] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')
  const [aiResult, setAiResult] = useState<any>(null)

  const handleGenerate = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsGenerating(true)
    setErrorMsg('')
    setAiResult(null)

    const payload: any = {
      feature,
      property_id: selectedPropertyId,
      campaign_goal: goal,
      campaign_tone: tone
    }

    if (feature === 'freeform') {
      payload.freeform_data = {
        title: freeformTitle,
        transaction_type: freeformTxType,
        property_type: freeformPropType,
        price: freeformPrice,
        area: freeformArea,
        address: freeformAddress,
        highlights: freeformHighlights,
        tone
      }
    }

    try {
      const res = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setAiResult(data.data)
      } else {
        setErrorMsg(data.error || data.message || 'Lỗi xử lý sinh nội dung AI.')
      }
    } catch (err) {
      setErrorMsg('Lỗi kết nối mạng hoặc timeout, vui lòng thử lại.')
      console.error(err)
    } finally {
      setIsGenerating(false)
    }
  }

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text)
    alert('Đã sao chép nội dung vào bộ nhớ tạm!')
  }

  const featureLabels: Record<string, { icon: string; label: string; color: string }> = {
    facebook: { icon: 'fa-brands fa-facebook', label: 'Facebook Ads & Social', color: 'text-blue-600' },
    tiktok: { icon: 'fa-brands fa-tiktok', label: 'TikTok / Youtube Short', color: 'text-slate-800' },
    seo: { icon: 'fa-solid fa-globe', label: 'SEO Website', color: 'text-green-600' },
    emailsms: { icon: 'fa-solid fa-envelope', label: 'Email & SMS & Prompts', color: 'text-violet-600' },
    freeform: { icon: 'fa-solid fa-wand-magic-sparkles', label: 'AI Studio tự do', color: 'text-amber-600' }
  }

  return (
    <div className="space-y-8">
      {/* Page Header - consistent with other tabs */}
      <div className="pb-5 border-b border-slate-100 text-left">
        <h2 className="text-xl font-bold text-slate-800">AI Content Studio</h2>
        <p className="text-xs text-slate-400 mt-1 font-semibold">Tự động sinh bài viết Facebook, kịch bản TikTok, bài SEO, email marketing bằng Google Gemini AI.</p>
      </div>

      {/* Feature Tabs - horizontal pills */}
      <div className="flex flex-wrap gap-2">
        {Object.entries(featureLabels).map(([key, { icon, label, color }]) => (
          <button
            key={key}
            type="button"
            onClick={() => { setFeature(key as any); setAiResult(null) }}
            className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer border ${
              feature === key
                ? 'bg-primary text-white border-primary shadow-md shadow-primary/20'
                : 'bg-white text-slate-600 border-slate-200 hover:border-primary/30 hover:text-primary'
            }`}
          >
            <i className={`${icon} text-sm ${feature === key ? 'text-white' : color}`} />
            <span className="hidden sm:inline">{label}</span>
          </button>
        ))}
      </div>

      {/* Config Form */}
      <form onSubmit={handleGenerate} className="bg-slate-50/80 border border-slate-200/60 rounded-2xl p-5 sm:p-6 space-y-5">
        
        {/* Common Controls Row */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div className="space-y-1.5">
            <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Giọng điệu (Tone)</label>
            <select
              value={tone}
              onChange={(e) => setTone(e.target.value)}
              className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
            >
              <option value="friendly">Thân thiện, gần gũi</option>
              <option value="professional">Chuyên nghiệp, tin cậy</option>
              <option value="funny">Hài hước, dí dỏm, bắt trend</option>
              <option value="emotional">Gợi mở cảm xúc, chạm tâm lý</option>
            </select>
          </div>

          {feature !== 'freeform' && (
            <div className="space-y-1.5">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mục tiêu chiến dịch</label>
              <select
                value={goal}
                onChange={(e) => setGoal(e.target.value)}
                className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
              >
                <option value="rent_fast">Cho thuê / Bán nhanh trong tuần</option>
                <option value="luxury_brand">Quảng bá thương hiệu đẳng cấp</option>
                <option value="price_deal">Cắt lỗ bán gấp / Ưu đãi giảm sâu</option>
                <option value="review_detail">Review chi tiết trải nghiệm & dịch vụ</option>
              </select>
            </div>
          )}
        </div>

        {/* Dynamic Input based on Feature */}
        {feature !== 'freeform' ? (
          <div className="space-y-1.5">
            <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Chọn bất động sản</label>
            <select
              value={selectedPropertyId}
              onChange={(e) => setSelectedPropertyId(e.target.value)}
              className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
            >
              <optgroup label="Tài nguyên kiểm thử (Mock BĐS)">
                <option value="mock_prop_1">Căn hộ dịch vụ Hà Đô Centrosa Quận 10</option>
                <option value="mock_prop_2">Nhà nguyên căn Hẻm xe hơi Lê Quang Định Bình Thạnh</option>
              </optgroup>
              {properties.length > 0 && (
                <optgroup label="Bất động sản của bạn">
                  {properties.map(p => (
                    <option key={p.id} value={p.id}>{p.title}</option>
                  ))}
                </optgroup>
              )}
            </select>
          </div>
        ) : (
          /* Freeform manual data inputs */
          <div className="space-y-4 border-t border-slate-200 pt-4">
            <h4 className="text-xs font-bold text-slate-700 flex items-center gap-2 px-1">
              <i className="fa-solid fa-pen-nib text-primary text-sm" />
              Mô tả bất động sản tự do
            </h4>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5 sm:col-span-2">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Tên / Tiêu đề</label>
                <input
                  type="text"
                  value={freeformTitle}
                  onChange={(e) => setFreeformTitle(e.target.value)}
                  placeholder="Ví dụ: Biệt thự nhà vườn Đà Lạt..."
                  required
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                />
              </div>

              <div className="space-y-1.5">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Loại giao dịch</label>
                <select
                  value={freeformTxType}
                  onChange={(e) => setFreeformTxType(e.target.value)}
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                >
                  <option value="rent">Cho thuê</option>
                  <option value="sale">Bán</option>
                </select>
              </div>

              <div className="space-y-1.5">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Loại hình BĐS</label>
                <input
                  type="text"
                  value={freeformPropType}
                  onChange={(e) => setFreeformPropType(e.target.value)}
                  placeholder="Chung cư mini, Biệt thự..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                />
              </div>

              <div className="space-y-1.5">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Giá mong muốn</label>
                <input
                  type="text"
                  value={freeformPrice}
                  onChange={(e) => setFreeformPrice(e.target.value)}
                  placeholder="12 triệu/tháng, 6.5 tỷ..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                />
              </div>

              <div className="space-y-1.5">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Diện tích (m²)</label>
                <input
                  type="text"
                  value={freeformArea}
                  onChange={(e) => setFreeformArea(e.target.value)}
                  placeholder="120"
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                />
              </div>

              <div className="space-y-1.5 sm:col-span-2">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Địa điểm / Địa chỉ</label>
                <input
                  type="text"
                  value={freeformAddress}
                  onChange={(e) => setFreeformAddress(e.target.value)}
                  placeholder="Địa chỉ cụ thể..."
                  required
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                />
              </div>

              <div className="space-y-1.5 sm:col-span-2">
                <label className="block text-[10px] font-bold text-slate-500 uppercase px-1">Đặc điểm nổi bật</label>
                <textarea
                  value={freeformHighlights}
                  onChange={(e) => setFreeformHighlights(e.target.value)}
                  rows={2}
                  placeholder="Liệt kê các điểm nhấn: Hồ bơi vô cực, View đồi thông, Gần siêu thị..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none resize-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition"
                />
              </div>
            </div>
          </div>
        )}

        {/* Submit */}
        <div className="flex justify-end pt-1">
          <button
            type="submit"
            disabled={isGenerating}
            className="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-60 flex items-center gap-2"
          >
            {isGenerating ? (
              <>
                <i className="fa-solid fa-spinner animate-spin" />
                <span>AI Đang biên soạn...</span>
              </>
            ) : (
              <>
                <i className="fa-solid fa-wand-magic-sparkles" />
                <span>Tạo Content AI</span>
              </>
            )}
          </button>
        </div>
      </form>

      {/* Error */}
      {errorMsg && (
        <div className="p-4 bg-red-50 text-red-600 rounded-2xl text-xs font-bold flex items-center gap-2">
          <i className="fa-solid fa-circle-exclamation" />
          {errorMsg}
        </div>
      )}

      {/* ━━━━━━━━━━━━━━━━━ AI Result Visualizations ━━━━━━━━━━━━━━━━━ */}
      {aiResult && (
        <div className="space-y-6">
          {/* Success Banner */}
          <div className="bg-emerald-50 border border-emerald-200/60 p-4 rounded-2xl flex items-start gap-3">
            <i className="fa-solid fa-square-check text-emerald-500 text-lg mt-0.5" />
            <div>
              <h5 className="text-xs font-black text-emerald-800">Biên soạn hoàn tất!</h5>
              <p className="text-[10px] text-emerald-600 font-semibold mt-0.5">Google Gemini AI đã tạo các biến thể nội dung dưới đây.</p>
            </div>
          </div>

          {/* ─── 1. Facebook results ─── */}
          {feature === 'facebook' && Array.isArray(aiResult) && (
            <div className="space-y-4">
              <h4 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i className="fa-brands fa-facebook text-blue-600" />
                Danh sách {aiResult.length} bài đăng Facebook
              </h4>
              <div className="grid grid-cols-1 gap-4">
                {aiResult.map((post: any) => (
                  <div key={post.id} className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <span className="text-[10px] font-black bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-lg">Bài {post.id}</span>
                        <h5 className="text-xs font-bold text-slate-800">{post.title}</h5>
                      </div>
                      <button
                        onClick={() => copyToClipboard(post.content)}
                        className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary hover:text-white text-slate-500 transition rounded-lg text-[10px] font-bold cursor-pointer border border-slate-100 hover:border-primary"
                      >
                        <i className="fa-regular fa-copy" />
                        Sao chép
                      </button>
                    </div>
                    <p className="text-[11.5px] text-slate-600 leading-relaxed whitespace-pre-wrap font-medium select-all bg-slate-50/50 p-3 rounded-xl border border-slate-100/50 max-h-[200px] overflow-y-auto">
                      {post.content}
                    </p>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* ─── 2. TikTok results ─── */}
          {feature === 'tiktok' && Array.isArray(aiResult) && (
            <div className="space-y-4">
              <h4 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i className="fa-brands fa-tiktok" />
                Danh sách {aiResult.length} kịch bản video ngắn
              </h4>
              <div className="grid grid-cols-1 gap-4">
                {aiResult.map((video: any) => (
                  <div key={video.id} className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                    <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                      <div className="flex items-center gap-2">
                        <span className="text-[10px] font-black bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-lg">Kịch bản {video.id}</span>
                        <h5 className="text-xs font-bold text-slate-800">{video.title}</h5>
                      </div>
                      <button
                        onClick={() => copyToClipboard(`[Kịch bản: ${video.title}]\nVisual: ${video.visual}\nAudio: ${video.audio}\nOverlay: ${video.overlay}`)}
                        className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary hover:text-white text-slate-500 transition rounded-lg text-[10px] font-bold cursor-pointer border border-slate-100 hover:border-primary"
                      >
                        <i className="fa-regular fa-copy" />
                        Sao chép
                      </button>
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                      <div className="bg-slate-50/80 p-3 rounded-xl border border-slate-100/50">
                        <span className="block text-[9px] font-bold uppercase text-slate-400 mb-1.5">🎬 Hình ảnh (Visual)</span>
                        <p className="text-[11px] text-slate-600 leading-relaxed italic">{video.visual}</p>
                      </div>
                      <div className="bg-slate-50/80 p-3 rounded-xl border border-slate-100/50">
                        <span className="block text-[9px] font-bold uppercase text-slate-400 mb-1.5">🎤 Lời thoại (Audio)</span>
                        <p className="text-[11px] text-slate-800 leading-relaxed select-all">{video.audio}</p>
                      </div>
                      <div className="bg-slate-50/80 p-3 rounded-xl border border-slate-100/50">
                        <span className="block text-[9px] font-bold uppercase text-slate-400 mb-1.5">📝 Chữ chạy (Overlay)</span>
                        <p className="text-[11px] text-slate-600 leading-relaxed font-bold">{video.overlay}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* ─── 3. SEO Website results ─── */}
          {feature === 'seo' && Array.isArray(aiResult) && (
            <div className="space-y-4">
              <h4 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i className="fa-solid fa-globe text-green-600" />
                Danh sách {aiResult.length} bài viết SEO
              </h4>
              <div className="space-y-4">
                {aiResult.map((art: any, index: number) => (
                  <div key={index} className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
                    <div className="border-b border-slate-100 pb-3 text-left space-y-1">
                      <span className="text-[9px] font-bold text-primary uppercase">Chủ đề {index + 1}</span>
                      <h4 className="text-sm font-extrabold text-slate-800 select-all">{art.title}</h4>
                      <p className="text-[10px] text-slate-400 font-semibold select-all">Meta: {art.meta}</p>
                    </div>
                    <div 
                      className="prose prose-xs max-w-none text-slate-600 text-xs leading-relaxed border border-slate-100/50 p-4 rounded-xl bg-slate-50/50 max-h-[300px] overflow-y-auto select-all"
                      dangerouslySetInnerHTML={{ __html: art.content }}
                    />
                    <div className="flex justify-end">
                      <button
                        onClick={() => copyToClipboard(art.content)}
                        className="flex items-center gap-1.5 px-4 py-1.5 bg-slate-50 hover:bg-primary hover:text-white text-slate-500 transition rounded-lg text-[10px] font-bold cursor-pointer border border-slate-100 hover:border-primary"
                      >
                        <i className="fa-regular fa-copy" />
                        Sao chép HTML
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* ─── 4. Email, SMS & Prompts ─── */}
          {feature === 'emailsms' && (
            <div className="space-y-6">
              <h4 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i className="fa-solid fa-envelope text-violet-600" />
                Bộ Email Marketing, SMS & Image Prompts
              </h4>

              {/* Email templates */}
              <div className="space-y-3">
                <h5 className="text-xs font-black text-slate-700 uppercase px-1">Email Marketing</h5>
                <div className="grid grid-cols-1 gap-3">
                  {aiResult.emailTemplates?.map((em: any, index: number) => (
                    <div key={index} className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                      <div className="border-b border-slate-100 pb-2 flex items-center justify-between">
                        <div>
                          <span className="block text-[9px] font-bold text-slate-400 mb-0.5">Tiêu đề (Subject)</span>
                          <strong className="text-xs text-slate-800 select-all">{em.subject}</strong>
                        </div>
                        <button
                          onClick={() => copyToClipboard(`Subject: ${em.subject}\n\n${em.content}`)}
                          className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary hover:text-white text-slate-500 transition rounded-lg text-[10px] font-bold cursor-pointer border border-slate-100 hover:border-primary flex-shrink-0"
                        >
                          <i className="fa-regular fa-copy" />
                          Sao chép
                        </button>
                      </div>
                      <div className="text-[11px] text-slate-600 leading-relaxed whitespace-pre-wrap font-medium max-h-[200px] overflow-y-auto select-all bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                        {em.content}
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* SMS */}
              <div className="space-y-3">
                <h5 className="text-xs font-black text-slate-700 uppercase px-1">SMS & Zalo Templates</h5>
                <div className="space-y-2">
                  {aiResult.smsTemplates?.map((sms: string, idx: number) => (
                    <div key={idx} className="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex items-center justify-between gap-3">
                      <p className="text-[11px] text-slate-600 leading-relaxed select-all font-semibold">{sms}</p>
                      <button
                        onClick={() => copyToClipboard(sms)}
                        className="w-8 h-8 rounded-lg hover:bg-primary hover:text-white text-slate-400 border border-slate-200/50 flex-shrink-0 flex items-center justify-center transition cursor-pointer"
                        title="Sao chép"
                      >
                        <i className="fa-regular fa-copy text-xs" />
                      </button>
                    </div>
                  ))}
                </div>
              </div>

              {/* Midjourney Prompts */}
              <div className="space-y-3">
                <h5 className="text-xs font-black text-slate-700 uppercase px-1">Midjourney Prompts (3D Rendering)</h5>
                <div className="space-y-2">
                  {aiResult.prompts?.map((pmt: string, idx: number) => (
                    <div key={idx} className="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm text-left relative group">
                      <span className="absolute top-2.5 right-2.5 text-[8px] bg-slate-800 px-2 py-0.5 rounded-md text-slate-400 font-bold select-none">IMAGE PROMPT</span>
                      <p className="text-[11px] text-slate-300 leading-relaxed select-all font-semibold italic pr-20">{pmt}</p>
                      <div className="flex justify-end pt-3">
                        <button
                          onClick={() => copyToClipboard(pmt)}
                          className="px-3 py-1.5 bg-slate-800 hover:bg-primary text-white transition rounded-lg text-[10px] font-bold flex items-center gap-1.5 cursor-pointer"
                        >
                          <i className="fa-regular fa-copy" />
                          Copy prompt
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* ─── 5. Freeform unified content package ─── */}
          {feature === 'freeform' && (
            <div className="space-y-6">
              <h4 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i className="fa-solid fa-wand-magic-sparkles text-amber-600" />
                Gói Nội Dung Truyền Thông AI Studio
              </h4>
              
              {/* Social Posts */}
              <div className="space-y-3">
                <h5 className="text-[11px] font-black uppercase text-primary tracking-wide flex items-center gap-2">
                  <i className="fa-brands fa-facebook text-blue-600" />
                  1. Facebook & Zalo Posts
                </h5>
                <div className="grid grid-cols-1 gap-3">
                  {aiResult.posts?.map((p: any) => (
                    <div key={p.id} className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm space-y-2">
                      <div className="flex items-center justify-between border-b border-slate-100 pb-2">
                        <strong className="text-xs text-slate-800">{p.title}</strong>
                        <button onClick={() => copyToClipboard(p.content)} className="text-slate-400 hover:text-primary transition cursor-pointer"><i className="fa-regular fa-copy text-xs" /></button>
                      </div>
                      <p className="text-[11px] text-slate-600 leading-relaxed whitespace-pre-wrap font-medium select-all">{p.content}</p>
                    </div>
                  ))}
                </div>
              </div>

              {/* TikTok scripts */}
              <div className="space-y-3">
                <h5 className="text-[11px] font-black uppercase text-primary tracking-wide flex items-center gap-2">
                  <i className="fa-brands fa-tiktok" />
                  2. Video Scripts (TikTok)
                </h5>
                <div className="grid grid-cols-1 gap-3">
                  {aiResult.videos?.map((v: any) => (
                    <div key={v.id} className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm space-y-2 text-left">
                      <div className="flex items-center justify-between border-b border-slate-100 pb-2">
                        <strong className="text-xs text-slate-800">{v.title}</strong>
                        <button onClick={() => copyToClipboard(`TikTok Script: ${v.title}\nVisual: ${v.visual}\nAudio: ${v.audio}\nOverlay: ${v.overlay}`)} className="text-slate-400 hover:text-primary transition cursor-pointer"><i className="fa-regular fa-copy text-xs" /></button>
                      </div>
                      <div className="text-[10px] space-y-1.5 font-semibold">
                        <div><span className="text-slate-400">🎬 Hình ảnh:</span> <span className="italic text-slate-600">{v.visual}</span></div>
                        <div><span className="text-slate-400">🎤 Lời thoại:</span> <span className="text-slate-800 select-all">{v.audio}</span></div>
                        <div><span className="text-slate-400">📝 Chữ chạy:</span> <span className="font-bold text-slate-700">{v.overlay}</span></div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Voiceover script */}
              <div className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                <h5 className="text-[11px] font-black uppercase text-primary tracking-wide flex items-center gap-2">
                  <i className="fa-solid fa-microphone" />
                  3. Kịch bản lời thoại thu âm
                </h5>
                <p className="text-[11px] text-slate-600 leading-relaxed select-all font-semibold italic bg-slate-50/50 p-4 rounded-xl border border-slate-100/50">
                  {aiResult.voice_script}
                </p>
                <button
                  onClick={() => copyToClipboard(aiResult.voice_script)}
                  className="w-full py-2 bg-slate-50 hover:bg-primary hover:text-white transition rounded-xl text-[10px] font-bold text-slate-600 flex items-center justify-center gap-1.5 cursor-pointer border border-slate-100 hover:border-primary"
                >
                  <i className="fa-regular fa-copy" />
                  Sao chép lời thoại
                </button>
              </div>

              {/* Midjourney thumbnail prompt */}
              <div className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm text-left space-y-3">
                <h5 className="text-[11px] font-black uppercase text-indigo-400 tracking-wide flex items-center gap-2">
                  <i className="fa-solid fa-image text-indigo-400" />
                  4. Midjourney Prompt (Vẽ ảnh Thumbnail)
                </h5>
                <p className="text-[10px] text-slate-300 leading-relaxed select-all font-semibold italic bg-slate-800/50 p-4 rounded-xl border border-slate-800">
                  {aiResult.thumbnail_prompt}
                </p>
                <button
                  onClick={() => copyToClipboard(aiResult.thumbnail_prompt)}
                  className="w-full py-2 bg-slate-800 hover:bg-primary text-slate-400 hover:text-white transition rounded-xl text-[10px] font-bold flex items-center justify-center gap-1.5 cursor-pointer"
                >
                  <i className="fa-regular fa-copy" />
                  Sao chép Image Prompt
                </button>
              </div>

              {/* SEO Website */}
              {aiResult.seo && (
                <div className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
                  <h5 className="text-[11px] font-black uppercase text-primary tracking-wide flex items-center gap-2">
                    <i className="fa-solid fa-globe text-green-600" />
                    5. Bài viết chuẩn SEO Website
                  </h5>
                  <div className="border-b border-slate-100 pb-2">
                    <strong className="text-xs text-slate-800 select-all">{aiResult.seo.title}</strong>
                    <div className="text-[10px] text-slate-400 font-semibold select-all mt-0.5">Meta: {aiResult.seo.meta}</div>
                  </div>
                  <div 
                    className="prose prose-xs max-w-none text-slate-600 text-xs leading-relaxed border border-slate-100/50 p-4 rounded-xl bg-slate-50/50 max-h-[250px] overflow-y-auto select-all"
                    dangerouslySetInnerHTML={{ __html: aiResult.seo.content }}
                  />
                  <div className="flex flex-wrap items-center gap-1.5">
                    <span className="text-[9px] font-bold text-slate-400">Keywords:</span>
                    {aiResult.seo.keywords?.map((kw: string, i: number) => (
                      <span key={i} className="inline-flex px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[9px] font-bold">{kw}</span>
                    ))}
                  </div>
                  <div className="flex justify-end pt-2 border-t border-slate-100/60">
                    <button
                      onClick={() => copyToClipboard(aiResult.seo.content)}
                      className="flex items-center gap-1.5 px-4 py-1.5 bg-slate-50 hover:bg-primary hover:text-white text-slate-500 transition rounded-lg text-[10px] font-bold cursor-pointer border border-slate-100 hover:border-primary"
                    >
                      <i className="fa-regular fa-copy" />
                      Sao chép HTML
                    </button>
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      )}
    </div>
  )
}
