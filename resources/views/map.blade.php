@extends('layouts.app')

@php
    $hideFooter = true;
@endphp

@section('title', 'Bản Đồ Bất Động Sản Cho Thuê | BDS Rental')

@section('content')
<!-- MapLibre GL JS CSS -->
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.css">

<style>
    /* Custom MapLibre Popups Style to match premium design */
    .maplibregl-popup-content {
        padding: 8px !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif !important;
    }
    .maplibregl-popup-close-button {
        display: none !important; /* Hide default close button for cleaner look */
    }
    .maplibregl-popup-tip {
        border-top-color: #ffffff !important;
        border-bottom-color: #ffffff !important;
    }
    /* Hide scrollbars for sliders */
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-none {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    /* Smooth scaling for price marker bubbles */
    .custom-price-marker {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }
</style>

<div 
    id="map-container"
    class="h-screen pt-[72px] flex flex-col md:flex-row overflow-hidden bg-slate-50 relative"
    x-data="mapApp()"
    x-init="initMap()"
>
    <!-- 1. LEFT SIDEBAR: Desktop Listing Cards (Hidden on mobile) -->
    <aside class="hidden md:flex flex-col w-[380px] lg:w-[420px] bg-white border-r border-slate-100 h-full flex-shrink-0 z-10 shadow-sm">
        <!-- Sidebar Header & Quick Filters -->
        <div class="p-5 border-b border-slate-100 flex-shrink-0 bg-white">
            <h1 class="text-lg font-black text-slate-800 flex items-center gap-2 mb-4 text-left">
                <i class="fa-solid fa-map-location-dot text-primary"></i>
                <span>Tìm kiếm qua bản đồ</span>
            </h1>

            <div class="grid grid-cols-2 gap-3 text-left relative">
                <!-- Filter: Property Type -->
                <div class="space-y-1 relative">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Loại nhà đất</label>
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'type' ? null : 'type')"
                        :class="filterType ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs font-semibold transition cursor-pointer h-[36px]"
                    >
                        <span x-text="typeLabel()"></span>
                        <i class="fa-solid fa-chevron-down text-[9px] transition duration-200" :class="activeDropdown === 'type' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'type'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-80 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 px-0.5">Chọn loại bất động sản</span>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                type="button" 
                                @click="filterType = ''"
                                :class="filterType === '' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-house-chimney text-xs"></i>
                                <span>Tất cả</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'apartment'"
                                :class="filterType === 'apartment' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-building text-xs"></i>
                                <span>Căn hộ</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'house'"
                                :class="filterType === 'house' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-house text-xs"></i>
                                <span>Nhà riêng</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'room'"
                                :class="filterType === 'room' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-door-open text-xs"></i>
                                <span>Phòng trọ</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'land'"
                                :class="filterType === 'land' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-map text-xs"></i>
                                <span>Đất nền</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'premises'"
                                :class="filterType === 'premises' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-store text-xs"></i>
                                <span>Mặt bằng</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'office'"
                                :class="filterType === 'office' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-briefcase text-xs"></i>
                                <span>Văn phòng</span>
                            </button>
                            <button 
                                type="button" 
                                @click="filterType = 'warehouse'"
                                :class="filterType === 'warehouse' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <i class="fa-solid fa-warehouse text-xs"></i>
                                <span>Kho xưởng</span>
                            </button>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-3.5">
                            <button type="button" @click="filterType = ''; activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
                            <button type="button" @click="activeDropdown = null;" class="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">Áp dụng</button>
                        </div>
                    </div>
                </div>

                <!-- Filter: Price Range -->
                <div class="space-y-1 relative">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Khoảng giá</label>
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'price' ? null : 'price')"
                        :class="filterPrice ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs font-semibold transition cursor-pointer h-[36px]"
                    >
                        <span x-text="priceLabel()"></span>
                        <i class="fa-solid fa-chevron-down text-[9px] transition duration-200" :class="activeDropdown === 'price' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'price'" 
                        @click.outside="activeDropdown = null"
                        class="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Chọn khoảng giá</span>
                        
                        <!-- Rent Options -->
                        <div x-show="!isSale()" class="grid grid-cols-2 gap-2">
                            <button type="button" @click="filterPrice = ''" :class="filterPrice === '' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer col-span-2">
                                Tất cả mức giá
                            </button>
                            <button type="button" @click="filterPrice = 'under_3'" :class="filterPrice === 'under_3' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                Dưới 3 triệu
                            </button>
                            <button type="button" @click="filterPrice = '3_5'" :class="filterPrice === '3_5' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                3 - 5 triệu
                            </button>
                            <button type="button" @click="filterPrice = '5_10'" :class="filterPrice === '5_10' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                5 - 10 triệu
                            </button>
                            <button type="button" @click="filterPrice = '10_20'" :class="filterPrice === '10_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                10 - 20 triệu
                            </button>
                            <button type="button" @click="filterPrice = 'above_20'" :class="filterPrice === 'above_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2">
                                Trên 20 triệu
                            </button>
                        </div>
                        
                        <!-- Sale Options -->
                        <div x-show="isSale()" class="grid grid-cols-2 gap-2">
                            <button type="button" @click="filterPrice = ''" :class="filterPrice === '' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer col-span-2">
                                Tất cả mức giá
                            </button>
                            <button type="button" @click="filterPrice = 'under_1b'" :class="filterPrice === 'under_1b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                Dưới 1 tỷ
                            </button>
                            <button type="button" @click="filterPrice = '1b_3b'" :class="filterPrice === '1b_3b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                1 - 3 tỷ
                            </button>
                            <button type="button" @click="filterPrice = '3b_5b'" :class="filterPrice === '3b_5b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                3 - 5 tỷ
                            </button>
                            <button type="button" @click="filterPrice = '5b_10b'" :class="filterPrice === '5b_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                5 - 10 tỷ
                            </button>
                            <button type="button" @click="filterPrice = 'above_10b'" :class="filterPrice === 'above_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2">
                                Trên 10 tỷ
                            </button>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-3.5">
                            <button type="button" @click="filterPrice = ''; activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
                            <button type="button" @click="activeDropdown = null;" class="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">Áp dụng</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Label -->
            <div class="mt-3 flex items-center justify-between text-left">
                <span class="text-[11px] font-semibold text-slate-400">BDS Rental Hà Nội</span>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">
                    Tìm thấy <span class="text-primary font-black" x-text="filteredPropertiesCount()"></span> tin đăng
                </span>
            </div>
        </div>

        <!-- Scrollable List of Horizontal Property Cards -->
        <div class="flex-grow overflow-y-auto p-4 space-y-3.5 bg-slate-50/50">
            <!-- Empty State -->
            <div x-show="filteredPropertiesCount() === 0" class="py-12 px-4 text-center" x-cloak>
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3">
                    <i class="fa-solid fa-magnifying-glass text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Không tìm thấy kết quả</h3>
                <p class="text-xs text-slate-400 mt-1">Vui lòng thay đổi bộ lọc tìm kiếm để xem thêm bất động sản.</p>
            </div>

            <!-- List Cards Loop -->
            @foreach($properties as $property)
                <div 
                    id="card-desktop-{{ $property['id'] }}"
                    @click="selectProperty({{ $property['id'] }})"
                    x-show="isPropertyVisible({{ $property['id'] }})"
                    :class="activeId === {{ $property['id'] }} ? 'border-primary ring-1 ring-primary bg-primary-light/10 shadow-lg shadow-primary/5' : 'border-slate-150/50 bg-white hover:border-slate-300 hover:shadow-md'"
                    class="group flex gap-3.5 p-3 rounded-2xl border transition-all duration-300 cursor-pointer text-left relative overflow-hidden"
                    x-cloak
                >
                    <!-- Visual Tag Indicator -->
                    <div 
                        :class="activeId === {{ $property['id'] }} ? 'bg-primary' : 'bg-transparent'"
                        class="absolute left-0 top-0 bottom-0 w-1 transition-colors duration-300"
                    ></div>

                    <!-- Image Thumbnail -->
                    <div class="w-[110px] h-[95px] rounded-xl overflow-hidden flex-shrink-0 bg-slate-100 relative">
                        <img 
                            src="{{ asset($property['image']) }}" 
                            alt="{{ $property['title'] }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                        >
                        @if($property['is_vip'] ?? false)
                            <span class="absolute top-1.5 left-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-red-500 text-white shadow-sm">
                                VIP
                            </span>
                        @endif
                    </div>

                    <!-- Details Content -->
                    <div class="flex flex-col justify-between flex-grow min-w-0">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="text-sm font-extrabold text-primary tracking-tight">
                                    {{ $property['price'] }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                    {{ $property['area'] }} m²
                                </span>
                            </div>
                            <h4 
                                :class="activeId === {{ $property['id'] }} ? 'text-primary' : 'text-slate-800 group-hover:text-primary'"
                                class="text-[12px] font-bold line-clamp-2 leading-snug transition duration-200"
                            >
                                {{ $property['title'] }}
                            </h4>
                        </div>
                        
                        <div class="flex items-center text-slate-400 text-[10px] font-medium mt-1">
                            <i class="fa-solid fa-location-dot text-[9px] mr-1 flex-shrink-0"></i>
                            <span class="truncate">{{ $property['location'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </aside>

    <!-- 2. RIGHT AREA: MAP & MOBILE FLOATING LAYERS -->
    <main class="flex-grow h-full relative z-0">
        <!-- Interactive Map Div Container with Loader -->
        <div class="w-full h-full bg-slate-100 relative">
            <div id="map" class="w-full h-full"></div>
            <div id="map-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-20 transition-opacity duration-300">
                <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
                <p class="text-xs font-bold text-slate-500">Đang xác định vị trí của bạn...</p>
            </div>
        </div>

        <!-- MOBILE ONLY: Floating Top Bar Filters -->
        <div class="absolute top-4 left-4 right-4 z-10 md:hidden bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-100/60 p-3.5 flex flex-col gap-2.5 text-left">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot text-primary"></i>
                    <span>BDS Rental Bản đồ</span>
                </span>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                    Tìm thấy <span class="text-primary font-black" x-text="filteredPropertiesCount()"></span> căn
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <!-- Mobile Select 1 -->
                <div class="relative">
                    <select 
                        x-model="filterType"
                        class="w-full pl-2 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-[11px] font-semibold outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">Loại nhà đất</option>
                        <option value="apartment">Chung cư</option>
                        <option value="house">Nhà riêng</option>
                        <option value="villa">Biệt thự</option>
                        <option value="office">Văn phòng</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[8px]"></i>
                </div>
                <!-- Mobile Select 2 -->
                <div class="relative">
                    <select 
                        x-model="filterPrice"
                        class="w-full pl-2 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-[11px] font-semibold outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">Giá thuê</option>
                        <option value="under_10">Dưới 10tr</option>
                        <option value="10_25">10 - 25tr</option>
                        <option value="above_25">Trên 25tr</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[8px]"></i>
                </div>
            </div>
        </div>

        <!-- MOBILE ONLY: Bottom Horizontal Slider Overlay -->
        <div 
            x-show="filteredPropertiesCount() > 0"
            class="absolute bottom-6 left-0 right-0 z-10 px-4 md:hidden"
            x-transition:enter="transition ease-out duration-350 transform"
            x-transition:enter-start="opacity-0 translate-y-12"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-12"
            x-cloak
        >
            <div 
                id="mobile-cards-slider"
                class="flex space-x-3.5 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-none scroll-smooth"
                @scroll="handleMobileScroll()"
            >
                @foreach($properties as $property)
                    <div 
                        id="card-mobile-{{ $property['id'] }}"
                        @click="selectProperty({{ $property['id'] }})"
                        x-show="isPropertyVisible({{ $property['id'] }})"
                        :class="activeId === {{ $property['id'] }} ? 'border-primary ring-1 ring-primary shadow-2xl' : 'border-slate-100/80 shadow-xl'"
                        class="flex-shrink-0 w-[285px] bg-white rounded-2xl p-3 border flex gap-3 text-left snap-center scroll-ml-4"
                        x-cloak
                    >
                        <!-- Mobile Thumbnail -->
                        <div class="w-24 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-slate-100 relative">
                            <img 
                                src="{{ asset($property['image']) }}" 
                                alt="{{ $property['title'] }}" 
                                class="w-full h-full object-cover"
                            >
                            @if($property['is_vip'] ?? false)
                                <span class="absolute top-1 left-1 inline-flex items-center px-1 rounded text-[7px] font-black uppercase bg-red-500 text-white">
                                    VIP
                                </span>
                            @endif
                        </div>
                        
                        <!-- Mobile Details -->
                        <div class="flex flex-col justify-between flex-grow min-w-0 py-0.5">
                            <div>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-black text-primary">{{ $property['price'] }}</span>
                                    <span class="text-[8px] font-bold text-slate-400 bg-slate-100 px-1 py-0.5 rounded">{{ $property['area'] }}m²</span>
                                </div>
                                <h4 class="text-[11px] font-bold text-slate-800 line-clamp-2 leading-snug mt-1">
                                    {{ $property['title'] }}
                                </h4>
                            </div>
                            
                            <div class="flex items-center text-slate-400 text-[9px] font-medium">
                                <i class="fa-solid fa-location-dot mr-1 flex-shrink-0"></i>
                                <span class="truncate">{{ $property['location'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<!-- MapLibre GL JS SDK -->
<script src="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.js"></script>

<script>
    function mapApp() {
        return {
            properties: @json($properties),
            activeId: null,
            filterType: '',
            filterPrice: '',
            activeDropdown: null,
            map: null,
            markers: {},
            ignoreMobileScroll: false,
            mobileScrollTimeout: null,

            isSale() {
                const urlParams = new URLSearchParams(window.location.search);
                const purpose = urlParams.get('purpose') || '';
                return purpose === 'sale' || this.properties.some(p => p.transaction_type === 'sale');
            },

            typeLabel() {
                if (!this.filterType) return 'Loại hình';
                const labels = {
                    'apartment': 'Căn hộ',
                    'house': 'Nhà riêng',
                    'room': 'Phòng trọ',
                    'land': 'Đất nền',
                    'premises': 'Mặt bằng',
                    'office': 'Văn phòng',
                    'warehouse': 'Kho xưởng'
                };
                return labels[this.filterType] || 'Loại hình';
            },

            priceLabel() {
                if (!this.filterPrice) return 'Mức giá';
                const labels = {
                    'under_3': 'Dưới 3 triệu',
                    '3_5': '3 - 5 triệu',
                    '5_10': '5 - 10 triệu',
                    '10_20': '10 - 20 triệu',
                    'above_20': 'Trên 20 triệu',
                    'under_1b': 'Dưới 1 tỷ',
                    '1b_3b': '1 - 3 tỷ',
                    '3b_5b': '3 - 5 tỷ',
                    '5b_10b': '5 - 10 tỷ',
                    'above_10b': 'Trên 10 tỷ'
                };
                return labels[this.filterPrice] || 'Mức giá';
            },

            // Filter properties based on select criteria
            filteredProperties() {
                return this.properties.filter(p => {
                    // 1. Match Property Type
                    const matchType = !this.filterType || p.property_type === this.filterType;

                    // 2. Match Price Range
                    let matchPrice = true;
                    if (this.filterPrice) {
                        const priceRaw = p.price_raw || 0;
                        const isRent = p.transaction_type === 'rent';
                        
                        if (isRent) {
                            if (this.filterPrice === 'under_3') matchPrice = priceRaw < 3000000;
                            else if (this.filterPrice === '3_5') matchPrice = priceRaw >= 3000000 && priceRaw <= 5000000;
                            else if (this.filterPrice === '5_10') matchPrice = priceRaw >= 5000000 && priceRaw <= 10000000;
                            else if (this.filterPrice === '10_20') matchPrice = priceRaw >= 10000000 && priceRaw <= 20000000;
                            else if (this.filterPrice === 'above_20') matchPrice = priceRaw > 20000000;
                        } else { // sale
                            if (this.filterPrice === 'under_1b') matchPrice = priceRaw < 1000000000;
                            else if (this.filterPrice === '1b_3b') matchPrice = priceRaw >= 1000000000 && priceRaw <= 3000000000;
                            else if (this.filterPrice === '3b_5b') matchPrice = priceRaw >= 3000000000 && priceRaw <= 5000000000;
                            else if (this.filterPrice === '5b_10b') matchPrice = priceRaw >= 5000000000 && priceRaw <= 10000000000;
                            else if (this.filterPrice === 'above_10b') matchPrice = priceRaw > 10000000000;
                        }
                    }

                    return matchType && matchPrice;
                });
            },

            filteredPropertiesCount() {
                return this.filteredProperties().length;
            },

            isPropertyVisible(id) {
                return this.filteredProperties().some(p => p.id === id);
            },

            // Map Initialization
            initMap() {
                // Check if lat/lng query params exist in URL (deep linking from detail page)
                const urlParams = new URLSearchParams(window.location.search);
                const queryLat = urlParams.get('lat');
                const queryLng = urlParams.get('lng');
                const queryId = urlParams.get('id');

                if (queryLat && queryLng) {
                    const lat = parseFloat(queryLat);
                    const lng = parseFloat(queryLng);
                    
                    this.createMapInstance([lng, lat], 14.5, false);

                    if (queryId) {
                        const propId = parseInt(queryId);
                        this.map.on('load', () => {
                            setTimeout(() => {
                                this.selectProperty(propId, false);
                            }, 500);
                        });
                    }
                    return;
                }

                // Safety net: hide loader after 6 seconds anyway
                setTimeout(() => {
                    const loader = document.getElementById('map-loader');
                    if (loader) {
                        loader.classList.add('opacity-0');
                        setTimeout(() => loader.remove(), 300);
                    }
                }, 6000);

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userLng = position.coords.longitude;
                            const userLat = position.coords.latitude;
                            this.createMapInstance([userLng, userLat], 13.5, true);
                        },
                        (error) => {
                            console.warn('Geolocation failed or permission denied:', error);
                            this.createMapInstance([105.81, 21.03], 12.2, false);
                        },
                        { enableHighAccuracy: true, timeout: 3500 }
                    );
                } else {
                    this.createMapInstance([105.81, 21.03], 12.2, false);
                }
            },

            createMapInstance(centerCoords, zoomLevel, showUserMarker) {
                if (this.map) return;

                // Instantiating MapLibre
                this.map = new maplibregl.Map({
                    container: 'map',
                    style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json', // Vector style Positron
                    center: centerCoords,
                    zoom: zoomLevel
                });

                // Zoom & Rotation control buttons
                this.map.addControl(new maplibregl.NavigationControl(), 'top-right');

                // Geolocate control to track user location
                const geolocate = new maplibregl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true
                    },
                    trackUserLocation: true,
                    showUserLocation: true
                });
                this.map.addControl(geolocate, 'top-right');

                this.map.on('load', () => {
                    this.renderMarkers();
                    
                    if (showUserMarker) {
                        geolocate.trigger();
                    }

                    // Smoothly remove loader
                    const loader = document.getElementById('map-loader');
                    if (loader) {
                        loader.classList.add('opacity-0');
                        setTimeout(() => loader.remove(), 300);
                    }
                });

                // Watch filters to dynamically update markers and fit bounds
                this.$watch('filterType', () => this.updateMarkersAndZoom());
                this.$watch('filterPrice', () => this.updateMarkersAndZoom());
            },

            // Clear and render new markers
            renderMarkers() {
                // Clean existing markers
                Object.values(this.markers).forEach(m => m.remove());
                this.markers = {};

                const filtered = this.filteredProperties();
                
                filtered.forEach(p => {
                    // Custom HTML element for Marker (Price Bubble)
                    const el = document.createElement('div');
                    el.id = 'marker-' + p.id;
                    el.className = 'custom-price-marker bg-primary hover:bg-primary-hover text-white text-[11px] font-black px-2.5 py-1.5 rounded-full shadow-lg border-2 border-white cursor-pointer flex items-center justify-center';
                    el.style.whiteSpace = 'nowrap';
                    el.innerHTML = p.price_label;

                    const imgUrl = (p.image && (p.image.startsWith('http://') || p.image.startsWith('https://')))
                        ? p.image
                        : (p.image ? (p.image.startsWith('/') ? p.image : '/' + p.image) : '/images/apartment_1.png');

                    // Detail popup markup inside map
                    const popupHTML = `
                        <div class="p-1 min-w-[210px] text-left">
                            <a href="/property/${p.id}" class="block overflow-hidden rounded-xl mb-2 group">
                                <img src="${imgUrl}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            <div class="px-1.5 pb-1">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="inline-block bg-[#0077bb]/10 text-primary text-[9px] font-bold px-2 py-0.5 rounded-lg">${p.type}</span>
                                    <span class="text-[9px] font-bold text-slate-500">${p.area}m²</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 line-clamp-2 hover:text-primary transition leading-snug mb-1">
                                    <a href="/property/${p.id}">${p.title}</a>
                                </h4>
                                <p class="text-[10px] text-slate-400 flex items-center gap-1 mb-2">
                                    <i class="fa-solid fa-location-dot text-slate-300"></i> <span class="truncate">${p.location}</span>
                                </p>
                                <div class="flex items-center justify-between mt-1.5 pt-1.5 border-t border-slate-100">
                                    <span class="text-xs font-black text-primary">${p.price}</span>
                                    <a href="/property/${p.id}" class="text-[10px] font-bold text-primary hover:underline flex items-center gap-0.5">
                                        Xem <i class="fa-solid fa-chevron-right text-[8px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;

                    // Create popup
                    const popup = new maplibregl.Popup({ 
                        offset: 25, 
                        closeButton: false,
                        closeOnClick: true
                    }).setHTML(popupHTML);

                    // Create and append marker
                    const marker = new maplibregl.Marker({ element: el })
                        .setLngLat([p.lng, p.lat])
                        .setPopup(popup)
                        .addTo(this.map);

                    // Attach click handler on the custom bubble
                    el.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.selectProperty(p.id, true);
                    });

                    // Save reference
                    this.markers[p.id] = marker;
                });
            },

            // Handles updates on map when filters change
            updateMarkersAndZoom() {
                this.renderMarkers();
                this.activeId = null;

                const filtered = this.filteredProperties();
                if (filtered.length > 0 && this.map) {
                    const bounds = new maplibregl.LngLatBounds();
                    filtered.forEach(p => bounds.extend([p.lng, p.lat]));
                    this.map.fitBounds(bounds, { 
                        padding: { top: 120, bottom: 180, left: 50, right: 50 }, // Account for headers and bottom overlays
                        maxZoom: 14.5,
                        duration: 800
                    });
                }
            },

            // Main interaction triggers: selects, flys, opens popups, scrolls UI cards
            selectProperty(id, fromMarker = false) {
                this.activeId = id;
                const property = this.properties.find(p => p.id === id);
                if (!property) return;

                // 1. Style Custom Markers (Highlight Active color)
                Object.keys(this.markers).forEach(markerId => {
                    const markerEl = document.getElementById('marker-' + markerId);
                    if (markerEl) {
                        if (parseInt(markerId) === id) {
                            markerEl.classList.remove('bg-primary', 'hover:bg-primary-hover');
                            markerEl.classList.add('bg-orange-500', 'scale-115', 'z-[999]', 'border-orange-200');
                        } else {
                            markerEl.classList.add('bg-primary', 'hover:bg-primary-hover');
                            markerEl.classList.remove('bg-orange-500', 'scale-115', 'z-[999]', 'border-orange-200');
                        }
                    }
                });

                // 2. Fly Map to active marker
                if (this.map) {
                    this.map.flyTo({
                        center: [property.lng, property.lat],
                        zoom: 14.5,
                        duration: 600,
                        essential: true
                    });
                }

                // 3. Open Popup
                if (this.markers[id]) {
                    this.markers[id].togglePopup();
                }

                // 4. Scroll corresponding Listing Cards into view (Smooth)
                this.$nextTick(() => {
                    // Desktop Card scroll
                    const desktopCard = document.getElementById('card-desktop-' + id);
                    if (desktopCard) {
                        desktopCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }

                    // Mobile Slider Card scroll
                    const mobileCard = document.getElementById('card-mobile-' + id);
                    if (mobileCard) {
                        this.ignoreMobileScroll = true; // Avoid circular triggering
                        mobileCard.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                        
                        // Reset scroll ignoring flag after scroll animation completes
                        clearTimeout(this.mobileScrollTimeout);
                        this.mobileScrollTimeout = setTimeout(() => {
                            this.ignoreMobileScroll = false;
                        }, 800);
                    }
                });
            },

            // Listen to mobile sliding container scroll events to snap active markers
            handleMobileScroll() {
                if (this.ignoreMobileScroll) return;

                clearTimeout(this.mobileScrollTimeout);
                this.mobileScrollTimeout = setTimeout(() => {
                    const container = document.getElementById('mobile-cards-slider');
                    if (!container) return;

                    const containerCenter = container.scrollLeft + (container.offsetWidth / 2);
                    const cards = container.children;
                    let closestCard = null;
                    let minDistance = Infinity;

                    for (let i = 0; i < cards.length; i++) {
                        const card = cards[i];
                        if (card.style.display === 'none') continue;

                        const cardCenter = card.offsetLeft + (card.offsetWidth / 2);
                        const distance = Math.abs(containerCenter - cardCenter);

                        if (distance < minDistance) {
                            minDistance = distance;
                            closestCard = card;
                        }
                    }

                    if (closestCard) {
                        const cardId = parseInt(closestCard.id.replace('card-mobile-', ''));
                        if (cardId && cardId !== this.activeId) {
                            // Focus map & highlight active card without scrolling mobile container again
                            this.activeId = cardId;
                            const property = this.properties.find(p => p.id === cardId);
                            if (property) {
                                // Style Markers
                                Object.keys(this.markers).forEach(markerId => {
                                    const markerEl = document.getElementById('marker-' + markerId);
                                    if (markerEl) {
                                        if (parseInt(markerId) === cardId) {
                                            markerEl.classList.remove('bg-primary', 'hover:bg-primary-hover');
                                            markerEl.classList.add('bg-orange-500', 'scale-115', 'z-[999]', 'border-orange-200');
                                        } else {
                                            markerEl.classList.add('bg-primary', 'hover:bg-primary-hover');
                                            markerEl.classList.remove('bg-orange-500', 'scale-115', 'z-[999]', 'border-orange-200');
                                        }
                                    }
                                });

                                // Center Map
                                if (this.map) {
                                    this.map.flyTo({
                                        center: [property.lng, property.lat],
                                        zoom: 14.5,
                                        duration: 600
                                    });
                                }

                                // Open Popup
                                if (this.markers[cardId]) {
                                    this.markers[cardId].togglePopup();
                                }
                            }
                        }
                    }
                }, 100); // Debounce scroll detection slightly for smoothness
            }
        };
    }
</script>
@endpush
