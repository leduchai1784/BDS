@extends('layouts.app')

@section('title', 'Danh Sách Bất Động Sản Cho Thuê Giá Tốt | BDS Rental')

@section('content')
<div class="bg-slate-50 pt-28 pb-16 min-h-screen" x-data="{ mobileFiltersOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-6 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800">Danh sách cho thuê</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-left">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Tìm kiếm bất động sản cho thuê</h1>
                <p class="text-xs text-slate-500 mt-1">Tìm thấy <span class="font-bold text-primary">{{ count($properties) }}</span> tin đăng phù hợp trên toàn quốc</p>
            </div>
            
            <!-- Mobile Filter Toggle Button -->
            <button 
                @click="mobileFiltersOpen = true"
                type="button" 
                class="lg:hidden inline-flex items-center justify-center space-x-2 px-5 py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-lg shadow-primary/20 cursor-pointer"
            >
                <i class="fa-solid fa-sliders"></i>
                <span>Bộ lọc & Tìm kiếm</span>
            </button>
        </div>

        <!-- Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- 1. LEFT FILTER COLUMN: Desktop view (Sticky sidebar, 3/12 cols) -->
            <aside class="hidden lg:block lg:col-span-3 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm sticky top-24 text-left">
                <form action="/listings" method="GET" class="space-y-6">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-primary"></i>
                        <span>Bộ lọc tìm kiếm</span>
                    </h3>

                    <!-- Filter: Location (Quận/Huyện) -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Khu vực / Quận Huyện</label>
                        <div class="relative">
                            <select 
                                name="district" 
                                class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                            >
                                <option value="">Tất cả Quận/Huyện</option>
                                <option value="Q1">Quận 1, TP. HCM</option>
                                <option value="Q3">Quận 3, TP. HCM</option>
                                <option value="BT">Bình Thạnh, TP. HCM</option>
                                <option value="CG">Cầu Giấy, Hà Nội</option>
                                <option value="GL">Gia Lâm, Hà Nội</option>
                                <option value="TH">Tây Hồ, Hà Nội</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter: Property Type -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Loại bất động sản</label>
                        <div class="relative">
                            <select 
                                name="type" 
                                class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                            >
                                <option value="">Tất cả loại nhà</option>
                                <option value="apartment">Căn hộ chung cư</option>
                                <option value="house">Nhà nguyên căn</option>
                                <option value="room">Phòng trọ giá rẻ</option>
                                <option value="office">Văn phòng cho thuê</option>
                                <option value="store">Mặt bằng kinh doanh</option>
                                <option value="villa">Biệt thự / Villa</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter: Price Range -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Khoảng giá thuê</label>
                        <div class="relative">
                            <select 
                                name="price" 
                                class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                            >
                                <option value="">Tất cả mức giá</option>
                                <option value="under_5">Dưới 5 triệu</option>
                                <option value="5_10">5 - 10 triệu</option>
                                <option value="10_20">10 - 20 triệu</option>
                                <option value="above_20">Trên 20 triệu</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter: Area Range -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Diện tích sử dụng</label>
                        <div class="relative">
                            <select 
                                name="area" 
                                class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                            >
                                <option value="">Tất cả diện tích</option>
                                <option value="under_30">Dưới 30 m²</option>
                                <option value="30_50">30 - 50 m²</option>
                                <option value="50_80">50 - 80 m²</option>
                                <option value="above_80">Trên 80 m²</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col gap-2.5">
                        <button 
                            type="submit" 
                            class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer"
                        >
                            Áp dụng bộ lọc
                        </button>
                        <a 
                            href="/listings" 
                            class="w-full py-3 border border-slate-200 hover:border-slate-355 bg-slate-50 hover:bg-slate-100 text-slate-500 font-bold rounded-xl text-xs text-center transition"
                        >
                            Đặt lại bộ lọc
                        </a>
                    </div>
                </form>
            </aside>

            <!-- 2. RIGHT CARD LIST COLUMN: (9/12 cols) -->
            <main class="col-span-1 lg:col-span-9">
                <!-- Sorting & Quick actions -->
                <div class="bg-white rounded-2xl px-5 py-3.5 border border-slate-100 shadow-sm flex items-center justify-between mb-8">
                    <span class="text-xs text-slate-400 font-bold hidden sm:inline">Xem dạng lưới</span>
                    
                    <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-start">
                        <label class="text-xs font-bold text-slate-500 whitespace-nowrap">Sắp xếp theo:</label>
                        <div class="relative min-w-[150px]">
                            <select 
                                name="sort" 
                                class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                            >
                                <option value="latest">Mới nhất</option>
                                <option value="price_asc">Giá tăng dần</option>
                                <option value="price_desc">Giá giảm dần</option>
                                <option value="area_desc">Diện tích lớn nhất</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <!-- Grid of Listings -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach($properties as $property)
                        @include('components.property-card', ['property' => $property])
                    @endforeach
                </div>

                <!-- Pagination Section -->
                <div class="flex justify-center">
                    <nav class="inline-flex space-x-1 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm" aria-label="Pagination">
                        <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                        <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary text-white font-bold shadow-md shadow-primary/20">1</a>
                        <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">2</a>
                        <span class="inline-flex items-center justify-center w-10 h-10 text-slate-400">...</span>
                        <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">5</a>
                        <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </nav>
                </div>
            </main>

        </div>
    </div>

    <!-- 3. MOBILE FILTER SLIDE-OVER (AlpineJS controlled) -->
    <div 
        x-show="mobileFiltersOpen" 
        class="fixed inset-0 z-50 overflow-hidden lg:hidden" 
        x-cloak
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="mobileFiltersOpen = false"></div>
        
        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="mobileFiltersOpen"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-xs bg-white h-full shadow-2xl flex flex-col p-6 text-left"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-primary"></i>
                        <span>Bộ lọc tìm kiếm</span>
                    </h3>
                    <button @click="mobileFiltersOpen = false" class="text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Scrollable filter form -->
                <form action="/listings" method="GET" class="flex-grow overflow-y-auto space-y-6 pr-1">
                    <!-- Filter: District -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Khu vực / Quận Huyện</label>
                        <div class="relative">
                            <select name="district" class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition">
                                <option value="">Tất cả Quận/Huyện</option>
                                <option value="Q1">Quận 1, TP. HCM</option>
                                <option value="Q3">Quận 3, TP. HCM</option>
                                <option value="BT">Bình Thạnh, TP. HCM</option>
                                <option value="CG">Cầu Giấy, Hà Nội</option>
                                <option value="GL">Gia Lâm, Hà Nội</option>
                                <option value="TH">Tây Hồ, Hà Nội</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter: Type -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Loại bất động sản</label>
                        <div class="relative">
                            <select name="type" class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition">
                                <option value="">Tất cả loại nhà</option>
                                <option value="apartment">Căn hộ chung cư</option>
                                <option value="house">Nhà nguyên căn</option>
                                <option value="room">Phòng trọ giá rẻ</option>
                                <option value="office">Văn phòng cho thuê</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter: Price -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Khoảng giá thuê</label>
                        <div class="relative">
                            <select name="price" class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition">
                                <option value="">Tất cả mức giá</option>
                                <option value="under_5">Dưới 5 triệu</option>
                                <option value="5_10">5 - 10 triệu</option>
                                <option value="10_20">10 - 20 triệu</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter: Area -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Diện tích sử dụng</label>
                        <div class="relative">
                            <select name="area" class="w-full pl-3 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition">
                                <option value="">Tất cả diện tích</option>
                                <option value="under_30">Dưới 30 m²</option>
                                <option value="30_50">30 - 50 m²</option>
                                <option value="50_80">50 - 80 m²</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>
                </form>

                <!-- Actions at bottom of mobile menu -->
                <div class="pt-4 border-t border-slate-100 flex flex-col gap-2 mb-2">
                    <button 
                        @click="mobileFiltersOpen = false"
                        type="submit" 
                        class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 px-4 rounded-xl text-xs shadow-md transition cursor-pointer"
                    >
                        Áp dụng bộ lọc
                    </button>
                    <a 
                        href="/listings" 
                        class="w-full py-3.5 border border-slate-250 bg-slate-50 hover:bg-slate-100 text-slate-500 font-bold rounded-xl text-xs text-center transition"
                    >
                        Đặt lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
