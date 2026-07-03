@extends('layouts.app')

@section('title', 'Tin tức & Kiến thức bất động sản mới nhất | BDS Rental')

@section('content')
<div class="bg-gradient-to-br from-slate-900 via-slate-800 to-primary/20 pt-28 pb-16 text-white text-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4">
            Tin Tức & <span class="text-primary-hover">Kiến Thức</span>
        </h1>
        <p class="text-slate-300 max-w-2xl mx-auto text-lg">
            Cập nhật báo cáo thị trường bất động sản mới nhất, phong thủy, thiết kế nội thất và cẩm nang kiến thức từ chuyên gia NKS.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Tab Container -->
    <div 
        x-data     ="{ 
            activeTab: 'baocao'
        }"
        class="w-full"
    >
        <!-- Tab Buttons -->
        <div class="flex items-center justify-start md:justify-center space-x-3 overflow-x-auto pb-4 mb-12 scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none]">
            <button 
                @click="activeTab = 'baocao'" 
                :class="activeTab === 'baocao' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/40'"
                class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
            >
                Báo cáo Thị trường BĐS
            </button>
            <button 
                @click="activeTab = 'gocnhin'" 
                :class="activeTab === 'gocnhin' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/40'"
                class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
            >
                Góc Nhìn NKS
            </button>
            <button 
                @click="activeTab = 'noithat'" 
                :class="activeTab === 'noithat' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/40'"
                class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
            >
                Nội Thất
            </button>
            <button 
                @click="activeTab = 'phongthuy'" 
                :class="activeTab === 'phongthuy' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/40'"
                class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
            >
                Phong Thủy
            </button>
            <button 
                @click="activeTab = 'tintuc'" 
                :class="activeTab === 'tintuc' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/40'"
                class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
            >
                Tin Tức
            </button>
            <button 
                @click="activeTab = 'kienthuc'" 
                :class="activeTab === 'kienthuc' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/40'"
                class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
            >
                Kiến Thức
            </button>
        </div>

        <!-- Tab Contents -->
        @php
            $tabData = [
                'baocao' => [
                    [
                        'title' => 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
                        'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Thị trường căn hộ dịch vụ và Studio ghi nhận tỷ lệ lấp đầy đạt 85%, giá thuê tăng nhẹ 3-5% tại các khu vực trung tâm Phú Nhuận, Quận 3.',
                        'date' => '28 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
                        'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Nhà đầu tư đang ưu tiên các dự án có pháp lý hoàn thiện và có khả năng tạo dòng tiền ngay từ hoạt động cho thuê căn hộ Studio tiện ích.',
                        'date' => '25 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
                        'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Các căn hộ thông minh tích hợp giải pháp xanh, tiện ích trọn gói và bếp tách riêng biệt đang trở thành ưu tiên số một của nhóm khách hàng trẻ tuổi.',
                        'date' => '18 THÁNG 6, 2026'
                    ]
                ],
                'gocnhin' => [
                    [
                        'title' => 'Góc nhìn NKS: Tại sao mô hình Studio tách bếp lại đắt khách?',
                        'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Phân tích chi tiết hành vi của khách hàng thuê hiện đại và lý do tại sao các phòng trọ/căn hộ có khu vực bếp riêng biệt luôn trong trạng thái cháy hàng.',
                        'date' => '02 THÁNG 7, 2026'
                    ],
                    [
                        'title' => 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
                        'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Những bài học thực tế từ NKS giúp các chủ đầu tư căn hộ tăng tỷ suất lợi nhuận lên đến 12%/năm nhờ cải tạo thiết kế và tối giản hóa quy trình vận hành.',
                        'date' => '29 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
                        'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Tầm nhìn chiến lược về sự phát triển vượt bậc của các căn hộ và khu dân cư dọc trục sông Sài Gòn, đặc biệt là khu vực Quận 2 cũ và Bình Thạnh.',
                        'date' => '20 THÁNG 6, 2026'
                    ]
                ],
                'noithat' => [
                    [
                        'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                        'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Ứng dụng phong cách tối giản Japandi giúp các không gian căn hộ Studio diện tích nhỏ trở nên thông thoáng, rộng rãi và tối ưu công năng sử dụng.',
                        'date' => '01 THÁNG 7, 2026'
                    ],
                    [
                        'title' => 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                        'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Hướng dẫn chọn gạch men chống thấm, sơn phủ acrylic chống ẩm và thiết kế hệ thống quạt thông gió tối ưu giúp căn phòng luôn sạch sẽ thơm tho.',
                        'date' => '27 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
                        'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Kết hợp hài hòa giữa ánh sáng tự nhiên ban ngày và hệ thống đèn LED âm trần, đèn thả ấm nhiệt độ màu 3000K để thư giãn tối đa sau ngày làm việc.',
                        'date' => '15 THÁNG 6, 2026'
                    ]
                ],
                'phongthuy' => [
                    [
                        'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
                        'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Tránh đặt giường đối diện cửa chính, dưới xà ngang nhà hay trước gương soi lớn nhằm bảo vệ sức khỏe và đón nhận luồng sinh khí tốt lành.',
                        'date' => '03 THÁNG 7, 2026'
                    ],
                    [
                        'title' => 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
                        'image' => 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Tư vấn chi tiết từ chuyên gia phong thủy giúp gia chủ mệnh Thổ đón vượng khí, tài lộc hanh thông nhờ việc lựa chọn đúng hướng nhà và tông màu sơn chủ đạo.',
                        'date' => '28 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
                        'image' => 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Gợi ý các loại cây dễ trồng như Kim Tiền, Thiết Mộc Lan, Vạn Niên Thanh giúp cải thiện chất lượng không khí và gia tăng năng lượng may mắn.',
                        'date' => '19 THÁNG 6, 2026'
                    ]
                ],
                'tintuc' => [
                    [
                        'title' => 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
                        'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Dự thảo luật mới siết chặt công tác phòng cháy chữa cháy (PCCC) và yêu cầu đăng ký kinh doanh bắt buộc đối với các hộ cho thuê quy mô lớn.',
                        'date' => '03 THÁNG 7, 2026'
                    ],
                    [
                        'title' => 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
                        'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Cung cấp hơn 3,000 căn hộ chất lượng cao giá cả phải chăng dành riêng cho công nhân, người lao động thu nhập thấp tại khu vực phía Tây thành phố.',
                        'date' => '30 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Khởi động dự án cải tạo hạ tầng giao thông và mở rộng lộ giới trục đường chính',
                        'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Kế hoạch triển khai nâng cấp mở rộng các tuyến giao thông huyết mạch kết nối trực tiếp với trung tâm thành phố giúp nâng tầm giá trị các dự án lân cận.',
                        'date' => '22 THÁNG 6, 2026'
                    ]
                ],
                'kienthuc' => [
                    [
                        'title' => 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                        'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Hướng dẫn đầy đủ các bước sang nhượng quyền thuê nhà, xử lý phần tiền đặt cọc và lập biên bản thanh lý hợp đồng cũ an toàn, nhanh chóng.',
                        'date' => '02 THÁNG 7, 2026'
                    ],
                    [
                        'title' => 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                        'image' => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Chia sẻ phương pháp kiểm tra phôi sổ hồng bằng mắt thường, xác thực thông tin quy hoạch tại phòng tài nguyên môi trường tránh bẫy lừa đảo.',
                        'date' => '26 THÁNG 6, 2026'
                    ],
                    [
                        'title' => 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
                        'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                        'excerpt' => 'Tổng hợp các loại phí cần đóng gồm thuế thu nhập cá nhân 2%, lệ phí trước bạ 0.5% và cách tính đơn giản chính xác cho người giao dịch lần đầu.',
                        'date' => '17 THÁNG 6, 2026'
                    ]
                ]
            ];
        @endphp

        @foreach($tabData as $tabName => $articles)
            <div 
                x-show="activeTab === '{{ $tabName }}'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-1 md:grid-cols-3 gap-8"
                x-cloak
            >
                @foreach($articles as $article)
                    <div class="bg-white rounded-[24px] border border-slate-100 p-4 hover:shadow-lg hover:-translate-y-1 transition duration-300 flex flex-col h-full group">
                        <!-- Image Container -->
                        <div class="rounded-2xl overflow-hidden aspect-[16/10] mb-4 relative bg-slate-100 flex-shrink-0">
                            <img 
                                src="{{ $article['image'] }}" 
                                alt="{{ $article['title'] }}"
                                class="w-full h-full object-cover group-hover:scale-103 transition duration-500"
                                loading="lazy"
                            >
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col flex-grow text-left">
                            <span class="text-[10px] text-slate-400 font-extrabold mb-2 block uppercase tracking-wider">
                                {{ $article['date'] }}
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-primary transition line-clamp-2 leading-snug mb-2">
                                <a href="#">{{ $article['title'] }}</a>
                            </h3>
                            <p class="text-xs text-slate-550 line-clamp-3 leading-relaxed mb-4">
                                {{ $article['excerpt'] }}
                            </p>
                            
                            <!-- Read more -->
                            <a href="#" class="mt-auto inline-flex items-center text-xs font-black text-primary hover:text-primary-hover transition duration-150 cursor-pointer">
                                Đọc tiếp <i class="fa-solid fa-arrow-right-long ml-1.5 transition-transform group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection
