@extends('layouts.app')

@section('title', $property['title'] . ' | BDS Rental')
@section('meta_description', Str::limit(strip_tags(str_replace('\n', ' ', $property['description'])), 150))

@section('content')
<!-- Breadcrumbs Section -->
<div class="bg-slate-100/50 pt-24 pb-4 border-b border-slate-200/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-[11px] font-bold text-slate-500 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="/listings" class="hover:text-primary transition">Nhà đất</a>
            <span>/</span>
            <a href="/listings{{ !empty($property['property_type']) ? '?property_type=' . $property['property_type'] : '' }}" class="hover:text-primary transition">{{ $property['type'] }}</a>
            <span>/</span>
            <span class="text-slate-800 truncate max-w-[200px] sm:max-w-none">{{ $property['title'] }}</span>
        </nav>
    </div>
</div>

<!-- Main Body Details -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: Gallery & Main Info (8/12 cols) -->
        <div class="lg:col-span-8 space-y-10">
            
            <!-- 1. Interactive Image Gallery & Title Info Card -->
            <div 
                x-data="{ activeImage: '{{ asset($property['images'][0]) }}' }" 
                class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm p-4 sm:p-6 space-y-6"
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
                <div class="grid grid-cols-4 gap-3.5">
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

                <!-- Title, Address, Price, Area Info Block -->
                <div class="border-t border-slate-100/80 pt-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex-grow space-y-3">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                @php
                                    $isSale = $property['price_label'] && stripos($property['price_label'], 'tháng') === false;
                                @endphp
                                @if($isSale)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-orange-500 text-white mr-2 align-middle"><i class="fa-solid fa-tags mr-1"></i> BÁN</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-blue-500 text-white mr-2 align-middle"><i class="fa-solid fa-key mr-1"></i> THUÊ</span>
                                @endif
                                {{ $property['title'] }}
                            </h1>
                            <div class="flex items-center text-slate-500 text-xs font-semibold">
                                <i class="fa-solid fa-location-dot text-slate-400 mr-2 text-sm flex-shrink-0"></i>
                                <span>{{ $property['location'] }}</span>
                            </div>
                        </div>
                        
                        <!-- Price and Area -->
                        <div class="flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-start gap-2 flex-shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <div class="text-xl sm:text-2xl font-black text-primary">{{ $property['price'] }}</div>
                            <div class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $property['area'] }} m²</div>
                        </div>
                    </div>
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

                    <!-- Legal / Deposit -->
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-file-contract text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Pháp lý</span>
                            <span class="text-sm font-extrabold text-slate-800 truncate block max-w-[150px]" title="{{ $property['legal'] }}">{{ $property['legal'] }}</span>
                        </div>
                    </div>

                    <!-- Additional Details for Sale -->
                    @if(isset($property['floors']) && $property['floors'] > 0)
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-layer-group text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Số tầng</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $property['floors'] }} tầng</span>
                        </div>
                    </div>
                    @endif

                    @if(isset($property['frontage']) && $property['frontage'] > 0)
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-arrows-left-right text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Mặt tiền</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ floatval($property['frontage']) }} m</span>
                        </div>
                    </div>
                    @endif

                    @if(isset($property['road_width']) && $property['road_width'] > 0)
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-road text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Đường rộng</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ floatval($property['road_width']) }} m</span>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Details for Rent -->
                    @if(isset($property['deposit']) && $property['deposit'] > 0)
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-hand-holding-dollar text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Tiền đặt cọc</span>
                            <span class="text-sm font-extrabold text-slate-800">
                                @if($property['deposit'] >= 1000000000)
                                    {{ round($property['deposit'] / 1000000000, 1) }} tỷ
                                @elseif($property['deposit'] >= 1000000)
                                    {{ round($property['deposit'] / 1000000, 1) }} triệu
                                @else
                                    {{ number_format($property['deposit']) }} đ
                                @endif
                            </span>
                        </div>
                    </div>
                    @endif

                    @if(isset($property['lease_term']) && $property['lease_term'])
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                            <i class="fa-solid fa-clock text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block mb-0.5">Thời hạn HĐ</span>
                            <span class="text-sm font-extrabold text-slate-800">{{ $property['lease_term'] }}</span>
                        </div>
                    </div>
                    @endif
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



            <!-- 5. Real Interactive Map Section -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm text-left">
                <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-primary"></i>
                    <span>Bản đồ vị trí</span>
                </h3>
                <div class="relative rounded-2xl overflow-hidden border border-slate-150 shadow-sm">
                    <div id="property-detail-map" class="h-[250px] sm:h-[320px] bg-slate-100"></div>
                    <!-- View on Large Map Floating Button -->
                    <a 
                        href="/map?lat={{ $property['lat'] }}&lng={{ $property['lng'] }}&id={{ $property['id'] }}" 
                        class="absolute bottom-4 left-4 z-10 bg-white/95 backdrop-blur-xs px-4 py-2.5 rounded-xl text-[11px] font-extrabold text-slate-700 hover:text-white bg-white hover:bg-primary border border-slate-200/60 shadow-lg transition duration-200 flex items-center gap-2"
                    >
                        <i class="fa-solid fa-expand text-xs"></i>
                        <span>Xem bản đồ lớn</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Sticky Agent Sidebar & Booking (4/12 cols) -->
        <div id="booking-section" class="lg:col-span-4 lg:sticky lg:top-24 space-y-6 z-20">
            
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
                <div 
                    x-data="{ 
                        liked: {{ app(App\Services\WishlistService::class)->isFavorite(Auth::id(), $property['id']) ? 'true' : 'false' }},
                        isProcessing: false,
                        toggleLike() {
                            if (this.isProcessing) return;
                            this.isProcessing = true;

                            fetch('{{ route('wishlist.toggle') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    property_id: '{{ $property['id'] }}'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.isProcessing = false;
                                if (data.success) {
                                    this.liked = data.is_favorite;
                                }
                            })
                            .catch(error => {
                                this.isProcessing = false;
                                console.error('Error:', error);
                            });
                        }
                    }"
                    class="grid grid-cols-12 gap-3 mb-6"
                >
                    <!-- Call Button -->
                    <a 
                        href="tel:{{ $property['agent']['phone'] }}" 
                        class="col-span-4 inline-flex items-center justify-center px-2 py-3 rounded-2xl text-white bg-green-500 hover:bg-green-600 shadow-md shadow-green-500/25 hover:shadow-green-600/35 transition font-bold text-xs cursor-pointer truncate"
                    >
                        <i class="fa-solid fa-phone mr-1"></i> Gọi ngay
                    </a>
                    
                    <!-- Zalo Button -->
                    <a 
                        href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', !empty($property['agent']['zalo']) ? $property['agent']['zalo'] : $property['agent']['phone']) }}" 
                        target="_blank"
                        class="col-span-4 inline-flex items-center justify-center px-2 py-3 rounded-2xl text-white bg-[#0068ff] hover:bg-[#0055d0] shadow-md shadow-[#0068ff]/25 hover:shadow-[#0055d0]/35 transition font-bold text-xs cursor-pointer truncate"
                    >
                        Chat Zalo
                    </a>

                    <!-- Wishlist Save Button -->
                    <button 
                        @click="toggleLike()"
                        type="button"
                        :class="liked ? 'bg-red-50 text-red-500 border-red-100' : 'bg-slate-50 hover:bg-slate-100 text-slate-500 border-slate-200'"
                        class="col-span-2 rounded-2xl flex items-center justify-center border transition cursor-pointer active:scale-95 h-[48px]"
                        title="Lưu yêu thích"
                    >
                        <i class="fa-solid fa-heart text-base transition" :class="liked ? 'text-red-500' : 'text-slate-400'"></i>
                    </button>

                    <!-- Share Button -->
                    <button 
                        @click="$dispatch('open-share-modal', { url: '{{ request()->fullUrl() }}', title: '{{ addslashes($property['title']) }}' })"
                        type="button"
                        class="col-span-2 rounded-2xl flex items-center justify-center border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-500 transition cursor-pointer active:scale-95 h-[48px]"
                        title="Chia sẻ tin đăng"
                    >
                        <i class="fa-solid fa-share-nodes text-base text-slate-400"></i>
                    </button>
                </div>

                <!-- Booking Appointment Form ("Đặt lịch xem nhà") -->
                <div 
                    x-data="{ 
                        name: '{{ Auth::check() ? Auth::user()->name : '' }}', 
                        phone: '{{ Auth::check() ? Auth::user()->phone : '' }}', 
                        email: '{{ Auth::check() ? Auth::user()->email : '' }}', 
                        date: '', 
                        time: '', 
                        message: '',
                        submitted: false,
                        errorMessage: '',
                        isProcessing: false,
                        submitForm() {
                            @guest
                                window.location.href = '{{ route('login') }}';
                                return;
                            @endguest

                            @if(Auth::check() && Auth::id() === $property['owner_id'])
                                alert('Bạn không thể tự đặt lịch xem nhà trên tin đăng của chính mình.');
                                return;
                            @endif

                            if (!this.date) {
                                this.errorMessage = 'Vui lòng chọn ngày hẹn.';
                                return;
                            }
                            if (!this.time) {
                                this.errorMessage = 'Vui lòng chọn giờ hẹn.';
                                return;
                            }

                            if (this.isProcessing) return;
                            this.isProcessing = true;
                            this.errorMessage = '';

                            fetch('{{ route('appointments.book') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    property_id: '{{ $property['id'] }}',
                                    name: this.name,
                                    phone: this.phone,
                                    email: this.email,
                                    date: this.date,
                                    time: this.time,
                                    message: this.message
                                })
                            })
                            .then(response => response.json().then(data => ({ status: response.status, body: data })))
                            .then(res => {
                                this.isProcessing = false;
                                if (res.status === 200 || res.status === 201 || res.body.success) {
                                    this.submitted = true;
                                } else {
                                    this.errorMessage = res.body.message || 'Có lỗi xảy ra, vui lòng thử lại.';
                                }
                            })
                            .catch(error => {
                                this.isProcessing = false;
                                this.errorMessage = 'Lỗi kết nối mạng, vui lòng thử lại.';
                                console.error('Error:', error);
                            });
                        }
                    }"
                    class="border-t border-slate-100 pt-5"
                >
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-primary"></i>
                            <span>Đặt lịch xem nhà</span>
                        </h5>
                        <button 
                            type="button"
                            @click="date = ''; time = ''; message = ''; errorMessage = ''; name = '{{ Auth::check() ? Auth::user()->name : '' }}'; phone = '{{ Auth::check() ? Auth::user()->phone : '' }}'; email = '{{ Auth::check() ? Auth::user()->email : '' }}';"
                            class="text-[10px] font-bold text-slate-400 hover:text-primary transition cursor-pointer flex items-center gap-1"
                        >
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                            <span>Đặt lại</span>
                        </button>
                    </div>

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
                        <div class="flex items-center justify-center gap-2 mt-4">
                            @auth
                                <a 
                                    href="{{ route('profile.index', ['tab' => 'appointments']) }}" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg shadow-sm hover:bg-primary-hover transition cursor-pointer"
                                >
                                    <i class="fa-regular fa-calendar-check mr-1"></i> Xem lịch hẹn
                                </a>
                            @else
                                <a 
                                    href="{{ route('login') }}" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg shadow-sm hover:bg-primary-hover transition cursor-pointer"
                                >
                                    Đăng nhập xem lịch hẹn
                                </a>
                            @endauth
                            <button 
                                @click="submitted = false" 
                                class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 text-[10px] font-bold rounded-lg text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                            >
                                Đặt lịch hẹn khác
                            </button>
                        </div>
                    </div>

                    <!-- Form State -->
                    <form x-show="!submitted" @submit.prevent="submitForm()" class="space-y-3.5">
                        <!-- Error Message if any -->
                        <div x-show="errorMessage" class="p-3 bg-red-50 text-red-500 rounded-xl text-[11px] font-bold" x-cloak>
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            <span x-text="errorMessage"></span>
                        </div>

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

                        <!-- Input Email -->
                        <div>
                            <input 
                                type="email" 
                                x-model="email"
                                placeholder="Địa chỉ email liên hệ..." 
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
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-xs font-medium outline-none transition cursor-pointer"
                                >
                            </div>
                            
                            <div class="relative text-left" x-data="{ open: false, dropdownPlacement: 'bottom' }" @click.outside="open = false">
                                <button 
                                    type="button"
                                    @click="open = !open; if(open) { dropdownPlacement = ($event.currentTarget.getBoundingClientRect().bottom + 210 > window.innerHeight) ? 'top' : 'bottom' }"
                                    :class="open ? 'border-primary bg-white ring-2 ring-primary/10' : 'border-slate-200 bg-slate-50 hover:bg-slate-100/70'"
                                    class="w-full border rounded-xl px-3 py-2.5 text-xs font-semibold outline-none transition cursor-pointer text-left flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-[11px]" :class="time ? 'text-slate-600' : 'text-slate-400'"></i>
                                        <span x-text="time ? time : 'Chọn giờ'" :class="time ? 'text-slate-800' : 'text-slate-400'"></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition duration-200" :class="open ? 'text-primary rotate-180' : ''"></i>
                                </button>
                                
                                <!-- Dropdown Panel -->
                                <div 
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    :class="dropdownPlacement === 'top' ? 'bottom-full mb-1.5' : 'top-full mt-1.5'"
                                    class="absolute left-0 right-0 bg-white border border-slate-150 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.08)] z-50 p-1 custom-scrollbar"
                                    style="max-height: 180px; overflow-y: auto;"
                                    x-cloak
                                >
                                    <div class="space-y-0.5">
                                        <template x-for="t in ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30']" :key="t">
                                            <button 
                                                type="button"
                                                @click="time = t; open = false"
                                                :class="time === t ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 font-semibold'"
                                                class="w-full text-left px-3 py-2 text-[11px] rounded-lg transition cursor-pointer flex items-center justify-between"
                                            >
                                                <span x-text="t"></span>
                                                <i x-show="time === t" class="fa-solid fa-check text-primary text-[10px]" x-cloak></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note / Message -->
                        <div>
                            <textarea 
                                x-model="message"
                                placeholder="Ghi chú thêm cho chủ nhà (nếu có)..." 
                                rows="2"
                                maxlength="1000"
                                class="w-full bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl px-4 py-2 text-xs font-medium outline-none transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Form Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="isProcessing"
                            class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-3.5 px-4 rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 disabled:opacity-55"
                        >
                            <span x-show="!isProcessing">Gửi yêu cầu đặt lịch</span>
                            <span x-show="isProcessing"><i class="fa-solid fa-spinner animate-spin mr-1"></i> Đang xử lý...</span>
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
        href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', !empty($property['agent']['zalo']) ? $property['agent']['zalo'] : $property['agent']['phone']) }}" 
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

