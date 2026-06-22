@extends('layouts.app')

@section('title', 'Dashboard Quản Lý Thành Viên | BDS Rental')

@section('content')
<!-- Dashboard Wrapper with AlpineJS -->
<div 
    x-data="{ 
        activeTab: '{{ request('tab') ?? 'profile' }}', 
        activeSubTab: '{{ request('subtab') ?? ($errors->has('current_password') || $errors->has('new_password') ? 'password' : ($errors->has('avatar') ? 'avatar' : ($errors->has('id_number') || $errors->has('id_date') || $errors->has('id_place') || $errors->has('cccd_front') || $errors->has('cccd_back') ? 'cccd' : 'info'))) }}',
        showToast: {{ session('success') ? 'true' : 'false' }}, 
        toastMessage: '{{ session('success') }}',
        init() {
            if (this.showToast) {
                setTimeout(() => this.showToast = false, 3500);
            }
        },
        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3500);
        }
    }"
    class="bg-slate-50 pt-28 pb-16 min-h-screen relative"
>
    <!-- Toast Popup Notification -->
    <div 
        x-show="showToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0"
        class="fixed top-24 right-4 z-50 max-w-sm bg-white border-l-4 border-green-500 rounded-2xl shadow-2xl p-4.5 flex items-center space-x-3.5"
        x-cloak
    >
        <div class="w-8 h-8 rounded-full bg-green-50 text-green-500 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-check text-lg"></i>
        </div>
        <div class="text-left">
            <p class="text-xs font-black text-slate-800">Thành công</p>
            <p class="text-[11px] text-slate-500 font-semibold" x-text="toastMessage"></p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-6 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-800">Dashboard thành viên</span>
        </nav>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-6 lg:pt-0">
            
            <!-- LEFT COLUMN: Dashboard Sidebar Navigation (3/12 cols) -->
            <div class="lg:col-span-3 lg:self-stretch">
                <!-- Sticky Wrapper for Card -->
                <div class="lg:sticky lg:top-24 max-h-[calc(100vh-7rem)] overflow-y-auto scrollbar-none text-left flex flex-col">
                    <!-- Sidebar Card -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-left flex-grow">
                    <!-- User Summary Header -->
                    <div class="relative text-center pb-6 border-b border-slate-100 bg-white">
                        <!-- Banner Background -->
                        <div class="h-24 bg-gradient-to-r from-primary/80 via-primary to-indigo-600/90 relative overflow-hidden">
                            <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-white/10 blur-lg"></div>
                            <div class="absolute -left-8 -bottom-8 w-24 h-24 rounded-full bg-white/10 blur-md"></div>
                        </div>
                        
                        <!-- Avatar Overlay -->
                        <div class="relative w-20 h-20 mx-auto -mt-10 mb-3 group">
                            <img 
                                src="{{ $user['avatar'] }}" 
                                alt="{{ $user['name'] }}" 
                                class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg group-hover:scale-105 transition-all duration-300"
                            >
                            <span class="absolute bottom-0.5 right-0.5 w-4.5 h-4.5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center shadow-md" title="Đang trực tuyến">
                                <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 animate-ping"></span>
                            </span>
                        </div>

                        <!-- User Name & Role Badge -->
                        <div class="px-5">
                            <h4 class="text-base font-extrabold text-slate-800 leading-snug tracking-tight mb-2">{{ $user['name'] }}</h4>
                            
                            <div class="flex items-center justify-center mb-3">
                                @if(Auth::user()->role === 'admin')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100/85 shadow-sm">
                                        <i class="fa-solid fa-shield-halved mr-1.5 text-rose-500"></i>
                                        Ban quản trị
                                    </span>
                                @elseif(Auth::user()->role === 'owner')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100/85 shadow-sm">
                                        <i class="fa-solid fa-circle-check mr-1.5 text-emerald-500"></i>
                                        Đối tác Chủ nhà
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-sky-50 text-sky-600 border border-sky-100/85 shadow-sm">
                                        <i class="fa-solid fa-house-user mr-1.5 text-sky-500"></i>
                                        Khách thuê nhà
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-center text-[10px] text-slate-400 font-semibold space-x-1.5">
                                <i class="fa-solid fa-calendar-days text-slate-355"></i>
                                <span>Thành viên từ: {{ $user['join_date'] }}</span>
                            </div>
                        </div>
                    </div>

                    <nav class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible scrollbar-none border-b lg:border-b-0 border-slate-100">
                        <button 
                            @click="activeTab = 'profile'; window.history.pushState(null, '', '?tab=profile');" 
                            :class="activeTab === 'profile' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <i class="fa-solid fa-user-gear text-sm"></i>
                            <span>Thông tin cá nhân</span>
                        </button>
                        
                        @if(Auth::user()->role === 'owner')
                        <button 
                            @click="activeTab = 'properties'; window.history.pushState(null, '', '?tab=properties');" 
                            :class="activeTab === 'properties' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-list-check text-sm"></i>
                                <span>Quản lý tin đăng</span>
                            </div>
                            <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                {{ $stats['total_properties'] }}
                            </span>
                        </button>
                        
                        <button 
                            @click="activeTab = 'appointments'; window.history.pushState(null, '', '?tab=appointments');" 
                            :class="activeTab === 'appointments' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-calendar-days text-sm"></i>
                                <span>Lịch hẹn khách đặt</span>
                            </div>
                            <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                {{ $stats['total_appointments'] }}
                            </span>
                        </button>
                        @endif

                        <button 
                            @click="activeTab = 'favorites'; window.history.pushState(null, '', '?tab=favorites');" 
                            :class="activeTab === 'favorites' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-heart text-sm"></i>
                                <span>Tin yêu thích</span>
                            </div>
                            <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                {{ $properties->count() }}
                            </span>
                        </button>

                        @if(Auth::user()->role === 'tenant')
                        <button 
                            @click="activeTab = 'appointments'; window.history.pushState(null, '', '?tab=appointments');" 
                            :class="activeTab === 'appointments' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-calendar-days text-sm"></i>
                                <span>Lịch hẹn xem nhà</span>
                            </div>
                            <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                {{ $stats['total_appointments'] }}
                            </span>
                        </button>
                        @endif

                        <button 
                            @click="activeTab = 'profile'; activeSubTab = 'password'; window.history.pushState(null, '', '?tab=profile&subtab=password');" 
                            :class="activeTab === 'profile' && activeSubTab === 'password' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <i class="fa-solid fa-key text-sm"></i>
                            <span>Đổi mật khẩu</span>
                        </button>

                        @if(Auth::user()->role === 'admin')
                        <a 
                            href="{{ route('admin.dashboard') }}" 
                            class="flex items-center space-x-3 px-5 py-4 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary border-b-2 lg:border-b-0 lg:border-l-4 border-transparent whitespace-nowrap flex-grow lg:flex-grow-0"
                        >
                            <i class="fa-solid fa-circle-arrow-left text-sm"></i>
                            <span>Quay lại Admin Panel</span>
                        </a>
                        @endif


                    </nav>
                </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Dashboard Tab Contents (9/12 cols) -->
            <div class="lg:col-span-9 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8 text-left">
                
                <!-- TAB 1: Profile Information & Statistics -->
                <div x-show="activeTab === 'profile'" x-transition:enter="transition duration-150" class="space-y-8">
                    <!-- Title -->
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Thông tin cá nhân</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Cập nhật hồ sơ và xem số liệu thống kê tài khoản của bạn.</p>
                    </div>

                    <!-- Statistics Cards Grid (Giai đoạn 7) -->
                    @if(Auth::user()->role === 'owner')
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Stat Item 1 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-primary-light text-primary flex items-center justify-center text-lg">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin đã đăng</span>
                                <span class="text-xl font-black text-slate-800">{{ $stats['total_properties'] }} tin</span>
                            </div>
                        </div>

                        <!-- Stat Item 2 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lượt xem tin</span>
                                <span class="text-xl font-black text-slate-800">{{ number_format($stats['total_views']) }} lượt</span>
                            </div>
                        </div>

                        <!-- Stat Item 3 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn khách đặt</span>
                                <span class="text-xl font-black text-slate-800">{{ $stats['total_appointments'] }} cuộc</span>
                            </div>
                        </div>
                    </div>
                    @elseif(Auth::user()->role === 'tenant')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Stat Item 1 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin yêu thích</span>
                                <span class="text-xl font-black text-slate-800">{{ $stats['total_favorites'] }} tin</span>
                            </div>
                        </div>

                        <!-- Stat Item 2 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn xem nhà</span>
                                <span class="text-xl font-black text-slate-800">{{ $stats['total_appointments'] }} cuộc</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Horizontal Sub-Tabs -->
                    <div class="flex flex-wrap gap-3 pb-5 border-b border-slate-100">
                        <!-- THÔNG TIN CÁ NHÂN -->
                        <button 
                            @click="activeSubTab = 'info'; window.history.pushState(null, '', '?tab=profile&subtab=info');"
                            :class="activeSubTab === 'info' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center space-x-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer"
                        >
                            <i class="fa-solid fa-user text-xs"></i>
                            <span>Thông tin cá nhân</span>
                        </button>

                        <!-- ĐỔI MẬT KHẨU -->
                        <button 
                            @click="activeSubTab = 'password'; window.history.pushState(null, '', '?tab=profile&subtab=password');"
                            :class="activeSubTab === 'password' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center space-x-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer"
                        >
                            <i class="fa-solid fa-key text-xs"></i>
                            <span>Đổi mật khẩu</span>
                        </button>

                        <!-- ẢNH ĐẠI DIỆN -->
                        <button 
                            @click="activeSubTab = 'avatar'; window.history.pushState(null, '', '?tab=profile&subtab=avatar');"
                            :class="activeSubTab === 'avatar' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center space-x-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer"
                        >
                            <i class="fa-solid fa-image text-xs"></i>
                            <span>Ảnh đại diện</span>
                        </button>

                        <!-- XÁC THỰC CCCD -->
                        <button 
                            @click="activeSubTab = 'cccd'; window.history.pushState(null, '', '?tab=profile&subtab=cccd');"
                            :class="activeSubTab === 'cccd' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center space-x-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer"
                        >
                            <i class="fa-solid fa-id-card text-xs"></i>
                            <span>Xác thực CCCD</span>
                        </button>
                    </div>

                    <!-- Sub-tab 1: Personal Info -->
                    <div x-show="activeSubTab === 'info'" class="space-y-6" x-cloak>
                        <form 
                            action="{{ route('profile.update') }}"
                            method="POST"
                            class="space-y-6"
                        >
                            @csrf
                            
                            <!-- Grid 1: Basic Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                <!-- Họ tên hiển thị -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="name"
                                            value="{{ old('name', $user['name']) }}"
                                            required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('name')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Họ & tên đệm -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ & tên đệm</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="firstname"
                                            value="{{ old('firstname', $user['firstname']) }}"
                                            placeholder="Lê Đức..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('firstname')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tên -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tên</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="lastname"
                                            value="{{ old('lastname', $user['lastname']) }}"
                                            placeholder="Hải..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('lastname')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Grid 2: Contact & Gender -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                <!-- SĐT -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="tel" 
                                            name="phone"
                                            value="{{ old('phone', $user['phone']) }}"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('phone')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1 flex justify-between items-center">
                                        <span>Địa chỉ Email</span>
                                        <span class="text-[9px] text-amber-500 normal-case font-bold"><i class="fa-solid fa-lock mr-1"></i> Email đăng nhập</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="email" 
                                            name="email"
                                            value="{{ old('email', $user['email']) }}"
                                            required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('email')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Giới tính -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giới tính</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-venus-mars absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <select 
                                            name="gender"
                                            class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none transition cursor-pointer"
                                        >
                                            <option value="0" {{ old('gender', $user['gender']) == 0 ? 'selected' : '' }}>Nam</option>
                                            <option value="1" {{ old('gender', $user['gender']) == 1 ? 'selected' : '' }}>Nữ</option>
                                            <option value="2" {{ old('gender', $user['gender']) == 2 ? 'selected' : '' }}>Khác</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Grid 3: Dob & Pob & Website -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                <!-- Ngày sinh -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày sinh</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-cake-candles absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="dob"
                                            value="{{ old('dob', $user['dob']) }}"
                                            placeholder="dd/mm/yyyy..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('dob')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Nơi sinh -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Nơi sinh</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="pob"
                                            value="{{ old('pob', $user['pob']) }}"
                                            placeholder="Hà Nội..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('pob')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Website -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Website</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-globe absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="website"
                                            value="{{ old('website', $user['website']) }}"
                                            placeholder="https://..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('website')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Grid 4: Address Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                                <!-- Tỉnh / Thành -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh / Thành phố</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-map-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="add_province"
                                            value="{{ old('add_province', $user['add_province']) }}"
                                            placeholder="Hà Nội..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                </div>

                                <!-- Quận / Huyện -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quận / Huyện</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-city absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="add_district"
                                            value="{{ old('add_district', $user['add_district']) }}"
                                            placeholder="Cầu Giấy..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                </div>

                                <!-- Phường / Xã -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường / Xã</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-tree-city absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="add_ward"
                                            value="{{ old('add_ward', $user['add_ward']) }}"
                                            placeholder="Dịch Vọng..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                </div>

                                <!-- Đường / Số nhà -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Đường / Số nhà</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-road absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="add_street"
                                            value="{{ old('add_street', $user['add_street']) }}"
                                            placeholder="Số 10 Duy Tân..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Grid 5: Zalo Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Zalo ID -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Zalo ID</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-comments absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="zalo_id"
                                            value="{{ old('zalo_id', $user['zalo_id']) }}"
                                            placeholder="Zalo ID của bạn..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                </div>

                                <!-- Zalo Key -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Zalo Key</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="zalo_key"
                                            value="{{ old('zalo_key', $user['zalo_key']) }}"
                                            placeholder="Zalo Key của bạn..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Giới thiệu bản thân -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giới thiệu bản thân</label>
                                <textarea 
                                    name="intro"
                                    rows="3"
                                    placeholder="Chia sẻ một chút về bản thân bạn..."
                                    class="w-full p-3.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                >{{ old('intro', $user['intro']) }}</textarea>
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end pt-4 border-t border-slate-100">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
                                >
                                    Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sub-tab 2: Change Password -->
                    <div x-show="activeSubTab === 'password'" class="space-y-6" x-cloak>
                        <form 
                            action="{{ route('profile.password') }}"
                            method="POST"
                            class="max-w-md space-y-4"
                        >
                            @csrf
                            <!-- Old Password -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mật khẩu hiện tại</label>
                                <div class="relative">
                                    <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="password" 
                                        name="current_password"
                                        required
                                        placeholder="Nhập mật khẩu hiện tại..."
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                    >
                                </div>
                                @error('current_password')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mật khẩu mới</label>
                                <div class="relative">
                                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="password" 
                                        name="new_password"
                                        required
                                        placeholder="Tối thiểu 8 ký tự..."
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                    >
                                </div>
                                @error('new_password')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Xác nhận mật khẩu mới</label>
                                <div class="relative">
                                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="password" 
                                        name="new_password_confirmation"
                                        required
                                        placeholder="Nhập lại mật khẩu mới..."
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                    >
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
                                >
                                    <span>Đổi mật khẩu</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sub-tab 3: Avatar -->
                    <div x-show="activeSubTab === 'avatar'" class="space-y-6" x-cloak>
                        <form 
                            action="{{ route('profile.avatar') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            x-data="{ 
                                avatarUrl: '{{ $user['avatar'] }}',
                                previewAvatar(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.avatarUrl = URL.createObjectURL(file);
                                    }
                                }
                            }"
                            class="space-y-6"
                        >
                            @csrf
                            <div class="flex flex-col items-center space-y-4 py-8 bg-slate-50 rounded-3xl border border-slate-100">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-200">
                                    <img :src="avatarUrl" alt="Avatar preview" class="w-full h-full object-cover">
                                </div>
                                <div class="text-center space-y-2">
                                    <h4 class="text-sm font-bold text-slate-800">Ảnh đại diện tài khoản</h4>
                                    <p class="text-xs text-slate-400 max-w-xs leading-normal">Hỗ trợ định dạng JPG, PNG dung lượng dưới 2MB.</p>
                                    <label class="inline-flex items-center justify-center px-4 py-2 border border-slate-200 hover:border-primary text-xs font-bold rounded-xl text-slate-700 hover:text-white bg-white hover:bg-primary shadow-sm transition cursor-pointer">
                                        <i class="fa-solid fa-camera mr-2 text-xs"></i> Chọn ảnh mới
                                        <input type="file" name="avatar" accept="image/*" @change="previewAvatar($event)" class="hidden">
                                    </label>
                                    @error('avatar')
                                        <p class="text-red-500 text-[10px] font-bold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-end pt-4 border-t border-slate-100">
                                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                    Lưu ảnh đại diện
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Sub-tab 4: CCCD Verification -->
                    <div x-show="activeSubTab === 'cccd'" class="space-y-6" x-cloak>
                        <form 
                            action="{{ route('profile.cccd') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            x-data="{
                                cccdFrontUrl: '{{ $user['cccd_front'] ? (str_starts_with($user['cccd_front'], 'http') ? $user['cccd_front'] : asset($user['cccd_front'])) : '' }}',
                                cccdBackUrl: '{{ $user['cccd_back'] ? (str_starts_with($user['cccd_back'], 'http') ? $user['cccd_back'] : asset($user['cccd_back'])) : '' }}',
                                previewFront(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.cccdFrontUrl = URL.createObjectURL(file);
                                    }
                                },
                                previewBack(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.cccdBackUrl = URL.createObjectURL(file);
                                    }
                                }
                            }"
                            class="space-y-6 text-left"
                        >
                            @csrf
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Xác thực CCCD / CMND</h2>
                                <p class="text-xs text-slate-400 mt-1 font-semibold">Tải lên hình ảnh CCCD 2 mặt và cập nhật thông tin giấy tờ</p>
                            </div>

                            <!-- Yêu cầu hình ảnh alert -->
                            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-4 flex items-start space-x-3 text-left">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-amber-800 mb-0.5">Yêu cầu hình ảnh:</h4>
                                    <p class="text-[11px] text-amber-700 font-semibold leading-relaxed">
                                        Ảnh chụp rõ nét, không bị lóa sáng, không mất góc và không bị che khuất các thông tin cá nhân quan trọng.
                                    </p>
                                </div>
                            </div>

                            <!-- CCCD Front / Back Images Upload -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Mặt trước -->
                                <div class="space-y-2 text-left">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt trước CCCD</label>
                                    <div class="relative border-2 border-dashed border-slate-200 hover:border-primary rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] transition group">
                                        <template x-if="cccdFrontUrl">
                                            <div class="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                                                <img :src="cccdFrontUrl" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                                    <span class="text-white text-xs font-bold"><i class="fa-solid fa-camera mr-1"></i> Thay đổi</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!cccdFrontUrl">
                                            <div class="text-center py-6 flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 bg-blue-50 text-primary flex items-center justify-center rounded-full mb-3">
                                                    <i class="fa-solid fa-camera text-lg"></i>
                                                </div>
                                                <p class="text-xs font-bold text-slate-700">Chọn ảnh mặt trước</p>
                                                <p class="text-[10px] text-slate-400 mt-1">Nhấp để tải lên</p>
                                            </div>
                                        </template>
                                        <input type="file" name="cccd_front" accept="image/*" @change="previewFront($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                    @error('cccd_front')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Mặt sau -->
                                <div class="space-y-2 text-left">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt sau CCCD</label>
                                    <div class="relative border-2 border-dashed border-slate-200 hover:border-primary rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] transition group">
                                        <template x-if="cccdBackUrl">
                                            <div class="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                                                <img :src="cccdBackUrl" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                                    <span class="text-white text-xs font-bold"><i class="fa-solid fa-camera mr-1"></i> Thay đổi</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!cccdBackUrl">
                                            <div class="text-center py-6 flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 bg-blue-50 text-primary flex items-center justify-center rounded-full mb-3">
                                                    <i class="fa-solid fa-camera text-lg"></i>
                                                </div>
                                                <p class="text-xs font-bold text-slate-700">Chọn ảnh mặt sau</p>
                                                <p class="text-[10px] text-slate-400 mt-1">Nhấp để tải lên</p>
                                            </div>
                                        </template>
                                        <input type="file" name="cccd_back" accept="image/*" @change="previewBack($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                    @error('cccd_back')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Input Fields Group -->
                            <div class="space-y-4 pt-2">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                    <!-- Họ và tên -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên (trên CCCD)</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="name"
                                                value="{{ old('name', $user['name']) }}"
                                                required
                                                placeholder="Ví dụ: LÊ ĐỨC HẢI"
                                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition uppercase"
                                            >
                                        </div>
                                        @error('name')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Giới tính -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Giới tính</label>
                                        <div class="relative">
                                            <select 
                                                name="gender"
                                                class="w-full pl-4 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none transition cursor-pointer"
                                            >
                                                <option value="0" {{ old('gender', $user['gender']) == 0 ? 'selected' : '' }}>Nam</option>
                                                <option value="1" {{ old('gender', $user['gender']) == 1 ? 'selected' : '' }}>Nữ</option>
                                                <option value="2" {{ old('gender', $user['gender']) == 2 ? 'selected' : '' }}>Khác</option>
                                            </select>
                                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                                        </div>
                                    </div>

                                    <!-- Ngày sinh -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày sinh</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="dob"
                                                value="{{ old('dob', $user['dob']) }}"
                                                required
                                                placeholder="dd/mm/yyyy"
                                                class="w-full pr-10 pl-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                            <i class="fa-regular fa-calendar absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        </div>
                                        @error('dob')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                    <!-- Số CCCD -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số CCCD / CMND (12 số)</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="id_number"
                                                value="{{ old('id_number', $user['id_number']) }}"
                                                required
                                                placeholder="Ví dụ: 012345678901"
                                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                        </div>
                                        @error('id_number')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Ngày cấp -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày cấp</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="id_date"
                                                value="{{ old('id_date', $user['id_date']) }}"
                                                required
                                                placeholder="dd/mm/yyyy"
                                                class="w-full pr-10 pl-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                            <i class="fa-regular fa-calendar absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        </div>
                                        @error('id_date')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Nơi cấp -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Nơi cấp</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="id_place"
                                                value="{{ old('id_place', $user['id_place']) }}"
                                                required
                                                placeholder="Ví dụ: Cục Cảnh sát QLHC về TTXH"
                                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                        </div>
                                        @error('id_place')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <!-- Quê quán -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quê quán (Nơi sinh)</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="pob"
                                                value="{{ old('pob', $user['pob']) }}"
                                                required
                                                placeholder="Ví dụ: Ba Đình, Hà Nội"
                                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                        </div>
                                        @error('pob')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Nơi thường trú -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Nơi thường trú</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="permanent_address"
                                                value="{{ old('permanent_address', $user['permanent_address']) }}"
                                                required
                                                placeholder="Ví dụ: 123 Nguyễn Huệ, Quận 1, TP.HCM"
                                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                        </div>
                                        @error('permanent_address')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- CCCD Info Summary Card -->
                            <div class="bg-slate-50/50 border border-slate-100 rounded-3xl p-5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-center sm:text-left mt-6">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Họ và tên</span>
                                    <span class="text-xs font-black text-slate-800">{{ $user['name'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Ngày sinh</span>
                                    <span class="text-xs font-black text-slate-800">{{ $user['dob'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Giới tính</span>
                                    <span class="text-xs font-black text-slate-800">
                                        @if(($user['gender'] ?? 0) == 0) Nam @elseif(($user['gender'] ?? 0) == 1) Nữ @else Khác @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Số CCCD đã lưu</span>
                                    <span class="text-xs font-black text-slate-800">{{ $user['id_number'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Ngày cấp</span>
                                    <span class="text-xs font-black text-slate-800">{{ $user['id_date'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nơi cấp</span>
                                    <span class="text-xs font-black text-slate-800">{{ $user['id_place'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Quê quán (Nơi sinh)</span>
                                    <span class="text-xs font-black text-slate-800 truncate block" title="{{ $user['pob'] ?? 'Chưa cập nhật' }}">{{ $user['pob'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="col-span-2 md:col-span-4">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nơi thường trú</span>
                                    <span class="text-xs font-black text-slate-800 truncate block" title="{{ $user['permanent_address'] ?? 'Chưa cập nhật' }}">{{ $user['permanent_address'] ?? 'Chưa cập nhật' }}</span>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-slate-100">
                                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                    Cập nhật thông tin CCCD
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB 2: Saved / Favorite Listings OR Owner Properties -->
                @if(Auth::user()->role === 'owner')
                @php
                    $saleProperties = $myProperties->filter(fn($p) => $p->price_label && stripos($p->price_label, 'tháng') === false);
                    $rentProperties = $myProperties->filter(fn($p) => !$p->price_label || stripos($p->price_label, 'tháng') !== false);
                @endphp
                <div x-show="activeTab === 'properties'" x-data="{ subTab: 'all' }" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Quản lý tin đăng</h2>
                            <p class="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và quản lý các bất động sản đã đăng của bạn.</p>
                        </div>
                    </div>

                    @if(!$myProperties->isEmpty())
                    <!-- Sub-tabs for Sale / Rent -->
                    <div class="flex border-b border-slate-150/80 mb-6 gap-6">
                        <button 
                            @click="subTab = 'all'" 
                            :class="subTab === 'all' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="px-2 py-2.5 text-xs border-b-2 transition focus:outline-none cursor-pointer"
                        >
                            Tất cả ({{ $myProperties->count() }})
                        </button>
                        <button 
                            @click="subTab = 'sale'" 
                            :class="subTab === 'sale' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="px-2 py-2.5 text-xs border-b-2 transition focus:outline-none cursor-pointer"
                        >
                            Tin bán ({{ $saleProperties->count() }})
                        </button>
                        <button 
                            @click="subTab = 'rent'" 
                            :class="subTab === 'rent' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="px-2 py-2.5 text-xs border-b-2 transition focus:outline-none cursor-pointer"
                        >
                            Tin cho thuê ({{ $rentProperties->count() }})
                        </button>
                    </div>
                    @endif

                    @if($myProperties->isEmpty())
                        <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500">Bạn chưa đăng bất kỳ bất động sản nào.</p>
                            <p class="text-[10px] text-slate-400 mt-1">Bấm nút "Đăng tin miễn phí" trên thanh điều hướng để bắt đầu.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-slate-150/80 rounded-2xl shadow-sm bg-white">
                            <table class="min-w-full divide-y divide-slate-100 text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <tr>
                                        <th scope="col" class="px-5 py-4">Bất động sản</th>
                                        <th scope="col" class="px-5 py-4">Danh mục</th>
                                        <th scope="col" class="px-5 py-4">Giá</th>
                                        <th scope="col" class="px-5 py-4">Diện tích</th>
                                        <th scope="col" class="px-5 py-4">Trạng thái</th>
                                        <th scope="col" class="px-5 py-4 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-semibold">
                                    @foreach($myProperties as $prop)
                                        @php
                                            $isPropSale = $prop->price_label && stripos($prop->price_label, 'tháng') === false;
                                        @endphp
                                        <tr 
                                            x-show="subTab === 'all' || 
                                                    (subTab === 'sale' && {{ $isPropSale ? 'true' : 'false' }}) || 
                                                    (subTab === 'rent' && {{ !$isPropSale ? 'true' : 'false' }})"
                                        >
                                            <td class="px-5 py-4 flex items-center space-x-3 min-w-[250px]">
                                                <img src="{{ asset($prop->image) }}" class="w-12 h-10 object-cover rounded-lg border border-slate-200 flex-shrink-0">
                                                <div class="truncate">
                                                    <a href="/property/{{ $prop->id }}" class="hover:text-primary font-bold text-slate-800 block truncate" title="{{ $prop->title }}">{{ $prop->title }}</a>
                                                    <span class="text-[10px] text-slate-400 block truncate"><i class="fa-solid fa-location-dot mr-1"></i>{{ $prop->location }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="block text-slate-800">{{ $prop->category->name ?? 'N/A' }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $prop->type }}</span>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap font-bold text-primary">{{ $prop->price_label }}</td>
                                            <td class="px-5 py-4 whitespace-nowrap">{{ $prop->area }} m²</td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                @if($prop->status === 'approved')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Hiển thị</span>
                                                @elseif($prop->status === 'pending')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                                                @elseif($prop->status === 'rejected')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200" title="Bị từ chối">Từ chối</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-700 border border-slate-200">Đang ẩn</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                                <div class="inline-flex items-center space-x-2">
                                                    <a href="{{ route('properties.edit', $prop->id) }}" class="p-1.5 text-slate-500 hover:text-primary transition" title="Chỉnh sửa">
                                                        <i class="fa-solid fa-pen text-sm"></i>
                                                    </a>
                                                    <form action="{{ route('properties.extend', $prop->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn gia hạn tin đăng này để đưa lên đầu trang?');">
                                                        @csrf
                                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-green-600 transition" title="Gia hạn (Đẩy lên đầu)">
                                                            <i class="fa-solid fa-arrow-up-from-bracket text-sm"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('properties.hide', $prop->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-amber-500 transition" title="{{ $prop->status === 'rented' ? 'Hiện tin đăng' : 'Ẩn tin đăng' }}">
                                                            @if($prop->status === 'rented')
                                                                <i class="fa-solid fa-eye text-sm"></i>
                                                            @else
                                                                <i class="fa-solid fa-eye-slash text-sm"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('properties.destroy', $prop->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin đăng này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-red-650 transition" title="Xóa">
                                                            <i class="fa-solid fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Empty states for sub-tabs -->
                                    <tr x-show="subTab === 'sale' && {{ $saleProperties->isEmpty() ? 'true' : 'false' }}" x-cloak>
                                        <td colspan="6" class="px-5 py-12 text-center text-slate-500 bg-slate-50 border-t border-slate-100">
                                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400 text-base">
                                                <i class="fa-solid fa-folder-open"></i>
                                            </div>
                                            <p class="text-xs font-bold">Bạn chưa đăng tin bán nào.</p>
                                        </td>
                                    </tr>
                                    <tr x-show="subTab === 'rent' && {{ $rentProperties->isEmpty() ? 'true' : 'false' }}" x-cloak>
                                        <td colspan="6" class="px-5 py-12 text-center text-slate-500 bg-slate-50 border-t border-slate-100">
                                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400 text-base">
                                                <i class="fa-solid fa-folder-open"></i>
                                            </div>
                                            <p class="text-xs font-bold">Bạn chưa đăng tin cho thuê nào.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @endif

                <!-- TAB 2.3: Favorites (All roles) -->
                <div x-show="activeTab === 'favorites'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Tin yêu thích đã lưu</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Xem danh sách các căn hộ, biệt thự bạn đã lưu yêu thích để dễ dàng tham khảo lại.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($properties->isEmpty())
                            <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                    <i class="fa-solid fa-heart-crack"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-500">Bạn chưa lưu tin yêu thích nào.</p>
                            </div>
                        @else
                            @foreach($properties as $property)
                                @include('components.property-card', ['property' => $property])
                            @endforeach
                        @endif
                    </div>
                </div>

                @if(Auth::user()->role !== 'admin')
                <!-- TAB 3: Appointments (Dynamic for Owner and Tenant) -->
                <div x-show="activeTab === 'appointments'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">
                            {{ Auth::user()->role === 'owner' ? 'Lịch hẹn khách đặt xem nhà' : 'Lịch hẹn xem nhà đã đặt' }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">
                            {{ Auth::user()->role === 'owner' ? 'Quản lý danh sách và phê duyệt lịch đi xem nhà của khách hàng.' : 'Theo dõi danh sách và trạng thái lịch đi xem nhà trực tiếp của bạn.' }}
                        </p>
                    </div>

                    <div 
                        x-data="{
                            rejectOpen: false,
                            rejectUrl: '',
                            openRejectModal(actionUrl) {
                                this.rejectUrl = actionUrl;
                                this.rejectOpen = true;
                            }
                        }"
                    >
                        <!-- Reject Reason Modal (Only for Owner) -->
                        @if(Auth::user()->role === 'owner')
                        <div 
                            x-show="rejectOpen" 
                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                            x-transition
                            x-cloak
                        >
                            <div @click.away="rejectOpen = false" class="bg-white rounded-3xl border border-slate-150 shadow-2xl p-6 w-full max-w-md text-left">
                                <h3 class="text-base font-bold text-slate-800 mb-2">Từ chối lịch hẹn xem nhà</h3>
                                <p class="text-xs text-slate-400 font-semibold mb-4 leading-normal">Vui lòng nhập lý do từ chối để thông báo cho khách hàng.</p>
                                
                                <form :action="rejectUrl" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <textarea 
                                            name="reject_reason" 
                                            required
                                            rows="3" 
                                            placeholder="Ví dụ: Căn hộ hiện tại đang được sửa chữa, Chủ nhà bận đột xuất..."
                                            class="w-full p-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                        ></textarea>
                                    </div>
                                    <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                                        <button type="button" @click="rejectOpen = false" class="px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer">Hủy</button>
                                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-650 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer">Xác nhận từ chối</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        <!-- List Appointments -->
                        @if(Auth::user()->role === 'owner')
                            @if($ownerAppointments->isEmpty())
                                <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-500">Chưa có khách đặt lịch hẹn xem nhà nào.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto border border-slate-150/80 rounded-2xl shadow-sm bg-white">
                                    <table class="min-w-full divide-y divide-slate-100 text-left">
                                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <tr>
                                                <th scope="col" class="px-5 py-4">Khách hàng</th>
                                                <th scope="col" class="px-5 py-4">Ngày giờ hẹn</th>
                                                <th scope="col" class="px-5 py-4">Bất động sản</th>
                                                <th scope="col" class="px-5 py-4">Trạng thái</th>
                                                <th scope="col" class="px-5 py-4 text-right">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-semibold">
                                            @foreach($ownerAppointments as $app)
                                                <tr>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <span class="block text-slate-800 font-bold">{{ $app->name }}</span>
                                                        <span class="text-[10px] text-slate-400"><i class="fa-solid fa-phone mr-1"></i>{{ $app->phone }}</span>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <span class="block text-slate-800">{{ \Carbon\Carbon::parse($app->date)->format('d/m/Y') }}</span>
                                                        <span class="text-[10px] text-slate-400"><i class="fa-solid fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($app->time)->format('H:i') }}</span>
                                                    </td>
                                                    <td class="px-5 py-4 max-w-[200px] truncate">
                                                        <a href="/property/{{ $app->property->id }}" class="hover:text-primary font-bold text-slate-800 block truncate" title="{{ $app->property->title }}">{{ $app->property->title }}</a>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        @if($app->status === 'approved')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Đã duyệt</span>
                                                        @elseif($app->status === 'pending')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                                                        @elseif($app->status === 'rejected')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200" title="Lý do: {{ $app->reject_reason }}">Từ chối</span>
                                                        @elseif($app->status === 'completed')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Đã xem nhà</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                                        @if($app->status === 'pending')
                                                            <div class="inline-flex items-center space-x-2">
                                                                <form action="{{ route('appointments.approve', $app->id) }}" method="POST" class="inline-block">
                                                                    @csrf
                                                                    <button type="submit" class="px-2.5 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer">
                                                                        Duyệt
                                                                    </button>
                                                                </form>
                                                                <button type="button" @click="openRejectModal('{{ route('appointments.reject', $app->id) }}')" class="px-2.5 py-1.5 bg-red-500 hover:bg-red-650 text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer">
                                                                    Từ chối
                                                                </button>
                                                            </div>
                                                        @elseif($app->status === 'approved')
                                                            <form action="{{ route('appointments.complete', $app->id) }}" method="POST" class="inline-block">
                                                                    @csrf
                                                                    <button type="submit" class="px-2.5 py-1.5 bg-primary hover:bg-primary-hover text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer">
                                                                        Đã xem nhà
                                                                    </button>
                                                            </form>
                                                        @else
                                                            <span class="text-[10px] text-slate-400">Không có thao tác</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @else
                            @if($appointments->isEmpty())
                                <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-500">Bạn chưa đặt lịch hẹn xem nhà nào.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto border border-slate-150/80 rounded-2xl shadow-sm bg-white">
                                    <table class="min-w-full divide-y divide-slate-100 text-left">
                                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            <tr>
                                                <th scope="col" class="px-5 py-4">Mã số</th>
                                                <th scope="col" class="px-5 py-4">Ngày hẹn</th>
                                                <th scope="col" class="px-5 py-4">Bất động sản</th>
                                                <th scope="col" class="px-5 py-4">Chủ nhà</th>
                                                <th scope="col" class="px-5 py-4">Trạng thái</th>
                                                <th scope="col" class="px-5 py-4 text-right">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-semibold">
                                            @foreach($appointments as $app)
                                                <tr>
                                                    <td class="px-5 py-4 text-slate-900 font-bold">#BK-{{ $app->id }}</td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <span class="block">{{ \Carbon\Carbon::parse($app->date)->format('d/m/Y') }}</span>
                                                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($app->time)->format('H:i') }}</span>
                                                    </td>
                                                    <td class="px-5 py-4 max-w-[200px] truncate">
                                                        <a href="/property/{{ $app->property->id }}" class="hover:text-primary font-bold text-slate-800">{{ $app->property->title }}</a>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">{{ $app->property->agent->name ?? 'N/A' }}</td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        @if($app->status === 'approved')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Đã xác nhận</span>
                                                        @elseif($app->status === 'pending')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                                                        @elseif($app->status === 'rejected')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200" title="Lý do từ chối: {{ $app->reject_reason }}">Đã từ chối</span>
                                                        @elseif($app->status === 'completed')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Đã xem nhà</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                                        @if($app->status === 'pending' || $app->status === 'approved')
                                                            <form action="{{ route('appointments.cancel', $app->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn xem nhà này không?');">
                                                                @csrf
                                                                <button type="submit" class="px-2.5 py-1.5 bg-red-500 hover:bg-red-650 text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer">
                                                                    Hủy lịch
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="text-[10px] text-slate-400">Không có thao tác</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif


            </div>
            
        </div>
    </div>
</div>
@endsection
