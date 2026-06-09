<header class="sticky top-0 h-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-6 z-40 transition-colors duration-200 shadow-sm">
    <div class="flex items-center space-x-3">
        <!-- Mobile Hamburger -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none cursor-pointer">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <!-- Breadcrumbs -->
        <nav class="hidden sm:flex items-center space-x-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <span class="text-slate-400 dark:text-slate-500">Admin Panel</span>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-700"></i>
            <span class="text-slate-800 dark:text-slate-200">@yield('breadcrumb', 'Dashboard')</span>
        </nav>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Dark Mode Toggle Button -->
        <button 
            @click="darkMode = !darkMode"
            class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition cursor-pointer text-xs focus:outline-none border border-slate-200/40 dark:border-slate-700/60"
            title="Chuyển chế độ sáng/tối"
        >
            <i :class="darkMode ? 'fa-solid fa-sun text-amber-500 text-sm' : 'fa-solid fa-moon text-sm'"></i>
        </button>

        <!-- Admin Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open" 
                @click.away="open = false" 
                class="flex items-center space-x-2.5 focus:outline-none cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 py-1.5 px-2 rounded-xl transition"
            >
                <img 
                    src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0077bb&color=fff' }}" 
                    alt="{{ Auth::user()->name }}" 
                    class="w-7 h-7 rounded-full object-cover border border-primary/20 shadow-sm"
                >
                <span class="hidden md:inline text-xs font-bold text-slate-700 dark:text-slate-300">{{ Auth::user()->name }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dark:text-slate-500"></i>
            </button>
            <!-- Dropdown Panel -->
            <div 
                x-show="open" 
                x-transition 
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-2xl overflow-hidden py-2 z-50 text-left"
                x-cloak
            >
                <a href="/" class="block px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-primary transition">
                    <i class="fa-solid fa-globe mr-2 text-slate-400 dark:text-slate-500"></i> Xem trang chủ
                </a>
                <a href="/profile" class="block px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-primary transition">
                    <i class="fa-solid fa-user-circle mr-2 text-slate-400 dark:text-slate-500"></i> Trang cá nhân
                </a>
                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                <a 
                    href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('admin-header-logout-form').submit();"
                    class="block px-4 py-2.5 text-xs font-bold text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition cursor-pointer"
                >
                    <i class="fa-solid fa-right-from-bracket mr-2 text-red-400 dark:text-red-500"></i> Đăng xuất
                </a>
                <form id="admin-header-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>
