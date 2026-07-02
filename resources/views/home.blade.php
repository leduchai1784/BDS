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

    <!-- Section 2.7: Dự án nổi bật (Featured Projects) -->
    <section class="py-16 bg-white border-t border-slate-100 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div class="text-left">
                    <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Dự án trọng điểm</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Dự Án Nổi Bật</h2>
                </div>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center text-sm font-bold text-primary hover:text-primary-hover hover:underline transition">
                    Xem tất cả dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <!-- Grid of Projects -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @if(isset($featuredProjects) && $featuredProjects->count() > 0)
                    @foreach($featuredProjects as $project)
                        <div class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl hover:shadow-slate-100/50 transition-all duration-300 flex flex-col h-full">
                            <!-- Project Image -->
                            <div class="relative aspect-video bg-slate-100 overflow-hidden flex-shrink-0">
                                @if(is_array($project->images) && count($project->images) > 0)
                                    <img 
                                        src="{{ $project->images[0] }}" 
                                        alt="{{ $project->title }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i class="fa-regular fa-image text-4xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4 z-10">
                                    @if($project->status === 'selling')
                                        <span class="bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đang mở bán</span>
                                    @elseif($project->status === 'upcoming')
                                        <span class="bg-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Sắp mở bán</span>
                                    @else
                                        <span class="bg-blue-600 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đã bàn giao</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Project Content -->
                            <div class="p-6 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-xs font-black uppercase text-primary tracking-widest block mb-2">{{ $project->investor }}</span>
                                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-primary transition duration-150 mb-3 line-clamp-1">
                                        <a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a>
                                    </h3>
                                    <p class="text-slate-500 text-sm mb-5 line-clamp-3 leading-relaxed">
                                        {{ $project->description }}
                                    </p>
                                </div>

                                <!-- Highlights info -->
                                <div class="pt-5 border-t border-slate-50 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                        <span class="truncate">{{ $project->price_range ?? 'Liên hệ' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-ruler-combined text-primary"></i>
                                        <span class="truncate">{{ $project->scale ?? 'Đang cập nhật' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 col-span-2">
                                        <i class="fa-solid fa-location-dot text-primary"></i>
                                        <span class="truncate">{{ $project->location }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- View details CTA -->
                            <div class="px-6 pb-6 pt-2">
                                <a 
                                    href="{{ route('projects.show', $project->slug) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border border-slate-100 text-sm font-extrabold rounded-2xl text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200"
                                >
                                    Xem chi tiết dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback / Mock Projects if database is empty -->
                    @php
                        $mocks = [
                            [
                                'title' => 'Vinhomes Grand Park',
                                'investor' => 'Vingroup',
                                'description' => 'Đại đô thị thông minh đẳng cấp quốc tế tại trung tâm Quận 9, TP. Hồ Chí Minh với quy mô lên đến 271 ha.',
                                'price_range' => '35 - 55 triệu/m²',
                                'scale' => '44.000 căn hộ',
                                'location' => 'Quận 9, TP. Hồ Chí Minh',
                                'status' => 'selling',
                                'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
                            ],
                            [
                                'title' => 'Masteri Centre Point',
                                'investor' => 'Masterise Homes',
                                'description' => 'Khu căn hộ compound cao cấp bậc nhất nằm tại trung tâm đại đô thị Vinhomes Grand Park Quận 9.',
                                'price_range' => '50 - 70 triệu/m²',
                                'scale' => '5.000 căn hộ',
                                'location' => 'Quận 9, TP. Hồ Chí Minh',
                                'status' => 'upcoming',
                                'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg'
                            ],
                            [
                                'title' => 'Eco Green Saigon',
                                'investor' => 'Xuân Mai Corp',
                                'description' => 'Tổ hợp thương mại dịch vụ và căn hộ cao cấp tọa lạc ngay mặt tiền đại lộ Nguyễn Văn Linh, Quận 7.',
                                'price_range' => '45 - 60 triệu/m²',
                                'scale' => '4.000 căn hộ',
                                'location' => 'Quận 7, TP. Hồ Chí Minh',
                                'status' => 'handed_over',
                                'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg'
                            ]
                        ];
                    @endphp
                    @foreach($mocks as $mock)
                        <div class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl hover:shadow-slate-100/50 transition-all duration-300 flex flex-col h-full">
                            <!-- Project Image -->
                            <div class="relative aspect-video bg-slate-100 overflow-hidden flex-shrink-0">
                                <img 
                                    src="{{ $mock['image'] }}" 
                                    alt="{{ $mock['title'] }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                >
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4 z-10">
                                    @if($mock['status'] === 'selling')
                                        <span class="bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đang mở bán</span>
                                    @elseif($mock['status'] === 'upcoming')
                                        <span class="bg-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Sắp mở bán</span>
                                    @else
                                        <span class="bg-blue-600 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đã bàn giao</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Project Content -->
                            <div class="p-6 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-xs font-black uppercase text-primary tracking-widest block mb-2">{{ $mock['investor'] }}</span>
                                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-primary transition duration-150 mb-3 line-clamp-1">
                                        <a href="/projects">{{ $mock['title'] }}</a>
                                    </h3>
                                    <p class="text-slate-500 text-sm mb-5 line-clamp-3 leading-relaxed">
                                        {{ $mock['description'] }}
                                    </p>
                                </div>

                                <!-- Highlights info -->
                                <div class="pt-5 border-t border-slate-50 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                        <span class="truncate">{{ $mock['price_range'] }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-ruler-combined text-primary"></i>
                                        <span class="truncate">{{ $mock['scale'] }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 col-span-2">
                                        <i class="fa-solid fa-location-dot text-primary"></i>
                                        <span class="truncate">{{ $mock['location'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- View details CTA -->
                            <div class="px-6 pb-6 pt-2">
                                <a 
                                    href="/projects"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border border-slate-100 text-sm font-extrabold rounded-2xl text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200"
                                >
                                    Xem chi tiết dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    <!-- Section 3: Nhu cầu cộng đồng (Community Demands Slider) -->
    <section class="py-16 bg-slate-50 border-t border-b border-slate-100 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div 
                x-data="{ 
                    slideNext() {
                        const container = $refs.demandContainer;
                        container.scrollBy({ left: 300, behavior: 'smooth' });
                    },
                    slidePrev() {
                        const container = $refs.demandContainer;
                        container.scrollBy({ left: -300, behavior: 'smooth' });
                    }
                }"
                class="bg-white rounded-[32px] p-8 sm:p-10 border border-slate-100 shadow-xs text-left relative"
            >
                <!-- Header row -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Nhu cầu</h2>
                        <p class="text-sm text-slate-500 mt-1">Khám phá nhu cầu mua, bán, thuê mới nhất từ cộng đồng</p>
                    </div>
                    <!-- Slider Navigation arrows -->
                    <div class="flex items-center space-x-2.5">
                        <button 
                            @click="slidePrev()" 
                            class="w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-primary hover:border-primary transition flex items-center justify-center shadow-xs cursor-pointer active:scale-95"
                        >
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <button 
                            @click="slideNext()" 
                            class="w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-primary hover:border-primary transition flex items-center justify-center shadow-xs cursor-pointer active:scale-95"
                        >
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Slides Container -->
                <div 
                    x-ref="demandContainer" 
                    class="flex space-x-6 overflow-x-auto [&::-webkit-scrollbar]:hidden scrollbar-none scroll-smooth pb-3"
                    style="-ms-overflow-style: none; scrollbar-width: none;"
                >
                    <!-- Card 1: Tạo nhu cầu -->
                    <div class="w-64 h-48 rounded-[24px] border-2 border-dashed border-primary-light hover:border-primary bg-white flex flex-col items-center justify-center p-6 flex-shrink-0 cursor-pointer group transition duration-300">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-115 transition duration-300 mb-3">
                            <i class="fa-solid fa-plus text-base"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-850 group-hover:text-primary transition duration-150 mb-0.5">Tạo nhu cầu</h4>
                        <p class="text-xs text-slate-400 font-medium">Chia sẻ điều bạn đang tìm</p>
                    </div>

                    <!-- Mock Card 2 -->
                    <div class="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300">
                        <div class="flex items-center justify-between">
                            <div class="w-8 h-8 rounded-full bg-primary-light text-primary font-bold text-xs flex items-center justify-center">HT</div>
                            <span class="px-2.5 py-0.5 rounded-lg bg-primary-light text-primary text-[10px] font-extrabold uppercase tracking-wider">Cho thuê</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
                            Cho thuê nhà hẻm Thanh Huy 1 Quận Thanh Khê, TP. Đ...
                        </h4>
                        <div class="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
                            <span>5 giờ trước</span>
                            <span class="truncate max-w-[130px]">Quận Thanh Khê, Thả...</span>
                        </div>
                    </div>

                    <!-- Mock Card 3 -->
                    <div class="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300">
                        <div class="flex items-center justify-between">
                            <div class="w-8 h-8 rounded-full bg-primary-light text-primary font-bold text-xs flex items-center justify-center">HT</div>
                            <span class="px-2.5 py-0.5 rounded-lg bg-primary-light text-primary text-[10px] font-extrabold uppercase tracking-wider">Cho thuê</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
                            Cho thuê nhà hẻm Mai Thúc Lân Quận Ngũ Hành...
                        </h4>
                        <div class="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
                            <span>6 giờ trước</span>
                            <span class="truncate max-w-[130px]">Quận Ngũ Hành Sơn, ...</span>
                        </div>
                    </div>

                    <!-- Mock Card 4 -->
                    <div class="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300">
                        <div class="flex items-center justify-between">
                            <div class="w-8 h-8 rounded-full bg-primary-light text-primary font-bold text-xs flex items-center justify-center">KL</div>
                            <span class="px-2.5 py-0.5 rounded-lg bg-primary-light text-primary text-[10px] font-extrabold uppercase tracking-wider">Cho thuê</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
                            Cho thuê phòng trọ Trần Cao Vân Quận Thanh Khê,...
                        </h4>
                        <div class="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
                            <span>7 giờ trước</span>
                            <span class="truncate max-w-[130px]">Quận Thanh Khê, Thả...</span>
                        </div>
                    </div>

                    <!-- Mock Card 5 -->
                    <div class="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300">
                        <div class="flex items-center justify-between">
                            <div class="w-8 h-8 rounded-full bg-primary-light text-primary font-bold text-xs flex items-center justify-center">LV</div>
                            <span class="px-2.5 py-0.5 rounded-lg bg-primary-light text-primary text-[10px] font-extrabold uppercase tracking-wider">Cho thuê</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
                            Cho thuê phòng trọ Hải Phòng Quận Hải Châu,...
                        </h4>
                        <div class="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
                            <span>8 giờ trước</span>
                            <span class="truncate max-w-[130px]">Quận Hải Châu, Thàn...</span>
                        </div>
                    </div>

                    <!-- Mock Card 6 -->
                    <div class="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300">
                        <div class="flex items-center justify-between">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg" class="w-8 h-8 rounded-full object-cover">
                            <span class="px-2.5 py-0.5 rounded-lg bg-orange-50 text-orange-600 text-[10px] font-extrabold uppercase tracking-wider">Cần bán</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
                            NHÀ ĐẸP – HẺM XE HƠI 6M – TRUNG TÂM TÂN...
                        </h4>
                        <div class="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
                            <span>một ngày trước</span>
                            <span class="truncate max-w-[130px]">Quận Tân Bình, Thàn...</span>
                        </div>
                    </div>

                    <!-- Mock Card 7 -->
                    <div class="w-64 h-48 bg-white rounded-[24px] border border-slate-200/60 p-6 flex flex-col justify-between flex-shrink-0 hover:shadow-md transition duration-300">
                        <div class="flex items-center justify-between">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg" class="w-8 h-8 rounded-full object-cover">
                            <span class="px-2.5 py-0.5 rounded-lg bg-orange-50 text-orange-600 text-[10px] font-extrabold uppercase tracking-wider">Cần bán</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug mt-2">
                            Bán nhà mặt tiền Nguyễn Đình Chính Phú Nhuận, TP. H...
                        </h4>
                        <div class="border-t border-slate-100 pt-2 flex items-center justify-between text-[10px] text-slate-400 font-semibold mt-auto">
                            <span>một ngày trước</span>
                            <span class="truncate max-w-[130px]">Phú Nhuận, TP. Hồ C...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Video Nhà Đất (Real Estate Video Showcase) -->
    <section 
        x-data="{ 
            videoModalOpen: false, 
            activeVideoUrl: '', 
            openVideo(id) { 
                this.activeVideoUrl = 'https://www.youtube.com/embed/' + id + '?autoplay=1'; 
                this.videoModalOpen = true; 
            },
            closeVideo() {
                this.videoModalOpen = false;
                this.activeVideoUrl = '';
            }
        }"
        class="py-20 bg-white text-left"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Trải nghiệm thực tế</span>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Video Review Nhà Đất</h2>
                <p class="text-slate-500 mt-2 max-w-xl mx-auto">Cùng khám phá các dự án căn hộ, nhà phố và không gian sống thực tế qua các thước phim sống động.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Featured Video (8 cols) -->
                <div class="lg:col-span-8">
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group relative rounded-3xl overflow-hidden aspect-video shadow-lg border border-slate-100 cursor-pointer"
                    >
                        <!-- Video Thumbnail -->
                        <img 
                            src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" 
                            alt="Featured Real Estate Tour" 
                            class="w-full h-full object-cover group-hover:scale-103 transition duration-500"
                        >
                        <!-- Dark overlay -->
                        <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/40 transition duration-300"></div>
                        
                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/45 group-hover:scale-110 active:scale-95 transition duration-300">
                                <i class="fa-solid fa-play text-xl ml-1"></i>
                            </div>
                        </div>

                        <!-- Video info -->
                        <div class="absolute bottom-6 left-6 right-6 text-left">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-primary text-white mb-3">
                                <i class="fa-solid fa-eye mr-1"></i> XEM REVIEW CHI TIẾT
                            </span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 leading-tight">
                                Khám Phá Căn Hộ Penthouse Cao Cấp Giữa Lòng Thành Phố
                            </h3>
                            <p class="text-xs text-slate-200 line-clamp-1 font-medium">
                                Review thực tế căn hộ Penthouse 3 phòng ngủ sang trọng bậc nhất với tầm nhìn Landmark 81 cực đỉnh.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Side video playlist (4 cols) -->
                <div class="lg:col-span-4 flex flex-col justify-between gap-4">
                    <!-- Video item 1 -->
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group flex items-center space-x-4 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="relative w-28 h-20 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg" alt="Video 1" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <div class="w-7 h-7 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-md">
                                    <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <h4 class="text-xs font-extrabold text-slate-800 group-hover:text-primary transition line-clamp-2 leading-snug mb-1">
                                Tour Căn Hộ Studio Tiện Nghi Quận 1 Giá Chỉ 8 Triệu
                            </h4>
                            <span class="text-[10px] text-slate-400 font-semibold block">Thời lượng: 5:45</span>
                        </div>
                    </div>

                    <!-- Video item 2 -->
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group flex items-center space-x-4 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="relative w-28 h-20 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg" alt="Video 2" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <div class="w-7 h-7 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-md">
                                    <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <h4 class="text-xs font-extrabold text-slate-800 group-hover:text-primary transition line-clamp-2 leading-snug mb-1">
                                Cận Cảnh Biệt Thự Sân Vườn Đáng Sống Tại Khu Phú Mỹ Hưng
                            </h4>
                            <span class="text-[10px] text-slate-400 font-semibold block">Thời lượng: 12:30</span>
                        </div>
                    </div>

                    <!-- Video item 3 -->
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group flex items-center space-x-4 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="relative w-28 h-20 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/mvt1mwpuj5vo4qm538rb.jpg" alt="Video 3" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <div class="w-7 h-7 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-md">
                                    <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <h4 class="text-xs font-extrabold text-slate-800 group-hover:text-primary transition line-clamp-2 leading-snug mb-1">
                                Đánh Giá Chung Cư Cao Cấp Đầy Đủ Nội Thất Ở Đà Nẵng
                            </h4>
                            <span class="text-[10px] text-slate-400 font-semibold block">Thời lượng: 8:15</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Modal Overlay -->
        <template x-teleport="body">
            <div 
                x-show="videoModalOpen" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 p-4 sm:p-6"
                x-transition
                x-cloak
            >
                <div 
                    @click.away="closeVideo()" 
                    class="relative w-full max-w-4xl bg-black rounded-3xl overflow-hidden shadow-2xl border border-slate-800"
                >
                    <!-- Close button -->
                    <button 
                        @click="closeVideo()" 
                        class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center transition border border-white/10"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>

                    <!-- Iframe Container -->
                    <div class="aspect-video w-full bg-black">
                        <template x-if="videoModalOpen">
                            <iframe 
                                :src="activeVideoUrl" 
                                class="w-full h-full border-0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                            ></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </template>
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
