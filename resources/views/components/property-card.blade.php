@props(['property'])

@php
    // Fallback default data if no property variable is passed
    $property = $property ?? [
        'id' => 1,
        'title' => 'Căn hộ chung cư Vinhomes Ocean Park 2 phòng ngủ full đồ',
        'type' => 'Căn hộ chung cư',
        'price' => '8.5 triệu/tháng',
        'area' => '65',
        'bedrooms' => 2,
        'bathrooms' => 2,
        'location' => 'Gia Lâm, Hà Nội',
        'image' => 'images/apartment_1.png',
        'is_vip' => true,
        'is_new' => false,
        'agent' => [
            'name' => 'Nguyễn Hải Đăng',
            'phone' => '0987.654.321',
            'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Hai+Dang&background=0077bb&color=fff'
        ],
        'created_at' => '2 giờ trước'
    ];

    $isFavorite = false;
    if (Auth::check()) {
        $isFavorite = app(App\Services\WishlistService::class)->isFavorite(Auth::id(), $property['id']);
    }
@endphp

<div class="group bg-white rounded-[24px] overflow-hidden border border-slate-100 hover:shadow-2xl hover:shadow-slate-200/80 transform hover:-translate-y-1.5 transition-all duration-350 flex flex-col h-full">
    <!-- 1. Hình ảnh (Image) -->
    <div class="relative h-56 w-full overflow-hidden bg-slate-100 flex-shrink-0">
        <!-- Image with hover zoom effect -->
        <a href="/property/{{ $property['id'] }}" class="absolute inset-0 block">
            <img 
                src="{{ asset($property['image']) }}" 
                alt="{{ $property['title'] }}" 
                class="w-full h-full object-cover object-center group-hover:scale-108 transition-transform duration-500 ease-out"
            >
        </a>

        <!-- VIP/NEW/Sale/Rent Badges Overlay (Trang trí cao cấp) -->
        <div class="absolute top-4 left-4 flex flex-col gap-1.5 z-10">
            @php
                $isSale = isset($property['price_label']) && stripos($property['price_label'], 'tháng') === false;
            @endphp
            @if($isSale)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-orange-500 text-white shadow-md shadow-orange-500/20">
                    <i class="fa-solid fa-tags mr-1"></i> BÁN
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-[#0077bb] text-white shadow-md shadow-blue-500/20">
                    <i class="fa-solid fa-key mr-1"></i> THUÊ
                </span>
            @endif
            @if($property['is_vip'] ?? false)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-red-500 text-white shadow-md shadow-red-500/20">
                    <i class="fa-solid fa-crown mr-1"></i> VIP
                </span>
            @endif
            @if($property['is_new'] ?? false)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-green-500 text-white shadow-md shadow-green-500/20">
                    <i class="fa-solid fa-sparkles mr-1"></i> MỚI
                </span>
            @endif
        </div>

        <!-- Buttons Overlay (Wishlist & Share) -->
        <!-- Buttons Overlay (Wishlist & Share) -->
        <div class="absolute top-4 right-4 z-10 flex items-center gap-2" x-data="{ 
            liked: {{ $isFavorite ? 'true' : 'false' }},
            isProcessing: false,
            openShareModal: false,
            shareCopied: false,
            toggleLike() {
                if (this.isProcessing) return;
                this.isProcessing = true;

                fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
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
                        if (!data.is_favorite && window.location.pathname.includes('/profile')) {
                            window.location.reload();
                        }
                    }
                })
                .catch(error => {
                    this.isProcessing = false;
                    console.error('Error:', error);
                });
            }
        }">
            <!-- Share Button -->
            <button 
                @click="openShareModal = true"
                type="button" 
                class="w-9 h-9 rounded-xl flex items-center justify-center border border-slate-100 bg-white/80 hover:bg-white text-slate-600 hover:text-primary shadow-sm transition active:scale-90 cursor-pointer"
                title="Chia sẻ tin đăng"
            >
                <i class="fa-solid fa-share-nodes text-xs"></i>
            </button>

            <!-- Wishlist Button -->
            <button 
                @click="toggleLike()"
                type="button" 
                :class="liked ? 'bg-red-50 text-red-500 border-red-100' : 'bg-white/80 hover:bg-white text-slate-600 border-slate-100'"
                class="w-9 h-9 rounded-xl flex items-center justify-center border shadow-sm transition active:scale-90 cursor-pointer"
            >
                <i class="fa-solid fa-heart transition" :class="liked ? 'text-red-500' : 'text-slate-400'"></i>
            </button>

            <!-- Share Listing Modal -->
            <div 
                x-show="openShareModal" 
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak
            >
                <!-- Modal Content Card -->
                <div 
                    @click.outside="openShareModal = false"
                    class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative space-y-5 text-left border border-slate-100"
                >
                    <!-- Close Button -->
                    <button 
                        type="button" 
                        @click="openShareModal = false" 
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-605 transition cursor-pointer text-sm"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <!-- Title -->
                    <div class="space-y-1 pr-6">
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-share-nodes text-primary"></i> Chia sẻ tin đăng này
                        </h3>
                        <p class="text-xs text-slate-400 font-semibold leading-normal">Chia sẻ bất động sản này với bạn bè và người thân của bạn qua các ứng dụng sau:</p>
                    </div>

                    <!-- Social Links Grid -->
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Facebook Share -->
                        <a 
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('properties.show', $property['id'])) }}" 
                            target="_blank"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-blue-500 bg-slate-50/50 hover:bg-blue-50/10 text-slate-655 hover:text-blue-600 transition cursor-pointer space-y-1.5"
                        >
                            <i class="fa-brands fa-facebook text-2xl text-[#1877f2]"></i>
                            <span class="text-[10px] font-bold">Facebook</span>
                        </a>
                        
                        <!-- Zalo Share -->
                        <a 
                            href="https://sp.zalo.me/share_to_zalo?url={{ urlencode(route('properties.show', $property['id'])) }}" 
                            target="_blank"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-sky-500 bg-slate-50/50 hover:bg-sky-50/10 text-slate-655 hover:text-sky-600 transition cursor-pointer space-y-1.5"
                        >
                            <img src="https://sp.zalo.me/favicon.ico" class="w-6 h-6 object-contain" onerror="this.src='https://res.cloudinary.com/dj8t18pke/image/upload/v1700000000/zalo-icon.png'">
                            <span class="text-[10px] font-bold">Zalo</span>
                        </a>
                        
                        <!-- Telegram Share -->
                        <a 
                            href="https://t.me/share/url?url={{ urlencode(route('properties.show', $property['id'])) }}&text={{ urlencode($property['title']) }}" 
                            target="_blank"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-cyan-500 bg-slate-50/50 hover:bg-cyan-50/10 text-slate-655 hover:text-cyan-600 transition cursor-pointer space-y-1.5"
                        >
                            <i class="fa-brands fa-telegram text-2xl text-[#0088cc]"></i>
                            <span class="text-[10px] font-bold">Telegram</span>
                        </a>
                    </div>

                    <!-- Copy Link Section -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <label class="block text-[9px] font-extrabold uppercase text-slate-400 mb-1 px-1">Sao chép liên kết</label>
                        <div class="relative flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <input 
                                type="text" 
                                readonly 
                                value="{{ route('properties.show', $property['id']) }}" 
                                class="w-full bg-transparent text-xs font-mono font-bold text-slate-600 outline-none pr-10 select-all"
                            >
                            <button 
                                type="button" 
                                @click="navigator.clipboard.writeText('{{ route('properties.show', $property['id']) }}'); shareCopied = true; setTimeout(() => shareCopied = false, 2000)"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition cursor-pointer"
                                title="Sao chép"
                            >
                                <i class="fa-solid text-xs" :class="shareCopied ? 'fa-check text-green-500' : 'fa-copy'"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Type Overlay Tag -->
        <div class="absolute bottom-4 left-4 z-10">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-900/85 backdrop-blur-md text-white">
                {{ $property['type'] }}
            </span>
        </div>
    </div>

    <!-- Nội dung thông tin card -->
    <div class="p-6 flex flex-col flex-grow">
        <!-- 2. Giá thuê (Price) -->
        <div class="flex items-center justify-between mb-2.5">
            <span class="text-xl font-extrabold text-primary tracking-tight">
                {{ $property['price'] }}
            </span>
            <!-- Kích thước/Diện tích nhỏ gọn (Trang trí phụ trợ) -->
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                {{ $property['area'] }} m²
            </span>
        </div>

        <!-- 3. Tiêu đề (Title) -->
        <h3 class="text-base font-bold text-slate-800 line-clamp-2 group-hover:text-primary transition duration-150 mb-3.5 leading-snug flex-grow">
            <a href="/property/{{ $property['id'] }}">{{ $property['title'] }}</a>
        </h3>

        <!-- 4. Địa chỉ (Address) -->
        <div class="flex items-center text-slate-500 text-xs font-semibold mb-6">
            <i class="fa-solid fa-location-dot text-slate-400 mr-2 text-base"></i>
            <span class="truncate">{{ $property['location'] }}</span>
        </div>

        <!-- 5. Thông tin chi tiết thu gọn (Property Specs) -->
        <div class="pt-4 border-t border-slate-100/80 mt-auto flex-shrink-0">
            <div class="flex items-center justify-between text-slate-600 text-xs px-1">
                @if(isset($property['bedrooms']) && $property['bedrooms'] > 0)
                    <div class="flex items-center space-x-1.5" title="{{ $property['bedrooms'] }} phòng ngủ">
                        <i class="fa-solid fa-bed text-[15px] text-slate-400"></i>
                        <span class="font-extrabold text-slate-700">{{ $property['bedrooms'] }}</span>
                    </div>
                @endif
                
                @if(isset($property['bathrooms']) && $property['bathrooms'] > 0)
                    <div class="flex items-center space-x-1.5" title="{{ $property['bathrooms'] }} phòng tắm">
                        <i class="fa-solid fa-bath text-[15px] text-slate-400"></i>
                        <span class="font-extrabold text-slate-700">{{ $property['bathrooms'] }}</span>
                    </div>
                @endif

                @if(isset($property['floors']) && $property['floors'] > 0)
                    <div class="flex items-center space-x-1.5" title="{{ $property['floors'] }} tầng">
                        <i class="fa-solid fa-layer-group text-[15px] text-slate-400"></i>
                        <span class="font-extrabold text-slate-700">{{ $property['floors'] }}</span>
                    </div>
                @endif

                @if(isset($property['area']) && $property['area'] > 0)
                    <div class="flex items-center space-x-1.5" title="Diện tích {{ $property['area'] }} m²">
                        <i class="fa-solid fa-crop-simple text-[15px] text-slate-400"></i>
                        <span class="font-extrabold text-slate-700">{{ $property['area'] }}m²</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
