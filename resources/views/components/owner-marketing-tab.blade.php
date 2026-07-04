@php
    // Chuẩn bị danh sách BĐS để chọn nhanh trong dropdown
    $myPropertiesList = $myProperties ?? [];
@endphp

<div 
    x-data="{
        properties: {{ json_encode($myPropertiesList) }},
        selectedPropertyId: '',
        campaignGoal: 'rent_fast',
        campaignTone: 'friendly',
        generating: false,
        hasResults: false,
        progress: 0,
        currentStep: '',
        activeResultTab: 'facebook',
        facebookPosts: [
            {
                id: 1,
                title: '⚡ GÓC TÌM KIẾM - CĂN HỘ CAO CẤP FULL TIỆN ÍCH GIÁ TỐT ⚡',
                content: '📍 Vị trí cực đẹp tại khu trung tâm, di chuyển thuận lợi. Căn hộ vừa mới bàn giao, nội thất mới 100% chỉ việc xách vali vào ở!\n\n✨ Đặc điểm nổi bật:\n- Diện tích rộng rãi, ban công thoáng gió mát mẻ.\n- Full nội thất cao cấp: Tivi, Tủ lạnh, Máy giặt, Điều hòa, Giường đệm...\n- Tiện ích nội khu đầy đủ: Hồ bơi vô cực, Gym, khu vui chơi trẻ em, siêu thị tiện lợi.\n- An ninh bảo vệ 24/7, khóa vân tay an toàn tuyệt đối.\n\n💰 Giá thuê siêu hấp dẫn (Có thương lượng cho khách thiện chí dọn vào sớm).\n📞 Liên hệ ngay chính chủ để đi xem nhà trực tiếp 24/7!'
            },
            {
                id: 2,
                title: '🏡 NHÀ ĐẸP đón chủ mới - Giá thuê siêu rẻ nhất khu vực 🏡',
                content: '🌿 Bạn đang tìm kiếm không gian sống bình yên, tiện nghi nhưng giá cả phải chăng? Đây chính là sự lựa chọn hoàn hảo dành cho bạn và gia đình!\n\n💎 Căn hộ được thiết kế tối giản, đón nắng tự nhiên cực tốt. Nằm trong khu dân cư văn minh, dân trí cao, yên tĩnh.\n\n🚗 Hẻm trước nhà rộng rãi xe hơi đỗ cửa, gần chợ, trường học và các bệnh viện lớn trong bán kính 1km.\n\n📩 Inbox ngay để nhận bảng giá chi tiết và lịch hẹn xem nhà!'
            },
            {
                id: 3,
                title: '🔥 CHỈ CÒN DUY NHẤT 1 CĂN HỘ STUDIO TRUNG TÂM GIÁ CỰC HỜI 🔥',
                content: '✨ Căn hộ dịch vụ studio khép kín lý tưởng cho người đi làm và sinh viên văn minh. Không chung chủ, giờ giấc hoàn toàn tự do!\n\n🛋️ Nội thất đã sắm sửa đầy đủ tiện nghi, thiết kế gác lửng thông minh giúp nhân đôi diện tích sử dụng. Bếp nấu ăn riêng biệt không lo bám mùi.\n\n🛵 Bãi xe rộng rãi, hệ thống camera giám sát chặt chẽ đảm bảo an toàn tuyệt đối.\n\n🚀 Nhanh tay liên hệ vì căn hộ thường hết phòng rất nhanh!'
            }
        ],
        tiktokScripts: [
            {
                id: 1,
                title: 'Review Căn Hộ Thực Tế 60s',
                visual: 'Mở đầu: Cận cảnh mở cửa căn hộ, view từ ban công lộng gió nhìn ra thành phố. Sau đó lia máy nhanh qua phòng khách và bếp ăn hiện đại.',
                audio: 'Mọi người nghĩ căn hộ full tiện ích thế này ở trung tâm quận 10 thì giá bao nhiêu? Đảm bảo nghe giá xong sẽ bất ngờ! Đi xem cùng mình nhé...',
                overlay: 'Thuê nhà Quận 10 dưới 15tr? | Có thật không?'
            },
            {
                id: 2,
                title: 'Câu chuyện dọn nhà phòng trọ sinh viên',
                visual: 'Cảnh 1: Cận cảnh vali và đống hộp các tông. Cảnh 2: Chuyển cảnh nhanh sang căn hộ mini sạch sẽ có gác lửng lung linh.',
                audio: 'Mệt mỏi vì ở phòng trọ cũ ẩm mốc chật hẹp? Mình đã quyết định đổi đời khi dọn qua chung cư mini này. Rộng rãi, sạch sẽ lại tự do!',
                overlay: 'Tạm biệt phòng trọ cũ! | Review phòng trọ gác lửng Gò Vấp'
            }
        ],
        seoArticles: [
            {
                title: 'Kinh nghiệm tìm thuê căn hộ chung cư mini giá rẻ, an toàn tại TP.HCM',
                meta: 'Hướng dẫn chi tiết cách tìm thuê chung cư mini giá rẻ tại TP.HCM. Các tiêu chí chọn phòng, kiểm tra pháp lý và lưu ý đặt cọc tránh lừa đảo.',
                content: '<h2>1. Nhu cầu thuê chung cư mini ngày càng tăng cao</h2><p>Với sự phát triển kinh tế nhanh chóng, nhu cầu sở hữu không gian sống riêng tư, sạch sẽ của các bạn trẻ đi làm và sinh viên tại các thành phố lớn như TP.HCM tăng mạnh...</p><h2>2. Những lưu ý quan trọng khi đi xem phòng</h2><ul><li>Kiểm tra hệ thống điện nước, đồng hồ riêng biệt.</li><li>Hỏi rõ các chi phí dịch vụ đi kèm (phí quản lý, gửi xe, rác, wifi).</li><li>Đánh giá an ninh khu vực xung quanh và hệ thống phòng cháy chữa cháy.</li></ul>'
            }
        ],
        emailTemplates: [
            {
                subject: '🔥 Cơ hội thuê căn hộ giá tốt nhất tháng này tại BDS Rental',
                content: 'Chào anh/chị,\n\nChúng tôi vừa cập nhật một bất động sản mới cực kỳ phù hợp với nhu cầu tìm kiếm của anh/chị trên hệ thống BDS Rental.\n\nThông tin chi tiết căn hộ:\n- Diện tích: 75m² (2 Phòng ngủ, 2 WC)\n- Vị trí: Khu dân cư đông đúc, gần trung tâm thương mại\n- Giá ưu đãi chính chủ thuê sớm chỉ từ 12 triệu/tháng.\n\nAnh/chị vui lòng nhấn vào link bên dưới để xem hình ảnh thực tế và đặt lịch hẹn xem nhà trực tiếp:\n[Xem chi tiết căn hộ tại đây]\n\nTrân trọng,\nĐội ngũ tư vấn BDS Rental.'
            }
        ],
        smsTemplates: [
            'BDS RENTAL: Can ho 2PN trung tam vua trong, gia thue cuc tot chi 12tr/thang. Dat lich xem nha ngay tai: bdsrental.com/prop-102. LH: 0912345678.',
            'BDS Rental thong bao: Phong tro studio moi tinh co ban cong tai Phan Van Tri, Go Vap gia 5.5tr/thang vua len song. Xem anh va dat lich: bdsrental.com/prop-345.'
        ],
        prompts: [
            'A high-end modern apartment living room, large balcony view with city skyline at sunset, natural sunlight, photorealistic, architectural digest style, interior design photography, 8k resolution --ar 16:9',
            'Sleek minimal studio loft apartment with wooden mezzanine, cozy warm lighting, plant decoration, clean aesthetics, cinematic lighting, 3d rendering --ar 4:3'
        ],
        startGeneration() {
            if (this.properties.length === 0 && !this.selectedPropertyId) {
                this.selectedPropertyId = 'mock_prop_1';
            }
            this.generating = true;
            this.hasResults = false;
            this.progress = 0;
            this.currentStep = 'Phân tích thông số bất động sản...';
            
            let interval = setInterval(() => {
                this.progress += 2;
                if (this.progress === 20) {
                    this.currentStep = 'AI đang soạn thảo 20 bài viết Facebook (Đa góc nhìn)...';
                } else if (this.progress === 45) {
                    this.currentStep = 'AI đang xây dựng 10 kịch bản TikTok Video chi tiết...';
                } else if (this.progress === 70) {
                    this.currentStep = 'AI đang viết 5 bài viết chuẩn SEO và lập tiêu đề bài đăng...';
                } else if (this.progress === 85) {
                    this.currentStep = 'AI đang tạo mẫu Email Marketing và kịch bản SMS chào hàng...';
                } else if (this.progress === 95) {
                    this.currentStep = 'Tối ưu hóa Prompts hình ảnh và sắp xếp nội dung...';
                } else if (this.progress >= 100) {
                    clearInterval(interval);
                    this.generating = false;
                    this.hasResults = true;
                    this.activeResultTab = 'facebook';
                    triggerToast('Khởi tạo chiến dịch Marketing AI Đa Kênh thành công!');
                }
            }, 100);
        },
        copyText(text) {
            navigator.clipboard.writeText(text);
            triggerToast('Đã sao chép nội dung vào Clipboard!');
        }
    }"
    class="space-y-6"
