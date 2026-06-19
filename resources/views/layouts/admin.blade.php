<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Control Panel') | BDS Rental</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome v6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body 
    class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased transition-colors duration-200" 
    x-data="{ 
        sidebarOpen: false, 
        darkMode: localStorage.getItem('darkMode') === 'true',
        confirmOpen: false,
        confirmTitle: 'Xác nhận',
        confirmMessage: 'Bạn có chắc chắn muốn thực hiện hành động này không?',
        confirmButtonText: 'Đồng ý',
        confirmButtonColor: 'bg-red-500 hover:bg-red-650',
        triggerConfirm(title, message, btnText, btnColor, actionCallback) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmButtonText = btnText || 'Đồng ý';
            this.confirmButtonColor = btnColor || 'bg-red-500 hover:bg-red-650';
            window.confirmActionCallback = actionCallback;
            this.confirmOpen = true;
        },
        executeConfirm() {
            if (typeof window.confirmActionCallback === 'function') {
                window.confirmActionCallback();
            }
            window.confirmActionCallback = null;
            this.confirmOpen = false;
        }
    }" 
    x-init="
        document.documentElement.classList.toggle('dark', darkMode);
        $watch('darkMode', val => {
            localStorage.setItem('darkMode', val ? 'true' : 'false');
            document.documentElement.classList.toggle('dark', val);
        });
    "
>
    
    <!-- Toast Popup Notification -->
    @if(session('success') || session('error'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-5 right-5 z-50 max-w-sm bg-white border-l-4 {{ session('success') ? 'border-green-500' : 'border-red-500' }} rounded-2xl shadow-2xl p-4 flex items-center space-x-3.5"
    >
        <div class="w-8 h-8 rounded-full {{ session('success') ? 'bg-green-50 text-green-500' : 'bg-red-50 text-red-500' }} flex items-center justify-center flex-shrink-0">
            <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-lg"></i>
        </div>
        <div class="text-left">
            <p class="text-xs font-bold text-slate-800">{{ session('success') ? 'Thành công' : 'Thất bại' }}</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ session('success') ?? session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="min-h-screen flex flex-col">
        <!-- HEADER / NAVBAR -->
        @include('admin.partials.navbar')

        <!-- MIDDLE LAYER -->
        <div class="flex-1 flex flex-col md:flex-row min-w-0 relative">
            <!-- SIDEBAR -->
            @include('admin.partials.sidebar')

            <!-- Mobile Sidebar Overlay -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-xs md:hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak
            ></div>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-6 md:p-8 bg-slate-50 dark:bg-slate-950 transition-colors duration-200 overflow-y-auto">
                @yield('content')
            </main>

            <!-- ASIDE PANEL -->
            <aside class="w-64 bg-white dark:bg-slate-900 border-l border-slate-200/80 dark:border-slate-800 p-6 flex-shrink-0 hidden xl:block xl:sticky xl:top-16 xl:h-[calc(100vh-4rem)] overflow-y-auto transition-colors duration-200">
                @hasSection('aside')
                    @yield('aside')
                @else
                    <!-- Default Aside Content -->
                    <div class="space-y-6 text-left">
                        <!-- System Status Widget -->
                        <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-150 dark:border-slate-800">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Trạng thái
                            </h4>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500">Neon Database</span>
                                    <span class="text-green-500 dark:text-green-400 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Connected
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500">Phản hồi</span>
                                    <span class="text-slate-700 dark:text-slate-350">12ms</span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500">Framework</span>
                                    <span class="text-slate-700 dark:text-slate-350">Laravel v{{ app()->version() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Hành động nhanh</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <a href="{{ route('admin.properties.index') }}" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition group border border-dashed border-slate-200 dark:border-slate-800">
                                    <i class="fa-solid fa-house-circle-check text-slate-400 group-hover:text-primary transition"></i>
                                    Duyệt tin đăng
                                </a>
                                <a href="{{ route('admin.users.index') }}?role=owner" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition group border border-dashed border-slate-200 dark:border-slate-800">
                                    <i class="fa-solid fa-user-shield text-slate-400 group-hover:text-primary transition"></i>
                                    Quản lý chủ nhà
                                </a>
                            </div>
                        </div>
                        
                        <!-- Notification Banner -->
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-primary/10 to-indigo-500/10 border border-primary/20 dark:border-primary/10">
                            <h5 class="text-xs font-extrabold text-primary dark:text-indigo-400 tracking-wide">Nhắc nhở công việc</h5>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-semibold mt-1.5 leading-relaxed">
                                Hãy duyệt các lịch hẹn thuê nhà và giải quyết khiếu nại của khách thuê sớm nhất.
                            </p>
                        </div>
                    </div>
                @endif
            </aside>
        </div>

        <!-- FOOTER -->
        @include('admin.partials.footer')
    </div>


    <!-- Reusable Confirm Modal -->
    <div 
        x-show="confirmOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/70 backdrop-blur-sm"
        x-transition
        x-cloak
    >
        <div 
            @click.away="confirmOpen = false"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 max-w-sm w-full text-center space-y-4"
        >
            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-950/30 text-red-500 dark:text-red-400 flex items-center justify-center text-xl mx-auto">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider" x-text="confirmTitle">Xác nhận</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold leading-relaxed" x-text="confirmMessage">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button 
                    @click="confirmOpen = false" 
                    class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition cursor-pointer"
                >
                    Hủy bỏ
                </button>
                <button 
                    @click="executeConfirm()" 
                    :class="confirmButtonColor" 
                    class="flex-1 px-4 py-2.5 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer"
                    x-text="confirmButtonText"
                >
                    Đồng ý
                </button>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
