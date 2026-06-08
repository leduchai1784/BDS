@extends('layouts.app')

@section('title', $property['title'] . ' | BDS Rental')
@section('meta_description', Str::limit(strip_tags(str_replace('\n', ' ', $property['description'])), 150))

@section('content')
<!-- Breadcrumbs & Title Section -->
<div class="bg-slate-100/50 pt-28 pb-8 border-b border-slate-200/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-4 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="#listings" class="hover:text-primary transition">{{ $property['type'] }}</a>
            <span>/</span>
            <span class="text-slate-800 truncate max-w-[200px] sm:max-w-none">{{ $property['title'] }}</span>
        </nav>

        <!-- Title and Address -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="max-w-4xl text-left">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-snug mb-3">
                    {{ $property['title'] }}
                </h1>
                <div class="flex items-center text-slate-500 text-sm font-medium">
                    <i class="fa-solid fa-location-dot text-slate-400 mr-2.5 text-base"></i>
                    <span>{{ $property['location'] }}</span>
                </div>
            </div>
            
            <!-- Price and Area (Desktop Right side) -->
            <div class="flex-shrink-0 text-left md:text-right flex md:flex-col items-baseline md:items-end gap-3 md:gap-1 mt-2 md:mt-0">
                <div class="text-2xl sm:text-3xl font-black text-primary">{{ $property['price'] }}</div>
                <div class="text-sm font-bold text-slate-500 bg-slate-200 px-3 py-1 rounded-xl">{{ $property['area'] }} m²</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Body Details -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: Gallery & Main Info (8/12 cols) -->
        <div class="lg:col-span-8 space-y-10">
            
            <!-- 1. Interactive Image Gallery -->
            <div 
                x-data="{ activeImage: '{{ asset($property['images'][0]) }}' }" 
                class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm p-4"
            >
                <!-- Large Primary Image View -->
                <div class="relative h-[280px] sm:h-[450px] w-full rounded-2xl overflow-hidden bg-slate-100">
                    <img 
                        :src="activeImage" 
                        alt="Property view" 
                        class="w-full h-full object-cover object-center transition-all duration-300"
                    >
                    
                    <!-- Badges Overlay -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                        @if($property['is_vip'] ?? false)
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black tracking-wider uppercase bg-red-500 text-white shadow-lg shadow-red-500/30">
                                <i class="fa-solid fa-crown mr-1.5"></i> VIP NỔI BẬT
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Gallery Thumbnails -->
                <div class="grid grid-cols-4 gap-3.5 mt-4">
                    @foreach($property['images'] as $img)
                        <button 
                            @click="activeImage = '{{ asset($img) }}'"
                            :class="activeImage === '{{ asset($img) }}' ? 'border-primary ring-2 ring-primary/20' : 'border-slate-100 hover:border-slate-350'"
                            class="relative h-16 sm:h-24 rounded-xl overflow-hidden border-2 bg-slate-50 transition duration-150 cursor-pointer"
                        >
                            <img src="{{ asset($img) }}" alt="Thumbnail" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- 2. Specifications Details Grid -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm text-left">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    <span>Thông số kỹ thuật</span>
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <!-- Area -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-ruler-combined text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Diện tích</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $property['area'] }} m²</span>
                        </div>
                    </div>

                    <!-- Bedrooms -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-bed text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Phòng ngủ</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $property['bedrooms'] > 0 ? $property['bedrooms'] . ' PN' : 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Bathrooms -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-bath text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Phòng tắm</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $property['bathrooms'] > 0 ? $property['bathrooms'] . ' WC' : 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Direction -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-compass text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Hướng</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $property['direction'] }}</span>
                        </div>
                    </div>

                    <!-- Furniture -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-chair text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Nội thất</span>
                            <span class="text-sm font-extrabold text-slate-800 truncate block max-w-[150px]" title="{{ $property['furniture'] }}">{{ $property['furniture'] }}</span>
                        </div>
                    </div>

                    <!-- Legal -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-file-contract text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Pháp lý</span>
                            <span class="text-sm font-extrabold text-slate-800 truncate block max-w-[150px]" title="{{ $property['legal'] }}">{{ $property['legal'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Description -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm text-left">
                <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-align-left text-primary"></i>
                    <span>Mô tả chi tiết</span>
                </h3>
                <div class="text-slate-600 text-sm leading-relaxed space-y-4 font-medium">
                    {!! nl2br(e(str_replace('\n', "\n", $property['description']))) !!}
                </div>
            </div>

            <!-- 4. Amenities list -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm text-left">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-star text-primary"></i>
                    <span>Tiện ích xung quanh</span>
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-shield text-green-500 text-base"></i>
                        <span>Bảo vệ 24/7</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-square-p text-primary text-base"></i>
                        <span>Bãi đỗ xe rộng</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-wifi text-cyan-500 text-base"></i>
                        <span>Internet tốc độ cao</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-store text-orange-500 text-base"></i>
                        <span>Cửa hàng tiện lợi</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-dumbbell text-purple-500 text-base"></i>
                        <span>Phòng Gym hiện đại</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-swimming-pool text-blue-500 text-base"></i>
                        <span>Hồ bơi nội khu</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-school text-emerald-500 text-base"></i>
                        <span>Trường học gần kề</span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-700 font-bold text-xs bg-slate-50 p-3 rounded-2xl">
                        <i class="fa-solid fa-tree text-green-600 text-base"></i>
                        <span>Công viên cây xanh</span>
                    </div>
                </div>
            </div>

            <!-- 5. Mock Map Section -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm text-left">
                <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-primary"></i>
                    <span>Bản đồ vị trí</span>
                </h3>
                <div class="relative h-[250px] sm:h-[320px] rounded-2xl overflow-hidden border border-slate-100 bg-slate-200">
                    <img 
                        src="{{ asset('images/hero_bg.png') }}" 
                        alt="Location map" 
                        class="w-full h-full object-cover opacity-60 filter blur-xs"
                    >
                    <div class="absolute inset-0 bg-slate-900/10 flex items-center justify-center">
                        <div class="flex flex-col items-center bg-white/95 backdrop-blur-sm p-5 rounded-2xl shadow-xl border border-slate-100 max-w-sm text-center">
                            <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-xl shadow-md mb-3">
                                <i class="fa-solid fa-location-dot animate-bounce"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 mb-1">Khu vực {{ $property['location'] }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Bản đồ đang được chuẩn bị. Hãy liên hệ với môi giới để được dẫn đi xem nhà trực tiếp.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Sticky Agent Sidebar & Booking (4/12 cols) -->
        <div id="booking-section" class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
            
            <!-- Agent Profile Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-md text-left">
                <!-- Profile Header -->
                <div class="flex items-center space-x-4 pb-4 border-b border-slate-100 mb-5">
                    <img 
                        src="{{ $property['agent']['avatar'] }}" 
                        alt="{{ $property['agent']['name'] }}" 
                        class="w-14 h-14 rounded-full object-cover border border-slate-150 shadow-sm"
                    >
                    <div>
                        <h4 class="text-base font-bold text-slate-800 leading-none mb-1.5">{{ $property['agent']['name'] }}</h4>
                        <span class="text-xs font-semibold text-slate-400 block mb-1">Môi giới chuyên nghiệp</span>
                        <div class="flex items-center text-amber-500 text-xs">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span class="text-slate-500 font-bold ml-1.5">5.0</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Phone & Zalo & Save -->
                <div class="grid grid-cols-12 gap-3 mb-6" x-data="{ liked: false }">
                    <!-- Call Button -->
                    <a 
                        href="tel:{{ $property['agent']['phone'] }}" 
                        class="col-span-5 inline-flex items-center justify-center px-4 py-3 rounded-2xl text-white bg-green-500 hover:bg-green-600 shadow-md shadow-green-500/25 hover:shadow-green-600/35 transition font-bold text-sm cursor-pointer"
                    >
                        <i class="fa-solid fa-phone mr-2"></i> Gọi ngay
                    </a>
                    
                    <!-- Zalo Button -->
                    <a 
                        href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $property['agent']['phone']) }}" 
                        target="_blank"
                        class="col-span-5 inline-flex items-center justify-center px-4 py-3 rounded-2xl text-white bg-[#0068ff] hover:bg-[#0055d0] shadow-md shadow-[#0068ff]/25 hover:shadow-[#0055d0]/35 transition font-bold text-sm cursor-pointer"
                    >
                        Chat Zalo
                    </a>

                    <!-- Wishlist Save Button -->
                    <button 
                        @click="liked = !liked"
                        type="button"
                        :class="liked ? 'bg-red-50 text-red-500 border-red-100' : 'bg-slate-50 hover:bg-slate-100 text-slate-500 border-slate-200'"
                        class="col-span-2 rounded-2xl flex items-center justify-center border transition cursor-pointer active:scale-95 h-[48px]"
                        title="Lưu yêu thích"
                    >
                        <i class="fa-solid fa-heart text-base transition" :class="liked ? 'text-red-500Scale' : 'text-slate-400'"></i>
                    </button>
                </div>

                <!-- Booking Appointment Form ("Đặt lịch xem nhà") -->
                <div 
                    x-data="{ 
                        name: '', 
                        phone: '', 
                        date: '', 
                        time: '', 
                        submitted: false 
                    }"
                    class="border-t border-slate-100 pt-5"
                >
                    <h5 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-primary"></i>
                        <span>Đặt lịch xem nhà</span>
                    </h5>

                    <!-- Success State -->
                    <div 
                        x-show="submitted" 
                        x-transition:enter="transition duration-350"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="bg-green-50 border border-green-150 rounded-2xl p-4 text-center" 
                        x-cloak
                    >
                        <i class="fa-solid fa-circle-check text-green-500 text-2xl mb-2"></i>
                        <h6 class="text-xs font-bold text-green-800 mb-1">Gửi lịch hẹn thành công!</h6>
                        <p class="text-[10px] text-green-600 leading-normal font-medium">Môi giới <span class="font-bold">{{ $property['agent']['name'] }}</span> sẽ liên hệ lại qua SĐT của bạn trong ít phút để xác nhận.</p>
                        <button @click="submitted = false" class="text-[10px] font-bold text-primary hover:underline mt-3">Đặt lịch hẹn khác</button>
                    </div>

                    <!-- Form State -->
                    <form x-show="!submitted" @submit.prevent="submitted = true" class="space-y-3.5">
                        <!-- Input Name -->
                        <div>
                            <input 
                                type="text" 
                                x-model="name"
                                placeholder="Họ và tên của bạn..." 
                                required
                                class="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition"
                            >
                        </div>
                        
                        <!-- Input Phone -->
                        <div>
                            <input 
                                type="tel" 
                                x-model="phone"
                                placeholder="Số điện thoại liên hệ..." 
                                required
                                class="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition"
                            >
                        </div>

                        <!-- Date and Time Picker -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input 
                                    type="date" 
                                    x-model="date"
                                    required
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-xs font-medium outline-none transition cursor-pointer"
                                >
                            </div>
                            <div>
                                <select 
                                    x-model="time"
                                    required
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-xs font-medium outline-none appearance-none transition cursor-pointer"
                                >
                                    <option value="">Chọn giờ</option>
                                    <option value="08:00">08:00 - 10:00</option>
                                    <option value="10:00">10:00 - 12:00</option>
                                    <option value="14:00">14:00 - 16:00</option>
                                    <option value="16:00">16:00 - 18:00</option>
                                    <option value="18:00">18:00 - 20:00</option>
                                </select>
                            </div>
                        </div>

                        <!-- Form Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-3.5 px-4 rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98"
                        >
                            Gửi yêu cầu đặt lịch
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- 6. Bottom Column: Similar Listings (Sản phẩm tương tự) -->
    <section class="mt-20 pt-12 border-t border-slate-200/60 text-left">
        <div class="mb-10">
            <span class="text-xs font-bold text-primary tracking-widest uppercase mb-1.5 block">Khám phá thêm</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">Bất Động Sản Tương Tự</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                // Filter similar properties (exclude current property, showing up to 3 listings)
                $similarProperties = collect($properties)
                    ->filter(fn($p) => $p['id'] !== $property['id'])
                    ->take(3);
            @endphp
            @foreach($similarProperties as $simProperty)
                @include('components.property-card', ['property' => $simProperty])
            @endforeach
        </div>
    </section>
