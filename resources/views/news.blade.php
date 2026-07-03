@extends('layouts.app')

@section('title', 'Thông tin bất động sản Việt Nam mới nhất | BDS Rental')

@section('content')
<!-- Header Banner Section -->
<div class="bg-slate-50 border-b border-slate-100 pt-28 pb-10 text-left">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-400 mb-4 space-x-2">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-600">Tin tức</span>
        </nav>
        
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#0f172a] leading-tight tracking-tight">
                Tin tức bất động sản mới nhất
            </h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base leading-relaxed">
                Thông tin mới, đầy đủ, hấp dẫn về thị trường bất động sản Việt Nam thông qua dữ liệu lớn về giá, giao dịch, nguồn cung - cầu và khảo sát thực tế.
            </p>
        </div>
    </div>
</div>

<!-- Main Editorial Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ activeTab: 'baocao' }">
    <!-- Featured Section (Highlights) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
        <!-- Big Featured Article (Left 66%) -->
        <div class="lg:col-span-2">
            <a href="#" class="group block relative rounded-3xl overflow-hidden aspect-[16/9] shadow-sm hover:shadow-md transition duration-300">
                <!-- Background Image -->
                <img 
                    src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1000&q=80" 
                    alt="Xu hướng thị trường bất động sản" 
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition duration-500"
                >
                <!-- Black Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                
                <!-- Overlay Content -->
                <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8 text-left z-10">
                    <span class="text-[10px] text-white/80 font-bold uppercase tracking-wider block mb-2">
                        02/07/2026 • Báo cáo thị trường
                    </span>
                    <h2 class="text-lg sm:text-2xl font-extrabold text-white leading-tight group-hover:underline mb-2.5">
                        Xu Hướng Đầu Tư Bất Động Sản Nghỉ Dưỡng: Từ "Lướt Sóng" Sang Ưu Tiên "Dòng Tiền Ổn Định"
                    </h2>
                    <p class="text-white/80 text-xs sm:text-sm line-clamp-2 leading-relaxed font-medium">
                        Thị trường bất động sản nghỉ dưỡng đang bước vào một chu kỳ mới. Không còn là những kỳ vọng chờ tăng giá hay đầu cơ "lướt sóng", nhà đầu tư ngày càng thực tế hơn và ưu tiên sản phẩm có khả năng tạo ra giá trị dài hạn.
                    </p>
                </div>
            </a>
        </div>

        <!-- Hot Sidebar Headlines (Right 33%) -->
        <div class="flex flex-col justify-between space-y-6">
            <div class="border-b border-slate-100 pb-5 text-left group">
                <span class="text-[10px] text-slate-400 font-extrabold uppercase block mb-1">02/07/2026 • Góc nhìn chuyên gia</span>
                <h3 class="text-sm font-extrabold text-slate-800 group-hover:text-primary transition leading-snug">
                    <a href="#">VIP Coffee Talk #05: BĐS Hải Phòng Trong Giai Đoạn "Thanh Lọc" Và Cuộc Chơi Của Những Giá Trị Thực</a>
                </h3>
            </div>
            
            <div class="border-b border-slate-100 pb-5 text-left group">
                <span class="text-[10px] text-slate-400 font-extrabold uppercase block mb-1">01/07/2026 • Tin tức nổi bật</span>
                <h3 class="text-sm font-extrabold text-slate-800 group-hover:text-primary transition leading-snug">
                    <a href="#">Không Gian Sống Ven Sông Hàn Trở Thành Tâm Điểm Mới Của Thị Trường Đà Nẵng</a>
                </h3>
            </div>

            <div class="pb-2 text-left group">
                <span class="text-[10px] text-slate-400 font-extrabold uppercase block mb-1">30/06/2026 • Dự án mới</span>
                <h3 class="text-sm font-extrabold text-slate-800 group-hover:text-primary transition leading-snug">
                    <a href="#">Bắc Ninh Sắp Có Khu Phố Đêm Hướng Tới Chuyên Gia Nước Ngoài: Cơ Hội Nào Cho Nhà Đầu Tư Tại Mangala Complex?</a>
                </h3>
            </div>
        </div>
    </div>

    <!-- Tab Category Navigation (Styled according to template) -->
    <div class="flex items-center justify-start gap-1.5 overflow-x-auto pb-4 mb-8 border-b border-slate-100 scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none]">
        <button 
            @click="activeTab = 'baocao'" 
            :class="activeTab === 'baocao' ? 'border-b-2 border-primary text-primary font-bold pb-2' : 'border-b-2 border-transparent text-slate-500 hover:text-primary font-semibold pb-2'"
            class="text-sm transition duration-150 whitespace-nowrap cursor-pointer px-1 mr-6 -mb-[18px] focus:outline-none"
        >
            Báo cáo Thị trường BĐS Việt Nam
        </button>
        <button 
            @click="activeTab = 'gocnhin'" 
            :class="activeTab === 'gocnhin' ? 'border-b-2 border-primary text-primary font-bold pb-2' : 'border-b-2 border-transparent text-slate-500 hover:text-primary font-semibold pb-2'"
            class="text-sm transition duration-150 whitespace-nowrap cursor-pointer px-1 mr-6 -mb-[18px] focus:outline-none"
        >
            Góc Nhìn NKS
        </button>
        <button 
            @click="activeTab = 'noithat'" 
            :class="activeTab === 'noithat' ? 'border-b-2 border-primary text-primary font-bold pb-2' : 'border-b-2 border-transparent text-slate-500 hover:text-primary font-semibold pb-2'"
            class="text-sm transition duration-150 whitespace-nowrap cursor-pointer px-1 mr-6 -mb-[18px] focus:outline-none"
        >
            Nội Thất
        </button>
        <button 
            @click="activeTab = 'phongthuy'" 
            :class="activeTab === 'phongthuy' ? 'border-b-2 border-primary text-primary font-bold pb-2' : 'border-b-2 border-transparent text-slate-500 hover:text-primary font-semibold pb-2'"
            class="text-sm transition duration-150 whitespace-nowrap cursor-pointer px-1 mr-6 -mb-[18px] focus:outline-none"
        >
            Phong Thủy
        </button>
        <button 
            @click="activeTab = 'tintuc'" 
            :class="activeTab === 'tintuc' ? 'border-b-2 border-primary text-primary font-bold pb-2' : 'border-b-2 border-transparent text-slate-500 hover:text-primary font-semibold pb-2'"
            class="text-sm transition duration-150 whitespace-nowrap cursor-pointer px-1 mr-6 -mb-[18px] focus:outline-none"
        >
            Tin Tức
        </button>
        <button 
            @click="activeTab = 'kienthuc'" 
            :class="activeTab === 'kienthuc' ? 'border-b-2 border-primary text-primary font-bold pb-2' : 'border-b-2 border-transparent text-slate-500 hover:text-primary font-semibold pb-2'"
            class="text-sm transition duration-150 whitespace-nowrap cursor-pointer px-1 -mb-[18px] focus:outline-none"
        >
            Kiến Thức
        </button>
    </div>

    <!-- Lower Section: Article Feed vs Sidebar Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pt-4">
        <!-- Articles Feed (Left 66%) -->
        <div class="lg:col-span-2">
            @php
                $tabData = [
                    'baocao' => [
                        [
                            'title' => 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
                            'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Thị trường căn hộ dịch vụ và Studio ghi nhận tỷ lệ lấp đầy đạt 85%, giá thuê tăng nhẹ 3-5% tại các khu vực trung tâm Phú Nhuận, Quận 3.',
                            'date' => '28/06/2026'
                        ],
                        [
                            'title' => 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
                            'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Nhà đầu tư đang ưu tiên các dự án có pháp lý hoàn thiện và có khả năng tạo dòng tiền ngay từ hoạt động cho thuê căn hộ Studio tiện ích.',
                            'date' => '25/06/2026'
                        ],
                        [
                            'title' => 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
                            'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Các căn hộ thông minh tích hợp giải pháp xanh, tiện ích trọn gói và bếp tách riêng biệt đang trở thành ưu tiên số một của nhóm khách hàng trẻ tuổi.',
                            'date' => '18/06/2026'
                        ],
                        [
                            'title' => 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
                            'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=500&q=80',
                            'excerpt' => 'Hạ tầng giao thông, pháp lý dự án và các tiện ích xanh xung quanh là 3 trụ cột cốt lõi quyết định biên độ tăng giá của bất động sản trong giai đoạn bình thường mới.',
                            'date' => '28/06/2026'
                        ]
                    ],
                    'gocnhin' => [
                        [
                            'title' => 'Góc nhìn NKS: Tại sao mô hình Studio tách bếp lại đắt khách?',
                            'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Phân tích chi tiết hành vi của khách hàng thuê hiện đại và lý do tại sao các phòng trọ/căn hộ có khu vực bếp riêng biệt luôn trong trạng thái cháy hàng.',
                            'date' => '02/07/2026'
                        ],
                        [
                            'title' => 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
                            'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Những bài học thực tế từ NKS giúp các chủ đầu tư căn hộ tăng tỷ suất lợi nhuận lên đến 12%/năm nhờ cải tạo thiết kế và tối giản hóa quy trình vận hành.',
                            'date' => '29/06/2026'
                        ],
                        [
                            'title' => 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
                            'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Tầm nhìn chiến lược về sự phát triển vượt bậc của các căn hộ và khu dân cư dọc trục sông Sài Gòn, đặc biệt là khu vực Quận 2 cũ và Bình Thạnh.',
                            'date' => '20/06/2026'
                        ],
                        [
                            'title' => 'Chiến lược phát triển thị trường căn hộ mini tại TP.HCM',
                            'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80',
                            'excerpt' => 'Phân tích chi tiết về nguồn cung, tỷ suất lấp đầy và hành vi khách hàng đối với loại hình chung cư mini/căn hộ dịch vụ quy mô nhỏ.',
                            'date' => '26/6/2026'
                        ]
                    ],
                    'noithat' => [
                        [
                            'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                            'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Ứng dụng phong cách tối giản Japandi giúp các không gian căn hộ Studio diện tích nhỏ trở nên thông thoáng, rộng rãi và tối ưu công năng sử dụng.',
                            'date' => '01/07/2026'
                        ],
                        [
                            'title' => 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                            'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Hướng dẫn chọn gạch men chống thấm, sơn phủ acrylic chống ẩm và thiết kế hệ thống quạt thông gió tối ưu giúp căn phòng luôn sạch sẽ thơm tho.',
                            'date' => '27/06/2026'
                        ],
                        [
                            'title' => 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
                            'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Kết hợp hài hòa giữa ánh sáng tự nhiên ban ngày và hệ thống đèn LED âm trần, đèn thả ấm nhiệt độ màu 3000K để thư giãn tối đa sau ngày làm việc.',
                            'date' => '15/06/2026'
                        ],
                        [
                            'title' => 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
                            'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=500&q=80',
                            'excerpt' => 'Tận dụng góc chết và sử dụng các sản phẩm ghế sofa đa năng, gấp gọn để tối ưu hóa không gian sử dụng của phòng khách nhỏ.',
                            'date' => '26/6/2026'
                        ]
                    ],
                    'phongthuy' => [
                        [
                            'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
                            'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Tránh đặt giường đối diện cửa chính, dưới xà ngang nhà hay trước gương soi lớn nhằm bảo vệ sức khỏe và đón nhận luồng sinh khí tốt lành.',
                            'date' => '03/07/2026'
                        ],
                        [
                            'title' => 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
                            'image' => 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Tư vấn chi tiết từ chuyên gia phong thủy giúp gia chủ mệnh Thổ đón vượng khí, tài lộc hanh thông nhờ việc lựa chọn đúng hướng nhà và tông màu sơn chủ đạo.',
                            'date' => '28/06/2026'
                        ],
                        [
                            'title' => 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
                            'image' => 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Gợi ý các loại cây dễ trồng như Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh giúp cải thiện chất lượng không khí và gia tăng năng lượng may mắn.',
                            'date' => '19/06/2026'
                        ],
                        [
                            'title' => 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
                            'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=500&q=80',
                            'excerpt' => 'Tác động xấu của gương đối diện giường ngủ/cửa phòng và các biện pháp hóa giải đơn giản như sử dụng rèm che hoặc dán mờ gương.',
                            'date' => '26/6/2026'
                        ]
                    ],
                    'tintuc' => [
                        [
                            'title' => 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
                            'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Dự thảo luật mới siết chặt công tác phòng cháy chữa cháy (PCCC) và yêu cầu đăng ký kinh doanh bắt buộc đối với các hộ cho thuê quy mô lớn.',
                            'date' => '03/07/2026'
                        ],
                        [
                            'title' => 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
                            'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Cung cấp hơn 3,000 căn hộ chất lượng cao giá cả phải chăng dành riêng cho công nhân, người lao động thu nhập thấp tại khu vực phía Tây thành phố.',
                            'date' => '30/06/2026'
                        ],
                        [
                            'title' => 'Khởi động dự án cải tạo hạ tầng giao thông và mở rộng lộ giới trục đường chính',
                            'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Kế hoạch triển khai nâng cấp mở rộng các tuyến giao thông huyết mạch kết nối trực tiếp với trung tâm thành phố giúp nâng tầm giá trị các dự án lân cận.',
                            'date' => '22/06/2026'
                        ],
                        [
                            'title' => 'Giá căn hộ cho thuê tiếp tục tăng trưởng nhẹ dịp cuối năm',
                            'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=500&q=80',
                            'excerpt' => 'Nhu cầu thuê căn hộ chung cư mini và studio tăng cao đột biến trong các tháng cuối năm kéo theo mức giá thuê trung bình tăng từ 5% - 8%.',
                            'date' => '26/6/2026'
                        ]
                    ],
                    'kienthuc' => [
                        [
                            'title' => 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                            'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Hướng dẫn đầy đủ các bước sang nhượng quyền thuê nhà, xử lý phần tiền đặt cọc và lập biên bản thanh lý hợp đồng cũ an toàn, nhanh chóng.',
                            'date' => '02/07/2026'
                        ],
                        [
                            'title' => 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                            'image' => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Chia sẻ phương pháp kiểm tra phôi sổ hồng bằng mắt thường, xác thực thông tin quy hoạch tại phòng tài nguyên môi trường tránh bẫy lừa đảo.',
                            'date' => '26/06/2026'
                        ],
                        [
                            'title' => 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
                            'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                            'excerpt' => 'Tổng hợp các loại phí cần đóng gồm thuế thu nhập cá nhân 2%, lệ phí trước bạ 0.5% và cách tính đơn giản chính xác cho người giao dịch lần đầu.',
                            'date' => '17/06/2026'
                        ],
                        [
                            'title' => 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
                            'image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=500&q=80',
                            'excerpt' => 'Lập kế hoạch trả nợ ngân hàng thông minh, áp dụng quy tắc 50/30/20 để quản lý chi tiêu và đảm bảo khả năng trả nợ gốc lãi hàng tháng đúng hạn.',
                            'date' => '26/6/2026'
                        ]
                    ]
                ];
                
                $categoryNames = [
                    'baocao' => 'Báo Cáo',
                    'gocnhin' => 'Góc Nhìn',
                    'noithat' => 'Nội Thất',
                    'phongthuy' => 'Phong Thủy',
                    'tintuc' => 'Tin Tức',
                    'kienthuc' => 'Kiến Thức'
                ];
            @endphp

            @foreach($tabData as $tabName => $articles)
                <div 
                    x-show="activeTab === '{{ $tabName }}'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="divide-y divide-slate-100"
                    x-cloak
                >
                    @foreach($articles as $article)
                        <div class="flex flex-col sm:flex-row gap-6 py-6 text-left group">
                            <!-- Article Image -->
                            <div class="w-full sm:w-48 aspect-[16/10] rounded-2xl overflow-hidden bg-slate-100 flex-shrink-0 relative">
                                <img 
                                    src="{{ $article['image'] }}" 
                                    alt="{{ $article['title'] }}" 
                                    class="w-full h-full object-cover group-hover:scale-103 transition duration-500"
                                    loading="lazy"
                                >
                            </div>
                            
                            <!-- Article Text -->
                            <div class="flex flex-col flex-grow">
                                <span class="text-[10px] text-primary font-extrabold uppercase tracking-wider block mb-1.5">
                                    {{ $categoryNames[$tabName] }} • {{ $article['date'] }}
                                </span>
                                <h3 class="text-base font-extrabold text-slate-900 group-hover:text-primary transition leading-snug mb-2">
                                    <a href="#">{{ $article['title'] }}</a>
                                </h3>
                                <p class="text-xs text-slate-550 line-clamp-3 leading-relaxed mb-3">
                                    {{ $article['excerpt'] }}
                                </p>
                                <a href="#" class="inline-flex items-center text-xs font-bold text-slate-400 group-hover:text-primary transition mt-auto">
                                    Xem thêm <i class="fa-solid fa-arrow-right-long ml-1.5 transition-transform group-hover:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Sidebar Widgets (Right 33%) -->
        <div class="space-y-12">
            <!-- Widget 1: Popular Articles -->
            <div class="bg-white rounded-3xl border border-slate-100 p-6 text-left">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 mb-5">
                    Bài viết xem nhiều nhất
                </h3>
                
                @php
                    $popular = [
                        'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                        'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                        'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất năm 2026',
                        'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                        'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng'
                    ];
                @endphp
                
                <div class="space-y-4">
                    @foreach($popular as $index => $title)
                        <div class="flex items-start space-x-3.5 group cursor-pointer">
                            <span class="w-6 h-6 rounded-full bg-slate-50 group-hover:bg-primary/10 text-slate-400 group-hover:text-primary flex items-center justify-center text-xs font-black flex-shrink-0 transition">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-xs font-extrabold text-slate-700 group-hover:text-primary leading-snug transition line-clamp-2">
                                <a href="#">{{ $title }}</a>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Widget 2: Hot Locations -->
            <div class="bg-white rounded-3xl border border-slate-100 p-6 text-left">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 mb-5">
                    Thị trường sôi động nhất
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Ha noi -->
                    <a href="#" class="group relative rounded-2xl overflow-hidden aspect-square block">
                        <img 
                            src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=200&q=80" 
                            alt="Bất động sản Hà Nội"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                        >
                        <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/20 transition"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-2 z-10">
                            <span class="text-xs font-extrabold text-white text-center tracking-wide uppercase">Hà Nội</span>
                        </div>
                    </a>
                    
                    <!-- HCMC -->
                    <a href="#" class="group relative rounded-2xl overflow-hidden aspect-square block">
                        <img 
                            src="https://images.unsplash.com/photo-1508189860359-777ad1585e5b?auto=format&fit=crop&w=200&q=80" 
                            alt="Bất động sản Hồ Chí Minh"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                        >
                        <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/20 transition"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-2 z-10">
                            <span class="text-xs font-extrabold text-white text-center tracking-wide uppercase">TP. HCM</span>
                        </div>
                    </a>
                    
                    <!-- Da Nang -->
                    <a href="#" class="group relative rounded-2xl overflow-hidden aspect-square block">
                        <img 
                            src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=200&q=80" 
                            alt="Bất động sản Đà Nẵng"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                        >
                        <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/20 transition"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-2 z-10">
                            <span class="text-xs font-extrabold text-white text-center tracking-wide uppercase">Đà Nẵng</span>
                        </div>
                    </a>
                    
                    <!-- Binh Duong -->
                    <a href="#" class="group relative rounded-2xl overflow-hidden aspect-square block">
                        <img 
                            src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=200&q=80" 
                            alt="Bất động sản Bình Dương"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                        >
                        <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/20 transition"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-2 z-10">
                            <span class="text-xs font-extrabold text-white text-center tracking-wide uppercase">Bình Dương</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
