@extends('layouts.app')

@section('title', 'Wiki Bất Động Sản - Tin Tức, Báo Cáo & Phong Thủy | BDS Rental')

@section('content')
@php
    $tabData = [
        'report' => [
            [
                'title' => 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
                'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Thị trường căn hộ dịch vụ và Studio ghi nhận tỷ lệ lấp đầy đạt 85%, giá thuê tăng nhẹ 3-5% tại các khu vực trung tâm Phú Nhuận, Quận 3.',
                'date' => '28/06/2026',
                'category_label' => 'Báo cáo thị trường'
            ],
            [
                'title' => 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Nhà đầu tư đang ưu tiên các dự án có pháp lý hoàn thiện và có khả năng tạo dòng tiền ngay từ hoạt động cho thuê căn hộ Studio tiện ích.',
                'date' => '25/06/2026',
                'category_label' => 'Báo cáo thị trường'
            ],
            [
                'title' => 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Các căn hộ thông minh tích hợp giải pháp xanh, tiện ích trọn gói và bếp tách riêng biệt đang trở thành ưu tiên số một của nhóm khách hàng trẻ tuổi.',
                'date' => '18/06/2026',
                'category_label' => 'Báo cáo thị trường'
            ],
            [
                'title' => 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
                'image' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Hạ tầng giao thông, pháp lý dự án và các tiện ích xanh xung quanh là 3 trụ cột cốt lõi quyết định biên độ tăng giá của bất động sản.',
                'date' => '28/06/2026',
                'category_label' => 'Báo cáo thị trường'
            ]
        ],
        'view' => [
            [
                'title' => 'Góc Nhìn NKS: Căn Hộ Studio Quận 7 Đang Dần Chiếm Lĩnh Phân Khúc Cho Thuê',
                'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=800',
                'excerpt' => 'Phân tích xu hướng lựa chọn không gian sống độc lập, tiện ích cao cấp của thế hệ Gen Z và người đi làm độc thân.',
                'date' => '27/06/2026',
                'category_label' => 'Góc nhìn NKS'
            ],
            [
                'title' => 'Góc nhìn NKS: Thị trường bất động sản cuối năm 2026 sẽ đi về đâu?',
                'image' => 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&q=80&w=600',
                'excerpt' => 'Phân tích đa chiều về nguồn cung căn hộ dịch vụ và xu hướng giá thuê bất động sản chính chủ.',
                'date' => '26/06/2026',
                'category_label' => 'Góc nhìn NKS'
            ],
            [
                'title' => 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
                'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Những bài học thực tế từ NKS giúp các chủ đầu tư căn hộ tăng tỷ suất lợi nhuận lên đến 12%/năm nhờ cải tạo thiết kế.',
                'date' => '29/06/2026',
                'category_label' => 'Góc nhìn NKS'
            ],
            [
                'title' => 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
                'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Tầm nhìn chiến lược về sự phát triển vượt bậc của các căn hộ và khu dân cư dọc trục sông Sài Gòn.',
                'date' => '20/06/2026',
                'category_label' => 'Góc nhìn NKS'
            ]
        ],
        'interior' => [
            [
                'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Ứng dụng phong cách tối giản Japandi giúp các không gian căn hộ Studio diện tích nhỏ trở nên thông thoáng, rộng rãi.',
                'date' => '01/07/2026',
                'category_label' => 'Nội Thất'
            ],
            [
                'title' => 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Hướng dẫn chọn gạch men chống thấm, sơn phủ acrylic chống ẩm và thiết kế hệ thống quạt thông gió tối ưu.',
                'date' => '27/06/2026',
                'category_label' => 'Nội Thất'
            ],
            [
                'title' => 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Kết hợp hài hòa giữa ánh sáng tự nhiên ban ngày và hệ thống đèn LED âm trần, đèn thả ấm nhiệt độ màu 3000K.',
                'date' => '15/06/2026',
                'category_label' => 'Nội Thất'
            ],
            [
                'title' => 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
                'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Tận dụng góc chết và sử dụng các sản phẩm ghế sofa đa năng, gấp gọn để tối ưu hóa không gian sử dụng.',
                'date' => '26/6/2026',
                'category_label' => 'Nội Thất'
            ]
        ],
        'fengshui' => [
            [
                'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Tránh đặt giường đối diện cửa chính, dưới xà ngang nhà hay trước gương soi lớn nhằm bảo vệ sức khỏe và đón nhận luồng sinh khí.',
                'date' => '03/07/2026',
                'category_label' => 'Phong Thủy'
            ],
            [
                'title' => 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
                'image' => 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Tư vấn chi tiết từ chuyên gia phong thủy giúp gia chủ mệnh Thổ đón vượng khí, tài lộc hanh thông.',
                'date' => '28/06/2026',
                'category_label' => 'Phong Thủy'
            ],
            [
                'title' => 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
                'image' => 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Gợi ý các loại cây dễ trồng như Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh giúp gia tăng năng lượng may mắn.',
                'date' => '19/06/2026',
                'category_label' => 'Phong Thủy'
            ],
            [
                'title' => 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
                'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Tác động xấu của gương đối diện giường ngủ/cửa phòng và các biện pháp hóa giải đơn giản như sử dụng rèm che.',
                'date' => '26/6/2026',
                'category_label' => 'Phong Thủy'
            ]
        ],
        'news' => [
            [
                'title' => 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
                'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Dự thảo luật mới siết chặt công tác phòng cháy chữa cháy (PCCC) và yêu cầu đăng ký kinh doanh bắt buộc.',
                'date' => '03/07/2026',
                'category_label' => 'Tin Tức'
            ],
            [
                'title' => 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Cung cấp hơn 3,000 căn hộ chất lượng cao giá cả phải chăng dành riêng cho công nhân, người lao động thu nhập thấp.',
                'date' => '30/06/2026',
                'category_label' => 'Tin Tức'
            ],
            [
                'title' => 'Khởi động dự án cải tạo hạ tầng giao thông trục đường chính',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Kế hoạch triển khai nâng cấp mở rộng các tuyến giao thông huyết mạch kết nối trực tiếp với trung tâm thành phố.',
                'date' => '22/06/2026',
                'category_label' => 'Tin Tức'
            ],
            [
                'title' => 'Giá căn hộ cho thuê tiếp tục tăng trưởng nhẹ dịp cuối năm',
                'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Nhu cầu thuê căn hộ chung cư mini và studio tăng cao đột biến trong các tháng cuối năm kéo theo mức giá thuê tăng nhẹ.',
                'date' => '26/6/2026',
                'category_label' => 'Tin Tức'
            ]
        ],
        'knowledge' => [
            [
                'title' => 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Hướng dẫn đầy đủ các bước sang nhượng quyền thuê nhà, xử lý phần tiền đặt cọc và lập biên bản thanh lý hợp đồng.',
                'date' => '02/07/2026',
                'category_label' => 'Kiến Thức'
            ],
            [
                'title' => 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                'image' => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Chia sẻ phương pháp kiểm tra phôi sổ hồng bằng mắt thường, xác thực thông tin quy hoạch tránh bẫy lừa đảo.',
                'date' => '26/06/2026',
                'category_label' => 'Kiến Thức'
            ],
            [
                'title' => 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
                'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Tổng hợp các loại phí cần đóng gồm thuế thu nhập cá nhân 2%, lệ phí trước bạ 0.5% và cách tính đơn giản chính xác.',
                'date' => '17/06/2026',
                'category_label' => 'Kiến Thức'
            ],
            [
                'title' => 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
                'image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=600&q=80',
                'excerpt' => 'Lập kế hoạch trả nợ ngân hàng thông minh, áp dụng quy tắc 50/30/20 để quản lý chi tiêu.',
                'date' => '26/6/2026',
                'category_label' => 'Kiến Thức'
            ]
        ]
    ];
