'use client'

import { useState, useEffect } from 'react'

interface PropertySelectOption {
  id: string
  title: string
  address: string
  priceLabel?: string
  price?: any
}

interface AiMarketingStudioProps {
  properties: PropertySelectOption[]
}

export default function AiMarketingStudio({ properties }: AiMarketingStudioProps) {
  const [localTab, setLocalTab] = useState<'marketing' | 'content_studio' | 'history'>('marketing')

  // AI Marketing State
  const [selectedPropertyId, setSelectedPropertyId] = useState('')
  const [campaignGoal, setCampaignGoal] = useState('rent_fast')
  const [campaignTone, setCampaignTone] = useState('friendly')
  const [generating, setGenerating] = useState(false)
  const [hasResults, setHasResults] = useState(false)
  const [progress, setProgress] = useState(0)
  const [currentStep, setCurrentStep] = useState('')
  const [activeResultTab, setActiveResultTab] = useState<'facebook' | 'tiktok' | 'seo' | 'email' | 'prompts'>('facebook')

  // Marketing Results
  const [facebookPosts, setFacebookPosts] = useState<any[]>([])
  const [tiktokScripts, setTiktokScripts] = useState<any[]>([])
  const [seoArticles, setSeoArticles] = useState<any[]>([])
  const [emailTemplates, setEmailTemplates] = useState<any[]>([])
  const [smsTemplates, setSmsTemplates] = useState<any[]>([])
  const [prompts, setPrompts] = useState<any[]>([])

  // AI Content Studio Inputs
  const [studioTitle, setStudioTitle] = useState('')
  const [studioTxType, setStudioTxType] = useState('rent')
  const [studioPropType, setStudioPropType] = useState('Căn hộ chung cư')
  const [studioPrice, setStudioPrice] = useState('')
  const [studioArea, setStudioArea] = useState('')
  const [studioAddress, setStudioAddress] = useState('')
  const [studioHighlights, setStudioHighlights] = useState('')
  const [studioTone, setStudioTone] = useState('friendly')

  // AI Content Studio Generation State
  const [generatingStudio, setGeneratingStudio] = useState(false)
  const [hasStudioResults, setHasStudioResults] = useState(false)
  const [progressStudio, setProgressStudio] = useState(0)
  const [currentStepStudio, setCurrentStepStudio] = useState('')

  // AI Content Studio Results
  const [studioResult, setStudioResult] = useState<any>(null)
  const [activeStudioPostIndex, setActiveStudioPostIndex] = useState(0)

  // AI Voiceover (Text-to-Speech)
  const [speaking, setSpeaking] = useState(false)
  const [speechSpeed, setSpeechSpeed] = useState(1)

  // AI Thumbnail State
  const [thumbnailLoaded, setThumbnailLoaded] = useState(false)

  // History State
  const [historyCampaigns, setHistoryCampaigns] = useState<any[]>([])
  const [loadingHistory, setLoadingHistory] = useState(false)

  useEffect(() => {
    loadHistory()
  }, [])

  const loadHistory = async () => {
    setLoadingHistory(true)
    try {
      const res = await fetch('/api/marketing/campaigns')
      const data = await res.json()
      if (data.success) {
        setHistoryCampaigns(data.campaigns)
      }
    } catch (err) {
      console.error('Failed to load campaign history:', err)
    } finally {
      setLoadingHistory(false)
    }
  }

  const deleteCampaign = async (id: string) => {
    if (!confirm('Bạn có chắc chắn muốn xóa chiến dịch này khỏi lịch sử?')) return
    try {
      const res = await fetch(`/api/marketing/campaigns/${id}`, {
        method: 'DELETE'
      })
      const data = await res.json()
      if (data.success) {
        alert('Đã xóa chiến dịch thành công.')
        loadHistory()
      } else {
        alert('Lỗi: ' + data.error)
      }
    } catch (err) {
      console.error(err)
      alert('Không thể kết nối đến máy chủ.')
    }
  }

  const viewCampaign = (camp: any) => {
    if (camp.type === 'marketing') {
      setFacebookPosts(camp.content.facebook || [])
      setTiktokScripts(camp.content.tiktok || [])
      setSeoArticles(camp.content.seo || [])
      setEmailTemplates(camp.content.email?.emailTemplates || [])
      setSmsTemplates(camp.content.email?.smsTemplates || [])
      setPrompts(camp.content.email?.prompts || [])

      setSelectedPropertyId(camp.propertyId || '')
      setCampaignGoal(camp.goal || 'rent_fast')
      setCampaignTone(camp.tone || 'friendly')

      setHasResults(true)
      setLocalTab('marketing')
      setActiveResultTab('facebook')
    } else if (camp.type === 'content_studio') {
      setStudioResult(camp.content)
      setStudioTitle(camp.title.replace('Studio: ', ''))
      setStudioTone(camp.tone)

      setHasStudioResults(true)
      setLocalTab('content_studio')
      setActiveStudioPostIndex(0)
      setThumbnailLoaded(false)
    }
  }

  // Sequential generation for multi-channel marketing campaign
  const startGeneration = async () => {
    if (!selectedPropertyId) {
      alert('Vui lòng chọn bất động sản nguồn!')
      return
    }

    setGenerating(true)
    setHasResults(false)
    setProgress(0)
    setCurrentStep('Đang phân tích thông số bất động sản nguồn...')

    try {
      // Step 1: Facebook (10%)
      setProgress(10)
      setCurrentStep('AI đang soạn thảo 20 bài đăng Facebook đa dạng góc nhìn...')
      const resFb = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          feature: 'facebook',
          property_id: selectedPropertyId,
          campaign_goal: campaignGoal,
          campaign_tone: campaignTone
        })
      })
      const dataFb = await resFb.json()
      if (!dataFb.success) throw new Error(dataFb.error || 'Lỗi sinh Facebook posts')
      setFacebookPosts(dataFb.data)

      // Step 2: TikTok (35%)
      setProgress(35)
      setCurrentStep('AI đang xây dựng kịch bản cho 10 video ngắn TikTok/Shorts...')
      const resTt = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          feature: 'tiktok',
          property_id: selectedPropertyId,
          campaign_goal: campaignGoal,
          campaign_tone: campaignTone
        })
      })
      const dataTt = await resTt.json()
      if (!dataTt.success) throw new Error(dataTt.error || 'Lỗi sinh TikTok scripts')
      setTiktokScripts(dataTt.data)

      // Step 3: SEO Website (60%)
      setProgress(60)
      setCurrentStep('AI đang viết 5 bài viết chuẩn SEO Website dạng HTML...')
      const resSeo = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          feature: 'seo',
          property_id: selectedPropertyId,
          campaign_goal: campaignGoal,
          campaign_tone: campaignTone
        })
      })
      const dataSeo = await resSeo.json()
      if (!dataSeo.success) throw new Error(dataSeo.error || 'Lỗi sinh bài viết SEO')
      setSeoArticles(dataSeo.data)

      // Step 4: Email, SMS & Prompts (80%)
      setProgress(80)
      setCurrentStep('AI đang thiết kế Email, SMS và Banner prompts...')
      const resEmail = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          feature: 'emailsms',
          property_id: selectedPropertyId,
          campaign_goal: campaignGoal,
          campaign_tone: campaignTone
        })
      })
      const dataEmail = await resEmail.json()
      if (!dataEmail.success) throw new Error(dataEmail.error || 'Lỗi sinh Email/SMS')

      setEmailTemplates(dataEmail.data.emailTemplates || [])
      setSmsTemplates(dataEmail.data.smsTemplates || [])
      setPrompts(dataEmail.data.prompts || [])

      // Step 5: Save to DB (90%)
      setProgress(90)
      setCurrentStep('Đang tự động lưu trữ chiến dịch vào Lịch sử...')

      let propertyTitle = 'Chiến dịch BĐS'
      if (selectedPropertyId === 'mock_prop_1') propertyTitle = 'Căn hộ dịch vụ Hà Đô Centrosa'
      else if (selectedPropertyId === 'mock_prop_2') propertyTitle = 'Nhà nguyên căn Lê Quang Định'
      else {
        const matched = properties.find(p => p.id === selectedPropertyId)
        if (matched) propertyTitle = matched.title
      }

      await fetch('/api/marketing/campaigns', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          type: 'marketing',
          property_id: selectedPropertyId,
          title: 'Chiến dịch: ' + propertyTitle,
          goal: campaignGoal,
          tone: campaignTone,
          content: {
            facebook: dataFb.data,
            tiktok: dataTt.data,
            seo: dataSeo.data,
            email: dataEmail.data
          }
        })
      })

      setProgress(100)
      setGenerating(false)
      setHasResults(true)
      setActiveResultTab('facebook')
      loadHistory()
    } catch (err: any) {
      console.error(err)
      setGenerating(false)
      alert('Lỗi khởi tạo AI: ' + err.message)
    }
  }

  // Generation for Content Studio freeform
  const startStudioGeneration = async () => {
    if (!studioTitle || !studioAddress) {
      alert('Vui lòng nhập Tiêu đề và Địa chỉ!')
      return
    }

    setGeneratingStudio(true)
    setHasStudioResults(false)
    setProgressStudio(20)
    setCurrentStepStudio('AI đang thiết kế trọn bộ Content Studio...')

    try {
      const res = await fetch('/api/marketing/studio', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          feature: 'freeform',
          freeform_data: {
            title: studioTitle,
            transaction_type: studioTxType,
            property_type: studioPropType,
            price: studioPrice,
            area: studioArea,
            address: studioAddress,
            highlights: studioHighlights,
            tone: studioTone
          }
        })
      })

      const data = await res.json()
      if (!data.success) throw new Error(data.error || 'Lỗi tạo Content Studio')

      setStudioResult(data.data)
      setThumbnailLoaded(false)

      setProgressStudio(80)
      setCurrentStepStudio('Đang tự động lưu gói nội dung vào Lịch sử...')

      await fetch('/api/marketing/campaigns', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          type: 'content_studio',
          property_id: null,
          title: 'Studio: ' + studioTitle,
          goal: null,
          tone: studioTone,
          content: data.data
        })
      })

      setProgressStudio(100)
      setGeneratingStudio(false)
      setHasStudioResults(true)
      setActiveStudioPostIndex(0)
      loadHistory()
    } catch (err: any) {
      console.error(err)
      setGeneratingStudio(false)
      alert('Lỗi AI Content Studio: ' + err.message)
    }
  }

  // Browser TTS Voiceover
  const speakVoiceover = (text: string) => {
    if (speaking) {
      window.speechSynthesis.cancel()
      setSpeaking(false)
      return
    }

    if (!text) return

    // Clean HTML / Markdown tags for smoother reading
    const cleanText = text.replace(/<[^>]*>/g, '').replace(/[#\*_\-\[\]\(\)]/g, '').trim()

    const utterance = new SpeechSynthesisUtterance(cleanText)
    utterance.lang = 'vi-VN'
    utterance.rate = speechSpeed

    // Try finding a Vietnamese voice
    const voices = window.speechSynthesis.getVoices()
    const viVoice = voices.find(v => v.lang.includes('vi') || v.lang.includes('VI'))
    if (viVoice) {
      utterance.voice = viVoice
    }

    utterance.onend = () => setSpeaking(false)
    utterance.onerror = () => setSpeaking(false)

    setSpeaking(true)
    window.speechSynthesis.speak(utterance)
  }

  const copyText = (text: string) => {
    navigator.clipboard.writeText(text)
    alert('Đã sao chép nội dung vào Clipboard!')
  }

  return (
    <div className="space-y-6 text-left">
      {/* Tab Switcher & Header */}
      <div className="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-3">
        <div>
          <h2 className="text-xl font-bold text-slate-800">AI Marketing & Studio</h2>
          <p className="text-xs text-slate-400 mt-1 font-semibold">Tự động hóa quảng bá, sáng tạo nội dung và lưu trữ chiến dịch thông minh.</p>
        </div>

        {/* Local Navigation Switches */}
        <div className="flex bg-slate-100 p-1 rounded-xl items-center gap-1">
          <button
            type="button"
            onClick={() => setLocalTab('marketing')}
            className={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition focus:outline-none cursor-pointer flex items-center gap-1.5 ${
              localTab === 'marketing' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            <i className="fa-solid fa-wand-magic-sparkles text-primary" />
            <span>AI Marketing Đa Kênh</span>
          </button>
          <button
            type="button"
            onClick={() => setLocalTab('content_studio')}
            className={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition focus:outline-none cursor-pointer flex items-center gap-1.5 ${
              localTab === 'content_studio' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            <i className="fa-solid fa-photo-film text-emerald-500" />
            <span>AI Content Studio</span>
          </button>
          <button
            type="button"
            onClick={() => {
              setLocalTab('history')
              loadHistory()
            }}
            className={`px-3.5 py-1.5 rounded-lg text-xs font-bold transition focus:outline-none cursor-pointer flex items-center gap-1.5 ${
              localTab === 'history' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'
            }`}
          >
            <i className="fa-solid fa-clock-rotate-left text-amber-500" />
            <span>Lịch sử</span>
            <span className="inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-black bg-slate-200 text-slate-600 rounded-md">
              {historyCampaigns.length}
            </span>
          </button>
        </div>
      </div>

      {/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */}
      {/* 1. TAB: AI MARKETING ĐA KÊNH */}
      {/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */}
      {localTab === 'marketing' && (
        <div>
          {/* Config Panel */}
          {!generating && !hasResults && (
            <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Property selector */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Chọn bất động sản nguồn</label>
                  <select
                    value={selectedPropertyId}
                    onChange={(e) => setSelectedPropertyId(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                  >
                    <option value="">-- Chọn bất động sản của bạn --</option>
                    {properties.map((prop) => (
                      <option key={prop.id} value={prop.id}>
                        {prop.title} ({prop.priceLabel || prop.price})
                      </option>
                    ))}
                    <option value="mock_prop_1">Căn hộ dịch vụ Hà Đô Centrosa Quận 10 (14.5tr/tháng)</option>
                    <option value="mock_prop_2">Nhà nguyên căn Hẻm xe hơi Lê Quang Định Bình Thạnh (4.2 tỷ)</option>
                  </select>
                </div>

                {/* Campaign Goal */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Mục tiêu chiến dịch</label>
                  <select
                    value={campaignGoal}
                    onChange={(e) => setCampaignGoal(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                  >
                    <option value="rent_fast">Đăng tin cho thuê nhanh (Đặc điểm nổi bật)</option>
                    <option value="luxury_brand">Xây dựng thương hiệu căn hộ cao cấp</option>
                    <option value="price_deal">Chương trình ưu đãi giảm giá / Cắt lỗ gấp</option>
                    <option value="review_detail">Bài viết Review trải nghiệm chi tiết</option>
                  </select>
                </div>

                {/* Tone */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Giọng văn của AI</label>
                  <select
                    value={campaignTone}
                    onChange={(e) => setCampaignTone(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                  >
                    <option value="friendly">Thân thiện, cởi mở (Phù hợp Facebook/TikTok)</option>
                    <option value="professional">Chuyên nghiệp, đáng tin cậy (Phù hợp SEO/Email)</option>
                    <option value="funny">Hài hước, bắt trend độc lạ</option>
                    <option value="emotional">Gợi mở cảm xúc, chạm tâm lý tìm tổ ấm</option>
                  </select>
                </div>
              </div>

              <div className="flex justify-end pt-4 border-t border-slate-100">
                <button
                  type="button"
                  onClick={startGeneration}
                  className="px-6 py-3 bg-gradient-to-r from-primary to-primary-hover hover:shadow-primary/30 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md cursor-pointer active:scale-98"
                >
                  <i className="fa-solid fa-wand-magic-sparkles text-sm animate-pulse" />
                  <span>Khởi tạo chiến dịch Marketing AI</span>
                </button>
              </div>
            </div>
          )}

          {/* Loader screen */}
          {generating && (
            <div className="bg-white border border-slate-100 rounded-3xl p-12 shadow-sm text-center space-y-6">
              <div className="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto text-xl relative">
                <i className="fa-solid fa-robot animate-bounce" />
                <span className="absolute inset-0 rounded-full border-2 border-primary border-t-transparent animate-spin" />
              </div>

              <div className="space-y-2 max-w-md mx-auto">
                <h4 className="font-extrabold text-slate-700 text-sm">Trình sáng tạo AI đang làm việc...</h4>
                <p className="text-[11px] text-slate-400 font-semibold">{currentStep}</p>
              </div>

              {/* Progress bar */}
              <div className="max-w-md mx-auto bg-slate-100 h-2 rounded-full overflow-hidden">
                <div className="bg-primary h-full transition-all duration-300" style={{ width: `${progress}%` }} />
              </div>
              <span className="inline-block text-[11px] font-black text-primary bg-primary/5 px-2.5 py-1 rounded-full">
                {progress}%
              </span>
            </div>
          )}

          {/* Results dashboard */}
          {hasResults && (
            <div className="space-y-6">
              <div className="bg-slate-50 border border-slate-150 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-primary text-base shadow-sm">
                    <i className="fa-solid fa-file-invoice" />
                  </div>
                  <div>
                    <span className="text-[9px] font-bold text-slate-400 uppercase">BĐS nguồn</span>
                    <h4 className="font-bold text-slate-800 text-xs mt-0.5">
                      {selectedPropertyId === 'mock_prop_1'
                        ? 'Căn hộ dịch vụ Hà Đô Centrosa'
                        : selectedPropertyId === 'mock_prop_2'
                        ? 'Nhà nguyên căn Lê Quang Định'
                        : 'Bất động sản đã chọn'}
                    </h4>
                  </div>
                </div>
                <button
                  type="button"
                  onClick={() => {
                    setHasResults(false)
                    setSelectedPropertyId('')
                  }}
                  className="px-4 py-2 bg-white border border-slate-200 hover:border-red-500 text-slate-600 hover:text-red-500 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer self-start sm:self-auto shadow-sm"
                >
                  <i className="fa-solid fa-arrow-left" />
                  <span>Tạo chiến dịch mới</span>
                </button>
              </div>

              {/* Grid split */}
              <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Left sidebar nav */}
                <div className="bg-white border border-slate-100 rounded-3xl p-3 shadow-sm h-fit flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible gap-1.5 scrollbar-none">
                  <button
                    onClick={() => setActiveResultTab('facebook')}
                    className={`flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none ${
                      activeResultTab === 'facebook' ? 'bg-primary/5 text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'
                    }`}
                  >
                    <i className="fa-brands fa-facebook text-sm text-blue-600" />
                    <span>Facebook Posts</span>
                  </button>
                  <button
                    onClick={() => setActiveResultTab('tiktok')}
                    className={`flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none ${
                      activeResultTab === 'tiktok' ? 'bg-primary/5 text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'
                    }`}
                  >
                    <i className="fa-brands fa-tiktok text-sm text-slate-800" />
                    <span>TikTok / Shorts</span>
                  </button>
                  <button
                    onClick={() => setActiveResultTab('seo')}
                    className={`flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none ${
                      activeResultTab === 'seo' ? 'bg-primary/5 text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'
                    }`}
                  >
                    <i className="fa-solid fa-file-word text-sm text-sky-500" />
                    <span>SEO Articles</span>
                  </button>
                  <button
                    onClick={() => setActiveResultTab('email')}
                    className={`flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none ${
                      activeResultTab === 'email' ? 'bg-primary/5 text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'
                    }`}
                  >
                    <i className="fa-solid fa-envelope text-sm text-rose-500" />
                    <span>Email & SMS</span>
                  </button>
                  <button
                    onClick={() => setActiveResultTab('prompts')}
                    className={`flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none ${
                      activeResultTab === 'prompts' ? 'bg-primary/5 text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'
                    }`}
                  >
                    <i className="fa-solid fa-image text-sm text-emerald-500" />
                    <span>Banner Prompts</span>
                  </button>
                </div>

                {/* Right content view */}
                <div className="lg:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm min-h-[400px]">
                  {/* FACEBOOK */}
                  {activeResultTab === 'facebook' && (
                    <div className="space-y-6">
                      <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 className="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                          <i className="fa-brands fa-facebook text-blue-600 text-base" />
                          <span>Bài đăng Facebook hàng tuần</span>
                        </h3>
                        <span className="text-[10px] font-bold text-slate-400 uppercase">Tạo bởi Gemini AI</span>
                      </div>

                      <div className="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                        {facebookPosts.map((post) => (
                          <div key={post.id} className="p-4 bg-slate-50 border border-slate-150 rounded-2xl space-y-3">
                            <div className="flex items-center justify-between">
                              <span className="text-[9px] font-black text-primary bg-primary/10 px-2 py-0.5 rounded-md uppercase">
                                Bài đăng #{post.id}
                              </span>
                              <button
                                type="button"
                                onClick={() => copyText(post.title + '\n\n' + post.content)}
                                className="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none shadow-sm"
                              >
                                <i className="fa-solid fa-copy" />
                                <span>Sao chép bài</span>
                              </button>
                            </div>
                            <h4 className="font-black text-slate-800 text-xs">{post.title}</h4>
                            <p className="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{post.content}</p>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* TIKTOK */}
                  {activeResultTab === 'tiktok' && (
                    <div className="space-y-6">
                      <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 className="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                          <i className="fa-brands fa-tiktok text-slate-900 text-base" />
                          <span>Kịch bản Video TikTok ngắn (60s)</span>
                        </h3>
                        <span className="text-[10px] font-bold text-slate-400 uppercase">10 ý tưởng kịch bản</span>
                      </div>

                      <div className="space-y-5 max-h-[500px] overflow-y-auto pr-1">
                        {tiktokScripts.map((script) => (
                          <div key={script.id} className="border border-slate-150 rounded-2xl overflow-hidden shadow-sm">
                            <div className="px-4 py-3 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                              <h4 className="font-bold text-slate-700 text-xs">
                                Kịch bản #{script.id}: {script.title}
                              </h4>
                              <button
                                type="button"
                                onClick={() =>
                                  copyText(`Kịch bản: ${script.title}\n\n[Visual]: ${script.visual}\n\n[Voiceover]: ${script.audio}`)
                                }
                                className="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                              >
                                <i className="fa-solid fa-copy" />
                                <span>Sao chép</span>
                              </button>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 text-xs">
                              <div className="p-4 border-b md:border-b-0 md:border-r border-slate-100 space-y-2">
                                <span className="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Hình ảnh gợi ý (Visual)</span>
                                <p className="text-slate-600 leading-relaxed font-semibold">{script.visual}</p>
                              </div>
                              <div className="p-4 space-y-3">
                                <div className="space-y-1">
                                  <span className="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Lời thoại Voiceover (Audio)</span>
                                  <p className="text-slate-700 leading-relaxed font-bold bg-primary/5 p-2.5 rounded-lg border border-primary/10">
                                    {script.audio}
                                  </p>
                                </div>
                                <div className="space-y-1">
                                  <span className="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Chữ hiển thị (Overlay)</span>
                                  <p className="text-slate-500 leading-normal font-mono">{script.overlay}</p>
                                </div>
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* SEO ARTICLES */}
                  {activeResultTab === 'seo' && (
                    <div className="space-y-6">
                      <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 className="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                          <i className="fa-solid fa-file-word text-blue-500 text-base" />
                          <span>Bài viết chuẩn SEO Website</span>
                        </h3>
                        <span className="text-[10px] font-bold text-slate-400">5 bài viết chuyên sâu</span>
                      </div>

                      <div className="space-y-6 max-h-[500px] overflow-y-auto pr-1">
                        {seoArticles.map((art, idx) => (
                          <div key={idx} className="p-5 border border-slate-150 rounded-2xl space-y-4 text-left">
                            <div className="flex items-center justify-between">
                              <h4 className="font-black text-slate-800 text-xs">{art.title}</h4>
                              <button
                                type="button"
                                onClick={() => copyText(art.title + '\n\n' + art.content)}
                                className="px-2 py-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none shadow-sm"
                              >
                                <i className="fa-solid fa-copy" />
                                <span>Sao chép</span>
                              </button>
                            </div>
                            <div className="bg-slate-50 p-3 rounded-xl border border-slate-150 space-y-1 text-xs">
                              <p className="text-slate-500 leading-normal">
                                <strong className="text-slate-700">Meta Title:</strong> {art.title}
                              </p>
                              <p className="text-slate-500 leading-normal">
                                <strong className="text-slate-700">Meta Description:</strong> {art.meta}
                              </p>
                            </div>
                            <div
                              className="prose prose-slate max-w-none text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3"
                              dangerouslySetInnerHTML={{ __html: art.content }}
                            />
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* EMAIL & SMS */}
                  {activeResultTab === 'email' && (
                    <div className="space-y-6">
                      <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 className="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                          <i className="fa-solid fa-envelope text-primary text-base" />
                          <span>Bản tin Email & Tin nhắn SMS</span>
                        </h3>
                      </div>

                      <div className="space-y-5 max-h-[500px] overflow-y-auto pr-1 text-xs">
                        {emailTemplates.map((email) => (
                          <div key={email.subject} className="border border-slate-150 rounded-2xl overflow-hidden">
                            <div className="px-4 py-3 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                              <span className="font-extrabold text-slate-700 text-xs">Mẫu Email Chăm sóc Khách hàng</span>
                              <button
                                type="button"
                                onClick={() => copyText(email.subject + '\n\n' + email.content)}
                                className="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                              >
                                <i className="fa-solid fa-copy" />
                                <span>Sao chép Email</span>
                              </button>
                            </div>
                            <div className="p-4 space-y-3 bg-slate-50/20 text-left">
                              <div>
                                <span className="block text-[9px] font-bold text-slate-400 uppercase">Tiêu đề (Subject)</span>
                                <p className="font-black text-slate-800">{email.subject}</p>
                              </div>
                              <div className="border-t border-slate-100 pt-3">
                                <span className="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nội dung (Body)</span>
                                <p className="text-slate-650 leading-relaxed whitespace-pre-line bg-white p-3 rounded-xl border border-slate-100">
                                  {email.content}
                                </p>
                              </div>
                            </div>
                          </div>
                        ))}

                        <div className="p-4 border border-slate-150 rounded-2xl space-y-3">
                          <span className="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">
                            SMS / Zalo ZNS ngắn gọn
                          </span>
                          <div className="space-y-2">
                            {smsTemplates.map((sms, idx) => (
                              <div key={idx} className="p-3 bg-slate-50 border border-slate-150 rounded-xl flex items-center justify-between gap-4">
                                <p className="font-mono text-slate-700 leading-normal">{sms}</p>
                                <button
                                  type="button"
                                  onClick={() => copyText(sms)}
                                  className="p-2 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg transition flex-shrink-0 cursor-pointer focus:outline-none"
                                >
                                  <i className="fa-solid fa-copy text-xs" />
                                </button>
                              </div>
                            ))}
                          </div>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* BANNER PROMPTS */}
                  {activeResultTab === 'prompts' && (
                    <div className="space-y-6">
                      <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 className="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                          <i className="fa-solid fa-image text-emerald-500 text-base" />
                          <span>Prompts thiết kế Banner quảng cáo</span>
                        </h3>
                      </div>

                      <div className="space-y-4 text-xs">
                        <p className="text-slate-500 leading-relaxed font-medium">
                          Sao chép các prompt tiếng Anh chi tiết bên dưới để đưa vào các công cụ sinh ảnh AI (như Midjourney, DALL-E) để vẽ ảnh truyền thông chất lượng cao:
                        </p>

                        {prompts.map((prompt, index) => (
                          <div key={index} className="p-4 bg-slate-50 border border-slate-150 rounded-xl space-y-3 text-left">
                            <div className="flex items-center justify-between">
                              <span className="text-[9px] font-black text-slate-400 uppercase">Prompt #{index + 1}</span>
                              <button
                                type="button"
                                onClick={() => copyText(prompt)}
                                className="px-2.5 py-1 bg-white border border-slate-200 hover:border-primary text-slate-550 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                              >
                                <i className="fa-solid fa-copy" />
                                <span>Sao chép câu lệnh</span>
                              </button>
                            </div>
                            <p className="font-mono text-slate-700 leading-relaxed bg-white p-3 rounded-lg border border-slate-100">
                              {prompt}
                            </p>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */}
      {/* 2. TAB: AI CONTENT STUDIO */}
      {/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */}
      {localTab === 'content_studio' && (
        <div>
          {/* Config Panel */}
          {!generatingStudio && !hasStudioResults && (
            <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Title */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Tên / Tiêu đề dự án BĐS</label>
                  <input
                    type="text"
                    value={studioTitle}
                    onChange={(e) => setStudioTitle(e.target.value)}
                    placeholder="Ví dụ: Villa song lập Diamond Island Quận 2"
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                  />
                </div>

                {/* Address */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Địa chỉ chi tiết</label>
                  <input
                    type="text"
                    value={studioAddress}
                    onChange={(e) => setStudioAddress(e.target.value)}
                    placeholder="Ví dụ: Đường số 5, KDC Bình Trưng Tây, Quận 2"
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                  />
                </div>

                {/* Transaction type */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Loại giao dịch</label>
                  <select
                    value={studioTxType}
                    onChange={(e) => setStudioTxType(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                  >
                    <option value="rent">Cho thuê bất động sản</option>
                    <option value="sale">Bán bất động sản</option>
                  </select>
                </div>

                {/* Property Type */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Loại hình BĐS</label>
                  <select
                    value={studioPropType}
                    onChange={(e) => setStudioPropType(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                  >
                    <option value="Căn hộ chung cư">Căn hộ chung cư</option>
                    <option value="Nhà nguyên căn">Nhà riêng / Nhà phố</option>
                    <option value="Biệt thự">Biệt thự cao cấp</option>
                    <option value="Phòng trọ">Phòng trọ / Chung cư mini</option>
                    <option value="Đất nền">Đất nền / Đất dự án</option>
                    <option value="Mặt bằng">Mặt bằng kinh doanh</option>
                    <option value="Văn phòng">Văn phòng cho thuê</option>
                  </select>
                </div>

                {/* Price */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Giá yêu cầu (Thuê/Bán)</label>
                  <input
                    type="text"
                    value={studioPrice}
                    onChange={(e) => setStudioPrice(e.target.value)}
                    placeholder="Ví dụ: 12 triệu/tháng hoặc 8.5 tỷ"
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                  />
                </div>

                {/* Area */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Diện tích (m²)</label>
                  <input
                    type="text"
                    value={studioArea}
                    onChange={(e) => setStudioArea(e.target.value)}
                    placeholder="Ví dụ: 95m2 (2 phòng ngủ, 2wc)"
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                  />
                </div>

                {/* Tone */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Giọng văn của AI</label>
                  <select
                    value={studioTone}
                    onChange={(e) => setStudioTone(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                  >
                    <option value="friendly">Thân thiện, cởi mở (Phù hợp Facebook/TikTok)</option>
                    <option value="professional">Chuyên nghiệp, uy tín (Phù hợp Website/Email)</option>
                    <option value="funny">Hài hước, dí dỏm độc lạ</option>
                    <option value="emotional">Truyền cảm hứng, tạo cảm xúc tổ ấm</option>
                  </select>
                </div>
              </div>

              {/* Highlights */}
              <div className="space-y-1.5">
                <label className="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">
                  Đặc điểm nổi bật / Tiện ích / Ghi chú khác
                </label>
                <textarea
                  rows={3}
                  value={studioHighlights}
                  onChange={(e) => setStudioHighlights(e.target.value)}
                  placeholder="Ví dụ: Full nội thất ngoại nhập, view trực diện sông Sài Gòn, hồ bơi tràn bờ nước mặn, compound an ninh khép kín..."
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                />
              </div>

              <div className="flex justify-end pt-4 border-t border-slate-100">
                <button
                  type="button"
                  onClick={startStudioGeneration}
                  className="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md cursor-pointer active:scale-98 border-none"
                >
                  <i className="fa-solid fa-photo-film text-sm" />
                  <span>Tạo nội dung AI Studio</span>
                </button>
              </div>
            </div>
          )}

          {/* Loader screen */}
          {generatingStudio && (
            <div className="bg-white border border-slate-100 rounded-3xl p-12 shadow-sm text-center space-y-6">
              <div className="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl relative">
                <i className="fa-solid fa-photo-film animate-spin" />
              </div>

              <div className="space-y-2 max-w-sm mx-auto">
                <h4 className="font-extrabold text-slate-700 text-sm">AI Content Studio đang xử lý dữ liệu...</h4>
                <p className="text-[11px] text-slate-400 font-semibold">{currentStepStudio}</p>
              </div>

              {/* Progress bar */}
              <div className="max-w-md mx-auto bg-slate-100 h-2 rounded-full overflow-hidden">
                <div className="bg-emerald-500 h-full transition-all duration-300" style={{ width: `${progressStudio}%` }} />
              </div>
              <span className="inline-block text-[11px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                {progressStudio}%
              </span>
            </div>
          )}

          {/* Studio results view */}
          {hasStudioResults && studioResult && (
            <div className="space-y-6">
              <div className="bg-slate-50 border border-slate-150 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-emerald-650 text-base shadow-sm">
                    <i className="fa-solid fa-wand-magic-sparkles" />
                  </div>
                  <div>
                    <span className="text-[9px] font-bold text-slate-400 uppercase">Kết quả AI Content Studio</span>
                    <h4 className="font-bold text-slate-800 text-xs mt-0.5">{studioTitle}</h4>
                  </div>
                </div>
                <button
                  type="button"
                  onClick={() => {
                    setHasStudioResults(false)
                    setStudioResult(null)
                  }}
                  className="px-4 py-2 bg-white border border-slate-200 hover:border-red-500 text-slate-600 hover:text-red-500 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer self-start sm:self-auto shadow-sm"
                >
                  <i className="fa-solid fa-arrow-left" />
                  <span>Tạo nội dung mới</span>
                </button>
              </div>

              {/* Grid split */}
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left column: posts & video scripts */}
                <div className="lg:col-span-2 space-y-6">
                  {/* Posts Panel */}
                  <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div className="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                      <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i className="fa-brands fa-facebook text-blue-600 text-base" />
                        <span>Bài đăng Mạng xã hội</span>
                      </h3>

                      {/* Post Switcher */}
                      <div className="flex bg-slate-100 p-0.5 rounded-lg text-[10px]">
                        {studioResult.posts?.map((p: any, idx: number) => (
                          <button
                            key={p.id}
                            type="button"
                            onClick={() => setActiveStudioPostIndex(idx)}
                            className={`px-2.5 py-1 rounded-md focus:outline-none cursor-pointer ${
                              activeStudioPostIndex === idx ? 'bg-white font-bold shadow-sm' : 'text-slate-500'
                            }`}
                          >
                            Bài {idx + 1}
                          </button>
                        ))}
                      </div>
                    </div>

                    {/* Active post display */}
                    {studioResult.posts?.[activeStudioPostIndex] && (
                      <div className="space-y-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-left">
                        <div className="flex justify-between items-center">
                          <h4 className="font-black text-slate-800 text-xs">
                            {studioResult.posts[activeStudioPostIndex].title}
                          </h4>
                          <button
                            type="button"
                            onClick={() =>
                              copyText(
                                studioResult.posts[activeStudioPostIndex].title +
                                  '\n\n' +
                                  studioResult.posts[activeStudioPostIndex].content
                              )
                            }
                            className="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none shadow-sm"
                          >
                            <i className="fa-solid fa-copy" />
                            <span>Sao chép</span>
                          </button>
                        </div>
                        <p className="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                          {studioResult.posts[activeStudioPostIndex].content}
                        </p>
                      </div>
                    )}
                  </div>

                  {/* Video scripts panel */}
                  <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div className="pb-3 border-b border-slate-100">
                      <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i className="fa-brands fa-tiktok text-slate-900 text-base" />
                        <span>Kịch bản Video TikTok ngắn</span>
                      </h3>
                    </div>

                    <div className="space-y-4 text-left">
                      {studioResult.videos?.map((vid: any) => (
                        <div key={vid.id} className="border border-slate-150 rounded-2xl overflow-hidden">
                          <div className="px-4 py-2.5 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                            <h4 className="font-bold text-slate-700 text-xs">
                              Kịch bản #{vid.id}: {vid.title}
                            </h4>
                            <button
                              type="button"
                              onClick={() => copyText(`Kịch bản: ${vid.title}\n\n[Visual]: ${vid.visual}\n\n[Voiceover]: ${vid.audio}`)}
                              className="px-2 py-0.5 bg-white border border-slate-200 hover:border-primary text-slate-550 hover:text-primary rounded-md text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                            >
                              <i className="fa-solid fa-copy" />
                              <span>Copy</span>
                            </button>
                          </div>
                          <div className="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs bg-slate-50/10">
                            <div>
                              <span className="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mô tả hình ảnh</span>
                              <p className="text-slate-600 leading-relaxed font-medium">{vid.visual}</p>
                            </div>
                            <div className="space-y-2">
                              <div>
                                <span className="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lời thoại nói</span>
                                <p className="text-slate-700 leading-relaxed font-bold bg-primary/5 p-2 rounded-lg border border-primary/5">
                                  {vid.audio}
                                </p>
                              </div>
                              <div>
                                <span className="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Chữ hiển thị</span>
                                <p className="text-slate-500 font-mono text-[10px]">{vid.overlay}</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>

                {/* Right Column: voice, thumbnail, hashtags, seo */}
                <div className="space-y-6 text-left">
                  {/* AI voiceover (TTS) */}
                  <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div className="pb-3 border-b border-slate-100">
                      <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i className="fa-solid fa-microphone-lines text-primary text-base" />
                        <span>Giọng đọc AI Voice (Lời thoại)</span>
                      </h3>
                    </div>

                    <div className="bg-slate-50 border border-slate-150 rounded-2xl p-4 space-y-4">
                      {/* Wave visualizer */}
                      <div className="h-10 flex items-center justify-center gap-1.5 bg-white border border-slate-100 rounded-xl px-4">
                        <div className={`w-0.5 bg-primary rounded-full transition-all duration-300 ${speaking ? 'h-6 animate-pulse' : 'h-1.5'}`} />
                        <div className={`w-0.5 bg-primary rounded-full transition-all duration-300 ${speaking ? 'h-8 animate-pulse' : 'h-1.5'}`} style={{ animationDelay: '0.2s' }} />
                        <div className={`w-0.5 bg-primary rounded-full transition-all duration-300 ${speaking ? 'h-5 animate-pulse' : 'h-1.5'}`} style={{ animationDelay: '0.4s' }} />
                        <div className={`w-0.5 bg-primary rounded-full transition-all duration-300 ${speaking ? 'h-7 animate-pulse' : 'h-1.5'}`} style={{ animationDelay: '0.1s' }} />
                        <div className={`w-0.5 bg-primary rounded-full transition-all duration-300 ${speaking ? 'h-6 animate-pulse' : 'h-1.5'}`} style={{ animationDelay: '0.3s' }} />
                        <div className={`w-0.5 bg-primary rounded-full transition-all duration-300 ${speaking ? 'h-4 animate-pulse' : 'h-1.5'}`} style={{ animationDelay: '0.5s' }} />
                      </div>

                      {/* Controls */}
                      <div className="flex items-center justify-between gap-4">
                        <button
                          type="button"
                          onClick={() => speakVoiceover(studioResult.voice_script)}
                          className={`w-10 h-10 rounded-full text-white flex items-center justify-center transition cursor-pointer focus:outline-none shadow-md ${
                            speaking ? 'bg-red-500 hover:bg-red-600 shadow-red-200' : 'bg-primary hover:bg-primary-hover shadow-primary/20'
                          }`}
                        >
                          <i className={`fa-solid ${speaking ? 'fa-stop' : 'fa-play ml-0.5'}`} />
                        </button>

                        <div className="flex-grow space-y-1">
                          <div className="flex justify-between text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                            <span>Tốc độ đọc</span>
                            <span>{speechSpeed}x</span>
                          </div>
                          <input
                            type="range"
                            min="0.5"
                            max="1.5"
                            step="0.1"
                            value={speechSpeed}
                            onChange={(e) => setSpeechSpeed(parseFloat(e.target.value))}
                            className="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary focus:outline-none"
                          />
                        </div>
                      </div>

                      <div className="text-xs text-slate-500 bg-white p-3 rounded-xl border border-slate-100 max-h-24 overflow-y-auto leading-relaxed whitespace-pre-line font-medium">
                        {studioResult.voice_script}
                      </div>
                    </div>
                  </div>

                  {/* AI Thumbnail generation */}
                  <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div className="pb-3 border-b border-slate-100">
                      <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i className="fa-solid fa-image text-emerald-500 text-base" />
                        <span>Ảnh Thumbnail AI vẽ</span>
                      </h3>
                    </div>

                    <div className="relative overflow-hidden rounded-2xl border border-slate-150 shadow-sm aspect-video bg-slate-50 flex items-center justify-center">
                      {!thumbnailLoaded && (
                        <div className="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 text-slate-400 gap-2">
                          <i className="fa-solid fa-spinner animate-spin text-lg" />
                          <span className="text-[9px] font-black uppercase">Đang vẽ hình ảnh...</span>
                        </div>
                      )}
                      <img
                        src={`https://image.pollinations.ai/prompt/${encodeURIComponent(studioResult.thumbnail_prompt)}?width=800&height=500&model=flux&nologo=true`}
                        onLoad={() => setThumbnailLoaded(true)}
                        className={`w-full h-full object-cover transition-opacity duration-350 ${thumbnailLoaded ? 'opacity-100' : 'opacity-0'}`}
                        alt="AI Generated Thumbnail"
                      />
                    </div>

                    <div className="text-[10px] text-slate-500 bg-slate-50 border border-slate-150 p-3 rounded-xl">
                      <span className="block font-black text-slate-400 uppercase tracking-wider mb-1">Prompt tiếng Anh</span>
                      <p className="font-mono">{studioResult.thumbnail_prompt}</p>
                    </div>
                  </div>

                  {/* Hashtags & SEO */}
                  <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div className="pb-3 border-b border-slate-100">
                      <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i className="fa-solid fa-tags text-amber-500 text-base" />
                        <span>Bộ Hashtags & SEO</span>
                      </h3>
                    </div>

                    {/* Tags */}
                    <div className="space-y-2">
                      <div className="flex justify-between items-center">
                        <span className="text-[9px] font-black text-slate-400 uppercase tracking-wider">Hashtags đề xuất</span>
                        <button
                          type="button"
                          onClick={() => copyText(studioResult.hashtags.join(' '))}
                          className="text-[10px] font-bold text-primary hover:underline cursor-pointer focus:outline-none"
                        >
                          Copy bộ tag
                        </button>
                      </div>
                      <div className="flex flex-wrap gap-1.5">
                        {studioResult.hashtags?.map((tag: string) => (
                          <span key={tag} className="inline-block text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                            {tag}
                          </span>
                        ))}
                      </div>
                    </div>

                    {/* SEO preview */}
                    {studioResult.seo && (
                      <div className="border-t border-slate-100 pt-3 space-y-2 text-xs">
                        <span className="block text-[9px] font-black text-slate-400 uppercase tracking-wider">SEO Title & Meta</span>
                        <div className="p-3 bg-slate-50 border border-slate-150 rounded-xl space-y-1.5">
                          <p className="text-slate-600">
                            <strong className="text-slate-800">Title:</strong> {studioResult.seo.title}
                          </p>
                          <p className="text-slate-655">
                            <strong className="text-slate-800">Meta:</strong> {studioResult.seo.meta}
                          </p>
                        </div>
                        <button
                          type="button"
                          onClick={() => copyText(studioResult.seo.title + '\n\n' + studioResult.seo.meta + '\n\n' + studioResult.seo.content)}
                          className="w-full text-center py-2 border border-slate-200 hover:border-primary text-slate-600 hover:text-primary rounded-xl text-xs font-bold transition cursor-pointer focus:outline-none shadow-sm bg-white"
                        >
                          <i className="fa-solid fa-copy mr-1" />
                          <span>Sao chép bài SEO Website</span>
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */}
      {/* 3. TAB: LỊCH SỬ CHIẾN DỊCH */}
      {/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */}
      {localTab === 'history' && (
        <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
          <div className="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 className="font-bold text-slate-800 text-sm flex items-center gap-2">
              <i className="fa-solid fa-clock-rotate-left text-amber-500 text-base" />
              <span>Các chiến dịch đã lưu</span>
            </h3>
            <button
              type="button"
              onClick={loadHistory}
              className="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-lg cursor-pointer focus:outline-none transition border border-transparent hover:border-slate-100"
              title="Tải lại"
            >
              <i className={`fa-solid fa-rotate ${loadingHistory ? 'animate-spin' : ''}`} />
            </button>
          </div>

          {/* Loader */}
          {loadingHistory && historyCampaigns.length === 0 && (
            <div className="py-12 text-center text-slate-400 text-xs">
              <i className="fa-solid fa-spinner animate-spin text-base mb-2 text-primary" />
              <p className="font-bold uppercase tracking-wider">Đang tải lịch sử...</p>
            </div>
          )}

          {/* History list */}
          {!loadingHistory && historyCampaigns.length > 0 && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {historyCampaigns.map((camp) => (
                <div key={camp.id} className="p-4 bg-slate-50 border border-slate-150 hover:border-slate-200 rounded-2xl flex items-center justify-between gap-4 transition shadow-sm text-left">
                  <div className="space-y-1.5 flex-grow">
                    <div className="flex items-center gap-2 flex-wrap">
                      {/* Type badge */}
                      <span
                        className={`inline-block text-[9px] font-black uppercase tracking-wider px-2 py-0.5 border rounded-md ${
                          camp.type === 'marketing'
                            ? 'bg-blue-50 text-blue-600 border-blue-200'
                            : 'bg-emerald-50 text-emerald-600 border-emerald-200'
                        }`}
                      >
                        {camp.type === 'marketing' ? 'AI Marketing' : 'Content Studio'}
                      </span>

                      {/* Date */}
                      <span className="text-[10px] text-slate-400 font-bold">
                        {new Date(camp.createdAt).toLocaleDateString('vi-VN')} {new Date(camp.createdAt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </div>

                    <h4 className="font-black text-slate-800 text-xs">{camp.title}</h4>
                    <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider flex gap-3">
                      <span>
                        Tone: <span className="text-slate-600">{camp.tone}</span>
                      </span>
                      {camp.goal && (
                        <span>
                          Mục tiêu: <span className="text-slate-600">{camp.goal}</span>
                        </span>
                      )}
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex gap-1.5 flex-shrink-0">
                    <button
                      type="button"
                      onClick={() => viewCampaign(camp)}
                      className="p-2 bg-white border border-slate-200 hover:border-primary text-slate-550 hover:text-primary rounded-xl transition cursor-pointer focus:outline-none shadow-sm"
                      title="Xem chi tiết"
                    >
                      <i className="fa-solid fa-eye text-xs" />
                    </button>
                    <button
                      type="button"
                      onClick={() => deleteCampaign(camp.id)}
                      className="p-2 bg-white border border-slate-200 hover:border-red-500 text-slate-550 hover:text-red-500 rounded-xl transition cursor-pointer focus:outline-none shadow-sm"
                      title="Xóa"
                    >
                      <i className="fa-solid fa-trash text-xs" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Empty state */}
          {!loadingHistory && historyCampaigns.length === 0 && (
            <div className="py-12 text-center text-slate-400 space-y-3">
              <div className="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-lg text-slate-400">
                <i className="fa-solid fa-folder-open animate-pulse" />
              </div>
              <div className="space-y-1">
                <h4 className="font-bold text-slate-700 text-xs">Lịch sử trống</h4>
                <p className="text-[10px] text-slate-400 font-semibold">Bạn chưa khởi tạo chiến dịch AI nào. Hãy tạo một chiến dịch mới ở trên!</p>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  )
}
