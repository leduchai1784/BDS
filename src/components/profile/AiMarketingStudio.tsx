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

  return (
    <div className="space-y-6 text-left">
      <div>
        <h3 className="text-base font-black text-slate-800">AI Content Studio</h3>
        <p className="text-[11px] text-slate-500 font-semibold">Tự động sinh bộ nhận diện, bài viết Facebook, kịch bản TikTok, bài SEO chuẩn chỉnh bằng Google Gemini AI.</p>
      </div>

      <form onSubmit={handleGenerate} className="bg-slate-50 border border-slate-200/60 rounded-3xl p-5 sm:p-6 space-y-5">
        {/* Selector Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          {/* Feature Selector */}
          <div className="space-y-1">
            <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Chọn kênh phân phối</label>
            <select
              value={feature}
              onChange={(e) => {
                setFeature(e.target.value as any)
                setAiResult(null)
              }}
              className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer"
            >
              <option value="facebook">Facebook Ads & Social Posts (20 bài)</option>
              <option value="tiktok">Kịch bản video TikTok/Youtube Short (10 kịch bản)</option>
              <option value="seo">Bài viết chuẩn SEO Website (5 bài)</option>
              <option value="emailsms">Bộ Email Marketing & SMS & Image Prompts</option>
              <option value="freeform">AI Content Studio tự do (Tạo trọn bộ từ mô tả tự do)</option>
            </select>
          </div>

          {/* Tone Selector */}
          <div className="space-y-1">
            <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giọng điệu (Tone)</label>
            <select
              value={tone}
              onChange={(e) => setTone(e.target.value)}
              className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer"
            >
              <option value="friendly">Thân thiện, gần gũi</option>
              <option value="professional">Chuyên nghiệp, tin cậy</option>
              <option value="funny">Hài hước, dí dỏm, bắt trend</option>
              <option value="emotional">Gợi mở cảm xúc, chạm tâm lý</option>
            </select>
          </div>
        </div>

        {/* Dynamic Input based on Feature */}
        {feature !== 'freeform' ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {/* Property Selector */}
            <div className="space-y-1">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Chọn bất động sản</label>
              <select
                value={selectedPropertyId}
                onChange={(e) => setSelectedPropertyId(e.target.value)}
                className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer"
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

            {/* Campaign Goal */}
            <div className="space-y-1">
              <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mục tiêu chiến dịch</label>
              <select
                value={goal}
                onChange={(e) => setGoal(e.target.value)}
                className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer"
              >
                <option value="rent_fast">Cho thuê / Bán nhanh trong tuần</option>
                <option value="luxury_brand">Quảng bá thương hiệu đẳng cấp cao cấp</option>
                <option value="price_deal">Cắt lỗ bán gấp / Ưu đãi giảm sâu</option>
                <option value="review_detail">Review chi tiết trải nghiệm & dịch vụ</option>
              </select>
            </div>
          </div>
        ) : (
          /* Freeform manual data inputs */
          <div className="space-y-4 border-t border-slate-200 pt-4">
            <h4 className="text-xs font-bold text-slate-800 px-1">Mô tả bất động sản tự do</h4>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="space-y-1 md:col-span-2">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Tên/Tiêu đề</label>
                <input
                  type="text"
                  value={freeformTitle}
                  onChange={(e) => setFreeformTitle(e.target.value)}
                  placeholder="Ví dụ: Biệt thự nhà vườn Đà Lạt..."
                  required
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                />
              </div>

              <div className="space-y-1">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Loại giao dịch</label>
                <select
                  value={freeformTxType}
                  onChange={(e) => setFreeformTxType(e.target.value)}
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none cursor-pointer"
                >
                  <option value="rent">Cho thuê</option>
                  <option value="sale">Bán</option>
                </select>
              </div>

              <div className="space-y-1">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Loại hình</label>
                <input
                  type="text"
                  value={freeformPropType}
                  onChange={(e) => setFreeformPropType(e.target.value)}
                  placeholder="Ví dụ: Chung cư mini, Biệt thự..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                />
              </div>

              <div className="space-y-1">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Giá mong muốn</label>
                <input
                  type="text"
                  value={freeformPrice}
                  onChange={(e) => setFreeformPrice(e.target.value)}
                  placeholder="Ví dụ: 12 triệu/tháng, 6.5 tỷ..."
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                />
              </div>

              <div className="space-y-1">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Diện tích (m²)</label>
                <input
                  type="text"
                  value={freeformArea}
                  onChange={(e) => setFreeformArea(e.target.value)}
                  placeholder="Ví dụ: 120"
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                />
              </div>

              <div className="space-y-1 md:col-span-3">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Địa điểm / Địa chỉ</label>
                <input
                  type="text"
                  value={freeformAddress}
                  onChange={(e) => setFreeformAddress(e.target.value)}
                  placeholder="Địa chỉ cụ thể..."
                  required
                  className="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none"
                />
              </div>

              <div className="space-y-1 md:col-span-3">
                <label className="block text-[9px] font-bold text-slate-450 uppercase mb-1 px-1">Đặc điểm nổi bật (Highlights)</label>
                <textarea
                  value={freeformHighlights}
                  onChange={(e) => setFreeformHighlights(e.target.value)}
                  rows={2}
                  placeholder="Liệt kê các điểm nhấn: Hồ bơi vô cực, View đồi thông, Gần siêu thị..."
                  className="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none resize-none"
                />
              </div>
            </div>
          </div>
        )}

        <div className="flex justify-end pt-2">
          <button
            type="submit"
            disabled={isGenerating}
            className="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer disabled:opacity-60 flex items-center gap-1.5"
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

      {errorMsg && (
        <div className="p-4 bg-red-50 text-red-500 rounded-2xl text-xs font-bold">
          <i className="fa-solid fa-circle-exclamation mr-1.5" />
          {errorMsg}
        </div>
      )}

      {/* Render AI Result Visualizations */}
      {aiResult && (
        <div className="space-y-6 pt-4 border-t border-slate-100">
          <div className="bg-emerald-50 border border-emerald-150 p-4 rounded-2xl flex items-start gap-3">
            <i className="fa-solid fa-square-check text-emerald-500 text-lg mt-0.5" />
            <div>
              <h5 className="text-xs font-black text-emerald-800">Biên soạn hoàn tất thành công!</h5>
              <p className="text-[10px] text-emerald-600 font-semibold mt-0.5">Google Gemini AI đã tạo các biến thể nội dung chuẩn tối ưu tiếp thị của bạn dưới đây.</p>
            </div>
          </div>

          {/* 1. Facebook results */}
          {feature === 'facebook' && Array.isArray(aiResult) && (
            <div className="space-y-4">
              <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider px-1">Danh sách 20 bài đăng Facebook Ads & Social</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {aiResult.map((post: any) => (
                  <div key={post.id} className="bg-white border border-slate-100 rounded-2xl p-4.5 shadow-sm space-y-3 flex flex-col justify-between">
                    <div className="space-y-2">
                      <div className="flex items-center justify-between">
                        <span className="text-[10px] font-black bg-slate-150 px-2 py-0.5 rounded-md text-slate-600">Bài {post.id}</span>
                        <h5 className="text-xs font-bold text-slate-850 truncate max-w-[200px]" title={post.title}>{post.title}</h5>
                      </div>
                      <p className="text-[11px] text-slate-550 leading-relaxed whitespace-pre-wrap font-medium h-[150px] overflow-y-auto pr-1 select-all border border-slate-50 p-2 rounded-lg bg-slate-50/50">
                        {post.content}
                      </p>
                    </div>
                    <button
                      onClick={() => copyToClipboard(post.content)}
                      className="w-full py-2 bg-slate-100 hover:bg-primary hover:text-white transition rounded-xl text-[10px] font-bold text-slate-650 flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                      <i className="fa-regular fa-copy" />
                      Sao chép bài viết
                    </button>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* 2. TikTok results */}
          {feature === 'tiktok' && Array.isArray(aiResult) && (
            <div className="space-y-4">
              <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider px-1">Danh sách 10 kịch bản video ngắn (TikTok/Shorts)</h4>
              <div className="grid grid-cols-1 gap-4">
                {aiResult.map((video: any) => (
                  <div key={video.id} className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                    <div className="flex items-center justify-between border-b border-slate-100 pb-2">
                      <span className="text-[10px] font-black bg-slate-150 px-2 py-0.5 rounded-md text-slate-600">Kịch bản {video.id}</span>
                      <h5 className="text-xs font-bold text-slate-800">{video.title}</h5>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-semibold">
                      <div className="bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                        <span className="block text-[9px] font-bold uppercase text-slate-400 mb-1">Hình ảnh gợi ý (Visual)</span>
                        <p className="text-[11px] text-slate-600 leading-normal italic">{video.visual}</p>
                      </div>
                      <div className="bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                        <span className="block text-[9px] font-bold uppercase text-slate-400 mb-1">Lời thoại thu âm (Audio)</span>
                        <p className="text-[11px] text-slate-800 leading-normal select-all">{video.audio}</p>
                      </div>
                      <div className="bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                        <span className="block text-[9px] font-bold uppercase text-slate-400 mb-1">Chữ trên màn hình (Text overlay)</span>
                        <p className="text-[11px] text-slate-600 leading-normal font-bold">{video.overlay}</p>
                      </div>
                    </div>
                    <div className="flex justify-end pt-2">
                      <button
                        onClick={() => copyToClipboard(`[Kịch bản: ${video.title}]\nVisual: ${video.visual}\nAudio: ${video.audio}\nOverlay: ${video.overlay}`)}
                        className="px-4 py-1.5 bg-slate-100 hover:bg-primary hover:text-white transition rounded-lg text-[10px] font-bold text-slate-600 flex items-center gap-1.5 cursor-pointer"
                      >
                        <i className="fa-regular fa-copy" />
                        Sao chép kịch bản
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* 3. SEO Website results */}
          {feature === 'seo' && Array.isArray(aiResult) && (
            <div className="space-y-6">
              <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider px-1">Danh sách 5 bài viết chuẩn SEO Website</h4>
              <div className="space-y-4">
                {aiResult.map((art: any, index: number) => (
                  <div key={index} className="bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
                    <div className="border-b border-slate-100 pb-3 text-left space-y-1">
                      <span className="text-[9px] font-bold text-primary uppercase">Chủ đề {index + 1}</span>
                      <h4 className="text-sm font-extrabold text-slate-850 select-all">{art.title}</h4>
                      <p className="text-[10px] text-slate-400 font-semibold select-all">Meta Description: {art.meta}</p>
                    </div>
                    {/* Rendered HTML Preview */}
                    <div 
                      className="prose prose-xs max-w-none text-slate-600 text-xs leading-relaxed space-y-3 border border-slate-50 p-4 rounded-2xl bg-slate-50/50 h-64 overflow-y-auto select-all"
                      dangerouslySetInnerHTML={{ __html: art.content }}
                    />
                    <div className="flex justify-end gap-2">
                      <button
                        onClick={() => copyToClipboard(art.content)}
                        className="px-4 py-1.5 bg-slate-100 hover:bg-primary hover:text-white transition rounded-lg text-[10px] font-bold text-slate-600 flex items-center gap-1.5 cursor-pointer"
                      >
                        <i className="fa-regular fa-copy" />
                        Sao chép mã HTML
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* 4. Email, SMS templates & Prompts */}
          {feature === 'emailsms' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Email templates */}
              <div className="space-y-4">
                <h4 className="text-xs font-black text-slate-800 uppercase px-1">Email Marketing</h4>
                {aiResult.emailTemplates?.map((em: any, index: number) => (
                  <div key={index} className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
                    <div className="border-b border-slate-100 pb-2">
                      <span className="block text-[9px] font-bold text-slate-400">Tiêu đề (Subject)</span>
                      <strong className="text-xs text-slate-800 select-all">{em.subject}</strong>
                    </div>
                    <div className="text-[11px] text-slate-600 leading-relaxed whitespace-pre-wrap font-medium h-48 overflow-y-auto select-all bg-slate-50/50 p-3 rounded-xl border border-slate-100/50">
                      {em.content}
                    </div>
                    <button
                      onClick={() => copyToClipboard(`Subject: ${em.subject}\n\n${em.content}`)}
                      className="w-full py-2 bg-slate-100 hover:bg-primary hover:text-white transition rounded-xl text-[10px] font-bold text-slate-600 flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                      <i className="fa-regular fa-copy" />
                      Sao chép Email
                    </button>
                  </div>
                ))}
              </div>

              {/* SMS & Banners Prompts */}
              <div className="space-y-6">
                {/* SMS */}
                <div className="space-y-3">
                  <h4 className="text-xs font-black text-slate-800 uppercase px-1">SMS & Zalo templates</h4>
                  <div className="space-y-2">
                    {aiResult.smsTemplates?.map((sms: string, idx: number) => (
                      <div key={idx} className="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center justify-between gap-3">
                        <p className="text-[11px] text-slate-650 leading-normal select-all font-semibold">{sms}</p>
                        <button
                          onClick={() => copyToClipboard(sms)}
                          className="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-450 border border-slate-200/50 flex-shrink-0 flex items-center justify-center transition cursor-pointer"
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
                  <h4 className="text-xs font-black text-slate-800 uppercase px-1">Midjourney Prompts (Mô phỏng 3D)</h4>
                  <div className="space-y-2">
                    {aiResult.prompts?.map((pmt: string, idx: number) => (
                      <div key={idx} className="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm text-left relative group">
                        <span className="absolute top-2.5 right-2.5 text-[8px] bg-slate-850 px-2 py-0.5 rounded-md text-slate-400 font-bold select-none">IMAGE GENERATION</span>
                        <p className="text-[10px] text-slate-250 leading-relaxed select-all font-semibold italic pr-12">{pmt}</p>
                        <div className="flex justify-end pt-3">
                          <button
                            onClick={() => copyToClipboard(pmt)}
                            className="px-3 py-1 bg-slate-800 hover:bg-primary text-white transition rounded-md text-[9px] font-bold flex items-center gap-1 cursor-pointer"
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
            </div>
          )}

          {/* 5. Freeform unified content package */}
          {feature === 'freeform' && (
            <div className="space-y-6">
              <h4 className="text-xs font-black text-slate-800 uppercase tracking-wider px-1">Gói Nội Dung Truyền Thông AI Studio (Tự do)</h4>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {/* Social Posts */}
                <div className="space-y-4">
                  <h5 className="text-[11px] font-black uppercase text-primary tracking-wide">1. Facebook & Zalo Posts</h5>
                  {aiResult.posts?.map((p: any) => (
                    <div key={p.id} className="bg-white border border-slate-100 rounded-2xl p-4.5 shadow-sm space-y-2">
                      <div className="flex items-center justify-between border-b border-slate-100 pb-1">
                        <strong className="text-xs text-slate-800">{p.title}</strong>
                        <button onClick={() => copyToClipboard(p.content)} className="text-slate-400 hover:text-primary transition"><i className="fa-regular fa-copy text-xs" /></button>
                      </div>
                      <p className="text-[11px] text-slate-600 leading-relaxed whitespace-pre-wrap font-medium select-all">{p.content}</p>
                    </div>
                  ))}
                </div>

                {/* TikTok scripts */}
                <div className="space-y-4">
                  <h5 className="text-[11px] font-black uppercase text-primary tracking-wide">2. Video Scripts (TikTok)</h5>
                  {aiResult.videos?.map((v: any) => (
                    <div key={v.id} className="bg-white border border-slate-100 rounded-2xl p-4.5 shadow-sm space-y-2 text-left">
                      <div className="flex items-center justify-between border-b border-slate-100 pb-1.5">
                        <strong className="text-xs text-slate-800">{v.title}</strong>
                        <button onClick={() => copyToClipboard(`TikTok Script: ${v.title}\nVisual: ${v.visual}\nAudio: ${v.audio}\nOverlay: ${v.overlay}`)} className="text-slate-400 hover:text-primary transition"><i className="fa-regular fa-copy text-xs" /></button>
                      </div>
                      <div className="text-[10px] space-y-1.5 font-semibold">
                        <div><span className="text-slate-400">Hình ảnh:</span> <span className="italic text-slate-600">{v.visual}</span></div>
                        <div><span className="text-slate-400">Lời thoại:</span> <span className="text-slate-800 select-all">{v.audio}</span></div>
                        <div><span className="text-slate-400">Chữ chạy:</span> <span className="font-bold text-slate-700">{v.overlay}</span></div>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Voiceover script */}
                <div className="space-y-3 bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                  <h5 className="text-[11px] font-black uppercase text-primary tracking-wide">3. Kịch bản lời thoại thu âm (Text-to-Speech)</h5>
                  <p className="text-[11px] text-slate-600 leading-relaxed select-all font-semibold italic bg-slate-50/50 p-4 rounded-2xl border border-slate-100/50">
                    {aiResult.voice_script}
                  </p>
                  <button
                    onClick={() => copyToClipboard(aiResult.voice_script)}
                    className="w-full py-2 bg-slate-100 hover:bg-primary hover:text-white transition rounded-xl text-[10px] font-bold text-slate-600 flex items-center justify-center gap-1.5 cursor-pointer"
                  >
                    <i className="fa-regular fa-copy" />
                    Sao chép lời thoại
                  </button>
                </div>

                {/* Midjourney thumbnail prompt */}
                <div className="space-y-3 bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-sm text-left">
                  <h5 className="text-[11px] font-black uppercase text-indigo-400 tracking-wide">4. Midjourney Prompt (Vẽ ảnh Thumbnail)</h5>
                  <p className="text-[10px] text-slate-200 leading-relaxed select-all font-semibold italic bg-slate-850 p-4 rounded-2xl border border-slate-800">
                    {aiResult.thumbnail_prompt}
                  </p>
                  <button
                    onClick={() => copyToClipboard(aiResult.thumbnail_prompt)}
                    className="w-full py-2 bg-slate-800 hover:bg-primary hover:text-white transition rounded-xl text-[10px] font-bold text-slate-450 flex items-center justify-center gap-1.5 cursor-pointer"
                  >
                    <i className="fa-regular fa-copy" />
                    Sao chép Image Prompt
                  </button>
                </div>

                {/* SEO Website */}
                {aiResult.seo && (
                  <div className="space-y-3 bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 shadow-sm md:col-span-2">
                    <h5 className="text-[11px] font-black uppercase text-primary tracking-wide">5. Bài viết chuẩn SEO Website</h5>
                    <div className="border-b border-slate-100 pb-2">
                      <strong className="text-xs text-slate-850 select-all">{aiResult.seo.title}</strong>
                      <div className="text-[10px] text-slate-400 font-semibold select-all mt-0.5">Meta Description: {aiResult.seo.meta}</div>
                    </div>
                    <div 
                      className="prose prose-xs max-w-none text-slate-650 text-xs leading-relaxed space-y-3 border border-slate-50 p-4 rounded-xl bg-slate-50/50 h-48 overflow-y-auto select-all"
                      dangerouslySetInnerHTML={{ __html: aiResult.seo.content }}
                    />
                    <div className="flex flex-wrap items-center gap-1.5 pt-2">
                      <span className="text-[9px] font-bold text-slate-450">Keywords đề xuất:</span>
                      {aiResult.seo.keywords?.map((kw: string, i: number) => (
                        <span key={i} className="inline-flex px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[9px] font-bold">{kw}</span>
                      ))}
                    </div>
                    <div className="flex justify-end gap-2 pt-2 border-t border-slate-100/60">
                      <button
                        onClick={() => copyToClipboard(aiResult.seo.content)}
                        className="px-4 py-1.5 bg-slate-100 hover:bg-primary hover:text-white transition rounded-lg text-[10px] font-bold text-slate-600 flex items-center gap-1.5 cursor-pointer"
                      >
                        <i className="fa-regular fa-copy" />
                        Sao chép mã HTML
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  )
}