>
    <!-- Header -->
    <div class="pb-5 border-b border-slate-100">
        <h2 class="text-xl font-bold text-slate-800">AI Content Studio (AI Marketing)</h2>
        <p class="text-xs text-slate-400 mt-1 font-semibold">Tạo tự động 20 bài Facebook, 10 kịch bản TikTok, 5 bài SEO, Email & SMS chỉ trong vài phút.</p>
    </div>

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
                        <option :value="prop.id" x-text="prop.title + ' (' + prop.price + ')'"></option>
                    </template>
                    <!-- Mock fallback option -->
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
        
        <div class="space-y-2 max-w-sm mx-auto">
            <h4 class="font-extrabold text-slate-700 text-sm">Trình sáng tạo AI đang làm việc...</h4>
            <p class="text-[11px] text-slate-400 font-semibold" x-text="currentStep"></p>
        </div>

        <!-- Progress Bar container -->
        <div class="max-w-md mx-auto bg-slate-100 h-2 rounded-full overflow-hidden">
            <div class="bg-primary h-full transition-all duration-150" :style="'width: ' + progress + '%'"></div>
        </div>
        <span class="inline-block text-[11px] font-black text-primary bg-primary-light px-2.5 py-1 rounded-full" x-text="progress + '%'"></span>
    </div>

    <!-- Results Panel (Generated Campaign Content) -->
    <div x-show="hasResults" class="space-y-6" x-cloak x-transition>
        <!-- Property Meta Card & New Campaign Button -->
        <div class="bg-slate-50 border border-slate-150 rounded-2xl p-4.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-primary text-base shadow-sm">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-450 uppercase">Đang hiển thị chiến dịch cho BĐS</span>
                    <h4 class="font-bold text-slate-800 text-xs mt-0.5">Căn hộ dịch vụ Hà Đô Centrosa Quận 10</h4>
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

        <!-- Layout grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar result tabs -->
            <div class="bg-white border border-slate-100 rounded-3xl p-3 shadow-sm h-fit flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible gap-1.5 scrollbar-none">
                <button 
                    @click="activeResultTab = 'facebook'"
                    :class="activeResultTab === 'facebook' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                >
                    <i class="fa-brands fa-facebook text-sm"></i> Facebook (20 bài)
                </button>
                <button 
                    @click="activeResultTab = 'tiktok'"
                    :class="activeResultTab === 'tiktok' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                >
                    <i class="fa-brands fa-tiktok text-sm"></i> TikTok Scripts (10 kịch bản)
                </button>
                <button 
                    @click="activeResultTab = 'seo'"
                    :class="activeResultTab === 'seo' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                >
                    <i class="fa-solid fa-file-word text-sm"></i> SEO Articles (5 bài)
                </button>
                <button 
                    @click="activeResultTab = 'email'"
                    :class="activeResultTab === 'email' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                >
                    <i class="fa-solid fa-envelope text-sm"></i> Email & SMS
                </button>
                <button 
                    @click="activeResultTab = 'prompts'"
                    :class="activeResultTab === 'prompts' ? 'bg-primary-light text-primary font-extrabold' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-bold transition cursor-pointer text-left whitespace-nowrap lg:w-full focus:outline-none"
                >
                    <i class="fa-solid fa-image text-sm"></i> Banners & Prompts
                </button>
            </div>

            <!-- Tab content display area -->
            <div class="lg:col-span-3 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm min-h-[400px]">
                
                <!-- FACEBOOK POSTS TAB -->
                <div x-show="activeResultTab === 'facebook'" class="space-y-6" x-transition>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-brands fa-facebook text-blue-650 text-base"></i> Bài đăng Facebook hàng tuần
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400">Đã tạo 20 bài viết thành công</span>
                    </div>

                    <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                        <template x-for="(post, index) in facebookPosts" :key="post.id">
                            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="'Bài đăng #' + post.id"></span>
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

                <!-- TIKTOK SCRIPTS TAB -->
                <div x-show="activeResultTab === 'tiktok'" class="space-y-6" x-transition>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-brands fa-tiktok text-slate-900 text-base"></i> Kịch bản Video TikTok ngắn
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400">Đã tạo 10 kịch bản thành công</span>
                    </div>

                    <div class="space-y-5 max-h-[500px] overflow-y-auto pr-1">
                        <template x-for="script in tiktokScripts" :key="script.id">
                            <div class="border border-slate-150 rounded-2xl overflow-hidden shadow-sm">
                                <!-- Script Title bar -->
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                                    <h4 class="font-bold text-slate-700 text-xs" x-text="script.title"></h4>
                                    <button 
                                        type="button" 
                                        @click="copyText('Kịch bản: ' + script.title + '\n\n[Visual]: ' + script.visual + '\n\n[Voiceover]: ' + script.audio)"
                                        class="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                    >
                                        <i class="fa-solid fa-copy"></i> Sao chép kịch bản
                                    </button>
                                </div>
                                <!-- Script content table format -->
                                <div class="grid grid-cols-1 md:grid-cols-2 text-xs">
                                    <!-- Visual column -->
                                    <div class="p-4 border-b md:border-b-0 md:border-r border-slate-100 space-y-2">
                                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Mô tả hình ảnh (Visual)</span>
                                        <p class="text-slate-600 leading-relaxed font-semibold" x-text="script.visual"></p>
                                    </div>
                                    <!-- Audio column -->
                                    <div class="p-4 space-y-3">
                                        <div class="space-y-1">
                                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Lời thoại Voiceover (Audio)</span>
                                            <p class="text-slate-700 leading-relaxed font-bold bg-primary-light/30 p-2.5 rounded-lg border border-primary/10" x-text="script.audio"></p>
                                        </div>
                                        <div class="space-y-1">
                                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Chữ chạy trên màn hình (Text overlay)</span>
                                            <p class="text-slate-500 leading-normal font-mono" x-text="script.overlay"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SEO ARTICLES TAB -->
                <div x-show="activeResultTab === 'seo'" class="space-y-6" x-transition>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-file-word text-blue-500 text-base"></i> Bài viết chuẩn SEO Website
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400">Đã tạo 5 bài viết chuẩn SEO</span>
                    </div>

                    <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                        <template x-for="art in seoArticles" :key="art.title">
                            <div class="p-5 border border-slate-150 rounded-2xl space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-black text-slate-800 text-sm" x-text="art.title"></h4>
                                    <button 
                                        type="button" 
                                        @click="copyText(art.title + '\n\n' + art.content)"
                                        class="px-2 py-1 bg-slate-50 hover:bg-primary-light border border-slate-200 hover:border-primary/20 text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                    >
                                        <i class="fa-solid fa-copy"></i> Sao chép bài viết
                                    </button>
                                </div>
                                <!-- Meta fields -->
                                <div class="bg-slate-55 p-3.5 rounded-xl border border-slate-150 space-y-1.5 text-xs">
                                    <p class="text-slate-500 leading-normal"><strong class="text-slate-700">Meta Title:</strong> <span x-text="art.title"></span></p>
                                    <p class="text-slate-500 leading-normal"><strong class="text-slate-700">Meta Description:</strong> <span x-text="art.meta"></span></p>
                                </div>
                                <!-- Content preview -->
                                <div class="prose prose-slate max-w-none text-xs text-slate-600 leading-relaxed" x-html="art.content"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- EMAIL & SMS TAB -->
                <div x-show="activeResultTab === 'email'" class="space-y-6" x-transition>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-primary text-base"></i> Bản tin Email & Tin nhắn SMS
                        </h3>
                    </div>

                    <div class="space-y-5 max-h-[500px] overflow-y-auto pr-1 text-xs">
                        <!-- Email template -->
                        <div class="border border-slate-150 rounded-2xl overflow-hidden">
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                                <span class="font-extrabold text-slate-700 text-xs">Mẫu Email Chăm sóc Leads</span>
                                <button 
                                    type="button" 
                                    @click="copyText(emailTemplates[0].subject + '\n\n' + emailTemplates[0].content)"
                                    class="px-2 py-1 bg-white border border-slate-200 hover:border-primary text-slate-655 hover:text-primary rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer focus:outline-none"
                                >
                                    <i class="fa-solid fa-copy"></i> Sao chép Email
                                </button>
                            </div>
                            <div class="p-4 space-y-3.5 bg-slate-50/30">
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase">Tiêu đề (Subject)</span>
                                    <p class="font-black text-slate-800" x-text="emailTemplates[0].subject"></p>
                                </div>
                                <div class="border-t border-slate-100 pt-3">
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nội dung (Body)</span>
                                    <p class="text-slate-600 leading-relaxed whitespace-pre-line bg-white p-3 rounded-xl border border-slate-100" x-text="emailTemplates[0].content"></p>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Template -->
                        <div class="p-4 border border-slate-150 rounded-2xl space-y-3">
                            <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Các mẫu SMS ngắn (Zalo ZNS / SMS)</span>
                            <div class="space-y-2.5">
                                <template x-for="(sms, idx) in smsTemplates" :key="idx">
                                    <div class="p-3 bg-slate-50 border border-slate-150 rounded-xl flex items-center justify-between gap-4">
                                        <p class="font-mono text-slate-700 leading-normal" x-text="sms"></p>
                                        <button 
                                            type="button" 
                                            @click="copyText(sms)"
                                            class="p-2 bg-white border border-slate-200 hover:border-primary text-slate-500 hover:text-primary rounded-lg transition flex-shrink-0 cursor-pointer focus:outline-none"
                                            title="Copy SMS"
                                        >
                                            <i class="fa-solid fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BANNERS & IMAGES TAB -->
                <div x-show="activeResultTab === 'prompts'" class="space-y-6" x-transition>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-image text-emerald-500 text-base"></i> Banner & Thumbnail Generation Prompts
                        </h3>
                    </div>

                    <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1 text-xs">
                        <p class="text-slate-500 leading-relaxed font-semibold">Sao chép các Prompts mô tả chi tiết bằng tiếng Anh bên dưới để đưa vào các công cụ sinh ảnh AI (như Midjourney, DALL-E, Stable Diffusion) để tạo ra các ảnh banner quảng bá có chất lượng điện ảnh cao.</p>
                        
                        <template x-for="(prompt, index) in prompts" :key="index">
                            <div class="p-4 bg-slate-55 border border-slate-150 rounded-xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase" x-text="'Prompt #' + (index + 1)"></span>
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
