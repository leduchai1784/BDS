@extends('layouts.app')

@section('title', 'Dashboard Quản Lý Thành Viên | BDS Rental')

@section('content')
<!-- Dashboard Wrapper with AlpineJS -->
<div 
    x-data="{ 
        activeTab: '{{ request('tab') ?? 'profile' }}', 
        activeSubTab: '{{ request('subtab') ?? ($errors->has('current_password') || $errors->has('new_password') ? 'password' : ($errors->has('avatar') ? 'avatar' : ($errors->has('id_number') || $errors->has('id_date') || $errors->has('id_place') || $errors->has('cccd_front') || $errors->has('cccd_back') ? 'cccd' : 'info'))) }}',
        profileMenuOpen: false,
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
                            
                            <!-- Change Avatar Button -->
                            <button 
                                @click="activeTab = 'profile'; activeSubTab = 'avatar'; window.history.pushState(null, '', '?tab=profile&subtab=avatar');"
                                class="absolute bottom-0.5 left-0.5 w-6 h-6 rounded-full bg-white hover:bg-slate-50 text-slate-500 hover:text-primary border border-slate-200 shadow-md flex items-center justify-center cursor-pointer transition-all active:scale-90 z-10" 
                                title="Thay đổi ảnh đại diện"
                            >
                                <i class="fa-solid fa-camera text-[9px]"></i>
                            </button>
                        </div>

                        <!-- User Name & Role Badge -->
                        <div class="px-5">
                            <h4 class="text-base font-extrabold text-slate-800 leading-snug tracking-tight mb-2 flex items-center justify-center gap-1.5">
                                {{ $user['name'] }}
                                @if(!empty($user['id_number']))
                                    <span class="inline-flex items-center text-emerald-500" title="Tài khoản đã xác thực CCCD">
                                        <i class="fa-solid fa-circle-check text-sm"></i>
                                    </span>
                                @endif
                            </h4>
                            
                            <div class="flex flex-wrap items-center justify-center gap-2 mb-3">
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

                                @if(!empty($user['id_number']))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-500 text-white border border-emerald-600 shadow-sm" title="Tài khoản đã xác thực CCCD">
                                        <i class="fa-solid fa-circle-check mr-1.5"></i>
                                        Đã xác thực
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm" title="Chưa xác thực CCCD">
                                        <i class="fa-solid fa-circle-question mr-1.5 text-slate-400"></i>
                                        Chưa xác thực
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-center text-[10px] text-slate-400 font-semibold space-x-1.5">
                                <i class="fa-solid fa-calendar-days text-slate-355"></i>
                                <span>Thành viên từ: {{ $user['join_date'] }}</span>
                            </div>
                        </div>
                    </div>

                    <nav class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-y-auto lg:max-h-[350px] scrollbar-thin border-b lg:border-b-0 border-slate-100 lg:pb-8">
                        <!-- Dropdown wrapper for Profile -->
                        <div class="flex flex-col w-full">
                            <button 
                                @click="
                                    if (activeTab !== 'profile') {
                                        activeTab = 'profile';
                                        profileMenuOpen = true;
                                    } else {
                                        profileMenuOpen = !profileMenuOpen;
                                    }
                                    window.history.pushState(null, '', '?tab=profile');
                                " 
                                :class="activeTab === 'profile' ? 'bg-primary-light text-primary border-primary font-extrabold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                                class="flex items-center justify-between px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap cursor-pointer transition focus:outline-none w-full text-left"
                            >
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-user-gear text-sm"></i>
                                    <span>Thông tin</span>
                                </div>
                                <i :class="activeTab === 'profile' && profileMenuOpen ? 'rotate-180 text-primary' : 'text-slate-400'" class="fa-solid fa-chevron-down text-[10px] hidden lg:inline-block transition-transform duration-200 ml-2"></i>
                            </button>
                            
                            <!-- Subtabs list (Desktop only) -->
                            <div 
                                x-show="activeTab === 'profile' && profileMenuOpen" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="profile-subtabs-desktop pl-9 pr-4 py-2 space-y-1.5 bg-slate-50/40 border-l border-slate-100/80"
                            >
                                <div class="flex flex-col space-y-1.5">
                                    <button 
                                        @click="activeSubTab = 'info'; window.history.pushState(null, '', '?tab=profile&subtab=info');"
                                        :class="activeSubTab === 'info' ? 'text-primary font-black bg-primary-light/40' : 'text-slate-500 font-semibold hover:text-primary hover:bg-slate-50'"
                                        class="flex items-center space-x-2.5 px-3 py-2 rounded-xl text-[11px] cursor-pointer transition focus:outline-none text-left w-full"
                                    >
                                        <i class="fa-solid fa-user text-[10px]"></i>
                                        <span>Thông tin cá nhân</span>
                                    </button>
                                    
                                    <button 
                                        @click="activeSubTab = 'password'; window.history.pushState(null, '', '?tab=profile&subtab=password');"
                                        :class="activeSubTab === 'password' ? 'text-primary font-black bg-primary-light/40' : 'text-slate-500 font-semibold hover:text-primary hover:bg-slate-50'"
                                        class="flex items-center space-x-2.5 px-3 py-2 rounded-xl text-[11px] cursor-pointer transition focus:outline-none text-left w-full"
                                    >
                                        <i class="fa-solid fa-key text-[10px]"></i>
                                        <span>Đổi mật khẩu</span>
                                    </button>
                                    
                                    <button 
                                        @click="activeSubTab = 'cccd'; window.history.pushState(null, '', '?tab=profile&subtab=cccd');"
                                        :class="activeSubTab === 'cccd' ? 'text-primary font-black bg-primary-light/40' : 'text-slate-500 font-semibold hover:text-primary hover:bg-slate-50'"
                                        class="flex items-center space-x-2.5 px-3 py-2 rounded-xl text-[11px] cursor-pointer transition focus:outline-none text-left w-full"
                                    >
                                        <i class="fa-solid fa-id-card text-[10px]"></i>
                                        <span>Xác thực CCCD</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
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
                                <span>Lịch hẹn</span>
                            </div>
                            <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                {{ $stats['total_appointments'] }}
                            </span>
                        </button>



                        <button 
                            @click="activeTab = 'marketing'; window.history.pushState(null, '', '?tab=marketing');" 
                            :class="activeTab === 'marketing' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                                <span>AI Content Studio</span>
                            </div>
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
                                <span>Lịch hẹn</span>
                            </div>
                            <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                {{ $stats['total_appointments'] }}
                            </span>
                        </button>

                        <button 
                            @click="activeTab = 'register_owner'; window.history.pushState(null, '', '?tab=register_owner');" 
                            :class="activeTab === 'register_owner' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                            class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                        >
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-user-tie text-sm"></i>
                                <span>Đăng ký làm chủ nhà</span>
                            </div>
                        </button>
                        @endif



                        @if(Auth::user()->role === 'admin')
                            <!-- Quản lý thành viên -->
                            <button 
                                @click="activeTab = 'admin_users'; window.history.pushState(null, '', '?tab=admin_users');" 
                                :class="activeTab === 'admin_users' ? 'bg-primary-light text-primary border-primary font-bold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                                class="w-full text-left flex items-center justify-between space-x-3 px-5 py-3.5 text-xs border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                            >
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-users text-sm"></i>
                                    <span>Quản lý thành viên</span>
                                </div>
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                    {{ isset($adminUsers) ? count($adminUsers) : 0 }}
                                </span>
                            </button>

                            <!-- Quản lý tin đăng -->
                            <button 
                                @click="activeTab = 'admin_properties'; window.history.pushState(null, '', '?tab=admin_properties');" 
                                :class="activeTab === 'admin_properties' ? 'bg-primary-light text-primary border-primary font-bold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                                class="w-full text-left flex items-center justify-between space-x-3 px-5 py-3.5 text-xs border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                            >
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-rectangle-list text-sm"></i>
                                    <span>Quản lý tin đăng</span>
                                </div>
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                    {{ isset($adminProperties) ? count($adminProperties) : 0 }}
                                </span>
                            </button>

                            <!-- Quản lý lịch hẹn -->
                            <button 
                                @click="activeTab = 'admin_appointments'; window.history.pushState(null, '', '?tab=admin_appointments');" 
                                :class="activeTab === 'admin_appointments' ? 'bg-primary-light text-primary border-primary font-bold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                                class="w-full text-left flex items-center justify-between space-x-3 px-5 py-3.5 text-xs border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                            >
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-calendar-check text-sm"></i>
                                    <span>Quản lý lịch hẹn</span>
                                </div>
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                    {{ isset($adminAppointments) ? count($adminAppointments) : 0 }}
                                </span>
                            </button>

                            <!-- Quản lý Lead -->
                            <button 
                                @click="activeTab = 'leads'; window.history.pushState(null, '', '?tab=leads');" 
                                :class="activeTab === 'leads' ? 'bg-primary-light text-primary border-primary font-bold' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                                class="w-full text-left flex items-center justify-between space-x-3 px-5 py-3.5 text-xs border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                            >
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-user-group text-sm"></i>
                                    <span>Quản lý Lead</span>
                                </div>
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">
                                    {{ $stats['total_leads'] ?? 4 }}
                                </span>
                            </button>
                        @endif


                    </nav>

                    <!-- Subtabs for Mobile (shown below the main nav scrollbar on mobile, hidden on desktop) -->
                    <div 
                        x-show="activeTab === 'profile'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-16"
                        class="flex lg:hidden flex-row overflow-x-auto scrollbar-none border-t border-slate-100/70 p-2.5 gap-2 bg-slate-50/50 whitespace-nowrap w-full"
                    >
                        <button 
                            @click="activeSubTab = 'info'; window.history.pushState(null, '', '?tab=profile&subtab=info');"
                            :class="activeSubTab === 'info' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:text-primary'"
                            class="flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full text-[10px] font-bold transition cursor-pointer"
                        >
                            <i class="fa-solid fa-user text-[9px]"></i>
                            <span>Thông tin cá nhân</span>
                        </button>
                        
                        <button 
                            @click="activeSubTab = 'password'; window.history.pushState(null, '', '?tab=profile&subtab=password');"
                            :class="activeSubTab === 'password' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:text-primary'"
                            class="flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full text-[10px] font-bold transition cursor-pointer"
                        >
                            <i class="fa-solid fa-key text-[9px]"></i>
                            <span>Đổi mật khẩu</span>
                        </button>
                        
                        <button 
                            @click="activeSubTab = 'cccd'; window.history.pushState(null, '', '?tab=profile&subtab=cccd');"
                            :class="activeSubTab === 'cccd' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:text-primary'"
                            class="flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full text-[10px] font-bold transition cursor-pointer"
                        >
                            <i class="fa-solid fa-id-card text-[9px]"></i>
                            <span>Xác thực CCCD</span>
                        </button>
                    </div>
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
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn</span>
                                <span class="text-xl font-black text-slate-800">{{ $stats['total_appointments'] }} cuộc</span>
                            </div>
                        </div>
                    </div>
                    @endif



                    <!-- Sub-tab 1: Personal Info -->
                    <div x-show="activeSubTab === 'info'" class="space-y-6" x-cloak>
                        <form 
                            action="{{ route('profile.update') }}"
                            method="POST"
                            class="space-y-6"
                            x-data="{
                                provinces: [],
                                wards: [],
                                selectedProvinceId: null,
                                selectedWardId: null,
                                selectedProvince: '',
                                provinceSearch: '',
                                selectedWard: '',
                                wardSearch: '',
                                isEditing: {{ $errors->any() ? 'true' : 'false' }},
                                loadWards(provinceId) {
                                    if (!provinceId) { this.wards = []; return; }
                                    fetch('/locations/wards?province_id=' + provinceId)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.wards = data;
                                            // Re-match saved ward by ID (numeric) or by title (legacy text)
                                            const savedWard = '{{ old('add_ward', $user['add_ward'] ?? '') }}';
                                            if (savedWard) {
                                                const isId = /^\d+$/.test(savedWard);
                                                const match = isId
                                                    ? data.find(w => String(w.id) === savedWard)
                                                    : data.find(w => w.title.toLowerCase() === savedWard.toLowerCase());
                                                if (match) {
                                                    this.selectedWard   = match.title;
                                                    this.wardSearch     = match.title;
                                                    this.selectedWardId = match.id;
                                                }
                                            }
                                        })
                                        .catch(err => console.error('Error loading wards:', err));
                                },
                                selectProvince(p) {
                                    this.selectedProvince   = p.title;
                                    this.provinceSearch     = p.title;
                                    this.selectedProvinceId = p.id;
                                    this.selectedWard   = '';
                                    this.wardSearch     = '';
                                    this.selectedWardId = null;
                                    this.wards = [];
                                    this.loadWards(p.id);
                                },
                                init() {
                                    const savedProvince = '{{ old('add_province', $user['add_province'] ?? '') }}';
                                    fetch('/locations/provinces')
                                        .then(res => res.json())
                                        .then(data => {
                                            this.provinces = data;
                                            if (savedProvince) {
                                                // Match by numeric ID (new format) or by title text (legacy)
                                                const isId = /^\d+$/.test(savedProvince);
                                                const match = isId
                                                    ? data.find(p => String(p.id) === savedProvince)
                                                    : data.find(p => p.title.toLowerCase() === savedProvince.toLowerCase());
                                                if (match) {
                                                    this.selectedProvince   = match.title;
                                                    this.provinceSearch     = match.title;
                                                    this.selectedProvinceId = match.id;
                                                    this.loadWards(match.id);
                                                }
                                            }
                                        })
                                        .catch(err => console.error('Error loading provinces:', err));
                                }
                            }"
                        >
                            @csrf
                            
                            <!-- Grid 1: Basic Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Họ và tên -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="name"
                                            value="{{ old('name', $user['name']) }}"
                                            required
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                        >
                                    </div>
                                    @error('name')
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
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none transition cursor-pointer disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                        >
                                            <option value="0" {{ old('gender', $user['gender']) == 0 ? 'selected' : '' }}>Nam</option>
                                            <option value="1" {{ old('gender', $user['gender']) == 1 ? 'selected' : '' }}>Nữ</option>
                                            <option value="2" {{ old('gender', $user['gender']) == 2 ? 'selected' : '' }}>Khác</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Grid 2: Contact Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- SĐT -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="tel" 
                                            name="phone"
                                            value="{{ old('phone', $user['phone']) }}"
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
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
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                        >
                                    </div>
                                    @error('email')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Grid 3: Dob & Website -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Ngày sinh -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày sinh</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-cake-candles absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs z-10"></i>
                                        @php
                                            $dobVal = '';
                                            if (!empty($user['dob'])) {
                                                try {
                                                    $dobVal = \Carbon\Carbon::createFromFormat('d/m/Y', $user['dob'])->format('Y-m-d');
                                                } catch (\Exception $e) {
                                                    $dobVal = $user['dob'];
                                                }
                                            }
                                            $dobOld = old('dob', $dobVal);
                                            if (!empty($dobOld) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dobOld)) {
                                                try {
                                                    $dobOld = \Carbon\Carbon::createFromFormat('d/m/Y', $dobOld)->format('Y-m-d');
                                                } catch (\Exception $e) {}
                                            }
                                        @endphp
                                        <input 
                                            type="date" 
                                            name="dob"
                                            value="{{ $dobOld }}"
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                        >
                                    </div>
                                    @error('dob')
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
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                        >
                                    </div>
                                    @error('website')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Grid 4: Address Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                <!-- Tỉnh / Thành -->
                                <div class="space-y-1" x-data="{ open: false }" @click.outside="
                                    if (!provinceSearch) {
                                        selectedProvince = '';
                                        provinceSearch = '';
                                        selectedWard = '';
                                        wardSearch = '';
                                    } else {
                                        const found = provinces.find(p => p.title.toLowerCase() === provinceSearch.toLowerCase());
                                        if (found) {
                                            selectedProvince = found.title;
                                            provinceSearch = found.title;
                                        } else {
                                            provinceSearch = selectedProvince;
                                        }
                                    }
                                    open = false;
                                ">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh / Thành phố</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-map-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        {{-- Hidden input gửi province ID lên server --}}
                                        <input type="hidden" name="add_province" :value="selectedProvinceId">
                                        <input type="hidden" name="province" :value="selectedProvince">
                                        <input 
                                            type="text" 
                                            placeholder="Chọn Tỉnh/Thành phố..."
                                            x-model="provinceSearch"
                                            @focus="open = true; $el.select()"
                                            @input="open = true"
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer text-left disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                        >
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                                        
                                        <!-- Dropdown Panel -->
                                        <div 
                                            x-show="open" 
                                            class="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left"
                                            x-cloak
                                        >
                                            <template x-for="p in provinces.filter(prov => !provinceSearch || prov.title.toLowerCase().includes(provinceSearch.toLowerCase()))" :key="p.id">
                                                <div 
                                                    @click="
                                                        selectProvince(p);
                                                        open = false;
                                                    "
                                                    class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                                                    x-text="p.title"
                                                ></div>
                                            </template>
                                            <div x-show="provinces.filter(prov => !provinceSearch || prov.title.toLowerCase().includes(provinceSearch.toLowerCase())).length === 0" class="px-4 py-2.5 text-xs text-slate-400 font-semibold">
                                                Không tìm thấy kết quả
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phường / Xã -->
                                <div class="space-y-1" x-data="{ open: false }" @click.outside="
                                    if (!wardSearch) {
                                        selectedWard = '';
                                        wardSearch = '';
                                    } else {
                                        const found = wards ? wards.find(w => w.title.toLowerCase() === wardSearch.toLowerCase()) : null;
                                        if (found) {
                                            selectedWard = found.title;
                                            wardSearch = found.title;
                                        } else {
                                            wardSearch = selectedWard;
                                        }
                                    }
                                    open = false;
                                ">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường / Xã</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-tree-city absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        {{-- Hidden input gửi ward ID lên server --}}
                                        <input type="hidden" name="add_ward" :value="selectedWardId">
                                        <input type="hidden" name="ward" :value="selectedWard">
                                        <input 
                                            type="text" 
                                            placeholder="Chọn Phường/Xã..."
                                            x-model="wardSearch"
                                            @focus="open = true; $el.select()"
                                            @input="open = true"
                                            :disabled="!isEditing || !selectedProvince"
                                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition cursor-pointer text-left disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>

                                        <!-- Dropdown Panel -->
                                        <div 
                                            x-show="open" 
                                            class="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left"
                                            x-cloak
                                        >
                                            <template x-for="w in wards.filter(ward => !wardSearch || ward.title.toLowerCase().includes(wardSearch.toLowerCase()))" :key="w.id">
                                                <div 
                                                    @click="
                                                        selectedWard   = w.title;
                                                        wardSearch     = w.title;
                                                        selectedWardId = w.id;
                                                        open = false;
                                                    "
                                                    class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                                                    x-text="w.title"
                                                ></div>
                                            </template>
                                            <div x-show="wards.filter(ward => !wardSearch || ward.title.toLowerCase().includes(wardSearch.toLowerCase())).length === 0" class="px-4 py-2.5 text-xs text-slate-400 font-semibold">
                                                Không tìm thấy kết quả
                                            </div>
                                        </div>
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
                                            :disabled="!isEditing"
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
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
                                    :disabled="!isEditing"
                                    class="w-full p-3.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition disabled:opacity-65 disabled:bg-slate-100/70 disabled:cursor-not-allowed"
                                >{{ old('intro', $user['intro']) }}</textarea>
                            </div>

                            <!-- Submit -->
                            <!-- Action Buttons -->
                            <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                                <!-- Chỉnh sửa -->
                                <button 
                                    type="button" 
                                    x-show="!isEditing"
                                    @click="isEditing = true"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition cursor-pointer active:scale-98 min-w-[130px]"
                                >
                                    <i class="fa-solid fa-pen-to-square mr-2 text-xs"></i>Chỉnh sửa
                                </button>

                                 <!-- Hủy bỏ -->
                                <button 
                                    type="button" 
                                    x-show="isEditing"
                                    @click="
                                        isEditing = false;
                                        $el.form.reset();
                                        // Reset Alpine province/ward state (form.reset() won't trigger x-model)
                                        provinceSearch = selectedProvince = '';
                                        wardSearch = selectedWard = '';
                                        selectedProvinceId = null;
                                        selectedWardId = null;
                                        wards = [];
                                    "
                                    class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer active:scale-98 min-w-[100px]"
                                >
                                    Hủy
                                </button>

                                <!-- Lưu thay đổi -->
                                <button 
                                    type="submit" 
                                    x-show="isEditing"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
                                >
                                    <i class="fa-solid fa-floppy-disk mr-2"></i>Lưu thay đổi
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
                            x-data="{
                                passwordLength: 8,
                                hasEightChars: true,
                                useUpper: true,
                                useLower: true,
                                useNumbers: true,
                                useSpecial: true,
                                savedConfirm: false,
                                generatedPassText: '',
                                showGenPass: true,
                                genCopied: false,
                                newPassword: '',
                                showPassword: false,
                                copied: false,
                                openGen: false,
                                
                                generateRandomPassword() {
                                    let charset = '';
                                    if (this.useUpper) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                    if (this.useLower) charset += 'abcdefghijklmnopqrstuvwxyz';
                                    if (this.useNumbers) charset += '0123456789';
                                    if (this.useSpecial) charset += '!@#$%^&*()_+~|{}[]:;?';
                                    
                                    if (charset.length === 0) {
                                        this.useLower = true;
                                        this.useNumbers = true;
                                        charset = 'abcdefghijklmnopqrstuvwxyz0123456789';
                                    }
                                    
                                    let password = '';
                                    let guaranteed = [];
                                    if (this.useUpper) guaranteed.push('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.charAt(Math.floor(Math.random() * 26)));
                                    if (this.useLower) guaranteed.push('abcdefghijklmnopqrstuvwxyz'.charAt(Math.floor(Math.random() * 26)));
                                    if (this.useNumbers) guaranteed.push('0123456789'.charAt(Math.floor(Math.random() * 10)));
                                    if (this.useSpecial) {
                                        const specials = '!@#$%^&*()_+~|{}[]:;?';
                                        guaranteed.push(specials.charAt(Math.floor(Math.random() * specials.length)));
                                    }

                                    let remainingLength = this.passwordLength - guaranteed.length;
                                    if (remainingLength < 0) {
                                        guaranteed = guaranteed.slice(0, this.passwordLength);
                                        remainingLength = 0;
                                    }

                                    for (let i = 0; i < remainingLength; i++) {
                                        password += charset.charAt(Math.floor(Math.random() * charset.length));
                                    }
                                    
                                    let finalPasswordArray = guaranteed.concat(password.split(''));
                                    for (let i = finalPasswordArray.length - 1; i > 0; i--) {
                                        const j = Math.floor(Math.random() * (i + 1));
                                        [finalPasswordArray[i], finalPasswordArray[j]] = [finalPasswordArray[j], finalPasswordArray[i]];
                                    }
                                    
                                    this.generatedPassText = finalPasswordArray.join('');
                                    this.savedConfirm = false;
                                },
                                copyGenPass() {
                                    navigator.clipboard.writeText(this.generatedPassText);
                                    this.genCopied = true;
                                    setTimeout(() => this.genCopied = false, 2000);
                                },
                                applyGeneratedPassword() {
                                    this.newPassword = this.generatedPassText;
                                    this.showPassword = true;
                                    
                                    const confirmInput = document.getElementById('new_password_confirmation');
                                    if (confirmInput) {
                                        confirmInput.value = this.generatedPassText;
                                    }
                                },
                                copyToClipboard() {
                                    navigator.clipboard.writeText(this.newPassword);
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2000);
                                }
                            }"
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
                                <div class="flex justify-between items-center px-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Mật khẩu mới</label>
                                    
                                    <!-- Popover Dropdown for Generating Password -->
                                    <div class="relative">
                                        <button 
                                            type="button" 
                                            @click="openGen = !openGen; if(openGen) { generateRandomPassword(); }"
                                            class="text-[10px] font-bold text-primary hover:text-primary-hover flex items-center gap-1 transition cursor-pointer"
                                        >
                                            <i class="fa-solid fa-wand-magic-sparkles"></i> Mật khẩu ngẫu nhiên
                                        </button>
                                        
                                        <!-- The Popover Panel -->
                                        <div 
                                            x-show="openGen"
                                            @click.outside="openGen = false"
                                            x-transition
                                            class="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-slate-200 shadow-2xl p-3.5 z-50 text-left space-y-2.5 select-none"
                                            x-cloak
                                        >
                                            <div class="flex justify-between items-center pb-1.5 border-b border-slate-100">
                                                <span class="text-xs font-bold text-primary flex items-center gap-1.5">
                                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Password
                                                </span>
                                                <button type="button" @click="openGen = false" class="text-slate-400 hover:text-slate-650 text-xs">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Password Display Area -->
                                            <div class="relative bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 flex items-center justify-between min-h-[38px]">
                                                <span x-text="generatedPassText ? (showGenPass ? generatedPassText : '•'.repeat(generatedPassText.length)) : '***'" class="text-xs font-mono font-bold text-slate-700 tracking-wide break-all select-all"></span>
                                                <div class="flex items-center space-x-1.5 flex-shrink-0 ml-2">
                                                    <button type="button" @click="showGenPass = !showGenPass" class="text-slate-400 hover:text-slate-605 p-0.5" title="Hiện/Ẩn">
                                                        <i class="fa-solid text-[10px]" :class="showGenPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                    </button>
                                                    <button type="button" x-show="generatedPassText" @click="copyGenPass()" class="text-slate-400 hover:text-slate-605 p-0.5" title="Sao chép">
                                                        <i class="fa-solid text-[10px]" :class="genCopied ? 'fa-check text-green-500' : 'fa-copy'"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Length control slider -->
                                            <div class="space-y-1">
                                                <div class="flex justify-between items-center text-[9px] font-extrabold uppercase text-slate-400">
                                                    <span>Số lượng ký tự</span>
                                                    <span x-text="passwordLength" class="text-xs font-bold text-slate-700"></span>
                                                </div>
                                                <input 
                                                    type="range" 
                                                    min="8" 
                                                    max="32" 
                                                    x-model="passwordLength" 
                                                    @input="generateRandomPassword()"
                                                    class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary"
                                                >
                                            </div>

                                            <!-- Checkboxes -->
                                            <div class="space-y-1.5 text-[10px] font-bold text-slate-600 pt-0.5">
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" x-model="useNumbers" @change="generateRandomPassword()" class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5">
                                                    <span>Có ký tự số</span>
                                                </label>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" x-model="useLower" @change="generateRandomPassword()" class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5">
                                                    <span>Có ký tự thường</span>
                                                </label>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" x-model="useUpper" @change="generateRandomPassword()" class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5">
                                                    <span>Có ký tự hoa</span>
                                                </label>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" x-model="useSpecial" @change="generateRandomPassword()" class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5">
                                                    <span>Có ký tự đặc biệt</span>
                                                </label>
                                            </div>

                                            <!-- Save check checkbox -->
                                            <div class="pt-1.5 border-t border-slate-100">
                                                <label class="flex items-start space-x-2 cursor-pointer text-[10px] font-bold text-slate-600">
                                                    <input type="checkbox" x-model="savedConfirm" class="rounded border-slate-300 text-primary focus:ring-primary h-3.5 w-3.5 mt-0.5">
                                                    <span class="leading-tight">Tôi đã lưu lại mật khẩu mới</span>
                                                </label>
                                            </div>

                                            <!-- Submit (Apply) Button -->
                                            <button 
                                                type="button" 
                                                :disabled="!savedConfirm || !generatedPassText"
                                                @click="if (savedConfirm && generatedPassText) { applyGeneratedPassword(); openGen = false; }"
                                                :class="(!savedConfirm || !generatedPassText) ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-primary text-white hover:bg-primary-hover active:scale-98 shadow-md'"
                                                class="w-full py-2.5 text-xs font-bold rounded-full transition cursor-pointer text-center"
                                            >
                                                Xác nhận
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        :type="showPassword ? 'text' : 'password'" 
                                        name="new_password"
                                        x-model="newPassword"
                                        required
                                        id="new_password"
                                        placeholder="Tối thiểu 8 ký tự..."
                                        class="w-full pl-10 pr-20 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                    >
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center space-x-1">
                                        <!-- Show/Hide Button -->
                                        <button 
                                            type="button" 
                                            @click="showPassword = !showPassword"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-650 transition cursor-pointer"
                                            title="Hiện/Ẩn mật khẩu"
                                        >
                                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                        <!-- Copy Button -->
                                        <button 
                                            type="button" 
                                            x-show="newPassword.length > 0"
                                            @click="copyToClipboard()"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-650 transition cursor-pointer"
                                            title="Sao chép"
                                        >
                                            <i class="fa-solid" :class="copied ? 'fa-check text-green-500' : 'fa-copy'"></i>
                                        </button>
                                    </div>
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
                                        :type="showPassword ? 'text' : 'password'" 
                                        name="new_password_confirmation"
                                        required
                                        id="new_password_confirmation"
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
                            id="avatar-form"
                            action="{{ route('profile.avatar') }}"
                            method="POST"
                            x-data="{ 
                                hasImage: false,
                                isEditingAvatar: {{ $errors->has('avatar') ? 'true' : 'false' }}
                            }"
                            class="space-y-6"
                        >
                            @csrf
                            <!-- Hidden input to hold the cropped base64 image data -->
                            <input type="hidden" name="avatar" id="cropped-avatar-input">

                            <!-- READ-ONLY AVATAR VIEW -->
                            <div x-show="!isEditingAvatar" class="flex flex-col items-center justify-center space-y-6 py-10 bg-slate-50 rounded-3xl border border-slate-100 px-6">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-200">
                                    <img src="{{ $user['avatar'] }}" alt="Avatar preview" class="w-full h-full object-cover">
                                </div>
                                <div class="text-center space-y-2">
                                    <h4 class="text-sm font-bold text-slate-800">Ảnh đại diện hiện tại</h4>
                                    <p class="text-xs text-slate-400 max-w-xs leading-normal">Hình ảnh được sử dụng để nhận diện tài khoản của bạn trên hệ thống.</p>
                                    <button type="button" @click="isEditingAvatar = true" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-pen-to-square mr-2"></i> Thay đổi ảnh đại diện
                                    </button>
                                </div>
                            </div>

                            <!-- EDITABLE AVATAR VIEW -->
                            <div x-show="isEditingAvatar" class="space-y-6" x-cloak>
                                <div class="flex flex-col md:flex-row items-center justify-center gap-8 py-8 bg-slate-50 rounded-3xl border border-slate-100 px-6">
                                    <!-- Left side: Interactive Area (original avatar or Cropper container) -->
                                    <div class="flex flex-col items-center space-y-4 w-full max-w-sm">
                                        <!-- Current Avatar view (shows when no new image selected) -->
                                        <div x-show="!hasImage" class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-200 relative group">
                                            <img src="{{ $user['avatar'] }}" alt="Avatar preview" class="w-full h-full object-cover">
                                        </div>

                                        <!-- Cropper container (shows when a new image is selected) -->
                                        <div x-show="hasImage" class="w-full bg-slate-100 rounded-2xl overflow-hidden border border-slate-200" style="display: none;">
                                            <div class="avatar-crop-container flex justify-center items-center overflow-hidden h-64">
                                                <img id="cropper-image" src="" alt="Source image for cropping" class="max-w-full max-h-full">
                                            </div>
                                        </div>

                                        <!-- Action button for choosing files -->
                                        <div class="text-center space-y-2">
                                            <h4 class="text-sm font-bold text-slate-800">Chọn ảnh đại diện mới</h4>
                                            <p class="text-xs text-slate-400 max-w-xs leading-normal">Hỗ trợ định dạng JPG, PNG dung lượng dưới 5MB.</p>
                                            
                                            <div class="flex flex-wrap justify-center gap-3">
                                                <!-- Select Image Button -->
                                                <label class="inline-flex items-center justify-center px-4 py-2 border border-slate-200 hover:border-primary text-xs font-bold rounded-xl text-slate-700 hover:text-white bg-white hover:bg-primary shadow-sm transition cursor-pointer">
                                                    <i class="fa-solid fa-camera mr-2 text-xs"></i> Chọn ảnh
                                                    <input type="file" id="avatar-file-input" accept="image/*" class="hidden">
                                                </label>

                                                <!-- Cancel/Reset Selection Button -->
                                                <button 
                                                    type="button" 
                                                    x-show="hasImage" 
                                                    style="display: none;"
                                                    id="cancel-crop-btn"
                                                    class="inline-flex items-center justify-center px-4 py-2 border border-rose-200 hover:border-rose-500 text-xs font-bold rounded-xl text-rose-600 hover:text-white bg-white hover:bg-rose-500 shadow-sm transition cursor-pointer"
                                                >
                                                    Hủy chọn
                                                </button>
                                            </div>

                                            @error('avatar')
                                                <p class="text-red-500 text-[10px] font-bold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Right side: Cropper Controls & Cropped Preview (only shows when image is loaded) -->
                                    <div x-show="hasImage" class="flex flex-col items-center space-y-6 w-full max-w-xs border-t md:border-t-0 md:border-l border-slate-200/80 pt-6 md:pt-0 md:pl-8" style="display: none;">
                                        <!-- Cropped Preview Circle -->
                                        <div class="flex flex-col items-center space-y-2">
                                            <span class="text-xs font-bold text-slate-500">Xem trước kết quả</span>
                                            <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-slate-100">
                                                <!-- Preview element container for Cropper.js -->
                                                <div class="img-preview w-full h-full overflow-hidden rounded-full"></div>
                                            </div>
                                        </div>

                                        <!-- Navigation & Crop Controls -->
                                        <div class="flex flex-col space-y-3 w-full">
                                            <span class="text-xs font-bold text-slate-500 text-center">Công cụ thu phóng & xoay</span>
                                            
                                            <!-- Zoom & Pan Slider / Buttons -->
                                            <div class="flex justify-center gap-2">
                                                <!-- Zoom In -->
                                                <button type="button" id="btn-zoom-in" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-650 flex items-center justify-center transition border border-slate-200 shadow-sm" title="Phóng to">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-sm"></i>
                                                </button>
                                                
                                                <!-- Zoom Out -->
                                                <button type="button" id="btn-zoom-out" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-650 flex items-center justify-center transition border border-slate-200 shadow-sm" title="Thu nhỏ">
                                                    <i class="fa-solid fa-magnifying-glass-minus text-sm"></i>
                                                </button>

                                                <!-- Rotate Left -->
                                                <button type="button" id="btn-rotate-left" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-650 flex items-center justify-center transition border border-slate-200 shadow-sm" title="Xoay trái">
                                                    <i class="fa-solid fa-rotate-left text-sm"></i>
                                                </button>

                                                <!-- Rotate Right -->
                                                <button type="button" id="btn-rotate-right" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-650 flex items-center justify-center transition border border-slate-200 shadow-sm" title="Xoay phải">
                                                    <i class="fa-solid fa-rotate-right text-sm"></i>
                                                </button>

                                                <!-- Reset -->
                                                <button type="button" id="btn-reset" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-rose-500 hover:text-white text-slate-650 flex items-center justify-center transition border border-slate-200 shadow-sm" title="Đặt lại">
                                                    <i class="fa-solid fa-arrows-rotate text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                                    <button type="button" @click="isEditingAvatar = false; if (hasImage) { document.getElementById('cancel-crop-btn').click(); }" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-slate-500 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition cursor-pointer active:scale-98">
                                        Hủy bỏ
                                    </button>
                                    <button type="submit" id="save-avatar-btn" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu ảnh đại diện
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Sub-tab 4: CCCD Verification -->
                    <div x-show="activeSubTab === 'cccd'" class="space-y-6" x-cloak>
                        <form 
                            action="{{ route('profile.cccd') }}"
                            method="POST"
                            x-data="{
                                cccdFrontUrl: '{{ $user['cccd_front'] ? (str_starts_with($user['cccd_front'], 'http') ? $user['cccd_front'] : (str_starts_with(ltrim($user['cccd_front'], '/'), 'uploads/') ? asset(ltrim($user['cccd_front'], '/')) : 'https://data.nks.vn/storage/' . ltrim(str_replace('storage/', '', ltrim($user['cccd_front'], '/')), '/'))) : '' }}',
                                cccdBackUrl: '{{ $user['cccd_back'] ? (str_starts_with($user['cccd_back'], 'http') ? $user['cccd_back'] : (str_starts_with(ltrim($user['cccd_back'], '/'), 'uploads/') ? asset(ltrim($user['cccd_back'], '/')) : 'https://data.nks.vn/storage/' . ltrim(str_replace('storage/', '', ltrim($user['cccd_back'], '/')), '/'))) : '' }}',
                                isScanningFront: false,
                                isScanningBack: false,
                                isEditingCccd: {{ (!empty($user['id_number']) && !$errors->hasAny(['id_number', 'dob', 'id_date', 'id_place', 'pob', 'permanent_address', 'cccd_front', 'cccd_back'])) ? 'false' : 'true' }},
                                hasVerifiedCccd: {{ (!empty($user['id_number'])) ? 'true' : 'false' }},
                                
                                compressImage(file, callback) {
                                    const reader = new FileReader();
                                    reader.readAsDataURL(file);
                                    reader.onload = (e) => {
                                        const img = new Image();
                                        img.src = e.target.result;
                                        img.onload = () => {
                                            const canvas = document.createElement('canvas');
                                            const maxW = 1000;
                                            const maxH = 1000;
                                            let w = img.width;
                                            let h = img.height;
                                            if (w > h) {
                                                if (w > maxW) {
                                                    h = Math.round((h * maxW) / w);
                                                    w = maxW;
                                                }
                                            } else {
                                                if (h > maxH) {
                                                    w = Math.round((w * maxH) / h);
                                                    h = maxH;
                                                }
                                            }
                                            canvas.width = w;
                                            canvas.height = h;
                                            const ctx = canvas.getContext('2d');
                                            ctx.drawImage(img, 0, 0, w, h);
                                            callback(canvas.toDataURL('image/jpeg', 0.8));
                                        };
                                    };
                                },
                                
                                previewFront(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.cccdFrontUrl = URL.createObjectURL(file);
                                        this.isScanningFront = true;
                                        const startTime = Date.now();
                                        
                                        this.compressImage(file, (base64Data) => {
                                            document.getElementById('cccd-front-base64').value = base64Data;
                                            
                                            // Call Laravel OCR API
                                            fetch('{{ route('profile.scan-cccd') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    image: base64Data,
                                                    side: 'front'
                                                })
                                            })
                                            .then(res => res.json())
                                            .then(resData => {
                                                const elapsed = Date.now() - startTime;
                                                const remaining = Math.max(0, 1800 - elapsed);
                                                
                                                setTimeout(() => {
                                                    this.isScanningFront = false;
                                                    
                                                    if (resData.success && resData.data) {
                                                        const idInput = document.getElementById('cccd-id-number');
                                                        const dobInput = document.getElementById('cccd-dob');
                                                        const pobInput = document.getElementById('cccd-pob');
                                                        const addrInput = document.getElementById('cccd-permanent-address');
                                                        
                                                        if (resData.data.number && idInput) {
                                                            idInput.value = resData.data.number;
                                                            idInput.classList.add('ocr-highlight');
                                                            setTimeout(() => idInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        if (resData.data.dob && dobInput) {
                                                            dobInput.value = resData.data.dob;
                                                            dobInput.classList.add('ocr-highlight');
                                                            setTimeout(() => dobInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        if (resData.data.pob && pobInput) {
                                                            pobInput.value = resData.data.pob;
                                                            pobInput.classList.add('ocr-highlight');
                                                            setTimeout(() => pobInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        if (resData.data.permanent_address && addrInput) {
                                                            addrInput.value = resData.data.permanent_address;
                                                            addrInput.classList.add('ocr-highlight');
                                                            setTimeout(() => addrInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        
                                                        this.showToastNotification('Quét thông tin mặt trước CCCD thành công!');
                                                    } else {
                                                        this.showToastNotification(resData.message || 'Không thể nhận dạng hình ảnh. Vui lòng điền thủ công.');
                                                    }
                                                }, remaining);
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                const elapsed = Date.now() - startTime;
                                                const remaining = Math.max(0, 1800 - elapsed);
                                                setTimeout(() => {
                                                    this.isScanningFront = false;
                                                    this.showToastNotification('Lỗi kết nối OCR. Vui lòng điền thủ công.');
                                                }, remaining);
                                            });
                                        });
                                    }
                                },
                                
                                previewBack(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.cccdBackUrl = URL.createObjectURL(file);
                                        this.isScanningBack = true;
                                        const startTime = Date.now();
                                        
                                        this.compressImage(file, (base64Data) => {
                                            document.getElementById('cccd-back-base64').value = base64Data;
                                            
                                            // Call Laravel OCR API
                                            fetch('{{ route('profile.scan-cccd') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    image: base64Data,
                                                    side: 'back'
                                                })
                                            })
                                            .then(res => res.json())
                                            .then(resData => {
                                                const elapsed = Date.now() - startTime;
                                                const remaining = Math.max(0, 1800 - elapsed);
                                                
                                                setTimeout(() => {
                                                    this.isScanningBack = false;
                                                    
                                                    if (resData.success && resData.data) {
                                                        const dateInput = document.getElementById('cccd-id-date');
                                                        const placeInput = document.getElementById('cccd-id-place');
                                                        const addrInput = document.getElementById('cccd-permanent-address');
                                                        
                                                        if (resData.data.issue_date && dateInput) {
                                                            dateInput.value = resData.data.issue_date;
                                                            dateInput.classList.add('ocr-highlight');
                                                            setTimeout(() => dateInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        if (resData.data.issue_place && placeInput) {
                                                            placeInput.value = resData.data.issue_place;
                                                            placeInput.classList.add('ocr-highlight');
                                                            setTimeout(() => placeInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        if (resData.data.permanent_address && addrInput && !addrInput.value) {
                                                            addrInput.value = resData.data.permanent_address;
                                                            addrInput.classList.add('ocr-highlight');
                                                            setTimeout(() => addrInput.classList.remove('ocr-highlight'), 1500);
                                                        }
                                                        
                                                        this.showToastNotification('Quét thông tin mặt sau CCCD thành công!');
                                                    } else {
                                                        this.showToastNotification(resData.message || 'Không thể nhận dạng hình ảnh. Vui lòng điền thủ công.');
                                                    }
                                                }, remaining);
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                const elapsed = Date.now() - startTime;
                                                const remaining = Math.max(0, 1800 - elapsed);
                                                setTimeout(() => {
                                                    this.isScanningBack = false;
                                                    this.showToastNotification('Lỗi kết nối OCR. Vui lòng điền thủ công.');
                                                }, remaining);
                                            });
                                        });
                                    }
                                },
 
                                showToastNotification(msg) {
                                    const rootEl = document.querySelector('[x-data]');
                                    if (rootEl) {
                                        if (window.Alpine && Alpine.$data) {
                                            const data = Alpine.$data(rootEl);
                                            if (data && data.triggerToast) data.triggerToast(msg);
                                        } else if (rootEl.__x && rootEl.__x.$data) {
                                            rootEl.__x.$data.triggerToast(msg);
                                        }
                                    }
                                }
                            }"
                            class="space-y-6 text-left"
                        >
                            @csrf
                            
                            <!-- Hidden inputs for Base64 CCCD images -->
                            <input type="hidden" name="cccd_front" id="cccd-front-base64">
                            <input type="hidden" name="cccd_back" id="cccd-back-base64">

                            <!-- READ-ONLY VERIFIED VIEW -->
                            <div x-show="!isEditingCccd" class="space-y-6">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-800">Xác thực CCCD / CMND</h2>
                                    <p class="text-xs text-slate-400 mt-1 font-semibold">Thông tin xác thực danh tính của bạn</p>
                                </div>



                                <!-- Read-only images -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <!-- Mặt trước -->
                                    <div class="space-y-2 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt trước CCCD</label>
                                        <div class="border border-slate-150 rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] overflow-hidden">
                                            <template x-if="cccdFrontUrl">
                                                <div class="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                                                    <img :src="cccdFrontUrl" class="w-full h-full object-cover">
                                                </div>
                                            </template>
                                            <template x-if="!cccdFrontUrl">
                                                <div class="text-center py-6 flex flex-col items-center justify-center text-slate-400">
                                                    <i class="fa-solid fa-image text-3xl mb-2"></i>
                                                    <p class="text-xs font-bold">Chưa có ảnh mặt trước</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Mặt sau -->
                                    <div class="space-y-2 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt sau CCCD</label>
                                        <div class="border border-slate-150 rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] overflow-hidden">
                                            <template x-if="cccdBackUrl">
                                                <div class="w-full h-full max-h-[160px] rounded-2xl overflow-hidden relative">
                                                    <img :src="cccdBackUrl" class="w-full h-full object-cover">
                                                </div>
                                            </template>
                                            <template x-if="!cccdBackUrl">
                                                <div class="text-center py-6 flex flex-col items-center justify-center text-slate-400">
                                                    <i class="fa-solid fa-image text-3xl mb-2"></i>
                                                    <p class="text-xs font-bold">Chưa có ảnh mặt sau</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Card -->
                                <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
                                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs">
                                            <i class="fa-solid fa-address-card"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-800">Thông tin Căn cước công dân đã lưu</h4>
                                            <p class="text-[9px] text-slate-400 font-semibold">Dữ liệu hiện tại trong hệ thống</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-left">
                                        <!-- Row 1 Left: Số CCCD -->
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                                <i class="fa-solid fa-hashtag"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Số CCCD / CMND</span>
                                                <span class="text-xs font-black text-slate-800">{{ $user['id_number'] ?? 'Chưa cập nhật' }}</span>
                                            </div>
                                        </div>
                                        <!-- Row 1 Right: Ngày sinh -->
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                                <i class="fa-solid fa-calendar-day"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày sinh</span>
                                                <span class="text-xs font-black text-slate-800">{{ $user['dob'] ?? 'Chưa cập nhật' }}</span>
                                            </div>
                                        </div>
                                        <!-- Row 2 Left: Ngày cấp -->
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                                <i class="fa-solid fa-calendar-check"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày cấp</span>
                                                <span class="text-xs font-black text-slate-800">{{ $user['id_date'] ?? 'Chưa cập nhật' }}</span>
                                            </div>
                                        </div>
                                        <!-- Row 2 Right: Nơi cấp -->
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                                <i class="fa-solid fa-building-columns"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi cấp</span>
                                                <span class="text-xs font-black text-slate-800 leading-relaxed">{{ $user['id_place'] ?? 'Chưa cập nhật' }}</span>
                                            </div>
                                        </div>
                                        <!-- Row 3: Quê quán -->
                                        <div class="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5 mt-1">
                                            <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                                <i class="fa-solid fa-map-location-dot"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Quê quán (Nơi sinh)</span>
                                                <span class="text-xs font-black text-slate-800 leading-relaxed">{{ $user['pob'] ?? 'Chưa cập nhật' }}</span>
                                            </div>
                                        </div>
                                        <!-- Row 4: Nơi thường trú -->
                                        <div class="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5">
                                            <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                                <i class="fa-solid fa-house-user"></i>
                                            </div>
                                            <div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi thường trú</span>
                                                <span class="text-xs font-black text-slate-800 leading-relaxed">{{ $user['permanent_address'] ?? 'Chưa cập nhật' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit action -->
                                <div class="flex justify-end pt-4 border-t border-slate-100">
                                    <button type="button" @click="isEditingCccd = true" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-pen-to-square mr-2"></i> Chỉnh sửa thông tin
                                    </button>
                                </div>
                            </div>

                            <!-- EDITABLE FORM VIEW -->
                            <div x-show="isEditingCccd" class="space-y-6" x-cloak>
                                <div>
                                    <h2 class="text-xl font-bold text-slate-800">Cập nhật thông tin CCCD</h2>
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
                                    <div class="relative border-2 border-dashed border-slate-200 hover:border-primary rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] transition group overflow-hidden">
                                        <!-- Scanning Overlay -->
                                        <div x-show="isScanningFront" class="absolute inset-0 bg-slate-950/65 flex flex-col items-center justify-center text-white z-20" x-cloak>
                                            <div class="scanner-line absolute left-0 right-0 h-1 bg-emerald-500 shadow-[0_0_12px_#10b981]"></div>
                                            <i class="fa-solid fa-circle-notch animate-spin text-2xl mb-2 text-emerald-400"></i>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 animate-pulse">Đang quét mặt trước...</span>
                                        </div>

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
                                        <input type="file" accept="image/*" @change="previewFront($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    </div>
                                    @error('cccd_front')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Mặt sau -->
                                <div class="space-y-2 text-left">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Mặt sau CCCD</label>
                                    <div class="relative border-2 border-dashed border-slate-200 hover:border-primary rounded-3xl bg-slate-50 p-4 flex flex-col items-center justify-center min-h-[180px] transition group overflow-hidden">
                                        <!-- Scanning Overlay -->
                                        <div x-show="isScanningBack" class="absolute inset-0 bg-slate-950/65 flex flex-col items-center justify-center text-white z-20" x-cloak>
                                            <div class="scanner-line absolute left-0 right-0 h-1 bg-emerald-500 shadow-[0_0_12px_#10b981]"></div>
                                            <i class="fa-solid fa-circle-notch animate-spin text-2xl mb-2 text-emerald-400"></i>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 animate-pulse">Đang quét mặt sau...</span>
                                        </div>

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
                                        <input type="file" accept="image/*" @change="previewBack($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    </div>
                                    @error('cccd_back')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Input Fields Group -->
                            <div class="space-y-4 pt-2">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <!-- Số CCCD -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số CCCD / CMND (12 số)</label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                name="id_number"
                                                id="cccd-id-number"
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

                                    <!-- Ngày sinh -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày sinh</label>
                                        <div class="relative">
                                            @php
                                                $cccdDobVal = '';
                                                if (!empty($user['dob'])) {
                                                    try {
                                                        $cccdDobVal = \Carbon\Carbon::createFromFormat('d/m/Y', $user['dob'])->format('Y-m-d');
                                                    } catch (\Exception $e) {
                                                        $cccdDobVal = $user['dob'];
                                                    }
                                                }
                                                $cccdDobOld = old('dob', $cccdDobVal);
                                                if (!empty($cccdDobOld) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $cccdDobOld)) {
                                                    try {
                                                        $cccdDobOld = \Carbon\Carbon::createFromFormat('d/m/Y', $cccdDobOld)->format('Y-m-d');
                                                    } catch (\Exception $e) {}
                                                }
                                            @endphp
                                            <input 
                                                type="date" 
                                                name="dob"
                                                id="cccd-dob"
                                                value="{{ $cccdDobOld }}"
                                                required
                                                class="w-full pr-4 pl-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
                                        </div>
                                        @error('dob')
                                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <!-- Ngày cấp -->
                                    <div class="space-y-1 text-left">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Ngày cấp</label>
                                        <div class="relative">
                                            @php
                                                $cccdIdDateVal = '';
                                                if (!empty($user['id_date'])) {
                                                    try {
                                                        $cccdIdDateVal = \Carbon\Carbon::createFromFormat('d/m/Y', $user['id_date'])->format('Y-m-d');
                                                    } catch (\Exception $e) {
                                                        $cccdIdDateVal = $user['id_date'];
                                                    }
                                                }
                                                $cccdIdDateOld = old('id_date', $cccdIdDateVal);
                                                if (!empty($cccdIdDateOld) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $cccdIdDateOld)) {
                                                    try {
                                                        $cccdIdDateOld = \Carbon\Carbon::createFromFormat('d/m/Y', $cccdIdDateOld)->format('Y-m-d');
                                                    } catch (\Exception $e) {}
                                                }
                                            @endphp
                                            <input 
                                                type="date" 
                                                name="id_date"
                                                id="cccd-id-date"
                                                value="{{ $cccdIdDateOld }}"
                                                required
                                                class="w-full pr-4 pl-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                                            >
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
                                                id="cccd-id-place"
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
                                                id="cccd-pob"
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
                                                id="cccd-permanent-address"
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
                            <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-100 rounded-3xl p-6 mt-6 shadow-sm">
                                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs">
                                        <i class="fa-solid fa-address-card"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800">Thông tin Căn cước công dân đã lưu</h4>
                                        <p class="text-[9px] text-slate-400 font-semibold">Dữ liệu hiện tại trong hệ thống</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-left">
                                    <!-- Row 1 Left: Số CCCD -->
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                            <i class="fa-solid fa-hashtag"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Số CCCD / CMND</span>
                                            <span class="text-xs font-black text-slate-800">{{ $user['id_number'] ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                    <!-- Row 1 Right: Ngày sinh -->
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                            <i class="fa-solid fa-calendar-day"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày sinh</span>
                                            <span class="text-xs font-black text-slate-800">{{ $user['dob'] ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                    <!-- Row 2 Left: Ngày cấp -->
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                            <i class="fa-solid fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Ngày cấp</span>
                                            <span class="text-xs font-black text-slate-800">{{ $user['id_date'] ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                    <!-- Row 2 Right: Nơi cấp -->
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                            <i class="fa-solid fa-building-columns"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi cấp</span>
                                            <span class="text-xs font-black text-slate-800 leading-relaxed">{{ $user['id_place'] ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                    <!-- Row 3: Quê quán -->
                                    <div class="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5 mt-1">
                                        <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Quê quán (Nơi sinh)</span>
                                            <span class="text-xs font-black text-slate-800 leading-relaxed">{{ $user['pob'] ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                    <!-- Row 4: Nơi thường trú -->
                                    <div class="flex items-start gap-3 md:col-span-2 border-t border-slate-100 pt-3.5">
                                        <div class="mt-1 text-slate-400 text-xs w-4 text-center">
                                            <i class="fa-solid fa-house-user"></i>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Nơi thường trú</span>
                                            <span class="text-xs font-black text-slate-800 leading-relaxed">{{ $user['permanent_address'] ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <!-- Edit/Update Buttons at bottom of Editable View -->
                                <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                                    <template x-if="hasVerifiedCccd">
                                        <button type="button" @click="isEditingCccd = false" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-slate-500 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition cursor-pointer active:scale-98">
                                            Hủy bỏ
                                        </button>
                                    </template>
                                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                        Cập nhật thông tin CCCD
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB 2: Saved / Favorite Listings OR Owner Properties -->
                @if(Auth::user()->role === 'owner' || Auth::user()->role === 'admin')
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
                        <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar border border-slate-150/80 rounded-2xl shadow-sm bg-white">
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
                                                    <button 
                                                        type="button" 
                                                        @click="
                                                            activeTab = 'marketing'; 
                                                            selectedPropertyId = '{{ $prop->id }}'; 
                                                            window.history.pushState(null, '', '?tab=marketing');
                                                        " 
                                                        class="p-1.5 text-slate-500 hover:text-primary transition focus:outline-none cursor-pointer" 
                                                        title="AI Content Studio (Marketing)"
                                                    >
                                                        <i class="fa-solid fa-wand-magic-sparkles text-sm text-primary"></i>
                                                    </button>
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
                                <p class="text-xs font-bold text-slate-500 mb-4">Bạn chưa lưu tin yêu thích nào.</p>
                                <a href="/listings" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Khám phá ngay
                                </a>
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
                            ownerActiveSubTab: 'received',
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

                        <!-- Owner Sub-tabs Navigation -->
                        @if(Auth::user()->role === 'owner')
                            <div class="flex items-center gap-2 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
                                <button 
                                    type="button" 
                                    @click="ownerActiveSubTab = 'received'"
                                    :class="ownerActiveSubTab === 'received' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer"
                                >
                                    <i class="fa-solid fa-calendar-check mr-1.5"></i> Lịch khách đặt
                                </button>
                                <button 
                                    type="button" 
                                    @click="ownerActiveSubTab = 'sent'"
                                    :class="ownerActiveSubTab === 'sent' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer"
                                >
                                    <i class="fa-solid fa-calendar-plus mr-1.5"></i> Lịch của tôi
                                </button>
                            </div>
                        @endif

                        <!-- List Appointments -->
                        @if(Auth::user()->role === 'owner')
                            <!-- Received appointments list -->
                            <div x-show="ownerActiveSubTab === 'received'">
                                @if($ownerAppointments->isEmpty())
                                    <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                            <i class="fa-solid fa-calendar-xmark"></i>
                                        </div>
                                        <p class="text-xs font-bold text-slate-500">Chưa có khách đặt lịch hẹn xem nhà nào.</p>
                                    </div>
                                @else
                                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar border border-slate-150/80 rounded-2xl shadow-sm bg-white">
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
                                                            @if($app->property)
                                                                <a href="/property/{{ $app->property->id }}" class="hover:text-primary font-bold text-slate-800 block truncate" title="{{ $app->property->title }}">{{ $app->property->title }}</a>
                                                            @else
                                                                <span class="text-slate-400 italic">BĐS không tồn tại</span>
                                                            @endif
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
                            </div>

                            <!-- Sent appointments list (the ones they booked themselves) -->
                            <div x-show="ownerActiveSubTab === 'sent'">
                                @if($appointments->isEmpty())
                                    <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                            <i class="fa-solid fa-calendar-xmark"></i>
                                        </div>
                                        <p class="text-xs font-bold text-slate-500 mb-4">Bạn chưa đặt lịch hẹn xem nhà nào.</p>
                                        <a href="/listings" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                            <i class="fa-solid fa-calendar-plus mr-2"></i> Đặt lịch ngay
                                        </a>
                                    </div>
                                @else
                                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar border border-slate-150/80 rounded-2xl shadow-sm bg-white">
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
                                                            @if($app->property)
                                                                <a href="/property/{{ $app->property->id }}" class="hover:text-primary font-bold text-slate-800">{{ $app->property->title }}</a>
                                                            @else
                                                                <span class="text-slate-400 italic">BĐS không tồn tại</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 whitespace-nowrap">{{ $app->property ? ($app->property->agent->name ?? 'N/A') : 'N/A' }}</td>
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
                            </div>
                        @else
                            @if($appointments->isEmpty())
                                <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-500 mb-4">Bạn chưa đặt lịch hẹn xem nhà nào.</p>
                                    <a href="/listings" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-calendar-plus mr-2"></i> Đặt lịch ngay
                                    </a>
                                </div>
                            @else
                                <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar border border-slate-150/80 rounded-2xl shadow-sm bg-white">
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
                                                        @if($app->property)
                                                            <a href="/property/{{ $app->property->id }}" class="hover:text-primary font-bold text-slate-800">{{ $app->property->title }}</a>
                                                        @else
                                                            <span class="text-slate-400 italic">BĐS không tồn tại</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">{{ $app->property ? ($app->property->agent->name ?? 'N/A') : 'N/A' }}</td>
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

                <!-- TAB Leads -->
                @if(Auth::user()->role === 'admin')
                <div x-show="activeTab === 'leads'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    @include('components.owner-leads-tab')
                </div>
                @endif

                <!-- TAB Marketing -->
                <div x-show="activeTab === 'marketing'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    @include('components.owner-marketing-tab')
                </div>

                @if(Auth::user()->role === 'admin')
                <!-- TAB Admin Users -->
                <div x-show="activeTab === 'admin_users'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <!-- Title -->
                    <div class="pb-5 border-b border-slate-100 mb-6 text-left">
                        <h2 class="text-xl font-bold text-slate-800">Quản lý thành viên</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và khóa/mở khóa tài khoản khách thuê, chủ nhà hoặc quản trị viên.</p>
                    </div>

                    <!-- Filters & Search Card -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 shadow-sm text-left">
                        <form action="{{ route('profile.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <input type="hidden" name="tab" value="admin_users">
                            
                            <!-- Search Keyword -->
                            <div class="sm:col-span-6 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('tab') === 'admin_users' ? request('search') : '' }}"
                                    placeholder="Tìm kiếm theo tên, email hoặc SĐT..." 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-850 text-xs font-semibold outline-none transition"
                                    onchange="this.form.submit()"
                                >
                            </div>

                            <!-- Role Filter -->
                            <div 
                                x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tab') === 'admin_users' ? request('role') : '' }}',
                                    selectedLabel: '{{ request('tab') === 'admin_users' && request('role') === 'tenant' ? 'Khách thuê' : (request('tab') === 'admin_users' && request('role') === 'owner' ? 'Chủ nhà' : (request('tab') === 'admin_users' && request('role') === 'admin' ? 'Quản trị viên' : '-- Tất cả vai trò --')) }}'
                                }" 
                                class="relative sm:col-span-3"
                            >
                                <input type="hidden" name="role" :value="selected">
                            
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
                                >
                                    <span x-text="selectedLabel"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                            
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    x-transition
                                    class="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="selected = ''; selectedLabel = '-- Tất cả vai trò --'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === '' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        -- Tất cả vai trò --
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'tenant'; selectedLabel = 'Khách thuê'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'tenant' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Khách thuê
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'owner'; selectedLabel = 'Chủ nhà'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'owner' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Chủ nhà
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'admin'; selectedLabel = 'Quản trị viên'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'admin' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Quản trị viên
                                    </button>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div 
                                x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tab') === 'admin_users' ? request('status') : '' }}',
                                    selectedLabel: '{{ request('tab') === 'admin_users' && request('status') === 'active' ? 'Hoạt động' : (request('tab') === 'admin_users' && request('status') === 'locked' ? 'Đang khóa' : '-- Trạng thái --') }}'
                                }" 
                                class="relative sm:col-span-3"
                            >
                                <input type="hidden" name="status" :value="selected">
                            
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
                                >
                                    <span x-text="selectedLabel"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                            
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    x-transition
                                    class="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="selected = ''; selectedLabel = '-- Trạng thái --'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === '' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        -- Trạng thái --
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'active'; selectedLabel = 'Hoạt động'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'active' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Hoạt động
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'locked'; selectedLabel = 'Đang khóa'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'locked' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Đang khóa
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
                        <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
                            @if(isset($adminUsers) && $adminUsers->count() > 0)
                            <table class="min-w-full text-left text-xs text-slate-600 font-semibold">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Thành viên</th>
                                        <th scope="col" class="px-6 py-4">Liên hệ</th>
                                        <th scope="col" class="px-6 py-4">Vai trò</th>
                                        <th scope="col" class="px-6 py-4">Trạng thái</th>
                                        <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($adminUsers as $userItem)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <!-- Avatar & Name -->
                                        <td class="px-6 py-4 flex items-center space-x-3.5">
                                            <img 
                                                src="{{ $userItem->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($userItem->name) . '&background=0077bb&color=fff' }}" 
                                                alt="{{ $userItem->name }}" 
                                                class="w-9 h-9 rounded-full object-cover border border-slate-100 shadow-sm"
                                            >
                                            <div>
                                                <span class="font-bold text-slate-800 text-xs block leading-none">
                                                    {{ $userItem->name }}
                                                </span>
                                                <span class="text-[9px] text-slate-400 block mt-1">ID: #{{ $userItem->id }}</span>
                                            </div>
                                        </td>
                                        <!-- Email & Phone -->
                                        <td class="px-6 py-4">
                                            <span class="block text-slate-750 font-semibold leading-none">{{ $userItem->email }}</span>
                                            <span class="text-[10px] text-slate-400 block mt-1">{{ $userItem->phone ?? 'Chưa cập nhật SĐT' }}</span>
                                        </td>
                                        <!-- Role -->
                                        <td class="px-6 py-4">
                                            @if($userItem->role === 'admin')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Admin</span>
                                            @elseif($userItem->role === 'owner')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Chủ nhà</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200">Khách thuê</span>
                                            @endif
                                        </td>
                                        <!-- Status -->
                                        <td class="px-6 py-4">
                                            @if($userItem->status === 'locked')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                    <i class="fa-solid fa-lock mr-1.5 text-[8px]"></i> Đã khóa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">
                                                    <i class="fa-solid fa-circle-check mr-1.5 text-[8px]"></i> Hoạt động
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($userItem->id !== Auth::id())
                                                    <form id="toggle-status-form-{{ $userItem->id }}" action="{{ route('admin.users.toggle-status', $userItem->id) }}" method="POST" class="inline" onsubmit="return window.confirmAction('Bạn có chắc chắn muốn {{ $userItem->status === 'locked' ? 'mở khóa' : 'khóa' }} tài khoản này?', this);">
                                                        @csrf
                                                        @if($userItem->status === 'locked')
                                                            <button 
                                                                type="submit" 
                                                                title="Mở khóa tài khoản"
                                                                class="w-7 h-7 rounded-lg border text-xs cursor-pointer transition shadow-sm bg-green-50 hover:bg-green-100 text-green-600 border-green-200 inline-flex items-center justify-center"
                                                            >
                                                                <i class="fa-solid fa-lock-open text-[10px]"></i>
                                                            </button>
                                                        @else
                                                            <button 
                                                                type="submit" 
                                                                title="Khóa tài khoản"
                                                                class="w-7 h-7 rounded-lg border text-xs cursor-pointer transition shadow-sm bg-red-50 hover:bg-red-100 text-red-650 border-red-250 inline-flex items-center justify-center"
                                                            >
                                                                <i class="fa-solid fa-lock text-[10px]"></i>
                                                            </button>
                                                        @endif
                                                    </form>
                                                    
                                                    <button 
                                                        type="button"
                                                        @click="$dispatch('open-user-modal', { userId: {{ $userItem->id }} })"
                                                        title="Xem chi tiết"
                                                        class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-primary text-xs cursor-pointer transition shadow-sm inline-flex items-center justify-center"
                                                    >
                                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-slate-400">Không có thao tác</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="py-16 text-center text-slate-400 font-semibold">
                                <i class="fa-solid fa-users-slash text-3xl mb-3 block text-slate-350"></i>
                                Chưa có thành viên nào.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB Admin Properties -->
                <div x-show="activeTab === 'admin_properties'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <!-- Title -->
                    <div class="pb-5 border-b border-slate-100 mb-6 text-left">
                        <h2 class="text-xl font-bold text-slate-800">Quản lý tin đăng</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái duyệt và quản lý toàn bộ các bất động sản trên hệ thống.</p>
                    </div>

                    <!-- Filters & Search Card -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 shadow-sm text-left">
                        <form action="{{ route('profile.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <input type="hidden" name="tab" value="admin_properties">
                            
                            <!-- Search Keyword -->
                            <div class="sm:col-span-3 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('tab') === 'admin_properties' ? request('search') : '' }}"
                                    placeholder="Tìm kiếm tin..." 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
                                    onchange="this.form.submit()"
                                >
                            </div>

                            <!-- Category Filter -->
                            <div 
                                x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tab') === 'admin_properties' ? request('category_id') : '' }}',
                                    selectedLabel: '{{ request('tab') === 'admin_properties' && request('category_id') ? ($categories->firstWhere('id', request('category_id'))->name ?? '-- Tất cả danh mục --') : '-- Tất cả danh mục --' }}'
                                }" 
                                class="relative sm:col-span-3"
                            >
                                <input type="hidden" name="category_id" :value="selected">
                            
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full px-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
                                >
                                    <span x-text="selectedLabel"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                            
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    x-transition
                                    class="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden max-h-60 overflow-y-auto thin-scrollbar"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="selected = ''; selectedLabel = '-- Tất cả danh mục --'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === '' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        -- Tất cả danh mục --
                                    </button>
                                    @foreach($categories as $cat)
                                        <button 
                                            type="button" 
                                            @click="selected = '{{ $cat->id }}'; selectedLabel = '{{ addslashes($cat->name) }}'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                            class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                            :class="selected == '{{ $cat->id }}' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                        >
                                            {{ $cat->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Transaction Type Filter -->
                            <div 
                                x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tab') === 'admin_properties' ? request('transaction_type') : '' }}',
                                    selectedLabel: '{{ request('tab') === 'admin_properties' && request('transaction_type') === 'rent' ? 'Cho thuê' : (request('tab') === 'admin_properties' && request('transaction_type') === 'sale' ? 'Bán' : '-- Tất cả --') }}'
                                }" 
                                class="relative sm:col-span-3"
                            >
                                <input type="hidden" name="transaction_type" :value="selected">
                            
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full px-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
                                >
                                    <span x-text="selectedLabel"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                            
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    x-transition
                                    class="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="selected = ''; selectedLabel = '-- Tất cả --'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === '' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        -- Tất cả --
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'sale'; selectedLabel = 'Bán'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'sale' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Bán
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'rent'; selectedLabel = 'Cho thuê'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'rent' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Cho thuê
                                    </button>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div 
                                x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tab') === 'admin_properties' ? request('status') : '' }}',
                                    selectedLabel: '{{ request('tab') === 'admin_properties' && request('status') === 'pending' ? 'Chờ duyệt' : (request('tab') === 'admin_properties' && request('status') === 'approved' ? 'Đã duyệt' : (request('tab') === 'admin_properties' && request('status') === 'hidden' ? 'Đã ẩn' : '-- Trạng thái --')) }}'
                                }" 
                                class="relative sm:col-span-3"
                            >
                                <input type="hidden" name="status" :value="selected">
                            
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full px-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
                                >
                                    <span x-text="selectedLabel"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                            
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    x-transition
                                    class="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="selected = ''; selectedLabel = '-- Trạng thái --'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === '' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        -- Trạng thái --
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'pending'; selectedLabel = 'Chờ duyệt'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'pending' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Chờ duyệt
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'approved'; selectedLabel = 'Đã duyệt'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'approved' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Đã duyệt
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'hidden'; selectedLabel = 'Đã ẩn'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'hidden' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Đã ẩn
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Properties Table Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
                        <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
                            @if(isset($adminProperties) && count($adminProperties) > 0)
                            <table class="min-w-full text-left text-xs text-slate-600 font-semibold">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Bất động sản</th>
                                        <th scope="col" class="px-6 py-4">Giá / Diện tích</th>
                                        <th scope="col" class="px-6 py-4">Kiểu giao dịch</th>
                                        <th scope="col" class="px-6 py-4">Trạng thái</th>
                                        <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($adminProperties as $propItem)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <!-- Title -->
                                        <td class="px-6 py-4 max-w-[280px]">
                                            <a href="/property/{{ $propItem['id'] }}" class="font-bold text-slate-800 hover:text-primary transition block truncate leading-none mb-1">
                                                {{ $propItem['title'] }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 block"><i class="fa-solid fa-location-dot mr-1"></i>{{ $propItem['location'] }}</span>
                                        </td>
                                        <!-- Price / Area -->
                                        <td class="px-6 py-4">
                                            <span class="block text-primary font-bold leading-none mb-1">{{ $propItem['price'] }}</span>
                                            <span class="text-[10px] text-slate-400 block">{{ $propItem['area'] }} m²</span>
                                        </td>
                                        <!-- Transaction Type -->
                                        <td class="px-6 py-4">
                                            @php
                                                $isSale = isset($propItem['price_label']) && stripos($propItem['price_label'], 'tháng') === false;
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold {{ $isSale ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $isSale ? 'Bán' : 'Cho thuê' }}
                                            </span>
                                        </td>
                                        <!-- Status -->
                                        <td class="px-6 py-4">
                                            @if(($propItem['status'] ?? 'approved') === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">
                                                    Đã duyệt
                                                </span>
                                            @elseif(($propItem['status'] ?? 'approved') === 'hidden')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 text-slate-600 border border-slate-200">
                                                    Đã ẩn
                                                </span>
                                            @elseif(($propItem['status'] ?? 'approved') === 'rejected')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                    Từ chối
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Chờ duyệt
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if(($propItem['status'] ?? 'approved') === 'pending')
                                                    <form id="approve-prop-form-{{ $propItem['id'] }}" action="{{ route('admin.properties.status', $propItem['id']) }}" method="POST" class="inline" onsubmit="return window.confirmAction('Bạn có chắc chắn muốn duyệt đăng tin này?', this);">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 text-[10px] font-extrabold cursor-pointer transition shadow-sm">
                                                            Duyệt tin
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(($propItem['status'] ?? 'approved') === 'approved')
                                                    <form id="hide-prop-form-{{ $propItem['id'] }}" action="{{ route('admin.properties.status', $propItem['id']) }}" method="POST" class="inline" onsubmit="return window.confirmAction('Bạn có chắc chắn muốn ẩn tin đăng này không?', this);">
                                                        @csrf
                                                        <input type="hidden" name="status" value="hidden">
                                                        <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition cursor-pointer" title="Ẩn tin đăng">
                                                            <i class="fa-solid fa-eye-slash text-xs"></i>
                                                        </button>
                                                    </form>
                                                @elseif(($propItem['status'] ?? 'approved') === 'hidden')
                                                    <form id="approve-prop-form-{{ $propItem['id'] }}" action="{{ route('admin.properties.status', $propItem['id']) }}" method="POST" class="inline" onsubmit="return window.confirmAction('Bạn có chắc chắn muốn hiển thị lại tin đăng này?', this);">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 border border-green-200 flex items-center justify-center transition cursor-pointer" title="Hiện tin đăng">
                                                            <i class="fa-solid fa-eye text-xs"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="py-16 text-center text-slate-400 font-semibold">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 block text-slate-350"></i>
                                Chưa có tin đăng nào trên hệ thống.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB Admin Appointments -->
                <div x-show="activeTab === 'admin_appointments'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <!-- Title -->
                    <div class="pb-5 border-b border-slate-100 mb-6 text-left">
                        <h2 class="text-xl font-bold text-slate-800">Quản lý lịch hẹn</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và phê duyệt lịch đi xem nhà của khách hàng trên hệ thống.</p>
                    </div>

                    <!-- Filters & Search Card -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 shadow-sm text-left">
                        <form action="{{ route('profile.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <input type="hidden" name="tab" value="admin_appointments">
                            
                            <!-- Search Keyword -->
                            <div class="sm:col-span-8 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('tab') === 'admin_appointments' ? request('search') : '' }}"
                                    placeholder="Tìm kiếm theo tên khách hàng hoặc số điện thoại..." 
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-205 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition"
                                    onchange="this.form.submit()"
                                >
                            </div>

                            <!-- Status Filter -->
                            <div 
                                x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tab') === 'admin_appointments' ? request('status') : '' }}',
                                    selectedLabel: '{{ request('tab') === 'admin_appointments' && request('status') === 'pending' ? 'Chờ duyệt' : (request('tab') === 'admin_appointments' && request('status') === 'approved' ? 'Đã duyệt' : (request('tab') === 'admin_appointments' && request('status') === 'rejected' ? 'Từ chối' : '-- Trạng thái --')) }}'
                                }" 
                                class="relative sm:col-span-4"
                            >
                                <input type="hidden" name="status" :value="selected">
                            
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-slate-800 text-xs font-semibold outline-none transition cursor-pointer flex items-center justify-between text-left"
                                >
                                    <span x-text="selectedLabel"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                            
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    x-transition
                                    class="absolute z-30 mt-1 w-full bg-white border border-slate-150 rounded-2xl shadow-xl py-1 overflow-hidden"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="selected = ''; selectedLabel = '-- Trạng thái --'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === '' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        -- Trạng thái --
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'pending'; selectedLabel = 'Chờ duyệt'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'pending' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Chờ duyệt
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'approved'; selectedLabel = 'Đã duyệt'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'approved' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Đã duyệt
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selected = 'rejected'; selectedLabel = 'Từ chối'; open = false; $nextTick(() => $el.closest('form').submit())" 
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-semibold transition"
                                        :class="selected === 'rejected' ? 'bg-primary-light/30 text-primary font-bold' : ''"
                                    >
                                        Từ chối
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Appointments Table Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
                        <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
                            @if(isset($adminAppointments) && $adminAppointments->count() > 0)
                            <table class="min-w-full text-left text-xs text-slate-600 font-semibold">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Khách hàng</th>
                                        <th scope="col" class="px-6 py-4">Ngày giờ hẹn</th>
                                        <th scope="col" class="px-6 py-4">Bất động sản</th>
                                        <th scope="col" class="px-6 py-4">Trạng thái</th>
                                        <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($adminAppointments as $appItem)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <!-- Guest info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-slate-800 font-bold">{{ $appItem->name }}</span>
                                            <span class="text-[10px] text-slate-400"><i class="fa-solid fa-phone mr-1"></i>{{ $appItem->phone }}</span>
                                        </td>
                                        <!-- Date/Time -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-slate-800">{{ \Carbon\Carbon::parse($appItem->date)->format('d/m/Y') }}</span>
                                            <span class="text-[10px] text-slate-400"><i class="fa-solid fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($appItem->time)->format('H:i') }}</span>
                                        </td>
                                        <!-- Property -->
                                        <td class="px-6 py-4 max-w-[200px] truncate">
                                            @if($appItem->property)
                                                <a href="/property/{{ $appItem->property->id }}" class="hover:text-primary font-bold text-slate-800 block truncate" title="{{ $appItem->property->title }}">{{ $appItem->property->title }}</a>
                                            @else
                                                <span class="text-slate-400 italic">BĐS không tồn tại</span>
                                            @endif
                                        </td>
                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($appItem->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Đã duyệt</span>
                                            @elseif($appItem->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                                            @elseif($appItem->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Từ chối</span>
                                            @elseif($appItem->status === 'completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Đã xem nhà</span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            @if($appItem->status === 'pending')
                                                <form action="{{ route('appointments.cancel', $appItem->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?');">
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
                            @else
                            <div class="py-16 text-center text-slate-400 font-semibold">
                                <i class="fa-solid fa-calendar-xmark text-3xl mb-3 block text-slate-350"></i>
                                Chưa có lịch hẹn xem nhà nào trên hệ thống.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if(Auth::user()->role === 'tenant')
                <!-- TAB 5: Register Owner -->
                <div x-show="activeTab === 'register_owner'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <!-- Title -->
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Đăng ký làm chủ nhà</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Trở thành đối tác chủ nhà để đăng tin cho thuê và bán bất động sản.</p>
                    </div>

                    <!-- Registration Form -->
                    <div class="bg-slate-50 border border-slate-100 p-6 rounded-3xl">
                        <form action="{{ route('profile.register-owner') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Họ và tên -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="name"
                                            value="{{ old('name', Auth::user()->name) }}"
                                            required
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('name')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Số điện thoại -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="phone"
                                            value="{{ old('phone', Auth::user()->phone) }}"
                                            required
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('phone')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Công ty (nếu có) -->
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Công ty / Tổ chức (nếu có)</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input 
                                            type="text" 
                                            name="company"
                                            value="{{ old('company', Auth::user()->company ?? '') }}"
                                            placeholder="Ví dụ: Công ty Bất động sản ABC..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 focus:border-primary rounded-xl text-xs font-semibold outline-none transition"
                                        >
                                    </div>
                                    @error('company')
                                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-slate-100">
                                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md transition cursor-pointer active:scale-98">
                                    <i class="fa-solid fa-circle-check mr-2"></i> Xác nhận đăng ký làm chủ nhà
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif


            </div>
            
        </div>
    </div>
</div>

<!-- User Details Modal (AlpineJS based) -->
@if(Auth::user()->role === 'admin')
<div 
    x-data="{
        open: false,
        loading: false,
        htmlContent: '',
        openModal(userId) {
            this.open = true;
            this.loading = true;
            this.htmlContent = '';
            fetch('/admin/users/' + userId)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const content = doc.querySelector('#user-detail-content');
                    if (content) {
                        // Strip any breadcrumbs or navigation back buttons
                        const backBtn = content.querySelector('.fa-arrow-left')?.closest('div');
                        if (backBtn) {
                            backBtn.remove();
                        }
                        this.htmlContent = content.innerHTML;
                    } else {
                        this.htmlContent = '<div class=\'p-8 text-center text-red-500 font-bold\'>Không thể tìm thấy nội dung chi tiết thành viên.</div>';
                    }
                    this.loading = false;
                })
                .catch(err => {
                    this.htmlContent = '<div class=\'p-8 text-center text-red-500 font-bold\'>Lỗi kết nối khi tải dữ liệu.</div>';
                    this.loading = false;
                });
        }
    }"
    @open-user-modal.window="openModal($event.detail.userId)"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    x-transition
    x-cloak
>
    <div @click.outside="open = false" class="bg-white rounded-3xl max-w-3xl w-full p-5 shadow-2xl relative border border-slate-100 max-h-[90vh] overflow-y-auto thin-scrollbar">
        <!-- Close button -->
        <button type="button" @click="open = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition cursor-pointer text-base z-10">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div x-show="loading" class="py-24 text-center text-slate-500 font-semibold">
            <i class="fa-solid fa-circle-notch fa-spin text-3xl mb-3 block text-primary"></i>
            Đang tải chi tiết thành viên...
        </div>
        
        <div x-show="!loading" x-html="htmlContent"></div>
    </div>
</div>
@endif

<!-- Custom Confirmation Modal (AlpineJS based) -->
<div 
    x-data="{
        open: false,
        message: 'Bạn có chắc chắn muốn thực hiện hành động này?',
        type: 'danger',
        confirmCallback: null,
        showConfirm(message, callback, type) {
            this.message = message;
            this.confirmCallback = callback;
            this.type = type || 'danger';
            this.open = true;
        },
        triggerConfirm() {
            if (this.confirmCallback) this.confirmCallback();
            this.open = false;
        }
    }"
    @trigger-custom-confirm.window="showConfirm($event.detail.message, $event.detail.callback, $event.detail.type)"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    x-transition
    x-cloak
>
    <div @click.outside="open = false" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative border border-slate-100 text-center space-y-4">
        <div 
            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto text-xl border"
            :class="type === 'danger' ? 'bg-red-50 text-red-500 border-red-100' : 'bg-amber-50 text-amber-500 border-amber-100'"
        >
            <i class="fa-solid" :class="type === 'danger' ? 'fa-circle-exclamation' : 'fa-triangle-exclamation'"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Xác nhận</h3>
        <p class="text-xs text-slate-500 leading-relaxed" x-text="message"></p>
        <div class="flex items-center justify-center gap-3 pt-2">
            <button type="button" @click="open = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition cursor-pointer">
                Hủy bỏ
            </button>
            <button 
                type="button" 
                @click="triggerConfirm()" 
                class="px-4 py-2 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-sm"
                :class="type === 'danger' ? 'bg-red-500 hover:bg-red-655 shadow-red-500/20' : 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20'"
            >
                Xác nhận
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Cropper.js CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<style>
    @media (max-width: 1023px) {
        .profile-subtabs-desktop {
            display: none !important;
        }
    }
    .avatar-crop-container {
        max-width: 100%;
        max-height: 280px;
        background-color: #f8fafc;
        position: relative;
    }
    /* Make the crop box circular */
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
        outline: initial;
        border: 3px solid #3b82f6;
    }
    .cropper-line, .cropper-point {
        display: none !important;
    }
    /* Style the preview circular image */
    .img-preview {
        width: 112px;
        height: 112px;
        overflow: hidden;
        border-radius: 50%;
    }
    /* Fix legibility of Cancel button when hovered */
    #cancel-crop-btn {
        color: #e11d48 !important;
        border-color: #fecdd3 !important;
        background-color: #ffffff !important;
        transition: all 0.2s ease-in-out;
    }
    #cancel-crop-btn:hover {
        color: #ffffff !important;
        background-color: #e11d48 !important;
        border-color: #e11d48 !important;
    }
    /* Scanning laser animation */
    @keyframes scan {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
    .scanner-line {
        animation: scan 2s linear infinite;
    }
    /* Highlighting populated fields */
    .ocr-highlight {
        animation: flash-highlight 1.5s ease-out;
    }
    @keyframes flash-highlight {
        0% { border-color: #10b981; background-color: #ecfdf5; box-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
        100% { border-color: #cbd5e1; background-color: #f8fafc; }
    }
</style>

<script>
window.confirmAction = function(message, formElement, type = 'danger') {
    window.dispatchEvent(new CustomEvent('trigger-custom-confirm', {
        detail: {
            message: message,
            callback: () => {
                formElement.submit();
            },
            type: type
        }
    }));
    return false;
};

document.addEventListener('DOMContentLoaded', function () {
    const avatarFileInput = document.getElementById('avatar-file-input');
    const cropperImage = document.getElementById('cropper-image');
    const cancelCropBtn = document.getElementById('cancel-crop-btn');
    const avatarForm = document.getElementById('avatar-form');
    const croppedAvatarInput = document.getElementById('cropped-avatar-input');

    let cropper = null;

    // Controls
    const btnZoomIn = document.getElementById('btn-zoom-in');
    const btnZoomOut = document.getElementById('btn-zoom-out');
    const btnRotateLeft = document.getElementById('btn-rotate-left');
    const btnRotateRight = document.getElementById('btn-rotate-right');
    const btnReset = document.getElementById('btn-reset');

    avatarFileInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            
            // Validate size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.');
                avatarFileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                if (cropper) {
                    cropper.destroy();
                }

                cropperImage.src = event.target.result;
                
                // Show Cropper & controls in Alpine
                const formEl = document.getElementById('avatar-form');
                if (formEl) {
                    if (formEl.__x) {
                        formEl.__x.$data.hasImage = true;
                    } else if (window.Alpine) {
                        const alpineData = Alpine.$data(formEl);
                        if (alpineData) {
                            alpineData.hasImage = true;
                        }
                    }
                }
                
                // Safe DOM fallbacks
                document.querySelectorAll('[x-show="hasImage"]').forEach(el => {
                    el.style.display = 'block';
                });
                document.querySelectorAll('[x-show="!hasImage"]').forEach(el => {
                    el.style.display = 'none';
                });

                // Initialize Cropper
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    toggleDragModeOnDblclick: false,
                    preview: '.img-preview',
                    ready() {
                        // Cropper is loaded
                    }
                });
            };
            reader.readAsDataURL(file);
        }
    });

    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', () => cropper && cropper.zoom(0.1));
    }
    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', () => cropper && cropper.zoom(-0.1));
    }
    if (btnRotateLeft) {
        btnRotateLeft.addEventListener('click', () => cropper && cropper.rotate(-90));
    }
    if (btnRotateRight) {
        btnRotateRight.addEventListener('click', () => cropper && cropper.rotate(90));
    }
    if (btnReset) {
        btnReset.addEventListener('click', () => cropper && cropper.reset());
    }

    if (cancelCropBtn) {
        cancelCropBtn.addEventListener('click', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            avatarFileInput.value = '';
            cropperImage.src = '';
            
            const formEl = document.getElementById('avatar-form');
            if (formEl) {
                if (formEl.__x) {
                    formEl.__x.$data.hasImage = false;
                } else if (window.Alpine) {
                    const alpineData = Alpine.$data(formEl);
                    if (alpineData) {
                        alpineData.hasImage = false;
                    }
                }
            }
            
            document.querySelectorAll('[x-show="hasImage"]').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('[x-show="!hasImage"]').forEach(el => {
                el.style.display = 'block';
            });
        });
    }

    avatarForm.addEventListener('submit', function (e) {
        if (cropper) {
            e.preventDefault();
            
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            if (canvas) {
                const base64Data = canvas.toDataURL('image/jpeg', 0.9);
                croppedAvatarInput.value = base64Data;
                avatarForm.submit();
            } else {
                alert('Có lỗi xảy ra khi xử lý ảnh đại diện.');
            }
        } else {
            e.preventDefault();
            alert('Vui lòng chọn ảnh đại diện trước khi lưu.');
        }
    });
});
</script>
@endpush