@push('scripts')
<!-- MapLibre GL JS CSS & SDK -->
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.css">
<script src="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.js"></script>
<style>
    /* Custom MapLibre Popups Style for Detail Map */
    #property-detail-map .maplibregl-popup-content {
        padding: 8px !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }
    #property-detail-map .maplibregl-popup-close-button {
        display: none !important;
    }
    #property-detail-map .maplibregl-popup-tip {
        border-top-color: #ffffff !important;
        border-bottom-color: #ffffff !important;
    }
    
    /* Custom scrollbar for time dropdown */
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        display: block !important;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9; /* slate-100 */
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1; /* slate-300 */
        border-radius: 9999px;
        border: 2px solid #f1f5f9;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8; /* slate-400 */
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('property-detail-map')) return;
        
        const lat = {{ $property['lat'] }};
        const lng = {{ $property['lng'] }};
        
        const map = new maplibregl.Map({
            container: 'property-detail-map',
            style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            center: [lng, lat],
            zoom: 14.5,
            scrollZoom: false
        });

        // Add controls
        map.addControl(new maplibregl.NavigationControl(), 'top-right');

        // Custom HTML element for Marker (Price Bubble)
        const el = document.createElement('div');
        el.className = 'w-9 h-9 rounded-full bg-primary border-4 border-white shadow-xl flex items-center justify-center cursor-pointer hover:scale-110 transition duration-200';
        el.innerHTML = '<i class="fa-solid fa-house-chimney text-xs text-white"></i>';

        const popup = new maplibregl.Popup({ 
            offset: 15,
            closeOnClick: false
        }).setHTML('<a href="/map?lat={{ $property['lat'] }}&lng={{ $property['lng'] }}&id={{ $property['id'] }}" class="text-[11px] font-extrabold text-slate-800 p-1 leading-snug hover:text-primary transition block text-left">{{ $property['title'] }}</a>');

        // Add Marker
        const marker = new maplibregl.Marker({ element: el })
            .setLngLat([lng, lat])
            .setPopup(popup)
            .addTo(map);

        // Auto open popup
        marker.togglePopup();
    });
</script>
@endpush
