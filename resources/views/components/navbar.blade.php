<header 
    x-data="{ mobileMenuOpen: false, isScrolled: window.pageYOffset > 20 || window.location.pathname !== '/' }" 
    @scroll.window="isScrolled = window.pageYOffset > 20 || window.location.pathname !== '/'"
    :class="isScrolled ? 'bg-white/95 backdrop-blur-md shadow-md border-b border-slate-100 py-3' : 'bg-transparent py-5'"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 w-full"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="w-full flex items-center justify-between gap-4 relative">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center justify-center mx-auto md:order-2">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </div>
                    <span :class="isScrolled ? 'text-slate-900' : 'text-white'" class="font-bold text-2xl tracking-tight transition-colors duration-300">
                        BDS<span class="text-primary">Rental</span>
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden md:flex flex-1 items-center justify-start space-x-3 lg:space-x-5 md:order-1">
                <a href="/" :class="isScrolled ? 'text-primary' : 'text-white'" class="font-bold text-sm lg:text-base hover:text-primary transition duration-150 whitespace-nowrap">Trang chủ</a>
                <div 
                    class="relative" 
                    x-data="{ rentDropdownOpen: false }"
                    @mouseenter="rentDropdownOpen = true"
                    @mouseleave="rentDropdownOpen = false"
                >
                    <button 
                        @click="rentDropdownOpen = !rentDropdownOpen"
                        type="button"
                        class="flex items-center space-x-1.5 font-bold text-sm lg:text-base cursor-pointer focus:outline-none transition duration-150 whitespace-nowrap"
                        :class="isScrolled ? 'text-slate-600 hover:text-primary' : 'text-slate-200 hover:text-white'"
                    >
                        <span>Thuê</span>
                        <i 
                            class="fa-solid fa-chevron-down text-[10px] transition duration-200" 
                            :class="rentDropdownOpen ? 'rotate-180' : ''"
                        ></i>
                    </button>
                    
                    <!-- Dropdown Panel -->
                    <div 
                        x-show="rentDropdownOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2.5 w-44 rounded-2xl overflow-hidden bg-white border border-slate-150/50 shadow-xl py-2 z-50 text-left"
                        x-cloak
                    >
                        <a href="/listings?type=apartment" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            Thuê Căn hộ
                        </a>
                        <a href="/listings?type=house" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition border-t border-slate-50">
                            Thuê Nhà riêng
                        </a>
                        <a href="/listings?type=office" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition border-t border-slate-50">
                            Thuê Văn phòng
                        </a>
                    </div>
                </div>
                <a href="/map" :class="isScrolled ? 'text-slate-600 hover:text-primary' : 'text-slate-200 hover:text-white'" class="font-bold text-sm lg:text-base hover:text-primary transition duration-150 whitespace-nowrap">Bản đồ</a>
                <a href="#news" :class="isScrolled ? 'text-slate-600 hover:text-primary' : 'text-slate-200 hover:text-white'" class="font-bold text-sm lg:text-base hover:text-primary transition duration-150 whitespace-nowrap">Tin tức</a>
                <a href="#contact" :class="isScrolled ? 'text-slate-600 hover:text-primary' : 'text-slate-200 hover:text-white'" class="font-bold text-sm lg:text-base hover:text-primary transition duration-150 whitespace-nowrap">Liên hệ</a>
            </nav>

            <!-- Actions (Profile & CTA) -->
            @auth
            <div class="hidden md:flex flex-1 items-center justify-end space-x-2.5 lg:space-x-4 md:order-3" x-data="{ userDropdownOpen: false }">
                <!-- Đăng tin miễn phí -->
                <a href="{{ Auth::user()->role === 'owner' ? route('profile.index', ['tab' => 'create_property']) : route('profile.index') }}" class="inline-flex items-center justify-center px-3 lg:px-5 py-2 lg:py-2.5 border border-transparent text-sm font-extrabold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 hover:shadow-primary/35 transform hover:-translate-y-0.5 transition duration-200 whitespace-nowrap flex-shrink-0">
                    <i class="fa-solid fa-circle-plus mr-1.5 lg:mr-2"></i> Đăng tin miễn phí
                </a>

                <!-- User Account Dropdown -->
                <div class="relative flex-shrink-0">
                    <button 
                        @click="userDropdownOpen = !userDropdownOpen"
                        @click.away="userDropdownOpen = false"
                        type="button"
                        class="flex items-center space-x-1.5 lg:space-x-2.5 focus:outline-none cursor-pointer py-1.5 px-2.5 rounded-xl transition whitespace-nowrap flex-shrink-0"
                        :class="isScrolled ? 'hover:bg-slate-50' : 'hover:bg-white/10'"
                    >
                        <img 
                            src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0077bb&color=fff' }}" 
                            alt="{{ Auth::user()->name }}" 
                            class="w-7 h-7 rounded-full object-cover border border-primary/20 shadow-sm"
                        >
                        <span 
                            class="text-sm font-black transition-colors duration-250 whitespace-nowrap"
                            :class="isScrolled ? 'text-slate-700' : 'text-slate-100'"
                        >
                            {{ Auth::user()->name }}
                        </span>
                        <i 
                            class="fa-solid fa-chevron-down text-[10px] transition duration-200"
                            :class="[isScrolled ? 'text-slate-500' : 'text-slate-300', userDropdownOpen ? 'rotate-180' : '']"
                        ></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div 
                        x-show="userDropdownOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2.5 w-48 rounded-2xl overflow-hidden bg-white border border-slate-150/50 shadow-xl py-2 z-50 text-left"
                        x-cloak
                    >
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition border-b border-slate-50">
                            <i class="fa-solid fa-shield-halved mr-2 text-sm text-slate-400"></i> Trang quản trị
                        </a>
                        @endif
                        <a href="/profile" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-user-gear mr-2 text-sm text-slate-400"></i> Trang cá nhân
                        </a>
                        <a href="{{ route('profile.index', ['tab' => 'favorites']) }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-heart mr-2 text-sm text-slate-400"></i> Tin đăng đã lưu
                        </a>
                        @if(Auth::user()->role === 'owner')
                        <a href="{{ route('profile.index', ['tab' => 'properties']) }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-list-check mr-2 text-sm text-slate-400"></i> Quản lý tin đăng
                        </a>
                        <a href="{{ route('profile.index', ['tab' => 'appointments']) }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-calendar-days mr-2 text-sm text-slate-400"></i> Lịch hẹn khách đặt
                        </a>
                        @elseif(Auth::user()->role === 'tenant')
                        <a href="{{ route('profile.index', ['tab' => 'appointments']) }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-calendar-days mr-2 text-sm text-slate-400"></i> Lịch hẹn xem nhà
                        </a>
                        @endif
                        <div class="border-t border-slate-100 my-1"></div>
                        <a 
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="block px-4 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 transition"
                        >
                            <i class="fa-solid fa-right-from-bracket mr-2 text-sm text-red-400"></i> Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
            @endauth

            @guest
            <div class="hidden md:flex flex-1 items-center justify-end space-x-3.5 lg:space-x-5 md:order-3" x-data="{ guestDropdownOpen: false }">
                <!-- Đăng tin miễn phí -->
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-3 lg:px-5 py-2 lg:py-2.5 border border-transparent text-sm font-extrabold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 hover:shadow-primary/35 transform hover:-translate-y-0.5 transition duration-200 whitespace-nowrap">
                    <i class="fa-solid fa-circle-plus mr-1.5 lg:mr-2"></i> Đăng tin miễn phí
                </a>

                <!-- Guest Account Dropdown -->
                <div class="relative flex-shrink-0">
                    <button 
                        @click="guestDropdownOpen = !guestDropdownOpen"
                        @click.away="guestDropdownOpen = false"
                        type="button"
                        class="flex items-center space-x-2 focus:outline-none cursor-pointer py-1.5 px-3 rounded-xl transition whitespace-nowrap flex-shrink-0"
                        :class="isScrolled ? 'hover:bg-slate-50' : 'hover:bg-white/10'"
                    >
                        <i 
                            class="fa-regular fa-circle-user text-lg transition-colors"
                            :class="isScrolled ? 'text-slate-600' : 'text-slate-200'"
                        ></i>
                        <span 
                            class="text-sm font-bold transition-colors duration-250 whitespace-nowrap"
                            :class="isScrolled ? 'text-slate-700' : 'text-slate-100'"
                        >
                            Tài khoản
                        </span>
                        <i 
                            class="fa-solid fa-chevron-down text-[10px] transition duration-200"
                            :class="[isScrolled ? 'text-slate-500' : 'text-slate-300', guestDropdownOpen ? 'rotate-180' : '']"
                        ></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div 
                        x-show="guestDropdownOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2.5 w-44 rounded-2xl overflow-hidden bg-white border border-slate-150/50 shadow-xl py-2 z-50 text-left"
                        x-cloak
                    >
                        <a href="{{ route('login') }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition">
                            <i class="fa-solid fa-right-to-bracket mr-2 text-sm text-slate-400"></i> Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary transition border-t border-slate-50">
                            <i class="fa-solid fa-user-plus mr-2 text-sm text-slate-400"></i> Đăng ký
                        </a>
                    </div>
                </div>
            </div>
            @endguest

            <!-- Hamburger Button for Mobile -->
            <div class="flex items-center md:hidden absolute right-0">
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button" 
                    class="inline-flex items-center justify-center p-2 rounded-xl focus:outline-none transition duration-150"
                    :class="isScrolled ? 'text-slate-700 hover:bg-slate-100' : 'text-white hover:bg-white/10'"
                >
                    <span class="sr-only">Mở menu</span>
                    <!-- Icon Open (Hamburger) -->
                    <svg class="h-6 w-6" :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <!-- Icon Close -->
                    <svg class="h-6 w-6" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div 
        x-show="mobileMenuOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="md:hidden bg-white/98 backdrop-blur-lg shadow-xl border-b border-slate-100 absolute top-full left-0 right-0 z-40 overflow-hidden" 
        x-cloak
    >
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="/" class="block px-3 py-3 rounded-xl text-base font-semibold text-primary bg-primary-light">Trang chủ</a>
            <div x-data="{ mobileRentOpen: false }" class="space-y-1">
                <button 
                    @click="mobileRentOpen = !mobileRentOpen"
                    type="button"
                    class="w-full flex items-center justify-between px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition focus:outline-none"
                >
                    <span>Thuê</span>
                    <i class="fa-solid fa-chevron-down text-xs transition duration-200" :class="mobileRentOpen ? 'rotate-180' : ''"></i>
                </button>
                <div 
                    x-show="mobileRentOpen" 
                    x-transition:enter="transition ease-out duration-100" 
                    x-transition:enter-start="opacity-0 transform -translate-y-2" 
                    x-transition:enter-end="opacity-100 transform translate-y-0" 
                    class="pl-4 space-y-1" 
                    x-cloak
                >
                    <a href="/listings?type=apartment" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition">Thuê Căn hộ</a>
                    <a href="/listings?type=house" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition">Thuê Nhà riêng</a>
                    <a href="/listings?type=office" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary transition">Thuê Văn phòng</a>
                </div>
            </div>
            <a href="/map" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Bản đồ</a>
            <a href="#news" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Tin tức</a>
            <a href="#contact" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Liên hệ</a>
            
            <div class="pt-4 border-t border-slate-100 flex flex-col space-y-2">
                @auth
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-shield-halved text-slate-400 text-lg w-6 text-center"></i>
                    <span>Trang quản trị (Admin)</span>
                </a>
                @endif
                <a href="/profile" class="flex items-center space-x-3 px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <img 
                        src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0077bb&color=fff' }}" 
                        alt="{{ Auth::user()->name }}" 
                        class="w-6 h-6 rounded-full object-cover border border-primary/20"
                    >
                    <span>Trang cá nhân của {{ Auth::user()->name }}</span>
                </a>
                <a 
                    href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                    class="block px-3 py-3 rounded-xl text-base font-semibold text-red-500 hover:bg-red-50 transition"
                >
                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Đăng xuất
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                @endauth

                @guest
                <a href="{{ route('login') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Đăng nhập</a>
                <a href="{{ route('register') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition">Đăng ký</a>
                @endguest

                <a href="{{ Auth::check() ? (Auth::user()->role === 'owner' ? route('profile.index', ['tab' => 'create_property']) : route('profile.index')) : route('login') }}" class="inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 transition">
                    <i class="fa-solid fa-circle-plus mr-2"></i> Đăng tin miễn phí
                </a>
            </div>
        </div>
    </div>
</header>
