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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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

    <hr class="border-slate-100 mb-12">

    <!-- Lower Section: Article Feed vs Sidebar Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Articles Feed (Left 66%) -->
        <div class="lg:col-span-2 space-y-8">
            <h2 class="text-lg font-black text-slate-900 border-b border-slate-200 pb-3 text-left">
                Tin mới cập nhật
            </h2>

            <!-- Feed list -->
            @php
                $feedArticles = [
                    [
                        'title' => 'Hướng Dẫn Đăng Tin Nhà Đất Chuẩn SEO và AI Lên Xu Hướng NKS',
                        'category' => 'Kiến Thức',
                        'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=500&q=80',
                        'excerpt' => 'Làm thế nào để tin đăng cho thuê phòng trọ, căn hộ tiếp cận hàng ngàn khách thuê tiềm năng? Áp dụng ngay phương pháp chuẩn SEO và tích hợp AI tối ưu hóa tiêu đề của NKS.',
                        'date' => '29/06/2026'
                    ],
                    [
                        'title' => 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
                        'category' => 'Báo Cáo',
                        'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=500&q=80',
                        'excerpt' => 'Hạ tầng giao thông, pháp lý dự án và các tiện ích xanh xung quanh là 3 trụ cột cốt lõi quyết định biên độ tăng giá của bất động sản trong giai đoạn bình thường mới.',
                        'date' => '28/06/2026'
                    ],
                    [
                        'title' => 'Nhà Đầu Tư Phía Bắc Nam Tiến Thị Trường Bất Động Sản',
                        'category' => 'Tin Tức',
                        'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=500&q=80',
                        'excerpt' => 'Nhận định xu hướng chuyển dịch dòng vốn mạnh mẽ từ Hà Nội và các tỉnh phía Bắc vào phân khúc căn hộ dịch vụ tạo dòng tiền mặt hàng tháng tại TP. Hồ Chí Minh.',
                        'date' => '27/06/2026'
                    ],
                    [
                        'title' => 'Cách Tối Ưu Hóa Quy Trình Mua Nhà Qua Nền Tảng Online 2026',
                        'category' => 'Kiến Thức',
                        'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=500&q=80',
                        'excerpt' => 'Ứng dụng công nghệ thực tế ảo VR 360 và chữ ký số chuẩn hóa giao dịch giúp người mua tiết kiệm 80% thời gian đi xem nhà thực tế và hoàn tất pháp lý an toàn.',
                        'date' => '26/6/2026'
                    ],
                    [
                        'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                        'category' => 'Nội Thất',
                        'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=500&q=80',
                        'excerpt' => 'Khám phá các giải pháp thiết kế nội thất đa năng, tối giản nhưng hiện đại giúp biến đổi không gian căn hộ diện tích nhỏ trở nên tiện nghi rộng rãi hơn bao giờ hết.',
                        'date' => '25/06/2026'
                    ],
                    [
                        'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường ngủ',
                        'category' => 'Phong Thủy',
                        'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=500&q=80',
                        'excerpt' => 'Tư vấn từ chuyên gia phong thủy về hướng giường, vị trí gương soi và cách bố trí vật dụng phòng ngủ để đem lại sức khỏe dồi dào, thu hút tài lộc.',
                        'date' => '24/06/2026'
                    ]
                ];
            @endphp

            <div class="divide-y divide-slate-100">
                @foreach($feedArticles as $article)
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
                                {{ $article['category'] }} • {{ $article['date'] }}
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-primary transition leading-snug mb-2">
                                <a href="#">{{ $article['title'] }}</a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-3">
                                {{ $article['excerpt'] }}
                            </p>
                            <a href="#" class="inline-flex items-center text-xs font-bold text-slate-400 group-hover:text-primary transition mt-auto">
                                Xem thêm <i class="fa-solid fa-arrow-right-long ml-1.5 transition-transform group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
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