@endphp

<!-- Main Content Area -->
<div class="space-y-12 pb-20 bg-white" 
     x-data="{
         activeTab: (new URLSearchParams(window.location.search).get('category') || 'report'),
         searchQuery: '',
         tabData: {{ json_encode($tabData) }},
         
         get filteredArticles() {
             let articles = this.tabData[this.activeTab] || [];
             if (this.searchQuery.trim() !== '') {
                 let q = this.searchQuery.toLowerCase();
                 return articles.filter(a => a.title.toLowerCase().includes(q) || a.excerpt.toLowerCase().includes(q));
             }
             return articles;
         },
         
         changeTab(tab) {
             this.activeTab = tab;
             const url = new URL(window.location);
             url.searchParams.set('category', tab);
             window.history.pushState({}, '', url);
         }
     }"
>
    <!-- Hero / Header Title Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
        <div class="space-y-2 border-b border-slate-100 pb-6 text-left">
            <span class="text-xs font-black text-primary uppercase tracking-widest">NKS WIKI TIN TỨC</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight font-sans">
                Tin Tức Bất Động Sản
            </h1>
            <p class="text-slate-400 text-xs sm:text-sm font-medium">
                Cập nhật nhanh chóng xu hướng thị trường, kiến thức đầu tư, thiết kế nội thất và cẩm nang phong thủy.
            </p>
        </div>
    </section>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Main Content Area (Spotlight & Grid) -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Spotlight Featured Post (Derived dynamically from activeTab first element) -->
                <template x-if="filteredArticles.length > 0">
                    <div class="relative rounded-[36px] overflow-hidden shadow-sm group cursor-pointer border border-slate-100/50 h-[380px] sm:h-[440px]"
                         @click="window.location.href = '#'">
                        
                        <!-- Image with Zoom effect -->
                        <img :src="filteredArticles[0].image" 
                             :alt="filteredArticles[0].title" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-700 ease-out">
                        
                        <!-- Dark overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>

                        <!-- Card text content -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10 space-y-4 text-white text-left z-10">
                            <!-- Category Badge -->
                            <span class="inline-block bg-primary text-white text-[9px] font-black px-3 py-1 rounded-[6px] uppercase tracking-wider" 
                                  x-text="filteredArticles[0].category_label">
                            </span>

                            <h2 class="text-xl sm:text-3xl font-extrabold line-clamp-2 hover:underline tracking-tight leading-snug" 
                                x-text="filteredArticles[0].title">
                            </h2>

                            <p class="text-white/80 text-xs font-semibold leading-relaxed line-clamp-2 max-w-xl" 
                               x-text="filteredArticles[0].excerpt">
                            </p>

                            <div class="flex items-center justify-between pt-2 text-[10px] text-white/60 font-bold border-t border-white/10">
                                <span x-text="filteredArticles[0].date"></span>
                                <span class="flex items-center gap-1">Đọc bài viết <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Category Filtering Navigation Menu -->
                <div class="flex flex-wrap gap-2 border-b border-slate-100 pb-4">
                    <button @click="changeTab('report')" 
                            :class="activeTab === 'report' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Báo cáo Thị trường BĐS
                    </button>
                    <button @click="changeTab('view')" 
                            :class="activeTab === 'view' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Góc Nhìn NKS
                    </button>
                    <button @click="changeTab('interior')" 
                            :class="activeTab === 'interior' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Nội Thất
                    </button>
                    <button @click="changeTab('fengshui')" 
                            :class="activeTab === 'fengshui' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Phong Thủy
                    </button>
                    <button @click="changeTab('news')" 
                            :class="activeTab === 'news' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Tin Tức
                    </button>
                    <button @click="changeTab('knowledge')" 
                            :class="activeTab === 'knowledge' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Kiến Thức
                    </button>
                </div>

                <!-- Articles Grid (Derived dynamically from activeTab elements slice) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                    <template x-for="(article, index) in filteredArticles.slice(1)" :key="index">
                        <div class="space-y-4 group cursor-pointer" @click="window.location.href = '#'">
                            
                            <div class="h-48 rounded-[24px] overflow-hidden shadow-2xs relative border border-slate-100/60">
                                <img :src="article.image" 
                                     :alt="article.title" 
                                     class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                            </div>
                            
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="article.category_label">
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-800 group-hover:text-primary transition-colors leading-snug line-clamp-2" 
                                    x-text="article.title">
                                </h4>
                                <p class="text-xs text-slate-400 font-medium line-clamp-2" x-text="article.excerpt">
                                </p>
                                <p class="text-[10px] text-slate-400 font-bold pt-1" x-text="article.date">
                                </p>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <template x-if="filteredArticles.length === 0">
                    <div class="text-center py-16 bg-slate-50 rounded-3xl border border-slate-100">
                        <i class="fa-solid fa-folder-open text-slate-300 text-4xl mb-3 block"></i>
                        <span class="text-slate-500 font-bold text-sm">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</span>
                    </div>
                </template>
                
            </div>

            <!-- Sidebar Column -->
            <div class="space-y-8">
                
                <!-- Search Box Widget -->
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-left">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
                    <div class="flex gap-2">
                        <div class="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3 py-2.5 flex items-center gap-2 shadow-2xs">
                            <input type="text" 
                                   x-model="searchQuery" 
                                   placeholder="Nhập từ khóa tìm kiếm..." 
                                   class="w-full bg-transparent border-0 p-0 text-slate-700 placeholder-slate-400 font-bold focus:outline-none focus:ring-0 text-xs">
                        </div>
                        <button type="button" 
                                class="bg-primary hover:bg-primary-hover text-white font-bold px-4 py-2.5 rounded-2xl transition-all text-xs cursor-pointer">
                            Tìm
                        </button>
                    </div>
                </div>

                <!-- Popular Articles Widget (Tin đọc nhiều) -->
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6 text-left">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest pb-3 border-b border-slate-50">
                        Tin đọc nhiều
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex gap-3 items-start group cursor-pointer" @click="window.location.href = '#'">
                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=600" 
                                     alt="Cách Tối Ưu Hóa Quá Trình Mua Nhà Qua Nền Tảng Online 2026" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    Cách Tối Ưu Hóa Quá Trình Mua Nhà Qua Nền Tảng Online 2026
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 items-start group cursor-pointer" @click="window.location.href = '#'">
                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                <img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&q=80&w=600" 
                                     alt="Nhà Đầu Đầu Tư Phía Bắc Nam Tiến Thị Trường Bất Động Sản" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    Nhà Đầu Tư Phía Bắc Nam Tiến Thị Trường Bất Động Sản
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 items-start group cursor-pointer" @click="window.location.href = '#'">
                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                <img src="https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&q=80&w=600" 
                                     alt="Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 items-start group cursor-pointer" @click="window.location.href = '#'">
                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=600" 
                                     alt="Hướng Dẫn Đăng Tin Nhà Đất Chuẩn SEO và AI Lên Xu Hướng NKS" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    Hướng Dẫn Đăng Tin Nhà Đất Chuẩn SEO và AI Lên Xu Hướng NKS
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
