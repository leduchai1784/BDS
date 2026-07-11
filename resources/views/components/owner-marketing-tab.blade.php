@php
    $myPropertiesList = $myProperties ?? [];
@endphp

<!-- Custom Styling for AI Waveform & Preloaders -->
<style>
    @keyframes soundWave {
        0%, 100% { height: 4px; }
        50% { height: 24px; }
    }
    .wave-bar {
        width: 3px;
        height: 6px;
        background-color: #0077bb;
        border-radius: 9999px;
        transition: all 0.2s ease;
    }
    .wave-active {
        animation: soundWave 1.2s ease-in-out infinite;
    }
    .wave-bar:nth-child(1) { animation-delay: 0.1s; }
    .wave-bar:nth-child(2) { animation-delay: 0.3s; }
    .wave-bar:nth-child(3) { animation-delay: 0.5s; }
    .wave-bar:nth-child(4) { animation-delay: 0.2s; }
    .wave-bar:nth-child(5) { animation-delay: 0.4s; }
    .wave-bar:nth-child(6) { animation-delay: 0.6s; }
</style>

<div 
    x-data="{
        localTab: 'marketing', // 'marketing', 'content_studio', 'history'
        
        // Property listings
        properties: {{ json_encode($myPropertiesList) }},
        selectedPropertyId: '',
        campaignGoal: 'rent_fast',
        campaignTone: 'friendly',
        
        // AI Marketing Generation State
        generating: false,
        hasResults: false,
        progress: 0,
        currentStep: '',
        activeResultTab: 'facebook',
        
        // AI Marketing Results
        facebookPosts: [],
        tiktokScripts: [],
        seoArticles: [],
        emailTemplates: [],
        smsTemplates: [],
        prompts: [],

        // AI Content Studio Inputs
        studioTitle: '',
        studioTxType: 'rent',
        studioPropType: 'Căn hộ chung cư',
        studioPrice: '',
        studioArea: '',
        studioAddress: '',
        studioHighlights: '',
        studioTone: 'friendly',

        // AI Content Studio Generation State
        generatingStudio: false,
        hasStudioResults: false,
        progressStudio: 0,
        currentStepStudio: '',
        
        // AI Content Studio Results
        studioResult: null,
        activeStudioTab: 'posts', // 'posts', 'videos', 'voice', 'thumbnail', 'seo'
        activeStudioPostIndex: 0,
        
        // AI Voiceover (Text-to-Speech)
        speaking: false,
        speechSpeed: 1,
        speechUtterance: null,
        
        // AI Thumbnail State
        thumbnailLoaded: false,
        
        // History State
        historyCampaigns: [],
        loadingHistory: false,

        init() {
            // Prime voices list for browser TTS
            if (typeof window !== 'undefined' && window.speechSynthesis) {
                window.speechSynthesis.getVoices();
            }
            this.loadHistory();
        },

        // Tải lịch sử chiến dịch từ DB
        async loadHistory() {
            this.loadingHistory = true;
            try {
                let res = await fetch('/owner/ai/campaigns/history');
                let data = await res.json();
                if (data.success) {
                    this.historyCampaigns = data.campaigns;
                }
            } catch (err) {
                console.error('Failed to load campaign history:', err);
            } finally {
                this.loadingHistory = false;
            }
        },

        // Xem lại chiến dịch cũ từ Lịch sử
        viewCampaign(camp) {
            if (camp.type === 'marketing') {
                this.facebookPosts = camp.content.facebook || [];
                this.tiktokScripts = camp.content.tiktok || [];
                this.seoArticles = camp.content.seo || [];
                this.emailTemplates = camp.content.email?.emailTemplates || [];
                this.smsTemplates = camp.content.email?.smsTemplates || [];
                this.prompts = camp.content.email?.prompts || [];
                
                this.selectedPropertyId = camp.property_id || '';
                this.campaignGoal = camp.goal || 'rent_fast';
                this.campaignTone = camp.tone || 'friendly';
                
                this.hasResults = true;
                this.localTab = 'marketing';
                this.activeResultTab = 'facebook';
                triggerToast('Đã tải thành công chiến dịch AI Marketing từ lịch sử.');
            } else if (camp.type === 'content_studio') {
                this.studioResult = camp.content;
                this.studioTitle = camp.title.replace('Studio: ', '');
                this.studioTone = camp.tone;
                
                this.hasStudioResults = true;
                this.localTab = 'content_studio';
                this.activeStudioTab = 'posts';
                this.activeStudioPostIndex = 0;
                this.thumbnailLoaded = false;
                triggerToast('Đã tải thành công gói Content Studio từ lịch sử.');
            }
        },

        // Xóa chiến dịch
        async deleteCampaign(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa chiến dịch này khỏi lịch sử?')) return;
            try {
                const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content') || '';
                let res = await fetch(`/owner/ai/campaign/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                let data = await res.json();
                if (data.success) {
                    triggerToast('Đã xóa chiến dịch thành công.');
                    this.loadHistory();
                } else {
                    triggerToast('Lỗi: ' + data.message);
                }
            } catch (err) {
                console.error(err);
                triggerToast('Không thể kết nối đến máy chủ.');
            }
        },

        // Khởi chạy tiến trình sinh content Marketing Đa Kênh (gọi AJAX tuần tự)
        async startGeneration() {
            if (!this.selectedPropertyId) {
                triggerToast('Vui lòng chọn bất động sản nguồn!');
                return;
            }
            
            this.generating = true;
            this.hasResults = false;
            this.progress = 0;
            this.currentStep = 'Đang phân tích thông số bất động sản nguồn...';
            
            try {
                const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content') || '';
                
                // Bước 1: Facebook (0% -> 25%)
                this.progress = 10;
                this.currentStep = 'AI đang soạn thảo 20 bài đăng Facebook đa dạng góc nhìn...';
                let resFb = await fetch('/owner/ai/marketing/facebook', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        property_id: this.selectedPropertyId,
                        campaign_goal: this.campaignGoal,
                        campaign_tone: this.campaignTone
                    })
                });
                let dataFb = await resFb.json();
                if (!dataFb.success) throw new Error(dataFb.message || 'Lỗi sinh Facebook posts');
                this.facebookPosts = dataFb.data;
                
                // Bước 2: TikTok (25% -> 50%)
                this.progress = 35;
                this.currentStep = 'AI đang xây dựng kịch bản cho 10 video ngắn TikTok/Shorts...';
                let resTt = await fetch('/owner/ai/marketing/tiktok', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        property_id: this.selectedPropertyId,
                        campaign_goal: this.campaignGoal,
                        campaign_tone: this.campaignTone
                    })
                });
                let dataTt = await resTt.json();
                if (!dataTt.success) throw new Error(dataTt.message || 'Lỗi sinh TikTok scripts');
                this.tiktokScripts = dataTt.data;
                
                // Bước 3: SEO Website (50% -> 75%)
                this.progress = 60;
                this.currentStep = 'AI đang viết 5 bài viết chuẩn SEO Website dạng HTML...';
                let resSeo = await fetch('/owner/ai/marketing/seo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        property_id: this.selectedPropertyId,
                        campaign_goal: this.campaignGoal,
                        campaign_tone: this.campaignTone
                    })
                });
                let dataSeo = await resSeo.json();
                if (!dataSeo.success) throw new Error(dataSeo.message || 'Lỗi sinh bài viết SEO');
                this.seoArticles = dataSeo.data;
                
                // Bước 4: Email, SMS & Banners (75% -> 90%)
                this.progress = 80;
                this.currentStep = 'AI đang thiết kế Email, SMS và Banner prompts...';
                let resEmail = await fetch('/owner/ai/marketing/email-sms', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        property_id: this.selectedPropertyId,
                        campaign_goal: this.campaignGoal,
                        campaign_tone: this.campaignTone
                    })
                });
                let dataEmail = await resEmail.json();
                if (!dataEmail.success) throw new Error(dataEmail.message || 'Lỗi sinh Email/SMS');
                
                this.emailTemplates = dataEmail.data.emailTemplates || [];
                this.smsTemplates = dataEmail.data.smsTemplates || [];
                this.prompts = dataEmail.data.prompts || [];
                
                // Bước 5: Autosave Campaign (90% -> 100%)
                this.progress = 90;
                this.currentStep = 'Đang lưu trữ chiến dịch vào Lịch sử...';
                
                let propertyTitle = 'Chiến dịch BĐS';
                if (this.selectedPropertyId === 'mock_prop_1') propertyTitle = 'Căn hộ dịch vụ Hà Đô Centrosa';
                else if (this.selectedPropertyId === 'mock_prop_2') propertyTitle = 'Nhà nguyên căn Lê Quang Định';
                else {
                    let matched = this.properties.find(p => p.id === this.selectedPropertyId);
                    if (matched) propertyTitle = matched.title;
                }

                await fetch('/owner/ai/campaign/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        type: 'marketing',
                        property_id: this.selectedPropertyId,
                        title: 'Chiến dịch: ' + propertyTitle,
                        goal: this.campaignGoal,
                        tone: this.campaignTone,
                        content: {
                            facebook: this.facebookPosts,
                            tiktok: this.tiktokScripts,
                            seo: this.seoArticles,
                            email: dataEmail.data
                        }
                    })
                });
                
                this.progress = 100;
                this.generating = false;
                this.hasResults = true;
                this.activeResultTab = 'facebook';
                triggerToast('Khởi tạo chiến dịch Marketing AI Đa Kênh thành công!');
                this.loadHistory(); // Tải lại lịch sử
                
            } catch (err) {
                console.error(err);
                this.generating = false;
                triggerToast('Lỗi khi khởi tạo AI: ' + err.message);
            }
        },

        // Khởi chạy sinh nhanh Content Studio tự do
        async startStudioGeneration() {
            if (!this.studioTitle || !this.studioAddress) {
                triggerToast('Vui lòng nhập Tiêu đề và Địa chỉ!');
                return;
            }

            this.generatingStudio = true;
            this.hasStudioResults = false;
            this.progressStudio = 20;
            this.currentStepStudio = 'AI đang thiết kế trọn bộ Content Studio...';
            
            try {
                const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content') || '';
                
                let res = await fetch('/owner/ai/content-studio/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        title: this.studioTitle,
                        transaction_type: this.studioTxType,
                        property_type: this.studioPropType,
                        price: this.studioPrice,
                        area: this.studioArea,
                        address: this.studioAddress,
                        highlights: this.studioHighlights,
                        tone: this.studioTone
                    })
                });
                
                let data = await res.json();
                if (!data.success) throw new Error(data.message || 'Lỗi tạo Content Studio');
                
                this.studioResult = data.data;
                this.thumbnailLoaded = false;
                
                this.progressStudio = 80;
                this.currentStepStudio = 'Đang tự động lưu gói nội dung vào Lịch sử...';
                
                await fetch('/owner/ai/campaign/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        type: 'content_studio',
                        property_id: null,
                        title: 'Studio: ' + this.studioTitle,
                        goal: null,
                        tone: this.studioTone,
                        content: this.studioResult
                    })
                });
                
                this.progressStudio = 100;
                this.generatingStudio = false;
                this.hasStudioResults = true;
                this.activeStudioTab = 'posts';
                this.activeStudioPostIndex = 0;
                triggerToast('Tạo gói Content Studio thành công!');
                this.loadHistory(); // Tải lại lịch sử
                
            } catch (err) {
                console.error(err);
                this.generatingStudio = false;
                triggerToast('Lỗi AI Content Studio: ' + err.message);
            }
        },

        // Phát giọng nói thoại bằng Web Speech API
        speakVoiceover(text) {
            if (this.speaking) {
                window.speechSynthesis.cancel();
                this.speaking = false;
                return;
            }
            
            if (!text) return;
            
            // Làm sạch văn bản khỏi HTML tags và Markdown để đọc mượt mà hơn
            let cleanText = text.replace(/<[^>]*>/g, '').replace(/[#\*_\-\[\]\(\)]/g, '').trim();
            
            const utterance = new SpeechSynthesisUtterance(cleanText);
            utterance.lang = 'vi-VN';
            utterance.rate = parseFloat(this.speechSpeed);
            
            // Tìm kiếm giọng nói tiếng Việt trong danh sách trình duyệt
            const voices = window.speechSynthesis.getVoices();
            const viVoice = voices.find(v => v.lang.includes('vi') || v.lang.includes('VI'));
            if (viVoice) {
                utterance.voice = viVoice;
            }
            
            utterance.onend = () => {
                this.speaking = false;
            };
            
            utterance.onerror = () => {
                this.speaking = false;
            };
            
            this.speaking = true;
            window.speechSynthesis.speak(utterance);
        },

        // Hàm copy text nhanh
        copyText(text) {
            navigator.clipboard.writeText(text);
            triggerToast('Đã sao chép nội dung vào Clipboard!');
        }
    }"
    class="space-y-6 text-left"
>
    <!-- Sub-tab switcher -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">AI Marketing & Studio</h2>
            <p class="text-xs text-slate-400 mt-1 font-semibold">Tự động hóa quảng bá, sáng tạo nội dung và lưu trữ chiến dịch thông minh.</p>
        </div>
        
        <!-- Local Navigation Buttons -->
        <div class="flex bg-slate-100 p-1 rounded-xl items-center gap-1">
            <button 
                type="button"
                @click="localTab = 'marketing'"
                :class="localTab === 'marketing' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition focus:outline-none cursor-pointer"
            >
                <i class="fa-solid fa-wand-magic-sparkles mr-1 text-primary"></i> AI Marketing Đa Kênh
            </button>
            <button 
                type="button"
                @click="localTab = 'content_studio'"
                :class="localTab === 'content_studio' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition focus:outline-none cursor-pointer"
            >
                <i class="fa-solid fa-photo-film mr-1 text-emerald-500"></i> AI Content Studio
            </button>
            <button 
                type="button"
                @click="localTab = 'history'; loadHistory();"
                :class="localTab === 'history' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition focus:outline-none cursor-pointer flex items-center gap-1.5"
            >
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Lịch sử
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-black bg-slate-200 text-slate-600 rounded-md" x-text="historyCampaigns.length">0</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 1. TAB: AI MARKETING ĐA KÊNH -->
    <!-- ========================================== -->
    <div x-show="localTab === 'marketing'" x-cloak>
        <!-- Generator Panel (Form) -->
        <div x-show="!generating && !hasResults" class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Chọn Bất động sản -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Chọn bất động sản nguồn</label>
                    <select 
                        x-model="selectedPropertyId"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                    >
                        <option value="">-- Chọn bất động sản của bạn --</option>
                        <template x-for="prop in properties" :key="prop.id">
                            <option :value="prop.id" x-text="prop.title + ' (' + (prop.price_label || prop.price) + ')'"></option>
                        </template>
                        <!-- Fallbacks -->
                        <option value="mock_prop_1">Căn hộ dịch vụ Hà Đô Centrosa Quận 10 (14.5tr/tháng)</option>
                        <option value="mock_prop_2">Nhà nguyên căn Hẻm xe hơi Lê Quang Định Bình Thạnh (4.2 tỷ)</option>
                    </select>
                </div>

                <!-- Chọn Mục tiêu chiến dịch -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Mục tiêu chiến dịch</label>
                    <select 
                        x-model="campaignGoal"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                    >
                        <option value="rent_fast">Đăng tin cho thuê nhanh (Đặc điểm nổi bật)</option>
                        <option value="luxury_brand">Xây dựng thương hiệu căn hộ cao cấp</option>
                        <option value="price_deal">Chương trình ưu đãi giảm giá / Cắt lỗ gấp</option>
                        <option value="review_detail">Bài viết Review trải nghiệm chi tiết</option>
                    </select>
                </div>

                <!-- Chọn Tone giọng AI -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Giọng văn của AI</label>
                    <select 
                        x-model="campaignTone"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                    >
                        <option value="friendly">Thân thiện, cởi mở (Phù hợp Facebook/TikTok)</option>
                        <option value="professional">Chuyên nghiệp, đáng tin cậy (Phù hợp SEO/Email)</option>
                        <option value="funny">Hài hước, bắt trend độc lạ</option>
                        <option value="emotional">Gợi mở cảm xúc, chạm tâm lý tìm tổ ấm</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    @click="startGeneration()"
                    class="px-6 py-3 bg-gradient-to-r from-primary to-primary-hover hover:shadow-primary/30 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md cursor-pointer active:scale-98"
                >
                    <i class="fa-solid fa-wand-magic-sparkles text-sm animate-pulse"></i> Khởi tạo chiến dịch Marketing AI
                </button>
            </div>
        </div>

        <!-- Generating Loader Screen -->
        <div x-show="generating" class="bg-white border border-slate-100 rounded-3xl p-12 shadow-sm text-center space-y-6" x-cloak x-transition>
            <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto text-xl relative">
                <i class="fa-solid fa-robot animate-bounce"></i>
                <span class="absolute inset-0 rounded-full border-2 border-primary border-t-transparent animate-spin"></span>
            </div>
            
            <div class="space-y-2 max-w-md mx-auto">
                <h4 class="font-extrabold text-slate-700 text-sm">Trình sáng tạo AI đang làm việc...</h4>
                <p class="text-[11px] text-slate-400 font-semibold" x-text="currentStep"></p>
            </div>

            <!-- Progress Bar -->
            <div class="max-w-md mx-auto bg-slate-100 h-2 rounded-full overflow-hidden">
                <div class="bg-primary h-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
            </div>
            <span class="inline-block text-[11px] font-black text-primary bg-primary-light px-2.5 py-1 rounded-full" x-text="progress + '%'"></span>
        </div>

        <!-- Results Panel -->
        <div x-show="hasResults" class="space-y-6" x-cloak x-transition>
            <!-- Meta top bar -->
            <div class="bg-slate-50 border border-slate-150 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-primary text-base shadow-sm">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">BĐS nguồn</span>
                        <h4 class="font-bold text-slate-800 text-xs mt-0.5" x-text="selectedPropertyId === 'mock_prop_1' ? 'Căn hộ dịch vụ Hà Đô Centrosa' : (selectedPropertyId === 'mock_prop_2' ? 'Nhà nguyên căn Lê Quang Định' : 'Bất động sản đã chọn')"></h4>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="hasResults = false; selectedPropertyId = ''"
                    class="px-4 py-2 bg-white border border-slate-200 hover:border-red-500 text-slate-600 hover:text-red-500 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <i class="fa-solid fa-arrow-left"></i> Tạo chiến dịch mới
                </button>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Tab headers -->
                <div class="bg-white border border-slate-100 rounded-3xl p-3 shadow-sm h-fit flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible gap-1.5 scrollbar-none">
                    <button 
                        @click="activeResultTab = 'facebook'"
                        :class="activeResultTab === 'facebook' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                        class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                    >
                        <i class="fa-brands fa-facebook text-sm text-blue-600"></i> Facebook (20 bài)
                    </button>
                    <button 
                        @click="activeResultTab = 'tiktok'"
                        :class="activeResultTab === 'tiktok' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                        class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                    >
                        <i class="fa-brands fa-tiktok text-sm text-slate-800"></i> TikTok/Shorts (10 kịch bản)
                    </button>
                    <button 
                        @click="activeResultTab = 'seo'"
                        :class="activeResultTab === 'seo' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                        class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                    >
                        <i class="fa-solid fa-file-word text-sm text-sky-500"></i> SEO Articles (5 bài)
                    </button>
                    <button 
                        @click="activeResultTab = 'email'"
                        :class="activeResultTab === 'email' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                        class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                    >
                        <i class="fa-solid fa-envelope text-sm text-rose-500"></i> Email & SMS
                    </button>
                    <button 
                        @click="activeResultTab = 'prompts'"
                        :class="activeResultTab === 'prompts' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                        class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                    >
                        <i class="fa-solid fa-image text-sm text-emerald-500"></i> Banner Prompts
                    </button>
                </div>

                <!-- Tab content -->
                <div class="lg:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm min-h-[400px]">
                    <!-- FACEBOOK -->
                    <div x-show="activeResultTab === 'facebook'" class="space-y-6" x-transition>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-brands fa-facebook text-blue-650 text-base"></i> Bài đăng Facebook hàng tuần
                            </h3>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Tạo bởi Gemini AI</span>
                        </div>

                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                            <template x-for="post in facebookPosts" :key="post.id">
                                <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[9px] font-black text-primary bg-primary-light/40 px-2 py-0.5 rounded-md uppercase" x-text="'Bài đăng #' + post.id"></span>
                                        <button 
                                            type="button" 
                                            @click="copyText(post.title + '\n\n' + post.content)"
                                            class="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                        >
                                            <i class="fa-solid fa-copy"></i> Sao chép bài
                                        </button>
                                    </div>
                                    <h4 class="font-black text-slate-800 text-xs" x-text="post.title"></h4>
                                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="post.content"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- TIKTOK -->
                    <div x-show="activeResultTab === 'tiktok'" class="space-y-6" x-transition>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-brands fa-tiktok text-slate-900 text-base"></i> Kịch bản Video TikTok ngắn (60s)
                            </h3>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">10 ý tưởng kịch bản</span>
                        </div>

                        <div class="space-y-5 max-h-[500px] overflow-y-auto pr-1">
                            <template x-for="script in tiktokScripts" :key="script.id">
                                <div class="border border-slate-150 rounded-2xl overflow-hidden shadow-sm">
                                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                                        <h4 class="font-bold text-slate-700 text-xs" x-text="'Kịch bản #' + script.id + ': ' + script.title"></h4>
                                        <button 
                                            type="button" 
                                            @click="copyText('Kịch bản: ' + script.title + '\n\n[Visual]: ' + script.visual + '\n\n[Voiceover]: ' + script.audio)"
                                            class="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                        >
                                            <i class="fa-solid fa-copy"></i> Sao chép kịch bản
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 text-xs">
                                        <div class="p-4 border-b md:border-b-0 md:border-r border-slate-100 space-y-2">
                                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Hình ảnh gợi ý (Visual)</span>
                                            <p class="text-slate-600 leading-relaxed font-semibold" x-text="script.visual"></p>
                                        </div>
                                        <div class="p-4 space-y-3">
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Lời thoại Voiceover (Audio)</span>
                                                <p class="text-slate-700 leading-relaxed font-bold bg-primary-light/30 p-2.5 rounded-lg border border-primary/10" x-text="script.audio"></p>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Chữ hiển thị (Overlay)</span>
                                                <p class="text-slate-500 leading-normal font-mono" x-text="script.overlay"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- SEO ARTICLES -->
                    <div x-show="activeResultTab === 'seo'" class="space-y-6" x-transition>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-file-word text-blue-500 text-base"></i> Bài viết chuẩn SEO Website
                            </h3>
                            <span class="text-[10px] font-bold text-slate-400">5 bài viết chuyên sâu</span>
                        </div>

                        <div class="space-y-6 max-h-[500px] overflow-y-auto pr-1">
                            <template x-for="(art, idx) in seoArticles" :key="idx">
                                <div class="p-5 border border-slate-150 rounded-2xl space-y-4 text-left">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-black text-slate-800 text-sm" x-text="art.title"></h4>
                                        <button 
                                            type="button" 
                                            @click="copyText(art.title + '\n\n' + art.content)"
                                            class="px-2 py-1 bg-slate-50 hover:bg-primary-light border border-slate-200 hover:border-primary/20 text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                        >
                                            <i class="fa-solid fa-copy"></i> Sao chép
                                        </button>
                                    </div>
                                    <!-- Meta fields -->
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-150 space-y-1 text-xs">
                                        <p class="text-slate-500 leading-normal"><strong class="text-slate-700">Meta Title:</strong> <span x-text="art.title"></span></p>
                                        <p class="text-slate-500 leading-normal"><strong class="text-slate-700">Meta Description:</strong> <span x-text="art.meta"></span></p>
                                    </div>
                                    <!-- Content HTML -->
                                    <div class="prose prose-slate max-w-none text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" x-html="art.content"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- EMAIL & SMS -->
                    <div x-show="activeResultTab === 'email'" class="space-y-6" x-transition>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-primary text-base"></i> Bản tin Email & Tin nhắn SMS
                            </h3>
                        </div>

                        <div class="space-y-5 max-h-[500px] overflow-y-auto pr-1 text-xs">
                            <!-- Email -->
                            <template x-for="email in emailTemplates" :key="email.subject">
                                <div class="border border-slate-150 rounded-2xl overflow-hidden">
                                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                                        <span class="font-extrabold text-slate-700 text-xs">Mẫu Email Chăm sóc Khách hàng</span>
                                        <button 
                                            type="button" 
                                            @click="copyText(email.subject + '\n\n' + email.content)"
                                            class="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                        >
                                            <i class="fa-solid fa-copy"></i> Sao chép Email
                                        </button>
                                    </div>
                                    <div class="p-4 space-y-3 bg-slate-50/20 text-left">
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase">Tiêu đề (Subject)</span>
                                            <p class="font-black text-slate-800" x-text="email.subject"></p>
                                        </div>
                                        <div class="border-t border-slate-100 pt-3">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nội dung (Body)</span>
                                            <p class="text-slate-650 leading-relaxed whitespace-pre-line bg-white p-3 rounded-xl border border-slate-100" x-text="email.content"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- SMS -->
                            <div class="p-4 border border-slate-150 rounded-2xl space-y-3">
                                <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">SMS / Zalo ZNS ngắn gọn</span>
                                <div class="space-y-2">
                                    <template x-for="(sms, idx) in smsTemplates" :key="idx">
                                        <div class="p-3 bg-slate-50 border border-slate-150 rounded-xl flex items-center justify-between gap-4">
                                            <p class="font-mono text-slate-700 leading-normal" x-text="sms"></p>
                                            <button 
                                                type="button" 
                                                @click="copyText(sms)"
                                                class="p-2 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg transition flex-shrink-0 cursor-pointer focus:outline-none"
                                            >
                                                <i class="fa-solid fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BANNER PROMPTS -->
                    <div x-show="activeResultTab === 'prompts'" class="space-y-6" x-transition>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-image text-emerald-500 text-base"></i> Prompts thiết kế Banner quảng cáo
                            </h3>
                        </div>

                        <div class="space-y-4 text-xs">
                            <p class="text-slate-550 leading-relaxed font-medium">Sao chép các prompt tiếng Anh chi tiết bên dưới để đưa vào các công cụ sinh ảnh AI (như Midjourney, DALL-E, Bing Image Creator) để vẽ ảnh truyền thông chất lượng cao:</p>
                            
                            <template x-for="(prompt, index) in prompts" :key="index">
                                <div class="p-4 bg-slate-50 border border-slate-150 rounded-xl space-y-3 text-left">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[9px] font-black text-slate-400 uppercase" x-text="'Prompt #' + (index + 1)"></span>
                                        <button 
                                            type="button" 
                                            @click="copyText(prompt)"
                                            class="px-2.5 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                        >
                                            <i class="fa-solid fa-copy"></i> Sao chép câu lệnh
                                        </button>
                                    </div>
                                    <p class="font-mono text-slate-700 leading-relaxed bg-white p-3 rounded-lg border border-slate-100" x-text="prompt"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. TAB: AI CONTENT STUDIO -->
    <!-- ========================================== -->
    <div x-show="localTab === 'content_studio'" x-cloak>
        <!-- Studio Form (If no results generated yet) -->
        <div x-show="!generatingStudio && !hasStudioResults" class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tiêu đề -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Tên / Tiêu đề dự án BĐS</label>
                    <input 
                        type="text" 
                        x-model="studioTitle"
                        placeholder="Ví dụ: Villa song lập Diamond Island Quận 2"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                    />
                </div>

                <!-- Địa chỉ -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Địa chỉ chi tiết</label>
                    <input 
                        type="text" 
                        x-model="studioAddress"
                        placeholder="Ví dụ: Đường số 5, KDC Bình Trưng Tây, Quận 2, TP. Thủ Đức"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                    />
                </div>

                <!-- Loại giao dịch -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Loại giao dịch</label>
                    <select 
                        x-model="studioTxType"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                    >
                        <option value="rent">Cho thuê bất động sản</option>
                        <option value="sale">Bán bất động sản</option>
                    </select>
                </div>

                <!-- Loại hình BĐS -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Loại hình BĐS</label>
                    <select 
                        x-model="studioPropType"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
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

                <!-- Giá cả -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Giá yêu cầu (Thuê/Bán)</label>
                    <input 
                        type="text" 
                        x-model="studioPrice"
                        placeholder="Ví dụ: 12 triệu/tháng hoặc 8.5 tỷ"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                    />
                </div>

                <!-- Diện tích -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Diện tích (m²)</label>
                    <input 
                        type="text" 
                        x-model="studioArea"
                        placeholder="Ví dụ: 95m2 (2 phòng ngủ, 2wc)"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                    />
                </div>

                <!-- Tone giọng -->
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Giọng văn của AI</label>
                    <select 
                        x-model="studioTone"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition cursor-pointer"
                    >
                        <option value="friendly">Thân thiện, cởi mở (Phù hợp Facebook/TikTok)</option>
                        <option value="professional">Chuyên nghiệp, uy tín (Phù hợp Website/Email)</option>
                        <option value="funny">Hài hước, dí dỏm độc lạ</option>
                        <option value="emotional">Truyền cảm hứng, tạo cảm xúc tổ ấm</option>
                    </select>
                </div>
            </div>

            <!-- Đặc điểm nổi bật -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Đặc điểm nổi bật / Tiện ích / Ghi chú khác</label>
                <textarea 
                    rows="3"
                    x-model="studioHighlights"
                    placeholder="Ví dụ: Full nội thất ngoại nhập, view trực diện sông Sài Gòn, hồ bơi tràn bờ nước mặn, khu compound an ninh khép kín, tặng gói voucher phí quản lý 1 năm..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:border-primary focus:outline-none transition"
                ></textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    @click="startStudioGeneration()"
                    class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:shadow-emerald-500/30 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md cursor-pointer active:scale-98"
                >
                    <i class="fa-solid fa-photo-film text-sm"></i> Tạo nội dung AI Studio
                </button>
            </div>
        </div>

        <!-- Studio Loader -->
        <div x-show="generatingStudio" class="bg-white border border-slate-100 rounded-3xl p-12 shadow-sm text-center space-y-6" x-cloak x-transition>
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl relative">
                <i class="fa-solid fa-photo-film animate-spin"></i>
            </div>
            
            <div class="space-y-2 max-w-sm mx-auto">
                <h4 class="font-extrabold text-slate-700 text-sm">AI Content Studio đang xử lý dữ liệu...</h4>
                <p class="text-[11px] text-slate-400 font-semibold" x-text="currentStepStudio"></p>
            </div>

            <div class="max-w-md mx-auto bg-slate-100 h-2 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full transition-all duration-300" :style="'width: ' + progressStudio + '%'"></div>
            </div>
            <span class="inline-block text-[11px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full" x-text="progressStudio + '%'"></span>
        </div>

        <!-- Studio Results -->
        <div x-show="hasStudioResults && studioResult" class="space-y-6" x-cloak x-transition>
            <!-- Top bar -->
            <div class="bg-slate-50 border border-slate-150 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-emerald-600 text-base shadow-sm">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Đang hiển thị kết quả Studio</span>
                        <h4 class="font-bold text-slate-800 text-xs mt-0.5" x-text="studioTitle"></h4>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="hasStudioResults = false; studioResult = null;"
                    class="px-4 py-2 bg-white border border-slate-200 hover:border-red-500 text-slate-600 hover:text-red-500 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <i class="fa-solid fa-arrow-left"></i> Tạo nội dung mới
                </button>
            </div>

            <!-- Content Studio Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT COLUMN: Posts & Video Scripts -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Posts Panel -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-brands fa-facebook text-blue-600 text-base"></i> Bài đăng Mạng xã hội
                            </h3>
                            
                            <!-- Post switcher -->
                            <div class="flex bg-slate-100 p-0.5 rounded-lg text-[10px]">
                                <button type="button" @click="activeStudioPostIndex = 0" :class="activeStudioPostIndex === 0 ? 'bg-white font-bold shadow-sm' : 'text-slate-500'" class="px-2.5 py-1 rounded-md focus:outline-none cursor-pointer">Bài 1</button>
                                <button type="button" @click="activeStudioPostIndex = 1" :class="activeStudioPostIndex === 1 ? 'bg-white font-bold shadow-sm' : 'text-slate-500'" class="px-2.5 py-1 rounded-md focus:outline-none cursor-pointer">Bài 2</button>
                                <button type="button" @click="activeStudioPostIndex = 2" :class="activeStudioPostIndex === 2 ? 'bg-white font-bold shadow-sm' : 'text-slate-500'" class="px-2.5 py-1 rounded-md focus:outline-none cursor-pointer">Bài 3</button>
                            </div>
                        </div>

                        <!-- Active Post Content -->
                        <template x-for="(post, index) in studioResult.posts" :key="index">
                            <div x-show="activeStudioPostIndex === index" class="space-y-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-left">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-black text-slate-800 text-xs" x-text="post.title"></h4>
                                    <button 
                                        type="button" 
                                        @click="copyText(post.title + '\n\n' + post.content)"
                                        class="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none shadow-sm"
                                    >
                                        <i class="fa-solid fa-copy"></i> Sao chép
                                    </button>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line" x-text="post.content"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Video Scripts Panel -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-brands fa-tiktok text-slate-900 text-base"></i> Kịch bản Video TikTok ngắn
                            </h3>
                        </div>

                        <div class="space-y-4 text-left">
                            <template x-for="vid in studioResult.videos" :key="vid.id">
                                <div class="border border-slate-150 rounded-2xl overflow-hidden">
                                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                                        <h4 class="font-bold text-slate-700 text-xs" x-text="'Kịch bản #' + vid.id + ': ' + vid.title"></h4>
                                        <button 
                                            type="button" 
                                            @click="copyText('Kịch bản: ' + vid.title + '\n\n[Visual]: ' + vid.visual + '\n\n[Voiceover]: ' + vid.audio)"
                                            class="px-2 py-0.5 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-md text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                        >
                                            <i class="fa-solid fa-copy"></i> Copy
                                        </button>
                                    </div>
                                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs bg-slate-50/10">
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mô tả hình ảnh</span>
                                            <p class="text-slate-600 leading-relaxed font-medium" x-text="vid.visual"></p>
                                        </div>
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lời thoại nói</span>
                                                <p class="text-slate-700 leading-relaxed font-bold bg-primary-light/10 p-2 rounded-lg border border-primary/5" x-text="vid.audio"></p>
                                            </div>
                                            <div>
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Chữ hiển thị</span>
                                                <p class="text-slate-500 font-mono text-[10px]" x-text="vid.overlay"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Voice, Thumbnail, Hashtags, SEO -->
                <div class="space-y-6 text-left">
                    <!-- AI Voiceover Panel (TTS) -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-microphone-lines text-primary text-base"></i> Giọng đọc AI Voice (Lời thoại)
                            </h3>
                        </div>

                        <!-- Audio Player Layout -->
                        <div class="bg-slate-50 border border-slate-150 rounded-2xl p-4 space-y-4">
                            <!-- Visualizer Waveform -->
                            <div class="h-10 flex items-center justify-center gap-1.5 bg-white border border-slate-100 rounded-xl px-4">
                                <div class="wave-bar" :class="speaking ? 'wave-active' : ''"></div>
                                <div class="wave-bar" :class="speaking ? 'wave-active' : ''"></div>
                                <div class="wave-bar" :class="speaking ? 'wave-active' : ''"></div>
                                <div class="wave-bar" :class="speaking ? 'wave-active' : ''"></div>
                                <div class="wave-bar" :class="speaking ? 'wave-active' : ''"></div>
                                <div class="wave-bar" :class="speaking ? 'wave-active' : ''"></div>
                            </div>

                            <!-- Play Controls -->
                            <div class="flex items-center justify-between gap-4">
                                <button 
                                    type="button" 
                                    @click="speakVoiceover(studioResult.voice_script)"
                                    :class="speaking ? 'bg-red-500 hover:bg-red-650' : 'bg-primary hover:bg-primary-hover'"
                                    class="w-10 h-10 rounded-full text-white flex items-center justify-center transition cursor-pointer focus:outline-none shadow-md shadow-primary/25"
                                >
                                    <i class="fa-solid" :class="speaking ? 'fa-stop' : 'fa-play ml-0.5'"></i>
                                </button>

                                <div class="flex-grow space-y-1">
                                    <div class="flex justify-between text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <span>Tốc độ đọc</span>
                                        <span x-text="speechSpeed + 'x'">1x</span>
                                    </div>
                                    <input 
                                        type="range" 
                                        min="0.5" 
                                        max="1.5" 
                                        step="0.1" 
                                        x-model="speechSpeed"
                                        class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary"
                                    />
                                </div>
                            </div>

                            <!-- Voice script text preview -->
                            <div class="text-xs text-slate-500 bg-white p-3 rounded-xl border border-slate-100 max-h-24 overflow-y-auto leading-relaxed whitespace-pre-line" x-text="studioResult.voice_script">
                            </div>
                        </div>
                    </div>

                    <!-- AI Thumbnail Panel -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-image text-emerald-500 text-base"></i> Ảnh Thumbnail AI vẽ
                            </h3>
                        </div>

                        <!-- Image Box with Pollinations.ai -->
                        <div class="relative overflow-hidden rounded-2xl border border-slate-150 shadow-sm aspect-video bg-slate-50 flex items-center justify-center">
                            <!-- Preloader Spinner -->
                            <div x-show="!thumbnailLoaded" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 text-slate-400 gap-2">
                                <i class="fa-solid fa-spinner animate-spin text-lg"></i>
                                <span class="text-[9px] font-black uppercase">Đang vẽ hình ảnh...</span>
                            </div>

                            <!-- Real Generated Image -->
                            <img 
                                :src="'https://image.pollinations.ai/prompt/' + encodeURIComponent(studioResult.thumbnail_prompt) + '?width=800&height=500&model=flux&nologo=true'" 
                                @load="thumbnailLoaded = true"
                                x-show="thumbnailLoaded"
                                class="w-full h-full object-cover transition-opacity duration-300"
                                :class="thumbnailLoaded ? 'opacity-100' : 'opacity-0'"
                                alt="Generated Thumbnail"
                            />
                        </div>

                        <!-- Prompt info -->
                        <div class="text-[10px] text-slate-500 bg-slate-50 border border-slate-150 p-3 rounded-xl">
                            <span class="block font-black text-slate-400 uppercase tracking-wider mb-1">Prompt tiếng Anh</span>
                            <p class="font-mono" x-text="studioResult.thumbnail_prompt"></p>
                        </div>
                    </div>

                    <!-- Hashtags & SEO -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-tags text-amber-500 text-base"></i> Bộ Hashtags & SEO
                            </h3>
                        </div>

                        <!-- Hashtags -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Hashtags đề xuất</span>
                                <button type="button" @click="copyText(studioResult.hashtags.join(' '))" class="text-[10px] font-bold text-primary hover:underline cursor-pointer focus:outline-none">Copy bộ tag</button>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="tag in studioResult.hashtags" :key="tag">
                                    <span class="inline-block text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md" x-text="tag"></span>
                                </template>
                            </div>
                        </div>

                        <!-- SEO Short Preview -->
                        <div class="border-t border-slate-100 pt-3 space-y-2 text-xs">
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider">SEO Title & Meta</span>
                            <div class="p-3 bg-slate-50 border border-slate-150 rounded-xl space-y-1.5">
                                <p class="text-slate-600"><strong class="text-slate-800">Title:</strong> <span x-text="studioResult.seo.title"></span></p>
                                <p class="text-slate-600"><strong class="text-slate-800">Meta:</strong> <span x-text="studioResult.seo.meta"></span></p>
                            </div>
                            <button 
                                type="button" 
                                @click="copyText(studioResult.seo.title + '\n\n' + studioResult.seo.meta + '\n\n' + studioResult.seo.content)"
                                class="w-full text-center py-2 border border-slate-200 hover:border-primary text-slate-600 hover:text-primary rounded-xl text-xs font-bold transition cursor-pointer focus:outline-none"
                            >
                                <i class="fa-solid fa-copy mr-1"></i> Sao chép toàn bộ bài SEO Website
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. TAB: LỊCH SỬ CHIẾN DỊCH -->
    <!-- ========================================== -->
    <div x-show="localTab === 'history'" x-cloak>
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-amber-500 text-base"></i> Các chiến dịch đã lưu
                </h3>
                <button type="button" @click="loadHistory()" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-lg cursor-pointer focus:outline-none" title="Refresh">
                    <i class="fa-solid fa-rotate animate-spin" x-show="loadingHistory"></i>
                    <i class="fa-solid fa-rotate" x-show="!loadingHistory"></i>
                </button>
            </div>

            <!-- Loader -->
            <div x-show="loadingHistory && historyCampaigns.length === 0" class="py-12 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-spinner animate-spin text-base mb-2"></i>
                <p class="font-bold uppercase tracking-wider">Đang tải lịch sử...</p>
            </div>

            <!-- History list -->
            <div x-show="!loadingHistory && historyCampaigns.length > 0" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="camp in historyCampaigns" :key="camp.id">
                        <div class="p-4 bg-slate-50 border border-slate-150 hover:border-slate-200 rounded-2xl flex items-center justify-between gap-4 transition shadow-sm text-left">
                            <div class="space-y-1.5 flex-grow">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <!-- Type badge -->
                                    <span 
                                        :class="camp.type === 'marketing' ? 'bg-primary-light text-primary border-primary/20' : 'bg-emerald-50 text-emerald-600 border-emerald-500/20'"
                                        class="inline-block text-[9px] font-black uppercase tracking-wider px-2 py-0.5 border rounded-md"
                                        x-text="camp.type === 'marketing' ? 'AI Marketing' : 'Content Studio'"
                                    ></span>
                                    
                                    <!-- Date -->
                                    <span class="text-[10px] text-slate-400 font-bold" x-text="new Date(camp.created_at).toLocaleDateString('vi-VN') + ' ' + new Date(camp.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})"></span>
                                </div>
                                <h4 class="font-black text-slate-800 text-xs" x-text="camp.title"></h4>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider flex gap-3">
                                    <span>Tone: <span class="text-slate-600" x-text="camp.tone"></span></span>
                                    <span x-show="camp.goal">Mục tiêu: <span class="text-slate-600" x-text="camp.goal"></span></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-1.5">
                                <button 
                                    type="button" 
                                    @click="viewCampaign(camp)"
                                    class="p-2 bg-white border border-slate-200 hover:border-primary text-slate-600 hover:text-primary rounded-xl transition cursor-pointer focus:outline-none"
                                    title="Xem chi tiết"
                                >
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                                <button 
                                    type="button" 
                                    @click="deleteCampaign(camp.id)"
                                    class="p-2 bg-white border border-slate-200 hover:border-red-500 text-slate-600 hover:text-red-500 rounded-xl transition cursor-pointer focus:outline-none"
                                    title="Xóa"
                                >
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Empty state -->
            <div x-show="!loadingHistory && historyCampaigns.length === 0" class="py-12 text-center text-slate-400 space-y-3" x-cloak>
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-lg text-slate-400">
                    <i class="fa-solid fa-folder-open animate-pulse"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="font-bold text-slate-700 text-xs">Lịch sử trống</h4>
                    <p class="text-[10px] text-slate-400 font-semibold">Bạn chưa khởi tạo chiến dịch AI nào. Hãy tạo một chiến dịch mới ở trên!</p>
                </div>
            </div>
        </div>
    </div>
</div>
