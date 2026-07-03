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
            <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin đăng nổi bật</h2>
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
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin đăng mới nhất</h2>
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

    <!-- Section 2.7: Kho dự án nổi bật (Featured Projects Slider) -->
    <section class="py-16 bg-white border-t border-slate-100 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div 
                x-data="{ 
                    slideNext() {
                        const container = $refs.projectContainer;
                        container.scrollBy({ left: 384, behavior: 'smooth' });
                    },
                    slidePrev() {
                        const container = $refs.projectContainer;
                        container.scrollBy({ left: -384, behavior: 'smooth' });
                    }
                }"
                class="text-left"
            >
                <!-- Section Header -->
                <div class="mb-8 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">Kho dự án nổi bật</h2>
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

                <!-- Projects Slides Container -->
                <div 
                    x-ref="projectContainer" 
                    class="flex space-x-6 overflow-x-auto [&::-webkit-scrollbar]:hidden scrollbar-none scroll-smooth pb-4"
                    style="-ms-overflow-style: none; scrollbar-width: none;"
                >
                    @if(isset($featuredProjects) && $featuredProjects->count() > 0)
                        @foreach($featuredProjects as $project)
                            @php
                                $imgUrl = (is_array($project->images) && count($project->images) > 0) 
                                    ? $project->images[0] 
                                    : 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg';
                                
                                $statusDotColor = 'bg-blue-500';
                                $statusText = 'Đang mở bán';
                                if($project->status === 'upcoming') {
                                    $statusDotColor = 'bg-orange-500';
                                    $statusText = 'Sắp mở bán';
                                } elseif($project->status === 'handed_over' || $project->status === 'completed') {
                                    $statusDotColor = 'bg-emerald-500';
                                    $statusText = 'Đã bàn giao';
                                }
                            @endphp
                            <!-- Project Card -->
                            <div class="w-96 h-64 rounded-[24px] overflow-hidden relative flex-shrink-0 group shadow-sm hover:shadow-lg transition-all duration-300">
                                <!-- Background Image -->
                                <img 
                                    src="{{ $imgUrl }}" 
                                    alt="{{ $project->title }}" 
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-103 transition duration-500"
                                >
                                <!-- Dark Overlay at the bottom -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-transparent z-1"></div>
                                
                                <!-- Top Badges -->
                                <div class="absolute top-4 left-4 z-10">
                                    <!-- Status Pill -->
                                    <div class="px-3 py-1.5 rounded-full bg-black/40 backdrop-blur-xs flex items-center">
                                        <span class="w-2 h-2 rounded-full {{ $statusDotColor }} mr-1.5"></span>
                                        <span class="text-[10px] text-white font-extrabold uppercase tracking-wider">{{ $statusText }}</span>
                                    </div>
                                </div>

                                <!-- Bottom Info -->
                                <div class="absolute bottom-5 left-6 right-6 z-10" style="text-align: left !important; left: 24px !important; right: 24px !important;">
                                    <h3 class="text-lg font-bold text-white uppercase tracking-wide line-clamp-1 mb-1" style="text-align: left !important;">
                                        <a href="{{ route('projects.show', $project->slug) }}" class="text-white hover:text-white hover:underline">{{ $project->title }}</a>
                                    </h3>
                                    <p class="text-xs text-white/90 font-medium line-clamp-1 mb-1.5" style="text-align: left !important;">
                                        {{ $project->location }}
                                    </p>
                                    <span class="text-sm font-extrabold text-white block" style="text-align: left !important;">
                                        {{ $project->price_range ?? 'Liên hệ' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback / Mock Projects if database is empty -->
                        @php
                            $mocks = [
                                [
                                    'title' => 'THE PRIVÉ',
                                    'location' => 'An Phú, Quận Thủ Đức, Hồ Chí Minh',
                                    'price' => '4,9 tỷ - 15 tỷ',
                                    'status_dot' => 'bg-blue-500',
                                    'status_text' => 'Đang mở bán',
                                    'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
                                ],
                                [
                                    'title' => 'THE EMERALD GARDEN VIEW',
                                    'location' => 'Hưng Định, Quận Thuận An, Bình Dương',
                                    'price' => '1,3 tỷ - 3,2 tỷ',
                                    'status_dot' => 'bg-blue-500',
                                    'status_text' => 'Đang mở bán',
                                    'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg'
                                ],
                                [
                                    'title' => 'Ansana by Kita',
                                    'location' => 'An Lạc, Quận Bình Tân, Hồ Chí Minh',
                                    'price' => '90 triệu - 100 triệu',
                                    'status_dot' => 'bg-emerald-500',
                                    'status_text' => 'Đang nhận booking',
                                    'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg'
                                ]
                            ];
                        @endphp
                        @foreach($mocks as $mock)
                            <div class="w-96 h-64 rounded-[24px] overflow-hidden relative flex-shrink-0 group shadow-sm hover:shadow-lg transition-all duration-300">
                                <!-- Background Image -->
                                <img 
                                    src="{{ $mock['image'] }}" 
                                    alt="{{ $mock['title'] }}" 
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-103 transition duration-500"
                                >
                                <!-- Dark Overlay at the bottom -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-transparent z-1"></div>
                                
                                <!-- Top Badges -->
                                <div class="absolute top-4 left-4 z-10">
                                    <!-- Status Pill -->
                                    <div class="px-3 py-1.5 rounded-full bg-black/40 backdrop-blur-xs flex items-center">
                                        <span class="w-2 h-2 rounded-full {{ $mock['status_dot'] }} mr-1.5"></span>
                                        <span class="text-[10px] text-white font-extrabold uppercase tracking-wider">{{ $mock['status_text'] }}</span>
                                    </div>
                                </div>

                                <!-- Bottom Info -->
                                <div class="absolute bottom-5 left-6 right-6 z-10" style="text-align: left !important; left: 24px !important; right: 24px !important;">
                                    <h3 class="text-lg font-bold text-white uppercase tracking-wide line-clamp-1 mb-1" style="text-align: left !important;">
                                        <a href="/projects" class="text-white hover:text-white hover:underline">{{ $mock['title'] }}</a>
                                    </h3>
                                    <p class="text-xs text-white/90 font-medium line-clamp-1 mb-1.5" style="text-align: left !important;">
                                        {{ $mock['location'] }}
                                    </p>
                                    <span class="text-sm font-extrabold text-white block" style="text-align: left !important;">
                                        {{ $mock['price'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
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

    <!-- Section 4: Video Nhà Đất (Real Estate Video Showcase Slider) -->
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
            },
            slideNext() {
                const container = $refs.videoContainer;
                container.scrollBy({ left: 384, behavior: 'smooth' });
            },
            slidePrev() {
                const container = $refs.videoContainer;
                container.scrollBy({ left: -384, behavior: 'smooth' });
            }
        }"
        class="py-16 bg-white text-left"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-8 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Video nhà đất</h2>
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

            <!-- Videos Slides Container -->
            <div 
                x-ref="videoContainer" 
                class="flex space-x-6 overflow-x-auto [&::-webkit-scrollbar]:hidden scrollbar-none scroll-smooth pb-4"
                style="-ms-overflow-style: none; scrollbar-width: none;"
            >
                @php
                    $videos = [
                        [
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'title' => 'Căn hộ Studio dịch vụ tách bếp Phú Nhuận',
                            'location' => 'Phú Nhuận, TPHCM',
                            'badge' => 'CHO THUÊ',
                            'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
                        ],
                        [
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'title' => 'Biệt thự song lập compound Thảo Điền Quận 2',
                            'location' => 'Quận 2, TPHCM',
                            'badge' => 'ĐANG BÁN',
                            'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg'
                        ],
                        [
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'title' => 'Nhà phố mặt tiền kinh doanh 222 Lê Văn Sỹ',
                            'location' => 'Phú Nhuận, TPHCM',
                            'badge' => 'ĐANG BÁN',
                            'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg'
                        ],
                        [
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'title' => 'Căn hộ Landmark 81 Full nội thất view sông',
                            'location' => 'Bình Thạnh, TPHCM',
                            'badge' => 'CHO THUÊ',
                            'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/mvt1mwpuj5vo4qm538rb.jpg'
                        ]
                    ];
                @endphp
                @foreach($videos as $video)
                    <div 
                        @click="openVideo('{{ $video['youtube_id'] }}')" 
                        class="w-96 h-64 rounded-[24px] overflow-hidden relative flex-shrink-0 group shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
                    >
                        <!-- Background Image -->
                        <img 
                            src="{{ $video['image'] }}" 
                            alt="{{ $video['title'] }}" 
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-103 transition duration-500"
                        >
                        <!-- Dark Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-transparent z-1"></div>
                        
                        <!-- Top-left category badge -->
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-2.5 py-1 rounded-lg bg-primary text-white text-[9px] font-black uppercase tracking-wider">
                                {{ $video['badge'] }}
                            </span>
                        </div>

                        <!-- Center Play Button icon -->
                        <div class="absolute inset-0 flex items-center justify-center z-10">
                            <div class="w-12 h-12 rounded-full border border-white/40 bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition duration-300 transform group-hover:scale-110 shadow-lg">
                                <i class="fa-solid fa-play text-sm ml-0.5"></i>
                            </div>
                        </div>

                        <!-- Bottom Title and Location -->
                        <div class="absolute bottom-5 left-6 right-6 z-10" style="text-align: left !important; left: 24px !important; right: 24px !important;">
                            <h4 class="text-sm font-bold text-white leading-snug line-clamp-2 mb-1.5" style="text-align: left !important; color: #ffffff !important;">
                                {{ $video['title'] }}
                            </h4>
                            <p class="text-[10px] font-medium block" style="text-align: left !important; color: rgba(255, 255, 255, 0.85) !important;">
                                {{ $video['location'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
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

    <!-- Section 4.5: News & Knowledge Tabbed Section -->
    <section id="news" class="py-16 bg-white text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header: Title and View More -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a]">Tin tức bất động sản</h2>
                <a href="{{ route('news') }}" class="text-sm font-semibold text-[#556070] hover:text-primary transition flex items-center gap-1 cursor-pointer">
                    Xem thêm <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <!-- Tab Container -->
            <div 
                x-data="{ 
                    activeTab: 'baocao'
                }"
                class="w-full"
            >
                <!-- Tab Buttons -->
                <div class="flex items-center justify-start gap-2.5 overflow-x-auto pb-4 mb-8 scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none]">
                    <button 
                        @click="activeTab = 'baocao'" 
                        :class="activeTab === 'baocao' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                        class="px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Báo cáo Thị trường BĐS Việt Nam
                    </button>
                    <button 
                        @click="activeTab = 'gocnhin'" 
                        :class="activeTab === 'gocnhin' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                        class="px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Góc Nhìn NKS
                    </button>
                    <button 
                        @click="activeTab = 'noithat'" 
                        :class="activeTab === 'noithat' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                        class="px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Nội Thất
                    </button>
                    <button 
                        @click="activeTab = 'phongthuy'" 
                        :class="activeTab === 'phongthuy' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                        class="px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Phong Thủy
                    </button>
                    <button 
                        @click="activeTab = 'tintuc'" 
                        :class="activeTab === 'tintuc' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                        class="px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Tin Tức
                    </button>
                    <button 
                        @click="activeTab = 'kienthuc'" 
                        :class="activeTab === 'kienthuc' ? 'bg-primary text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                        class="px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Kiến Thức
                    </button>
                </div>

                <!-- Tab Contents -->
                @php
                    $tabData = [
                        'baocao' => [
                            [
                                'title' => 'Hướng Dẫn Đăng Tin Nhà Đất Chuẩn SEO và AI Lên Xu hướng NKS',
                                'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
                                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Nhà Đầu Tư Phía Bắc Nam Tiến Thị Trường Bất Động Sản',
                                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Cách Tối Ưu Hóa Quy Trình Mua Nhà Qua Nền Tảng Online 2026',
                                'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ]
                        ],
                        'gocnhin' => [
                            [
                                'title' => 'Góc nhìn NKS: Tại sao mô hình Studio tách bếp lại đắt khách?',
                                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
                                'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
                                'image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Chiến lược phát triển thị trường căn hộ mini tại TP.HCM',
                                'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ]
                        ],
                        'noithat' => [
                            [
                                'title' => '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
                                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
                                'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
                                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
                                'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ]
                        ],
                        'phongthuy' => [
                            [
                                'title' => 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
                                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
                                'image' => 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
                                'image' => 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
                                'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ]
                        ],
                        'tintuc' => [
                            [
                                'title' => 'Đề xuất quy định mới về quản lý vận hành chung cư mini và nhà trọ',
                                'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Thành phố Hồ Chí Minh khởi công xây dựng 3 dự án nhà ở xã hội mới',
                                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Khởi động dự án cải tạo hạ tầng giao thông và mở rộng lộ giới trục đường',
                                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Giá căn hộ cho thuê tiếp tục tăng trưởng nhẹ dịp cuối năm',
                                'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ]
                        ],
                        'kienthuc' => [
                            [
                                'title' => 'Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý',
                                'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc',
                                'image' => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất',
                                'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
                            ],
                            [
                                'title' => 'Kinh nghiệm quản lý tài chính khi mua nhà trả góp cho gia đình trẻ',
                                'image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=500&q=80',
                                'date' => '26/6/2026'
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
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                        x-cloak
                    >
                        @foreach($articles as $article)
                            <div class="flex flex-col text-left group cursor-pointer">
                                <!-- Image Container -->
                                <div class="rounded-2xl overflow-hidden aspect-[16/10] mb-3 relative bg-slate-100 flex-shrink-0">
                                    <img 
                                        src="{{ $article['image'] }}" 
                                        alt="{{ $article['title'] }}"
                                        class="w-full h-full object-cover group-hover:scale-103 transition duration-500"
                                        loading="lazy"
                                    >
                                </div>

                                <!-- Title -->
                                <h3 class="text-[13px] md:text-sm font-extrabold text-[#0f172a] hover:text-primary transition line-clamp-2 leading-snug mb-1.5">
                                    <a href="#">{{ $article['title'] }}</a>
                                </h3>

                                <!-- Date -->
                                <span class="text-[11px] text-slate-400 font-semibold block">
                                    {{ $article['date'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
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
