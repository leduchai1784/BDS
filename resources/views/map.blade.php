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
        padding: 0 !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif !important;
        overflow: hidden !important;
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
    class="h-screen pt-[72px] flex flex-col overflow-hidden bg-slate-50 relative"
    x-data="mapApp()"
    x-init="initMap()"
>
    <!-- Hidden form for submissions -->
    <form x-ref="filterForm" action="/map" method="GET" class="hidden">
        <input type="hidden" name="purpose" :value="filterPurpose">
        <input type="hidden" name="keyword" :value="filterKeyword">
        <input type="hidden" name="property_type" :value="filterType">
        <input type="hidden" name="price" :value="filterPrice">
        <input type="hidden" name="bedrooms" :value="filterBedrooms">
        <input type="hidden" name="area" :value="filterArea">
        <input type="hidden" name="bathrooms" :value="filterBathrooms">
        <input type="hidden" name="furniture" :value="filterFurniture">
        <input type="hidden" name="direction" :value="filterDirection">
    </form>

    <!-- TOP HORIZONTAL SEARCH/FILTER BAR -->
    <div class="bg-white border-b border-slate-100 py-3.5 px-4 lg:px-6 z-20 flex-shrink-0 hidden md:block">
        <div class="w-full flex items-center justify-between gap-4">
            
            <!-- Left section: Purpose Toggle & Search Input -->
            <div class="flex items-center gap-4 flex-grow max-w-2xl">
                <!-- Transaction Type Toggle -->
                <div class="inline-flex p-1 bg-slate-100 rounded-full border border-slate-200 flex-shrink-0">
                    <button 
                        type="button" 
                        @click="setPurpose('sale')" 
                        :class="filterPurpose === 'sale' ? 'bg-primary text-white shadow-sm font-extrabold' : 'text-slate-600 font-bold hover:bg-slate-50'"
                        class="px-4 py-1.5 rounded-full text-xs transition duration-150 cursor-pointer focus:outline-none"
                    >
                        Đang bán
                    </button>
                    <button 
                        type="button" 
                        @click="setPurpose('rent')" 
                        :class="filterPurpose === 'rent' ? 'bg-primary text-white shadow-sm font-extrabold' : 'text-slate-600 font-bold hover:bg-slate-50'"
                        class="px-4 py-1.5 rounded-full text-xs transition duration-150 cursor-pointer focus:outline-none"
                    >
                        Cho thuê
                    </button>
                </div>

                <!-- Fuzzy Search Input with Autocomplete -->
                <div class="relative flex-grow flex items-center bg-slate-50 border border-slate-200 rounded-full px-3 py-1.5 focus-within:bg-white focus-within:border-primary transition duration-150">
                    <input 
                        type="text" 
                        x-model="query"
                        @input.debounce.250ms="fetchSuggestions()"
                        @focus="isOpen = suggestions.length > 0"
                        @keydown.arrow-down.prevent="activeIndex = (activeIndex + 1) % suggestions.length"
                        @keydown.arrow-up.prevent="activeIndex = (activeIndex - 1 + suggestions.length) % suggestions.length"
                        @keydown.enter.prevent="selectActiveIndex()"
                        @keydown.escape="isOpen = false"
                        placeholder="Tìm địa điểm, dự án..." 
                        autocomplete="off"
                        class="w-full bg-transparent border-none text-xs font-semibold outline-none py-1.5 pl-1.5"
                    >
                    <button 
                        type="button"
                        @click="submitSearch()"
                        class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center hover:bg-primary-hover shadow-md shadow-primary/20 transition cursor-pointer flex-shrink-0"
                    >
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </button>

                    <!-- Autocomplete Suggestions Dropdown -->
                    <div 
                        x-show="isOpen"
                        @click.outside="isOpen = false"
                        class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden text-left"
                        x-cloak
                    >
                        <div class="max-h-[300px] overflow-y-auto py-2">
                            <template x-for="(sug, index) in suggestions" :key="index">
                                <div 
                                    @click="selectSuggestion(sug)"
                                    @mouseenter="activeIndex = index"
                                    :class="{ 'bg-slate-50 text-primary': activeIndex === index }"
                                    class="px-4 py-2.5 cursor-pointer flex items-center justify-between border-b border-slate-50 last:border-0 hover:bg-slate-50 transition duration-150"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                            <template x-if="sug.type === 'city' || sug.type === 'district' || sug.type === 'ward' || sug.type === 'address'">
                                                <i class="fa-solid fa-location-dot text-xs text-primary"></i>
                                            </template>
                                            <template x-if="sug.type === 'property'">
                                                <i class="fa-solid fa-building text-xs text-amber-500"></i>
                                            </template>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-slate-800" x-text="sug.label"></div>
                                            <div class="text-[9px] text-slate-400" x-text="sug.sublabel"></div>
                                        </div>
                                    </div>
                                    <span 
                                        class="text-[8px] font-bold uppercase px-2 py-0.5 rounded-full"
                                        :class="{
                                            'bg-blue-50 text-blue-600': sug.type === 'city',
                                            'bg-indigo-50 text-indigo-600': sug.type === 'district',
                                            'bg-purple-50 text-purple-600': sug.type === 'ward',
                                            'bg-teal-50 text-teal-600': sug.type === 'address',
                                            'bg-amber-50 text-amber-600': sug.type === 'property'
                                        }"
                                        x-text="sug.type === 'city' ? 'Tỉnh thành' : (sug.type === 'district' ? 'Quận huyện' : (sug.type === 'ward' ? 'Phường xã' : (sug.type === 'address' ? 'Địa chỉ' : 'Bất động sản')))"
                                    ></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right section: Dropdowns and Reset -->
            <div class="flex items-center gap-2">
                <!-- Dropdown: Property Type -->
                <div class="relative">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'horizontal_type' ? null : 'horizontal_type')"
                        :class="filterType ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px]"
                    >
                        <span x-text="typeLabel()"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-200" :class="activeDropdown === 'horizontal_type' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'horizontal_type'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-80 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 px-0.5">Chọn loại hình</span>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="setType('apartment')" :class="filterType === 'apartment' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-building text-xs"></i>
                                <span>Căn hộ</span>
                            </button>
                            <button type="button" @click="setType('house')" :class="filterType === 'house' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-house text-xs"></i>
                                <span>Nhà riêng</span>
                            </button>
                            <button type="button" @click="setType('room')" :class="filterType === 'room' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-door-open text-xs"></i>
                                <span>Phòng trọ</span>
                            </button>
                            <button type="button" @click="setType('land')" :class="filterType === 'land' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-map text-xs"></i>
                                <span>Đất nền</span>
                            </button>
                            <button type="button" @click="setType('premises')" :class="filterType === 'premises' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-store text-xs"></i>
                                <span>Mặt bằng</span>
                            </button>
                            <button type="button" @click="setType('office')" :class="filterType === 'office' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-briefcase text-xs"></i>
                                <span>Văn phòng</span>
                            </button>
                            <button type="button" @click="setType('warehouse')" :class="filterType === 'warehouse' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer col-span-2 justify-center">
                                <i class="fa-solid fa-warehouse text-xs"></i>
                                <span>Kho xưởng</span>
                            </button>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-3.5">
                            <button type="button" @click="setType(''); activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
                            <button type="button" @click="activeDropdown = null;" class="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">Áp dụng</button>
                        </div>
                    </div>
                </div>

                <!-- Dropdown: Price Range -->
                <div class="relative">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'horizontal_price' ? null : 'horizontal_price')"
                        :class="filterPrice ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px]"
                    >
                        <span x-text="priceLabel()"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-200" :class="activeDropdown === 'horizontal_price' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'horizontal_price'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-64 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Chọn mức giá</span>
                        
                        <!-- Rent Price Options -->
                        <div x-show="filterPurpose === 'rent'" class="grid grid-cols-2 gap-2">
                            <button type="button" @click="setPrice('under_3')" :class="filterPrice === 'under_3' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                Dưới 3 triệu
                            </button>
                            <button type="button" @click="setPrice('3_5')" :class="filterPrice === '3_5' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                3 - 5 triệu
                            </button>
                            <button type="button" @click="setPrice('5_10')" :class="filterPrice === '5_10' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                5 - 10 triệu
                            </button>
                            <button type="button" @click="setPrice('10_20')" :class="filterPrice === '10_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                10 - 20 triệu
                            </button>
                            <button type="button" @click="setPrice('above_20')" :class="filterPrice === 'above_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2">
                                Trên 20 triệu
                            </button>
                        </div>

                        <!-- Sale Price Options -->
                        <div x-show="filterPurpose === 'sale'" class="grid grid-cols-2 gap-2" x-cloak>
                            <button type="button" @click="setPrice('under_1b')" :class="filterPrice === 'under_1b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                Dưới 1 tỷ
                            </button>
                            <button type="button" @click="setPrice('1b_3b')" :class="filterPrice === '1b_3b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                1 - 3 tỷ
                            </button>
                            <button type="button" @click="setPrice('3b_5b')" :class="filterPrice === '3b_5b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                3 - 5 tỷ
                            </button>
                            <button type="button" @click="setPrice('5b_10b')" :class="filterPrice === '5b_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer">
                                5 - 10 tỷ
                            </button>
                            <button type="button" @click="setPrice('above_10b')" :class="filterPrice === 'above_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[11px] font-bold transition cursor-pointer col-span-2">
                                Trên 10 tỷ
                            </button>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-3.5">
                            <button type="button" @click="setPrice(''); activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
                            <button type="button" @click="activeDropdown = null;" class="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">Áp dụng</button>
                        </div>
                    </div>
                </div>

                <!-- Dropdown: Bedrooms -->
                <div class="relative">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'horizontal_bedrooms' ? null : 'horizontal_bedrooms')"
                        :class="filterBedrooms ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px]"
                    >
                        <span x-text="bedroomsLabel()"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-200" :class="activeDropdown === 'horizontal_bedrooms' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'horizontal_bedrooms'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-48 rounded-2xl bg-white border border-slate-150 shadow-2xl p-3.5 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Số phòng ngủ</span>
                        <div class="flex flex-col space-y-1">
                            <button type="button" @click="setBedrooms('1')" :class="filterBedrooms === '1' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">1 Phòng ngủ</button>
                            <button type="button" @click="setBedrooms('2')" :class="filterBedrooms === '2' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">2 Phòng ngủ</button>
                            <button type="button" @click="setBedrooms('3')" :class="filterBedrooms === '3' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">3 Phòng ngủ</button>
                            <button type="button" @click="setBedrooms('4_plus')" :class="filterBedrooms === '4_plus' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">4+ Phòng ngủ</button>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-2.5">
                            <button type="button" @click="setBedrooms(''); activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
                        </div>
                    </div>
                </div>

                <!-- Dropdown: Area -->
                <div class="relative">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'horizontal_area' ? null : 'horizontal_area')"
                        :class="filterArea ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[120px]"
                    >
                        <span x-text="areaLabel()"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-200" :class="activeDropdown === 'horizontal_area' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'horizontal_area'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-52 rounded-2xl bg-white border border-slate-150 shadow-2xl p-3.5 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Chọn diện tích</span>
                        <div class="flex flex-col space-y-1">
                            <button type="button" @click="setArea('under_30')" :class="filterArea === 'under_30' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">Dưới 30 m²</button>
                            <button type="button" @click="setArea('30_50')" :class="filterArea === '30_50' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">30 - 50 m²</button>
                            <button type="button" @click="setArea('50_80')" :class="filterArea === '50_80' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">50 - 80 m²</button>
                            <button type="button" @click="setArea('80_120')" :class="filterArea === '80_120' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">80 - 120 m²</button>
                            <button type="button" @click="setArea('above_120')" :class="filterArea === 'above_120' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'" class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold transition cursor-pointer">Trên 120 m²</button>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2.5 mt-2.5">
                            <button type="button" @click="setArea(''); activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Xóa</button>
                        </div>
                    </div>
                </div>

                <!-- Dropdown: Advanced Filters -->
                <div class="relative">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'horizontal_advanced' ? null : 'horizontal_advanced')"
                        :class="(filterBathrooms || filterFurniture || filterDirection) ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1.5 px-4 py-2 border rounded-full text-xs font-bold transition cursor-pointer h-10 min-w-[125px]"
                    >
                        <i class="fa-solid fa-sliders text-xs"></i>
                        <span>Lọc nâng cao</span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-200" :class="activeDropdown === 'horizontal_advanced' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'horizontal_advanced'" 
                        @click.outside="activeDropdown = null"
                        class="absolute right-0 mt-2 w-80 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left"
                        x-cloak
                    >
                        <div class="space-y-4">
                            <!-- Bathrooms -->
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Số phòng vệ sinh</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="filterBathrooms = '1'" :class="filterBathrooms === '1' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">1 phòng</button>
                                    <button type="button" @click="filterBathrooms = '2'" :class="filterBathrooms === '2' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">2 phòng</button>
                                    <button type="button" @click="filterBathrooms = '3_plus'" :class="filterBathrooms === '3_plus' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">3+ phòng</button>
                                </div>
                            </div>

                            <!-- Direction -->
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Hướng nhà</span>
                                <div class="grid grid-cols-4 gap-1.5">
                                    <button type="button" @click="filterDirection = 'Đông'" :class="filterDirection === 'Đông' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">Đông</button>
                                    <button type="button" @click="filterDirection = 'Tây'" :class="filterDirection === 'Tây' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">Tây</button>
                                    <button type="button" @click="filterDirection = 'Nam'" :class="filterDirection === 'Nam' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">Nam</button>
                                    <button type="button" @click="filterDirection = 'Bắc'" :class="filterDirection === 'Bắc' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">Bắc</button>
                                    <button type="button" @click="filterDirection = 'Đông Bắc'" :class="filterDirection === 'Đông Bắc' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">Đông Bắc</button>
                                    <button type="button" @click="filterDirection = 'Đông Nam'" :class="filterDirection === 'Đông Nam' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">Đông Nam</button>
                                    <button type="button" @click="filterDirection = 'Tây Bắc'" :class="filterDirection === 'Tây Bắc' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">Tây Bắc</button>
                                    <button type="button" @click="filterDirection = 'Tây Nam'" :class="filterDirection === 'Tây Nam' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-1 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">Tây Nam</button>
                                </div>
                            </div>

                            <!-- Furniture -->
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Nội thất</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="filterFurniture = 'full'" :class="filterFurniture === 'full' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">Đầy đủ</button>
                                    <button type="button" @click="filterFurniture = 'basic'" :class="filterFurniture === 'basic' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">Cơ bản</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-slate-100 pt-3 mt-4">
                            <button type="button" @click="resetAdvanced(); activeDropdown = null;" class="text-[10px] text-slate-400 font-bold hover:text-slate-650 cursor-pointer">Đặt lại</button>
                            <button type="button" @click="applyAdvanced(); activeDropdown = null;" class="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">Áp dụng</button>
                        </div>
                    </div>
                </div>

                <!-- Reset Button -->
                <button 
                    type="button"
                    @click="resetAllFilters()"
                    title="Xóa tất cả bộ lọc"
                    class="w-10 h-10 border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-full flex items-center justify-center cursor-pointer transition flex-shrink-0"
                >
                    <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN BODY: SIDEBAR + MAP -->
    <div class="flex-grow flex flex-col md:flex-row overflow-hidden relative">
        <!-- 1. LEFT SIDEBAR: Desktop Listing Cards (Hidden on mobile) -->
        <aside class="hidden md:flex flex-col w-[380px] lg:w-[420px] bg-white border-r border-slate-100 h-full flex-shrink-0 z-10 shadow-sm">
            <!-- Sidebar Header -->
            <div class="p-5 border-b border-slate-100 flex-shrink-0 bg-white text-left">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-extrabold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot text-primary"></i>
                        <span>Bản đồ bất động sản</span>
                    </span>
                    <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">
                        Tìm thấy <span class="text-primary" x-text="filteredPropertiesCount()"></span> tin đăng
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
                        
                        <div class="flex items-center justify-between mt-1">
                            <div class="flex items-center text-slate-400 text-[10px] font-medium min-w-0">
                                <i class="fa-solid fa-location-dot text-[9px] mr-1 flex-shrink-0"></i>
                                <span class="truncate">{{ $property['location'] }}</span>
                            </div>
                            <button 
                                type="button"
                                @click.stop="window.dispatchEvent(new CustomEvent('open-property-modal', { detail: { id: {{ $property['id'] }} } }))"
                                class="text-[10px] font-black text-primary hover:underline ml-2 flex-shrink-0 cursor-pointer"
                            >
                                Chi tiết
                            </button>
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
                            
                            <div class="flex items-center justify-between text-slate-400 text-[9px] font-medium">
                                <div class="flex items-center min-w-0">
                                    <i class="fa-solid fa-location-dot mr-1 flex-shrink-0"></i>
                                    <span class="truncate">{{ $property['location'] }}</span>
                                </div>
                                <button 
                                    type="button"
                                    @click.stop="window.dispatchEvent(new CustomEvent('open-property-modal', { detail: { id: {{ $property['id'] }} } }))"
                                    class="text-[9px] font-black text-primary hover:underline ml-2 flex-shrink-0 cursor-pointer"
                                >
                                    Chi tiết
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- PREMIUM PROPERTY DETAIL MODAL -->
    <div 
        x-show="showModal" 
        @open-property-modal.window="openModal($event.detail.id)"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-350"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <!-- Modal Content Container -->
        <div 
            @click.outside="closeModal()"
            class="bg-white rounded-3xl w-full max-w-5xl h-[85vh] flex flex-col shadow-2xl overflow-hidden border border-slate-100 relative"
            x-show="showModal"
            x-transition:enter="transition ease-out duration-350 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        >
            <!-- Modal Header (Sticky) -->
            <div class="bg-slate-50 border-b border-slate-100 py-3.5 px-6 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-500">
                    <span class="bg-primary/10 text-primary px-2.5 py-1 rounded-lg" x-text="modalProperty?.type"></span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-700 truncate max-w-[200px] sm:max-w-md" x-text="modalProperty?.title"></span>
                </div>
                <button 
                    type="button"
                    @click="closeModal()" 
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition cursor-pointer"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Loader State -->
            <div x-show="modalLoading" class="flex-grow flex flex-col items-center justify-center bg-slate-50">
                <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin mb-3"></div>
                <p class="text-xs font-bold text-slate-500">Đang tải chi tiết nhà đất...</p>
            </div>

            <!-- Scrollable Content Area -->
            <div x-show="!modalLoading && modalProperty" class="flex-grow overflow-y-auto p-6 md:p-8 bg-slate-50 scrollbar-none" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (Gallery, Specifications, Description) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Image Gallery component -->
                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-sm space-y-4">
                            <!-- Large view -->
                            <div class="relative h-[250px] sm:h-[380px] w-full rounded-xl overflow-hidden bg-slate-100 group">
                                <img 
                                    :src="modalImages[modalActiveImageIndex]" 
                                    alt="Property view" 
                                    class="w-full h-full object-cover object-center transition-all duration-300"
                                >
                                <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                                    <template x-if="modalProperty?.is_vip">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black tracking-wider uppercase bg-red-500 text-white shadow-lg">
                                            <i class="fa-solid fa-crown mr-1"></i> VIP NỔI BẬT
                                        </span>
                                    </template>
                                </div>
                                
                                <template x-if="modalImages.length > 1">
                                    <div>
                                        <button 
                                            type="button"
                                            @click="modalActiveImageIndex = (modalActiveImageIndex - 1 + modalImages.length) % modalImages.length"
                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/45 hover:bg-black/60 text-white flex items-center justify-center transition shadow-md backdrop-blur-sm z-10 cursor-pointer opacity-0 group-hover:opacity-100 duration-200"
                                        >
                                            <i class="fa-solid fa-chevron-left text-xs"></i>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="modalActiveImageIndex = (modalActiveImageIndex + 1) % modalImages.length"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/45 hover:bg-black/60 text-white flex items-center justify-center transition shadow-md backdrop-blur-sm z-10 cursor-pointer opacity-0 group-hover:opacity-100 duration-200"
                                        >
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                        </button>
                                        <span class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-0.5 rounded-full z-10 select-none">
                                            <span x-text="modalActiveImageIndex + 1"></span> / <span x-text="modalImages.length"></span>
                                        </span>
                                    </div>
                                </template>
                            </div>

                            <!-- Thumbnail View (only if > 1 image) -->
                            <template x-if="modalImages.length > 1">
                                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                                    <template x-for="(img, idx) in modalImages" :key="idx">
                                        <button 
                                            type="button"
                                            @click="modalActiveImageIndex = idx"
                                            class="relative w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 border-2 transition cursor-pointer"
                                            :class="modalActiveImageIndex === idx ? 'border-primary' : 'border-transparent opacity-60 hover:opacity-100'"
                                        >
                                            <img :src="img" class="w-full h-full object-cover">
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Title & Price Block -->
                            <div class="border-t border-slate-100 pt-4 text-left">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div class="space-y-2">
                                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-snug">
                                            <span 
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black text-white mr-1.5 align-middle"
                                                :class="modalProperty?.transaction_type === 'sale' ? 'bg-orange-500' : 'bg-blue-500'"
                                            >
                                                <i class="fa-solid mr-1" :class="modalProperty?.transaction_type === 'sale' ? 'fa-tags' : 'fa-key'"></i>
                                                <span x-text="modalProperty?.transaction_type === 'sale' ? 'BÁN' : 'THUÊ'"></span>
                                            </span>
                                            <span x-text="modalProperty?.title"></span>
                                        </h2>
                                        <div class="flex items-center text-slate-400 text-xs font-medium">
                                            <i class="fa-solid fa-location-dot text-slate-400 mr-1.5 text-sm flex-shrink-0"></i>
                                            <span x-text="modalProperty?.location"></span>
                                        </div>
                                    </div>
                                    <div class="flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-start gap-1 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                        <div class="text-lg sm:text-xl font-black text-primary" x-text="modalProperty?.price"></div>
                                        <div class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg"><span x-text="modalProperty?.area"></span> m²</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Grid -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm text-left">
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-primary"></i>
                                <span>Thông số kỹ thuật</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div class="flex items-start space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                        <i class="fa-solid fa-ruler-combined text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Diện tích</span>
                                        <span class="text-xs font-extrabold text-slate-800"><span x-text="modalProperty?.area"></span> m²</span>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                        <i class="fa-solid fa-bed text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Phòng ngủ</span>
                                        <span class="text-xs font-extrabold text-slate-800" x-text="modalProperty?.bedrooms > 0 ? modalProperty?.bedrooms + ' PN' : 'N/A'"></span>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                        <i class="fa-solid fa-bath text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Phòng tắm</span>
                                        <span class="text-xs font-extrabold text-slate-800" x-text="modalProperty?.bathrooms > 0 ? modalProperty?.bathrooms + ' WC' : 'N/A'"></span>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                        <i class="fa-solid fa-compass text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Hướng</span>
                                        <span class="text-xs font-extrabold text-slate-800" x-text="modalProperty?.direction"></span>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                        <i class="fa-solid fa-chair text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Nội thất</span>
                                        <span class="text-xs font-extrabold text-slate-800 truncate block max-w-[130px]" x-text="modalProperty?.furniture" :title="modalProperty?.furniture"></span>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                        <i class="fa-solid fa-file-contract text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pháp lý</span>
                                        <span class="text-xs font-extrabold text-slate-800 truncate block max-w-[130px]" x-text="modalProperty?.legal" :title="modalProperty?.legal"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm text-left">
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-align-left text-primary"></i>
                                <span>Mô tả chi tiết</span>
                            </h3>
                            <div class="text-slate-650 text-xs leading-relaxed space-y-3 font-semibold whitespace-pre-line" x-html="modalPropertyDescription"></div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN (Agent & Booking Appointment) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Agent details card -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm text-left relative">
                            <!-- Wishlist Like Button -->
                            <button 
                                @click="modalToggleLike()"
                                type="button"
                                :class="modalLiked ? 'bg-red-50 text-red-500 border-red-100' : 'bg-slate-50 hover:bg-slate-100 text-slate-400 border-slate-200'"
                                class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center border transition cursor-pointer active:scale-95 z-10"
                                title="Lưu yêu thích"
                            >
                                <i class="fa-solid fa-heart text-xs" :class="modalLiked ? 'text-red-500' : 'text-slate-400'"></i>
                            </button>

                            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 mb-4 pr-10">
                                <img 
                                    :src="modalProperty?.agent?.avatar || 'https://ui-avatars.com/api/?name=' + urlencode(modalProperty?.agent?.name || 'Agent') + '&background=0077bb&color=fff'" 
                                    alt="Agent" 
                                    class="w-12 h-12 rounded-full object-cover border border-slate-150 shadow-sm flex-shrink-0"
                                >
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-800 truncate" x-text="modalProperty?.agent?.name"></h4>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block" x-text="modalProperty?.agent?.company || 'Chủ nhà chính chủ'"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <a 
                                    :href="'tel:' + modalProperty?.agent?.phone"
                                    class="inline-flex items-center justify-center px-2 py-2.5 rounded-xl text-white bg-green-500 hover:bg-green-600 transition font-bold text-xs cursor-pointer truncate"
                                >
                                    <i class="fa-solid fa-phone mr-1"></i> Gọi ngay
                                </a>
                                <a 
                                    :href="'https://zalo.me/' + modalProperty?.agent?.phone"
                                    target="_blank"
                                    class="inline-flex items-center justify-center px-2 py-2.5 rounded-xl text-white bg-[#0068ff] hover:bg-[#0055d0] transition font-bold text-xs cursor-pointer truncate"
                                >
                                    Chat Zalo
                                </a>
                            </div>
                        </div>

                        <!-- Appointment Booking Card -->
                        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm text-left">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar-days text-primary"></i>
                                    <span>Đặt lịch xem nhà</span>
                                </h5>
                                <button 
                                    type="button"
                                    @click="modalResetBooking()"
                                    class="text-[9px] font-bold text-slate-400 hover:text-primary transition cursor-pointer flex items-center gap-1"
                                >
                                    <i class="fa-solid fa-arrow-rotate-left"></i>
                                    <span>Đặt lại</span>
                                </button>
                            </div>

                            <!-- Success State -->
                            <div x-show="modalBookingSubmitted" class="bg-green-50 border border-green-150 rounded-xl p-4 text-center" x-cloak>
                                <i class="fa-solid fa-circle-check text-green-500 text-xl mb-1.5"></i>
                                <h6 class="text-xs font-bold text-green-800 mb-0.5">Gửi thành công!</h6>
                                <p class="text-[9px] text-green-600 leading-normal font-medium">Lịch hẹn đã được lưu. Môi giới sẽ sớm liên hệ xác nhận.</p>
                                <button type="button" @click="modalBookingSubmitted = false" class="mt-3 bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-primary-hover transition cursor-pointer">
                                    Đặt lịch hẹn khác
                                </button>
                            </div>

                            <!-- Form State -->
                            <form x-show="!modalBookingSubmitted" @submit.prevent="modalSubmitBooking()" class="space-y-3">
                                <div x-show="modalBookingError" class="p-2.5 bg-red-50 text-red-500 rounded-lg text-[10px] font-bold" x-cloak>
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                    <span x-text="modalBookingError"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Ngày hẹn</label>
                                        <input type="date" x-model="modalBookingDate" class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Giờ hẹn</label>
                                        <input type="time" x-model="modalBookingTime" class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Tên của bạn</label>
                                    <input type="text" x-model="modalBookingName" placeholder="Họ và tên..." class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                </div>

                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Số điện thoại</label>
                                    <input type="tel" x-model="modalBookingPhone" placeholder="Số điện thoại..." class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                </div>

                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Lời nhắn</label>
                                    <textarea x-model="modalBookingMessage" rows="2" placeholder="Tôi muốn xem nhà lúc..." class="w-full text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary resize-none"></textarea>
                                </div>

                                <button 
                                    type="submit" 
                                    :disabled="modalBookingProcessing"
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow shadow-primary/20 disabled:bg-slate-350 cursor-pointer"
                                >
                                    <span x-show="!modalBookingProcessing"><i class="fa-solid fa-paper-plane mr-1 text-[10px]"></i> Gửi yêu cầu hẹn</span>
                                    <span x-show="modalBookingProcessing" class="flex items-center gap-1.5"><i class="fa-solid fa-spinner animate-spin"></i> Đang gửi...</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
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
        const urlParams = new URLSearchParams(window.location.search);

        return {
            properties: @json($properties),
            activeId: null,
            filterPurpose: urlParams.get('purpose') || urlParams.get('transaction_type') || 'rent',
            filterType: urlParams.get('property_type') || '',
            filterPrice: urlParams.get('price') || '',
            filterBedrooms: urlParams.get('bedrooms') || '',
            filterArea: urlParams.get('area') || '',
            filterKeyword: urlParams.get('keyword') || '',
            filterBathrooms: urlParams.get('bathrooms') || '',
            filterFurniture: urlParams.get('furniture') || '',
            filterDirection: urlParams.get('direction') || '',

            // Autocomplete variables
            query: urlParams.get('keyword') || '',
            suggestions: [],
            isOpen: false,
            activeIndex: -1,
            activeDropdown: null,
            map: null,
            markers: {},
            ignoreMobileScroll: false,
            mobileScrollTimeout: null,

            // Modal state variables
            showModal: false,
            modalLoading: false,
            modalProperty: null,
            modalImages: [],
            modalActiveImageIndex: 0,
            modalLiked: false,
            modalPropertyDescription: '',
            modalBookingName: '{{ Auth::check() ? Auth::user()->name : "" }}',
            modalBookingPhone: '{{ Auth::check() ? Auth::user()->phone : "" }}',
            modalBookingEmail: '{{ Auth::check() ? Auth::user()->email : "" }}',
            modalBookingDate: '',
            modalBookingTime: '',
            modalBookingMessage: '',
            modalBookingSubmitted: false,
            modalBookingError: '',
            modalBookingProcessing: false,
            modalWishlistProcessing: false,

            isSale() {
                return this.filterPurpose === 'sale';
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

            bedroomsLabel() {
                if (!this.filterBedrooms) return 'Phòng ngủ';
                const labels = {
                    '1': '1 phòng ngủ',
                    '2': '2 phòng ngủ',
                    '3': '3 phòng ngủ',
                    '4_plus': '4+ phòng ngủ'
                };
                return labels[this.filterBedrooms] || 'Phòng ngủ';
            },

            areaLabel() {
                if (!this.filterArea) return 'Diện tích';
                const labels = {
                    'under_30': 'Dưới 30 m²',
                    '30_50': '30 - 50 m²',
                    '50_80': '50 - 80 m²',
                    '80_120': '80 - 120 m²',
                    'above_120': 'Trên 120 m²'
                };
                return labels[this.filterArea] || 'Diện tích';
            },

            setPurpose(val) {
                this.filterPurpose = val;
                this.filterPrice = ''; // Reset price filter when purpose changes
                this.submitForm();
            },
            setType(val) {
                this.filterType = val;
                this.submitForm();
            },
            setPrice(val) {
                this.filterPrice = val;
                this.submitForm();
            },
            setBedrooms(val) {
                this.filterBedrooms = val;
                this.submitForm();
            },
            setArea(val) {
                this.filterArea = val;
                this.submitForm();
            },
            submitSearch() {
                this.filterKeyword = this.query;
                this.submitForm();
            },
            applyAdvanced() {
                this.submitForm();
            },
            resetAdvanced() {
                this.filterBathrooms = '';
                this.filterFurniture = '';
                this.filterDirection = '';
                this.submitForm();
            },
            resetAllFilters() {
                this.filterType = '';
                this.filterPrice = '';
                this.filterBedrooms = '';
                this.filterArea = '';
                this.filterKeyword = '';
                this.filterBathrooms = '';
                this.filterFurniture = '';
                this.filterDirection = '';
                this.query = '';
                this.submitForm();
            },
            submitForm() {
                this.$nextTick(() => {
                    this.$refs.filterForm.submit();
                });
            },

            selectSuggestion(sug) {
                this.query = sug.label;
                this.filterKeyword = sug.label;
                this.isOpen = false;
                if (sug.type === 'property' && sug.id) {
                    window.location.href = `/property/${sug.id}`;
                } else {
                    this.submitForm();
                }
            },

            selectActiveIndex() {
                if (this.activeIndex >= 0 && this.activeIndex < this.suggestions.length) {
                    this.selectSuggestion(this.suggestions[this.activeIndex]);
                } else {
                    this.submitSearch();
                }
            },

            async fetchSuggestions() {
                if (this.query.trim().length < 2) {
                    this.suggestions = [];
                    this.isOpen = false;
                    return;
                }
                try {
                    const res = await fetch(`/api/properties/autocomplete?q=${encodeURIComponent(this.query)}`);
                    this.suggestions = await res.json();
                    this.isOpen = this.suggestions.length > 0;
                    this.activeIndex = -1;
                } catch (error) {
                    console.error('Error fetching suggestions:', error);
                }
            },

            // Filter properties based on select criteria
            filteredProperties() {
                return this.properties.filter(p => {
                    // 0. Match Purpose (Rent vs Sale)
                    const matchPurpose = !this.filterPurpose || p.transaction_type === this.filterPurpose;

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

                    return matchPurpose && matchType && matchPrice;
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
                    style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json', // Vector style Voyager
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
                    
                    if (this.properties.length > 0) {
                        const bounds = new maplibregl.LngLatBounds();
                        this.properties.forEach(p => bounds.extend([p.lng, p.lat]));
                        this.map.fitBounds(bounds, { 
                            padding: { top: 80, bottom: 80, left: 50, right: 50 },
                            maxZoom: 14.5,
                            duration: 800
                        });
                    } else if (showUserMarker) {
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
                        <div class="w-[240px] text-left relative bg-white">
                            <!-- Image Container with Absolute Badges -->
                            <div class="relative w-full h-28 overflow-hidden rounded-t-2xl">
                                <a href="javascript:void(0)" onclick="window.dispatchEvent(new CustomEvent('open-property-modal', { detail: { id: ${p.id} } }))" class="block w-full h-full">
                                    <img src="${imgUrl}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                </a>
                                <!-- Property Type Badge -->
                                <span class="absolute top-2 left-2 bg-[#0077bb] text-white text-[9px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">
                                    ${p.type}
                                </span>
                                <!-- Close Button Mock -->
                                <button onclick="window.activeMapPopup?.remove()" class="absolute top-2 right-2 w-6 h-6 bg-white/95 hover:bg-white rounded-full flex items-center justify-center text-slate-600 hover:text-slate-900 shadow-md transition focus:outline-none z-10">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                            
                            <!-- Body Info -->
                            <div class="p-3.5">
                                <h4 class="text-[13px] font-black text-slate-800 line-clamp-1 hover:text-[#0077bb] transition mb-1">
                                    <a href="javascript:void(0)" onclick="window.dispatchEvent(new CustomEvent('open-property-modal', { detail: { id: ${p.id} } }))">${p.title}</a>
                                </h4>
                                <p class="text-[10px] font-medium text-slate-400 truncate mb-3">
                                    ${p.location}
                                </p>
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                    <span class="text-[13px] font-black text-[#0077bb]">${p.price}</span>
                                    <a href="javascript:void(0)" onclick="window.dispatchEvent(new CustomEvent('open-property-modal', { detail: { id: ${p.id} } }))" class="text-[10px] font-black text-[#0077bb] hover:underline flex items-center gap-0.5">
                                        Chi tiết <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;

                    // Create popup
                    const popup = new maplibregl.Popup({ 
                        offset: 25, 
                        closeButton: false,
                        closeOnClick: true,
                        anchor: 'bottom'
                    }).setHTML(popupHTML);

                    // Track active popup globally
                    popup.on('open', () => {
                        window.activeMapPopup = popup;
                    });

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
                        const property = this.properties.find(prop => prop.id === parseInt(markerId));
                        if (property) {
                            if (parseInt(markerId) === id) {
                                markerEl.classList.remove('bg-primary', 'hover:bg-primary-hover', 'text-white', 'border-white');
                                markerEl.classList.add('bg-white', 'text-slate-800', 'border-[#0077bb]', 'scale-110', 'z-[999]');
                                markerEl.innerHTML = `<span class="flex items-center text-xs font-black"><i class="fa-solid fa-circle-check text-emerald-500 mr-1 text-[13px]"></i>${property.price_label}</span>`;
                            } else {
                                markerEl.classList.add('bg-primary', 'hover:bg-primary-hover', 'text-white', 'border-white');
                                markerEl.classList.remove('bg-white', 'text-slate-800', 'border-[#0077bb]', 'scale-110', 'z-[999]');
                                markerEl.innerHTML = property.price_label;
                            }
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
                }, 100); // Debounce scroll detection slightly for smoothness
            },

            // Modal methods
            openModal(id) {
                this.showModal = true;
                this.modalLoading = true;
                this.modalResetBooking();
                this.modalActiveImageIndex = 0;
                
                fetch(`/api/properties/${id}/json`)
                    .then(res => res.json())
                    .then(data => {
                        this.modalLoading = false;
                        if (data.success) {
                            this.modalProperty = data.property;
                            this.modalLiked = data.isLiked;
                            
                            // Format images
                            this.modalImages = (data.property.images || []).map(img => {
                                return (img.startsWith('http://') || img.startsWith('https://')) ? img : '/' + img.replace(/^\//, '');
                            });
                            if (this.modalImages.length === 0) {
                                this.modalImages.push('/images/apartment_1.png');
                            }
                            
                            // Format description
                            const desc = data.property.description || '';
                            this.modalPropertyDescription = desc.replace(/\\n/g, "\n").replace(/\n/g, '<br>');
                        } else {
                            this.closeModal();
                            alert('Không thể tải thông tin bất động sản này.');
                        }
                    })
                    .catch(err => {
                        this.modalLoading = false;
                        this.closeModal();
                        console.error('Error fetching property details:', err);
                        alert('Lỗi kết nối mạng, vui lòng thử lại.');
                    });
            },
            closeModal() {
                this.showModal = false;
                this.modalProperty = null;
            },
            modalToggleLike() {
                if (!this.modalProperty || this.modalWishlistProcessing) return;
                this.modalWishlistProcessing = true;

                fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        property_id: this.modalProperty.id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.modalWishlistProcessing = false;
                    if (data.success) {
                        this.modalLiked = data.is_favorite;
                    }
                })
                .catch(err => {
                    this.modalWishlistProcessing = false;
                    console.error('Error:', err);
                });
            },
            modalResetBooking() {
                this.modalBookingDate = '';
                this.modalBookingTime = '';
                this.modalBookingMessage = '';
                this.modalBookingSubmitted = false;
                this.modalBookingError = '';
                this.modalBookingProcessing = false;
            },
            modalSubmitBooking() {
                @guest
                    window.location.href = '{{ route('login') }}';
                    return;
                @endguest

                if ({{ Auth::check() ? Auth::id() : 0 }} === this.modalProperty?.owner_id) {
                    alert('Bạn không thể tự đặt lịch xem nhà trên tin đăng của chính mình.');
                    return;
                }

                if (!this.modalBookingDate) {
                    this.modalBookingError = 'Vui lòng chọn ngày hẹn.';
                    return;
                }
                if (!this.modalBookingTime) {
                    this.modalBookingError = 'Vui lòng chọn giờ hẹn.';
                    return;
                }

                if (this.modalBookingProcessing) return;
                this.modalBookingProcessing = true;
                this.modalBookingError = '';

                fetch('{{ route('appointments.book') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        property_id: this.modalProperty.id,
                        name: this.modalBookingName,
                        phone: this.modalBookingPhone,
                        email: this.modalBookingEmail,
                        date: this.modalBookingDate,
                        time: this.modalBookingTime,
                        message: this.modalBookingMessage
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    this.modalBookingProcessing = false;
                    if (res.status === 200 || res.status === 201 || res.body.success) {
                        this.modalBookingSubmitted = true;
                    } else {
                        this.modalBookingError = res.body.message || 'Có lỗi xảy ra, vui lòng thử lại.';
                    }
                })
                .catch(err => {
                    this.modalBookingProcessing = false;
                    this.modalBookingError = 'Lỗi kết nối mạng, vui lòng thử lại.';
                    console.error('Error:', err);
                });
            },
            urlencode(str) {
                return encodeURIComponent(str || '');
            }
        };
    }
</script>
@endpush