</div>

<!-- 7. Responsive Mobile Floating Sticky Bottom Bar -->
<div class="block md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-150 p-4.5 shadow-2xl flex items-center justify-between gap-3">
    <!-- Call Icon Button -->
    <a 
        href="tel:{{ $property['agent']['phone'] }}" 
        class="w-12 h-12 rounded-2xl bg-green-500 hover:bg-green-600 text-white flex items-center justify-center text-lg shadow-md shadow-green-500/20 active:scale-95 transition"
        title="Gọi điện"
    >
        <i class="fa-solid fa-phone"></i>
    </a>
    
    <!-- Zalo Text Button -->
    <a 
        href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $property['agent']['phone']) }}" 
        target="_blank"
        class="w-12 h-12 rounded-2xl bg-[#0068ff] hover:bg-[#0055d0] text-white flex items-center justify-center text-base font-bold shadow-md shadow-[#0068ff]/20 active:scale-95 transition"
        title="Chat Zalo"
    >
        Zalo
    </a>
    
    <!-- Book Viewing Big Button -->
    <a 
        href="#booking-section" 
        class="flex-grow inline-flex items-center justify-center px-4 h-12 rounded-2xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 transition font-bold text-xs active:scale-98"
    >
        <i class="fa-solid fa-calendar-days mr-2"></i> Đặt lịch xem nhà
    </a>
</div>
@endsection
