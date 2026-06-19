@extends('layouts.app')

@section('title', request('purpose') === 'rent' ? 'Kho Dự Án Bất Động Sản Cho Thuê | BDS Rental' : (request('purpose') === 'sale' ? 'Kho Dự Án Bất Động Sản Mua Bán | BDS Rental' : 'Kho Dự Án Bất Động Sản | BDS Rental'))

@section('content')
<div class="bg-slate-50 pt-28 pb-16 min-h-screen" x-data="{ 
    mobileFiltersOpen: false,
    provinces: [],
    districts: [],
    wards: [],
    selectedProvince: '{{ request('province') ?: request('city') ?: '' }}',
    selectedDistrict: '{{ request('district') ?: '' }}',
    selectedWard: '{{ request('ward') ?: '' }}',
    purpose: '{{ request('purpose') ?: request('transaction_type') ?: '' }}',
    price: '{{ request('price') ?: '' }}',
    property_type: '{{ request('property_type') ?: request('type') ?: '' }}',
    area: '{{ request('area') ?: '' }}',
    bedrooms: '{{ request('bedrooms') ?: request('bedroom') ?: '' }}',
    bathrooms: '{{ request('bathrooms') ?: request('bathroom') ?: '' }}',
    furniture: '{{ request('furniture') ?: '' }}',
    direction: '{{ request('direction') ?: '' }}',
    keyword: '{{ request('keyword') ?: request('search') ?: '' }}',
    showAdvanced: false,
    
    async init() {
        try {
            const response = await fetch('/vietnam_provinces.json');
            this.provinces = await response.json();
            
            if (this.selectedProvince) {
                const provObj = this.provinces.find(p => p.Name.includes(this.selectedProvince) || this.selectedProvince.includes(p.Name));
                if (provObj) {
                    this.selectedProvince = provObj.Name;
                    this.districts = provObj.Districts;
                    if (this.selectedDistrict) {
                        const distObj = this.districts.find(d => d.Name.includes(this.selectedDistrict) || this.selectedDistrict.includes(d.Name) || d.Id === this.selectedDistrict);
                        if (distObj) {
                            this.selectedDistrict = distObj.Name;
                            this.wards = distObj.Wards;
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Failed to load locations:', error);
        }
    },
    
    updateDistricts() {
        this.selectedDistrict = '';
        this.selectedWard = '';
        this.districts = [];
        this.wards = [];
        
        const provObj = this.provinces.find(p => p.Name === this.selectedProvince);
        if (provObj) {
            this.districts = provObj.Districts;
        }
    },
    
    updateWards() {
        this.selectedWard = '';
        this.wards = [];
        
        const distObj = this.districts.find(d => d.Name === this.selectedDistrict);
        if (distObj) {
            this.wards = distObj.Wards;
        }
    },
    
    resetFilters() {
        this.keyword = '';
        this.purpose = '';
        this.property_type = '';
        this.selectedProvince = '';
        this.selectedDistrict = '';
        this.selectedWard = '';
        this.price = '';
        this.area = '';
        this.bedrooms = '';
        this.bathrooms = '';
        this.furniture = '';
        this.direction = '';
        window.location.href = '/listings';
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-6 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            @if(request('purpose') === 'rent')
                <a href="/listings" class="hover:text-primary transition">Kho dự án</a>
                <span>/</span>
                <span class="text-slate-800 font-bold">Cho thuê</span>
            @elseif(request('purpose') === 'sale')
                <a href="/listings" class="hover:text-primary transition">Kho dự án</a>
                <span>/</span>
                <span class="text-slate-800 font-bold">Mua bán</span>
            @else
                <span class="text-slate-800 font-bold">Kho dự án</span>
            @endif
        </nav>

        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-left">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                    @if(request('purpose') === 'rent')
                        Kho dự án cho thuê
                    @elseif(request('purpose') === 'sale')
                        Kho dự án mua bán
                    @else
                        Kho dự án bất động sản
                    @endif
                </h1>
                <p class="text-xs text-slate-500 mt-1">Tìm thấy <span class="font-bold text-primary">{{ $properties->total() }}</span> tin đăng phù hợp trên toàn quốc</p>
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

        <!-- Horizontal Filter Bar Form (Desktop view) -->
        <form 
            action="/listings" 
            method="GET" 
            id="filter-form"
            class="bg-white rounded-3xl p-4 border border-slate-100 shadow-md mb-8 text-left hidden lg:block"
            x-data="{ activeDropdown: null }"
        >
            <!-- Hidden inputs for form submission -->
            <input type="hidden" name="purpose" :value="purpose">
            <input type="hidden" name="property_type" :value="property_type">
            <input type="hidden" name="province" :value="selectedProvince">
            <input type="hidden" name="district" :value="selectedDistrict">
            <input type="hidden" name="ward" :value="selectedWard">
            <input type="hidden" name="price" :value="price">
            <input type="hidden" name="area" :value="area">
            <input type="hidden" name="bedrooms" :value="bedrooms">
            <input type="hidden" name="bathrooms" :value="bathrooms">
            <input type="hidden" name="furniture" :value="furniture">
            <input type="hidden" name="direction" :value="direction">

            <div class="flex items-center space-x-2.5 w-full">
                <!-- 1. Keyword Search -->
                <div class="relative flex-1 min-w-[280px]">
                    <input 
                        type="text" 
                        name="keyword" 
                        x-model="keyword"
                        placeholder="Tìm địa điểm, dự án..." 
                        class="w-full pl-4 pr-3 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition h-12"
                    >
                </div>

                <!-- 2. Submit Button (Moved next to keyword search input) -->
                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center w-12 h-12 border border-transparent text-xs font-extrabold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 hover:shadow-primary/30 transition duration-150 cursor-pointer flex-shrink-0"
                >
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                <!-- 3. Button Pill: Purpose (Loại giao dịch) -->
                <div class="relative w-[130px] flex-shrink-0">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'purpose' ? null : 'purpose')"
                        :class="purpose ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1 px-4 h-12 border rounded-xl text-xs font-bold transition cursor-pointer w-full"
                    >
                        <span x-text="purpose === 'rent' ? 'Cho thuê' : (purpose === 'sale' ? 'Mua bán' : 'Loại giao dịch')"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-205" :class="activeDropdown === 'purpose' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'purpose'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-48 rounded-2xl bg-white border border-slate-150 shadow-2xl p-2 z-50 text-left"
                        x-cloak
                    >
                        <div class="flex flex-col space-y-0.5">
                            <button 
                                type="button" 
                                @click="purpose = 'rent'; price = ''; activeDropdown = null;"
                                :class="purpose === 'rent' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'"
                                class="w-full flex items-center space-x-2.5 px-3 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer text-left focus:outline-none"
                            >
                                <i class="fa-solid fa-key text-[11px]" :class="purpose === 'rent' ? 'text-primary' : 'text-slate-400'"></i>
                                <span>Cho thuê</span>
                            </button>
                            <button 
                                type="button" 
                                @click="purpose = 'sale'; price = ''; activeDropdown = null;"
                                :class="purpose === 'sale' ? 'bg-primary/5 text-primary' : 'text-slate-700 hover:bg-slate-50'"
                                class="w-full flex items-center space-x-2.5 px-3 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer text-left focus:outline-none"
                            >
                                <i class="fa-solid fa-tag text-[11px]" :class="purpose === 'sale' ? 'text-primary' : 'text-slate-400'"></i>
                                <span>Mua bán</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. Button Pill: Property Type (Loại hình) -->
                <div class="relative w-[115px] flex-shrink-0">
                    <button 
                        type="button"
                        @click.prevent="activeDropdown = (activeDropdown === 'type' ? null : 'type')"
                        :class="property_type ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                        class="flex items-center justify-between space-x-1 px-4 h-12 border rounded-xl text-xs font-bold transition cursor-pointer w-full"
                    >
                        <span x-text="property_type === 'apartment' ? 'Căn hộ' : (property_type === 'house' ? 'Nhà riêng' : (property_type === 'room' ? 'Phòng trọ' : (property_type === 'land' ? 'Đất nền' : (property_type === 'premises' ? 'Mặt bằng' : (property_type === 'office' ? 'Văn phòng' : (property_type === 'warehouse' ? 'Kho xưởng' : 'Loại hình'))))))"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition duration-205" :class="activeDropdown === 'type' ? 'rotate-180 text-primary' : 'text-slate-400'"></i>
                    </button>
                    
                    <div 
                        x-show="activeDropdown === 'type'" 
                        @click.outside="activeDropdown = null"
                        class="absolute left-0 mt-2 w-80 rounded-2xl bg-white border border-slate-150 shadow-2xl p-4 z-50 text-left"
                        x-cloak
                    >
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-0.5">Chọn loại hình bất động sản</span>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="property_type = 'apartment'; activeDropdown = null;" :class="property_type === 'apartment' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-building text-xs"></i>
                                <span>Căn hộ</span>
                            </button>
                            <button type="button" @click="property_type = 'house'; activeDropdown = null;" :class="property_type === 'house' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-house text-xs"></i>
                                <span>Nhà riêng</span>
                            </button>
                            <button type="button" @click="property_type = 'room'; activeDropdown = null;" :class="property_type === 'room' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-door-open text-xs"></i>
                                <span>Phòng trọ</span>
                            </button>
                            <button type="button" @click="property_type = 'land'; activeDropdown = null;" :class="property_type === 'land' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-map text-xs"></i>
                                <span>Đất nền</span>
                            </button>
                            <button type="button" @click="property_type = 'premises'; activeDropdown = null;" :class="property_type === 'premises' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-store text-xs"></i>
                                <span>Mặt bằng</span>
                            </button>
                            <button type="button" @click="property_type = 'office'; activeDropdown = null;" :class="property_type === 'office' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-briefcase text-xs"></i>
                                <span>Văn phòng</span>
                            </button>
                            <button type="button" @click="property_type = 'warehouse'; activeDropdown = null;" :class="property_type === 'warehouse' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'" class="flex items-center space-x-2 px-3 py-2 border rounded-xl text-xs font-bold transition cursor-pointer">
                                <i class="fa-solid fa-warehouse text-xs"></i>
                                <span>Kho xưởng</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 5. Toggle Button for Advanced Filters -->
                <button 
                    type="button" 
                    @click="showAdvanced = !showAdvanced"
                    :class="showAdvanced || selectedProvince || price || area || bedrooms || bathrooms || furniture || direction ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                    class="flex items-center justify-between space-x-1 px-4 h-12 border rounded-xl text-xs font-bold transition cursor-pointer w-[160px] flex-shrink-0"
                >
                    <span class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-sliders text-[10px]"></i>
                        <span>Bộ lọc nâng cao</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-[8px] transition duration-200" :class="showAdvanced ? 'rotate-180' : ''"></i>
                </button>

                <!-- 6. Reset Button -->
                <button 
                    type="button" 
                    @click="resetFilters()"
                    class="border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-500 font-bold w-12 h-12 rounded-xl text-xs transition cursor-pointer focus:outline-none flex items-center justify-center flex-shrink-0"
                >
                    <i class="fa-solid fa-arrow-rotate-left text-sm"></i>
                </button>
            </div>

            <!-- Collapsible Advanced Search Fields -->
            <div 
                x-show="showAdvanced"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-[1000px]"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 max-h-[1000px]"
                x-transition:leave-end="opacity-0 max-h-0"
                class="mt-6 pt-6 border-t border-slate-100 overflow-hidden text-left"
                x-cloak
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Column 1: Location Filters (Khu vực) -->
                    <div class="space-y-3">
                        <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Khu vực địa lý</span>
                        
                        <div class="space-y-2.5">
                            <!-- Province -->
                            <div class="relative">
                                <select 
                                    x-model="selectedProvince"
                                    @change="updateDistricts()"
                                    class="w-full pl-3.5 pr-2.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer appearance-none"
                                >
                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                    <template x-for="p in provinces" :key="p.Id">
                                        <option :value="p.Name" x-text="p.Name"></option>
                                    </template>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
                            </div>

                            <!-- District -->
                            <div class="relative">
                                <select 
                                    x-model="selectedDistrict"
                                    @change="updateWards()"
                                    class="w-full pl-3.5 pr-2.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer appearance-none"
                                    :disabled="!selectedProvince"
                                >
                                    <option value="">Chọn Quận/Huyện</option>
                                    <template x-for="d in districts" :key="d.Id">
                                        <option :value="d.Name" x-text="d.Name"></option>
                                    </template>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
                            </div>

                            <!-- Ward -->
                            <div class="relative">
                                <select 
                                    x-model="selectedWard"
                                    class="w-full pl-3.5 pr-2.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer appearance-none"
                                    :disabled="!selectedDistrict"
                                >
                                    <option value="">Chọn Phường/Xã</option>
                                    <template x-for="w in wards" :key="w.Id">
                                        <option :value="w.Name" x-text="w.Name"></option>
                                    </template>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Price & Area (Khoảng giá & Diện tích) -->
                    <div class="space-y-4">
                        <!-- Price -->
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Khoảng giá</span>
                            <!-- Rent Price Options -->
                            <div x-show="purpose === 'rent' || purpose === ''" class="grid grid-cols-3 gap-1.5">
                                <button type="button" @click="price = 'under_3'" :class="price === 'under_3' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Dưới 3tr</button>
                                <button type="button" @click="price = '3_5'" :class="price === '3_5' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">3 - 5tr</button>
                                <button type="button" @click="price = '5_10'" :class="price === '5_10' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">5 - 10tr</button>
                                <button type="button" @click="price = '10_20'" :class="price === '10_20' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">10 - 20tr</button>
                                <button type="button" @click="price = 'above_20'" :class="price === 'above_20' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Trên 20tr</button>
                            </div>
                            <!-- Sale Price Options -->
                            <div x-show="purpose === 'sale'" class="grid grid-cols-3 gap-1.5">
                                <button type="button" @click="price = 'under_1b'" :class="price === 'under_1b' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Dưới 1 tỷ</button>
                                <button type="button" @click="price = '1b_3b'" :class="price === '1b_3b' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">1 - 3 tỷ</button>
                                <button type="button" @click="price = '3b_5b'" :class="price === '3b_5b' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">3 - 5 tỷ</button>
                                <button type="button" @click="price = '5b_10b'" :class="price === '5b_10b' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">5 - 10 tỷ</button>
                                <button type="button" @click="price = 'above_10b'" :class="price === 'above_10b' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Trên 10 tỷ</button>
                            </div>
                        </div>

                        <!-- Area -->
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Diện tích</span>
                            <div class="grid grid-cols-3 gap-1.5">
                                <button type="button" @click="area = 'under_30'" :class="area === 'under_30' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Dưới 30m²</button>
                                <button type="button" @click="area = '30_50'" :class="area === '30_50' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">30 - 50m²</button>
                                <button type="button" @click="area = '50_80'" :class="area === '50_80' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">50 - 80m²</button>
                                <button type="button" @click="area = '80_120'" :class="area === '80_120' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">80 - 120m²</button>
                                <button type="button" @click="area = 'above_120'" :class="area === 'above_120' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Trên 120m²</button>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Bedrooms, Bathrooms, Furniture, Direction -->
                    <div class="space-y-4">
                        <!-- Bedrooms & Bathrooms -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Phòng ngủ</span>
                                <div class="bg-slate-50 p-0.5 rounded-lg flex space-x-0.5 border border-slate-200">
                                    <button type="button" @click="bedrooms = '1'" :class="bedrooms === '1' ? 'bg-white text-primary shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-1 rounded-md text-[10px] text-center transition cursor-pointer">1+</button>
                                    <button type="button" @click="bedrooms = '2'" :class="bedrooms === '2' ? 'bg-white text-primary shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-1 rounded-md text-[10px] text-center transition cursor-pointer">2+</button>
                                    <button type="button" @click="bedrooms = '3'" :class="bedrooms === '3' ? 'bg-white text-primary shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-1 rounded-md text-[10px] text-center transition cursor-pointer">3+</button>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Phòng vệ sinh</span>
                                <div class="bg-slate-50 p-0.5 rounded-lg flex space-x-0.5 border border-slate-200">
                                    <button type="button" @click="bathrooms = '1'" :class="bathrooms === '1' ? 'bg-white text-primary shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-1 rounded-md text-[10px] text-center transition cursor-pointer">1+</button>
                                    <button type="button" @click="bathrooms = '2'" :class="bathrooms === '2' ? 'bg-white text-primary shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-1 rounded-md text-[10px] text-center transition cursor-pointer">2+</button>
                                    <button type="button" @click="bathrooms = '3'" :class="bathrooms === '3' ? 'bg-white text-primary shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-1 rounded-md text-[10px] text-center transition cursor-pointer">3+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Furniture -->
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Nội thất</span>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="button" @click="furniture = 'full'" :class="furniture === 'full' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Đầy đủ</button>
                                <button type="button" @click="furniture = 'basic'" :class="furniture === 'basic' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-2 py-1.5 border rounded-lg text-center text-[10px] transition cursor-pointer">Cơ bản</button>
                            </div>
                        </div>

                        <!-- Direction -->
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-0.5">Hướng</span>
                            <div class="grid grid-cols-4 gap-1">
                                <button type="button" @click="direction = 'east'" :class="direction === 'east' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-1 py-1 border rounded-lg text-center text-[9px] transition cursor-pointer">Đông</button>
                                <button type="button" @click="direction = 'west'" :class="direction === 'west' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-1 py-1 border rounded-lg text-center text-[9px] transition cursor-pointer">Tây</button>
                                <button type="button" @click="direction = 'south'" :class="direction === 'south' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-1 py-1 border rounded-lg text-center text-[9px] transition cursor-pointer">Nam</button>
                                <button type="button" @click="direction = 'north'" :class="direction === 'north' ? 'border-primary bg-primary/5 text-primary font-bold shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-650 hover:bg-slate-100'" class="px-1 py-1 border rounded-lg text-center text-[9px] transition cursor-pointer">Bắc</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer of Collapsible Area -->
                <div class="flex justify-between items-center border-t border-slate-100 mt-6 pt-4">
                    <button 
                        type="button" 
                        @click="resetFilters(); showAdvanced = false;"
                        class="text-xs text-slate-450 hover:text-slate-700 font-bold flex items-center gap-1 cursor-pointer"
                    >
                        <i class="fa-solid fa-arrow-rotate-left text-[10px]"></i>
                        <span>Đặt lại bộ lọc</span>
                    </button>
                    <div class="flex gap-2">
                        <button 
                            type="button" 
                            @click="showAdvanced = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer"
                        >
                            Thu gọn
                        </button>
                        <button 
                            type="submit"
                            class="px-5 py-2 bg-primary hover:bg-primary-hover text-white font-extrabold rounded-xl text-xs shadow-md shadow-primary/20 hover:shadow-primary/30 transition cursor-pointer"
                        >
                            Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Main Content Area (Full Width Layout) -->
        <div class="w-full">
            <main class="w-full">
                <!-- Sorting & Quick actions -->
                <div class="bg-white rounded-2xl px-5 py-3.5 border border-slate-100 shadow-sm flex items-center justify-between mb-8">
                    <span class="text-xs text-slate-400 font-bold hidden sm:inline">Xem dạng lưới</span>
                    
                    <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-start">
                        <label class="text-xs font-bold text-slate-500 whitespace-nowrap">Sắp xếp theo:</label>
                        <div class="relative min-w-[150px]">
                            <select 
                                name="sort" 
                                @change="
                                    const url = new URL(window.location.href);
                                    url.searchParams.set('sort', $el.value);
                                    window.location.href = url.toString();
                                "
                                class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                            >
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                                <option value="area_asc" {{ request('sort') === 'area_asc' ? 'selected' : '' }}>Diện tích tăng dần</option>
                                <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>Diện tích giảm dần</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <!-- Grid of Listings (Responsive up to 4 columns on XL screens) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                    @forelse($properties as $property)
                        @include('components.property-card', ['property' => $property])
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-folder-open text-2xl text-slate-400"></i>
                            </div>
                            <h3 class="text-slate-800 font-bold mb-1">Không tìm thấy kết quả</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">Vui lòng thay đổi từ khóa hoặc bộ lọc để tìm thấy bất động sản mong muốn.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Section -->
                @if($properties->hasPages())
                <div class="flex justify-center mt-12">
                    <nav class="inline-flex space-x-1 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm" aria-label="Pagination">
                        {{-- Previous Page Link --}}
                        @if($properties->onFirstPage())
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-300 cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                            @if ($page == $properties->currentPage())
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary text-white font-bold shadow-md shadow-primary/20">{{ $page }}</span>
                            @else
                                <a href="{{ $properties->appends(request()->query())->url($page) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-650 hover:bg-slate-50 hover:text-primary transition font-bold">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($properties->hasMorePages())
                            <a href="{{ $properties->appends(request()->query())->nextPageUrl() }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-300 cursor-not-allowed">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </nav>
                </div>
                @endif
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
                <form id="mobile-filter-form" action="/listings" method="GET" class="flex-grow overflow-y-auto space-y-5 pr-1">
                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" name="purpose" :value="purpose">
                    <input type="hidden" name="property_type" :value="property_type">
                    <input type="hidden" name="province" :value="selectedProvince">
                    <input type="hidden" name="district" :value="selectedDistrict">
                    <input type="hidden" name="ward" :value="selectedWard">
                    <input type="hidden" name="price" :value="price">
                    <input type="hidden" name="area" :value="area">
                    <input type="hidden" name="bedrooms" :value="bedrooms">
                    <input type="hidden" name="bathrooms" :value="bathrooms">
                    <input type="hidden" name="furniture" :value="furniture">
                    <input type="hidden" name="direction" :value="direction">

                    <!-- Keyword -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Từ khóa tìm kiếm</label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="keyword" 
                                x-model="keyword"
                                placeholder="Nhập địa điểm, tên dự án..." 
                                class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition h-[38px]"
                            >
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Loại giao dịch</label>
                        <div class="bg-slate-100 p-1 rounded-xl flex space-x-1 border border-slate-200">
                            <button type="button" @click="purpose = 'rent'; price = '';" :class="purpose === 'rent' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1.5 rounded-lg text-xs font-bold text-center transition cursor-pointer flex items-center justify-center space-x-1">
                                <i class="fa-solid fa-key text-[10px]"></i>
                                <span>Thuê</span>
                            </button>
                            <button type="button" @click="purpose = 'sale'; price = '';" :class="purpose === 'sale' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1.5 rounded-lg text-xs font-bold text-center transition cursor-pointer flex items-center justify-center space-x-1">
                                <i class="fa-solid fa-tag text-[10px]"></i>
                                <span>Bán</span>
                            </button>
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div class="space-y-2 border-t border-slate-100 pt-3" x-data="{ open: true }">
                        <button type="button" @click="open = !open" class="flex items-center justify-between w-full py-1 focus:outline-none cursor-pointer">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Loại hình</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[8px] transition duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="grid grid-cols-2 gap-2 pt-1">
                            <button type="button" @click="property_type = 'apartment'" :class="property_type === 'apartment' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-building text-xs"></i>
                                <span>Căn hộ</span>
                            </button>
                            <button type="button" @click="property_type = 'house'" :class="property_type === 'house' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-house text-xs"></i>
                                <span>Nhà riêng</span>
                            </button>
                            <button type="button" @click="property_type = 'room'" :class="property_type === 'room' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-door-open text-xs"></i>
                                <span>Phòng trọ</span>
                            </button>
                            <button type="button" @click="property_type = 'land'" :class="property_type === 'land' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-map text-xs"></i>
                                <span>Đất nền</span>
                            </button>
                            <button type="button" @click="property_type = 'premises'" :class="property_type === 'premises' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-store text-xs"></i>
                                <span>Mặt bằng</span>
                            </button>
                            <button type="button" @click="property_type = 'office'" :class="property_type === 'office' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-briefcase text-xs"></i>
                                <span>Văn phòng</span>
                            </button>
                            <button type="button" @click="property_type = 'warehouse'" :class="property_type === 'warehouse' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="flex items-center space-x-2 px-2.5 py-2 border rounded-xl text-[10px] font-bold transition cursor-pointer">
                                <i class="fa-solid fa-warehouse text-xs"></i>
                                <span>Kho xưởng</span>
                            </button>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="space-y-2 border-t border-slate-100 pt-3" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center justify-between w-full py-1 focus:outline-none cursor-pointer">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Khu vực</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[8px] transition duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="space-y-3 pt-1">
                            <div class="space-y-1">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Tỉnh / Thành phố</label>
                                <div class="relative">
                                    <select 
                                        x-model="selectedProvince"
                                        @change="updateDistricts()"
                                        class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-xs font-semibold outline-none transition cursor-pointer appearance-none h-[36px]"
                                    >
                                        <option value="">Chọn Tỉnh/Thành phố</option>
                                        <template x-for="p in provinces" :key="p.Id">
                                            <option :value="p.Name" x-text="p.Name" :selected="p.Name === selectedProvince"></option>
                                        </template>
                                    </select>
                                    <i class="fa-solid fa-map-location-dot absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Quận / Huyện</label>
                                <div class="relative">
                                    <select 
                                        x-model="selectedDistrict"
                                        @change="updateWards()"
                                        class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-xs font-semibold outline-none transition cursor-pointer appearance-none h-[36px]"
                                        :disabled="!selectedProvince"
                                    >
                                        <option value="">Chọn Quận/Huyện</option>
                                        <template x-for="d in districts" :key="d.Id">
                                            <option :value="d.Name" x-text="d.Name" :selected="d.Name === selectedDistrict"></option>
                                        </template>
                                    </select>
                                    <i class="fa-solid fa-location-crosshairs absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phường / Xã</label>
                                <div class="relative">
                                    <select 
                                        x-model="selectedWard"
                                        class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-lg text-xs font-semibold outline-none transition cursor-pointer appearance-none h-[36px]"
                                        :disabled="!selectedDistrict"
                                    >
                                        <option value="">Chọn Phường/Xã</option>
                                        <template x-for="w in wards" :key="w.Id">
                                            <option :value="w.Name" x-text="w.Name" :selected="w.Name === selectedWard"></option>
                                        </template>
                                    </select>
                                    <i class="fa-solid fa-location-dot absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                    <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[8px] pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="space-y-2 border-t border-slate-100 pt-3" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center justify-between w-full py-1 focus:outline-none cursor-pointer">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Mức giá</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[8px] transition duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="pt-1">
                            <!-- Rent Options -->
                            <div x-show="purpose === 'rent' || purpose === ''" class="grid grid-cols-2 gap-2">
                                <button type="button" @click="price = 'under_3'" :class="price === 'under_3' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    Dưới 3 triệu
                                </button>
                                <button type="button" @click="price = '3_5'" :class="price === '3_5' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    3 - 5 triệu
                                </button>
                                <button type="button" @click="price = '5_10'" :class="price === '5_10' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    5 - 10 triệu
                                </button>
                                <button type="button" @click="price = '10_20'" :class="price === '10_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    10 - 20 triệu
                                </button>
                                <button type="button" @click="price = 'above_20'" :class="price === 'above_20' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">
                                    Trên 20 triệu
                                </button>
                            </div>
                            <!-- Sale Options -->
                            <div x-show="purpose === 'sale'" class="grid grid-cols-2 gap-2">
                                <button type="button" @click="price = 'under_1b'" :class="price === 'under_1b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    Dưới 1 tỷ
                                </button>
                                <button type="button" @click="price = '1b_3b'" :class="price === '1b_3b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    1 - 3 tỷ
                                </button>
                                <button type="button" @click="price = '3b_5b'" :class="price === '3b_5b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    3 - 5 tỷ
                                </button>
                                <button type="button" @click="price = '5b_10b'" :class="price === '5b_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                    5 - 10 tỷ
                                </button>
                                <button type="button" @click="price = 'above_10b'" :class="price === 'above_10b' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">
                                    Trên 10 tỷ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Area -->
                    <div class="space-y-2 border-t border-slate-100 pt-3" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center justify-between w-full py-1 focus:outline-none cursor-pointer">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Diện tích</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[8px] transition duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="grid grid-cols-2 gap-2 pt-1">
                            <button type="button" @click="area = 'under_30'" :class="area === 'under_30' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                Dưới 30 m²
                            </button>
                            <button type="button" @click="area = '30_50'" :class="area === '30_50' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                30 - 50 m²
                            </button>
                            <button type="button" @click="area = '50_80'" :class="area === '50_80' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                50 - 80 m²
                            </button>
                            <button type="button" @click="area = '80_120'" :class="area === '80_120' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer">
                                80 - 120 m²
                            </button>
                            <button type="button" @click="area = 'above_120'" :class="area === 'above_120' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-[10px] font-bold transition cursor-pointer col-span-2">
                                Trên 120 m²
                            </button>
                        </div>
                    </div>

                    <!-- More Filters -->
                    <div class="space-y-2 border-t border-slate-100 pt-3" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center justify-between w-full py-1 focus:outline-none cursor-pointer">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Lọc thêm</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[8px] transition duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="space-y-4 pt-1">
                            <!-- Bedrooms -->
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phòng ngủ</label>
                                <div class="bg-slate-100 p-1 rounded-xl flex space-x-1 border border-slate-200">
                                    <button type="button" @click="bedrooms = '1'" :class="bedrooms === '1' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">1+</button>
                                    <button type="button" @click="bedrooms = '2'" :class="bedrooms === '2' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">2+</button>
                                    <button type="button" @click="bedrooms = '3'" :class="bedrooms === '3' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">3+</button>
                                    <button type="button" @click="bedrooms = '4'" :class="bedrooms === '4' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">4+</button>
                                </div>
                            </div>

                            <!-- Bathrooms -->
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phòng vệ sinh</label>
                                <div class="bg-slate-100 p-1 rounded-xl flex space-x-1 border border-slate-200">
                                    <button type="button" @click="bathrooms = '1'" :class="bathrooms === '1' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">1+</button>
                                    <button type="button" @click="bathrooms = '2'" :class="bathrooms === '2' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">2+</button>
                                    <button type="button" @click="bathrooms = '3'" :class="bathrooms === '3' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="flex-1 py-1 rounded-lg text-xs font-bold text-center transition cursor-pointer">3+</button>
                                </div>
                            </div>

                            <!-- Furniture -->
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Nội thất</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="furniture = 'full'" :class="furniture === 'full' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">Đầy đủ nội thất</button>
                                    <button type="button" @click="furniture = 'basic'" :class="furniture === 'basic' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-2 py-1.5 border rounded-xl text-center text-xs font-bold transition cursor-pointer">Nội thất cơ bản</button>
                                </div>
                            </div>

                            <!-- Direction -->
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Hướng</label>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <button type="button" @click="direction = 'east'" :class="direction === 'east' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Đông</button>
                                    <button type="button" @click="direction = 'west'" :class="direction === 'west' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Tây</button>
                                    <button type="button" @click="direction = 'south'" :class="direction === 'south' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Nam</button>
                                    <button type="button" @click="direction = 'north'" :class="direction === 'north' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Bắc</button>
                                    <button type="button" @click="direction = 'southeast'" :class="direction === 'southeast' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Đông Nam</button>
                                    <button type="button" @click="direction = 'southwest'" :class="direction === 'southwest' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Tây Nam</button>
                                    <button type="button" @click="direction = 'northeast'" :class="direction === 'northeast' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Đông Bắc</button>
                                    <button type="button" @click="direction = 'northwest'" :class="direction === 'northwest' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50 text-slate-700'" class="px-1 py-1 border rounded-lg text-center text-[9px] font-bold transition cursor-pointer">Tây Bắc</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Actions at bottom of mobile menu -->
                <div class="pt-4 border-t border-slate-100 flex flex-col gap-2 mb-2">
                    <button 
                        @click="mobileFiltersOpen = false"
                        type="submit" 
                        form="mobile-filter-form"
                        class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 px-4 rounded-xl text-xs shadow-md transition cursor-pointer"
                    >
                        Áp dụng bộ lọc
                    </button>
                    <button 
                        type="button"
                        @click="resetFilters(); mobileFiltersOpen = false"
                        class="w-full py-3.5 border border-slate-250 bg-slate-50 hover:bg-slate-100 text-slate-500 font-bold rounded-xl text-xs text-center transition"
                    >
                        Đặt lại
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
