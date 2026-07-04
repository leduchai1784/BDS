<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Thuê Bất Động Sản Giá Tốt | Kênh Cho Thuê Nhà Đất Số 1 Việt Nam')</title>
    <meta name="description" content="@yield('meta_description', 'Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn, mặt bằng kinh doanh cho thuê uy tín, cập nhật liên tục với bộ lọc giá, diện tích thông minh.')">
    <meta name="keywords" content="cho thuê nhà, thuê căn hộ, thuê phòng trọ, thuê mặt bằng, tìm nhà đất, bất động sản">
    <meta name="robots" content="index, follow">
    <meta name="author" content="BDS Rental">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Thuê Bất Động Sản Giá Tốt | Kênh Cho Thuê Nhà Đất Số 1 Việt Nam')">
    <meta property="og:description" content="@yield('meta_description', 'Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn, mặt bằng kinh doanh cho thuê uy tín.')">
    <meta property="og:image" content="{{ asset('images/hero_bg.png') }}">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite Assets (Tailwind CSS, JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS for Interactive UI Elements (Mobile Menu, Tabs, Dropdowns) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="flex flex-col min-h-full font-sans antialiased text-slate-800 pb-16 md:pb-0">

    <!-- Header / Navbar Component -->
    @if(!isset($hideNavbar) || !$hideNavbar)
        @include('components.navbar')
    @endif

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Component -->
    @if(!isset($hideFooter) || !$hideFooter)
        @include('components.footer')
    @endif

    <!-- AI Chatbot Widget -->
    @include('components.chat-widget')

    <!-- 📱 Mobile Bottom Navigation Bar (Tabbar) like moso.vn -->
    @if(!isset($hideMobileTabbar) || !$hideMobileTabbar)
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-150 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] md:hidden safe-bottom">
        <div class="grid grid-cols-5 h-16">
            <!-- 1. Home -->
            <a 
                href="/" 
                class="flex flex-col items-center justify-center space-y-1 {{ request()->is('/') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }} transition duration-200"
            >
                <i class="fa-solid fa-house-chimney text-base"></i>
                <span class="text-[9px] font-bold">Trang chủ</span>
            </a>

            <!-- 2. Map -->
            <a 
                href="/map" 
                class="flex flex-col items-center justify-center space-y-1 {{ request()->is('map*') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }} transition duration-200"
            >
                <i class="fa-solid fa-map-location-dot text-base"></i>
                <span class="text-[9px] font-bold">Bản đồ</span>
            </a>

            <!-- 3. Add Property (Center Highlight Button) -->
            <a 
                href="{{ route('properties.choose-type') }}" 
                class="flex flex-col items-center justify-center relative"
            >
                <div class="absolute -top-4 w-12 h-12 rounded-full bg-primary hover:bg-primary-hover text-white flex items-center justify-center shadow-lg shadow-primary/30 active:scale-95 transition-all duration-200">
                    <i class="fa-solid fa-plus text-lg"></i>
                </div>
                <span class="text-[9px] font-bold text-slate-400 mt-7">Đăng tin</span>
            </a>

            <!-- 4. News -->
            <a 
                href="/news" 
                class="flex flex-col items-center justify-center space-y-1 {{ request()->is('news*') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }} transition duration-200"
            >
                <i class="fa-solid fa-newspaper text-base"></i>
                <span class="text-[9px] font-bold">Tin tức</span>
            </a>

            <!-- 5. Profile -->
            <a 
                href="/profile" 
                class="flex flex-col items-center justify-center space-y-1 {{ request()->is('profile*') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }} transition duration-200"
            >
                @auth
                    <img 
                        src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0077bb&color=fff' }}" 
                        alt="Profile" 
                        class="w-5.5 h-5.5 rounded-full object-cover border {{ request()->is('profile*') ? 'border-primary' : 'border-slate-300' }}"
                    >
                @else
                    <i class="fa-solid fa-circle-user text-base"></i>
                @endauth
                <span class="text-[9px] font-bold">Cá nhân</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Global Share Listing Modal -->
    <div 
        x-data="{
            open: false,
            url: '',
            title: '',
            copied: false,
            copyLink() {
                navigator.clipboard.writeText(this.url);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        }"
        @open-share-modal.window="open = true; url = $event.detail.url; title = $event.detail.title; copied = false;"
        x-show="open" 
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
            @click.outside="open = false"
            class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative space-y-5 text-left border border-slate-100"
        >
            <!-- Close Button -->
            <button 
                type="button" 
                @click="open = false" 
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition cursor-pointer text-sm"
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
                    :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url)" 
                    target="_blank"
                    class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-blue-500 bg-slate-50/50 hover:bg-blue-50/10 text-slate-655 hover:text-blue-600 transition cursor-pointer space-y-1.5"
                >
                    <i class="fa-brands fa-facebook text-2xl text-[#1877f2]"></i>
                    <span class="text-[10px] font-bold">Facebook</span>
                </a>
                
                <!-- Zalo Share -->
                <a 
                    :href="'https://sp.zalo.me/share_to_zalo?url=' + encodeURIComponent(url)" 
                    target="_blank"
                    class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-100 hover:border-sky-500 bg-slate-50/50 hover:bg-sky-50/10 text-slate-655 hover:text-sky-600 transition cursor-pointer space-y-1.5"
                >
                    <img src="https://sp.zalo.me/favicon.ico" class="w-6 h-6 object-contain" onerror="this.src='https://res.cloudinary.com/dj8t18pke/image/upload/v1700000000/zalo-icon.png'">
                    <span class="text-[10px] font-bold">Zalo</span>
                </a>
                
                <!-- Telegram Share -->
                <a 
                    :href="'https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title)" 
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
                        :value="url" 
                        class="w-full bg-transparent text-xs font-mono font-bold text-slate-600 outline-none pr-10 select-all"
                    >
                    <button 
                        type="button" 
                        @click="copyLink()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition cursor-pointer"
                        title="Sao chép"
                    >
                        <i class="fa-solid text-xs" :class="copied ? 'fa-check text-green-500' : 'fa-copy'"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
