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
    <section id="news" class="py-16 bg-slate-50 border-t border-b border-slate-100 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10 text-center">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Cập nhật tin mới nhất</span>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin tức & Kiến thức</h2>
                <p class="text-slate-500 mt-2 max-w-xl mx-auto">Khám phá báo cáo thị trường bất động sản mới nhất, phong thủy, nội thất và cẩm nang kiến thức từ chuyên gia NKS.</p>
            </div>

            <!-- Tab Container -->
            <div 
                x-data="{ 
                    activeTab: 'baocao'
                }"
                class="w-full"
            >
                <!-- Tab Buttons -->
                <div class="flex items-center justify-start md:justify-center space-x-3 overflow-x-auto pb-4 mb-10 scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none]">
                    <button 
                        @click="activeTab = 'baocao'" 
                        :class="activeTab === 'baocao' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/60'"
                        class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Báo cáo Thị trường BĐS
                    </button>
                    <button 
                        @click="activeTab = 'gocnhin'" 
                        :class="activeTab === 'gocnhin' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/60'"
                        class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Góc Nhìn NKS
                    </button>
                    <button 
                        @click="activeTab = 'noithat'" 
                        :class="activeTab === 'noithat' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/60'"
                        class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Nội Thất
                    </button>
                    <button 
                        @click="activeTab = 'phongthuy'" 
                        :class="activeTab === 'phongthuy' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/60'"
                        class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Phong Thủy
                    </button>
                    <button 
                        @click="activeTab = 'tintuc'" 
                        :class="activeTab === 'tintuc' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/60'"
                        class="px-6 py-2.5 rounded-full text-sm transition duration-200 whitespace-nowrap cursor-pointer"
                    >
                        Tin Tức
                    </button>
                    <button 
                        @click="activeTab = 'kienthuc'" 
                        :class="activeTab === 'kienthuc' ? 'bg-primary text-white font-extrabold shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200/60'"
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
