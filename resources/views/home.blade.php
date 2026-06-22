@extends('layouts.app')

@section('title', 'Tìm Kiếm Bất Động Sản Cho Thuê Nhanh Chóng | BDS Rental')

@section('content')
    <!-- Hero Banner Component (Tích hợp Thanh tìm kiếm) -->
    @include('components.hero')



    <!-- Section 2: Danh sách Bất động sản và Bộ lọc -->
    <section id="listings" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <!-- Section Header -->
        <div class="mb-10 text-left">
            <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Dành riêng cho bạn</span>
            <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin Đăng Cho Thuê Nổi Bật</h2>
        </div>

        @php
            // Dữ liệu $properties được truyền trực tiếp từ Route web.php
        @endphp

        <!-- Grid of Property Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
            @foreach($properties as $property)
                @include('components.property-card', ['property' => $property])
            @endforeach
        </div>

        <!-- Pagination Section -->
        <div class="flex justify-center mt-12">
            <nav class="inline-flex space-x-1 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm" aria-label="Pagination">
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary text-white font-bold shadow-md shadow-primary/20">1</a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">2</a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">3</a>
                <span class="inline-flex items-center justify-center w-10 h-10 text-slate-400">...</span>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">12</a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </nav>
        </div>
    </section>

    <!-- Section 2.5: Tin Đăng Mới Nhất (Giai đoạn 3) -->
    <section class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div class="text-left">
                    <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Cập nhật liên tục</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin Đăng Cho Thuê Mới Nhất</h2>
                </div>
                <a href="/listings" class="inline-flex items-center text-sm font-bold text-primary hover:text-primary-hover hover:underline transition">
                    Xem tất cả tin mới <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <!-- Grid of Latest Property Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @php
                    $displayLatest = isset($latestProperties) && count($latestProperties) > 0 ? $latestProperties : collect($properties)->sortByDesc('created_at')->take(4);
                @endphp
                @foreach($displayLatest as $property)
                    @include('components.property-card', ['property' => $property])
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 2.7: Thống Kê Hệ Thống (Giai đoạn 3) -->
    <section class="py-16 bg-gradient-to-r from-primary to-primary-hover text-white relative overflow-hidden">
        <!-- Background Decor circles -->
        <div class="absolute -top-24 -left-24 w-60 h-60 rounded-full bg-white/5 blur-2xl"></div>
        <div class="absolute -bottom-24 -right-24 w-60 h-60 rounded-full bg-white/10 blur-2xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 text-center">
                <!-- Stat 1 -->
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/10 text-white mb-2">
                        <i class="fa-solid fa-building-circle-check text-xl"></i>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black tracking-tight">50,000+</div>
                    <div class="text-xs font-bold text-slate-200 uppercase tracking-wider">Bất động sản cho thuê</div>
                </div>

                <!-- Stat 2 -->
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/10 text-white mb-2">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black tracking-tight">100,000+</div>
                    <div class="text-xs font-bold text-slate-200 uppercase tracking-wider">Khách hàng tin dùng</div>
                </div>

                <!-- Stat 3 -->
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/10 text-white mb-2">
                        <i class="fa-solid fa-handshake text-xl"></i>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black tracking-tight">15,000+</div>
                    <div class="text-xs font-bold text-slate-200 uppercase tracking-wider">Đối tác chủ nhà & môi giới</div>
                </div>

                <!-- Stat 4 -->
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/10 text-white mb-2">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black tracking-tight">99.8%</div>
                    <div class="text-xs font-bold text-slate-200 uppercase tracking-wider">Giao dịch an toàn</div>
                </div>
            </div>
        </div>
    </section>


    <!-- Section 3: Điểm đến phổ biến -->
    <section class="py-16 bg-slate-50 border-t border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Khu vực nổi bật</span>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Bất Động Sản Theo Tỉnh Thành</h2>
                <p class="text-slate-500 mt-2 max-w-xl mx-auto">Tìm kiếm căn hộ, phòng trọ cho thuê tại các thành phố lớn sầm uất trên cả nước.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- City 1 -->
                <a href="/listings?province=Hà Nội" class="group relative rounded-3xl overflow-hidden h-72 shadow-sm flex items-end p-6">
                    <div class="absolute inset-0">
                        <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" alt="Hà Nội" class="w-full h-full object-cover group-hover:scale-108 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                    </div>
                    <div class="relative z-10 text-left">
                        <h3 class="text-xl font-bold text-white mb-1">Hà Nội</h3>
                        <span class="text-xs font-semibold text-slate-300">12,450 tin đăng</span>
                    </div>
                </a>

                <!-- City 2 -->
                <a href="/listings?province=Thành phố Hồ Chí Minh" class="group relative rounded-3xl overflow-hidden h-72 shadow-sm flex items-end p-6">
                    <div class="absolute inset-0">
                        <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg" alt="TP. Hồ Chí Minh" class="w-full h-full object-cover group-hover:scale-108 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                    </div>
                    <div class="relative z-10 text-left">
                        <h3 class="text-xl font-bold text-white mb-1">TP. Hồ Chí Minh</h3>
                        <span class="text-xs font-semibold text-slate-300">18,320 tin đăng</span>
                    </div>
                </a>

                <!-- City 3 -->
                <a href="/listings?province=Đà Nẵng" class="group relative rounded-3xl overflow-hidden h-72 shadow-sm flex items-end p-6">
                    <div class="absolute inset-0">
                        <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg" alt="Đà Nẵng" class="w-full h-full object-cover group-hover:scale-108 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                    </div>
                    <div class="relative z-10 text-left">
                        <h3 class="text-xl font-bold text-white mb-1">Đà Nẵng</h3>
                        <span class="text-xs font-semibold text-slate-300">3,850 tin đăng</span>
                    </div>
                </a>

                <!-- City 4 -->
                <a href="/listings?province=Bình Dương" class="group relative rounded-3xl overflow-hidden h-72 shadow-sm flex items-end p-6">
                    <div class="absolute inset-0">
                        <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/mvt1mwpuj5vo4qm538rb.jpg" alt="Bình Dương" class="w-full h-full object-cover group-hover:scale-108 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                    </div>
                    <div class="relative z-10 text-left">
                        <h3 class="text-xl font-bold text-white mb-1">Bình Dương</h3>
                        <span class="text-xs font-semibold text-slate-300">2,410 tin đăng</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Section 4: Tại sao chọn chúng tôi -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Dịch vụ tin cậy</span>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Giải Pháp Thuê Bất Động Sản Hoàn Hảo</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Item 1 -->
                <div class="p-8 rounded-3xl border border-slate-100/80 hover:shadow-xl hover:shadow-slate-100 transition duration-300 text-left">
                    <div class="w-14 h-14 rounded-2xl bg-primary-light text-primary flex items-center justify-center text-xl font-bold mb-6">
                        <i class="fa-solid fa-shield-halved text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Tin đăng được xác thực</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Mọi tin đăng trên BDS Rental đều được kiểm duyệt chặt chẽ, đảm bảo thông tin hình ảnh, vị trí và giá thuê chính xác tuyệt đối.
                    </p>
                </div>

                <!-- Item 2 -->
                <div class="p-8 rounded-3xl border border-slate-100/80 hover:shadow-xl hover:shadow-slate-100 transition duration-300 text-left">
                    <div class="w-14 h-14 rounded-2xl bg-primary-light text-primary flex items-center justify-center text-xl font-bold mb-6">
                        <i class="fa-solid fa-bolt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Tìm kiếm siêu tốc</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Với bộ lọc nâng cao thông minh cùng công cụ gợi ý AI, bạn có thể tìm thấy không gian sống ưng ý chỉ trong 5 phút.
                    </p>
                </div>

                <!-- Item 3 -->
                <div class="p-8 rounded-3xl border border-slate-100/80 hover:shadow-xl hover:shadow-slate-100 transition duration-300 text-left">
                    <div class="w-14 h-14 rounded-2xl bg-primary-light text-primary flex items-center justify-center text-xl font-bold mb-6">
                        <i class="fa-solid fa-headset text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Hỗ trợ tận tình 24/7</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Đội ngũ chăm sóc khách hàng và chuyên viên bất động sản của chúng tôi luôn sẵn sàng hỗ trợ tư vấn pháp lý và thủ tục thuê.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Call to Action (CTA) -->
    <section class="pb-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl bg-slate-900 overflow-hidden shadow-xl py-12 px-6 sm:px-12 md:py-16 md:px-16 text-left border border-slate-800">
            <!-- Background effects -->
            <div class="absolute inset-0 opacity-10">
                <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" alt="Landlord CTA" class="w-full h-full object-cover">
            </div>
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-primary/30 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-primary/10 blur-3xl"></div>

            <div class="relative z-10 max-w-2xl flex flex-col justify-center h-full">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl leading-tight">
                    Bạn có bất động sản <br class="hidden sm:inline">
                    <span class="text-primary">muốn cho thuê?</span>
                </h2>
                <p class="mt-4 text-base text-slate-300 leading-relaxed">
                    Đăng tin ngay hôm nay để tiếp cận hơn 100,000 khách thuê tiềm năng truy cập mỗi tháng. Hoàn toàn miễn phí, nhanh chóng và dễ dàng.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="#" class="inline-flex items-center justify-center px-6 py-3.5 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 hover:shadow-primary/35 transition">
                        <i class="fa-solid fa-circle-plus mr-2"></i> Đăng tin cho thuê ngay
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-6 py-3.5 border border-slate-700 hover:border-slate-500 text-sm font-semibold rounded-xl text-slate-100 hover:text-white bg-slate-900/50 hover:bg-slate-900 transition">
                        Liên hệ tư vấn môi giới
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
