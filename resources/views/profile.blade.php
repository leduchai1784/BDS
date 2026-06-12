@extends('layouts.app')

@section('title', 'Dashboard Quản Lý Thành Viên | BDS Rental')

@section('content')
<!-- Dashboard Wrapper with AlpineJS -->
<div 
    x-data="{ 
        activeTab: '{{ request('tab') ?? (session('success') || $errors->has('current_password') || $errors->has('new_password') ? ($errors->any() ? 'password' : 'profile') : 'profile') }}', 
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
                            :class="activeTab === 'properties' || activeTab === 'create_property' || activeTab === 'edit_property' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
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

                        @if(Auth::user()->role === 'tenant')
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
                                {{ $stats['total_favorites'] }}
                            </span>
                        </button>

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
                            @click="activeTab = 'password'; window.history.pushState(null, '', '?tab=password');" 
                            :class="activeTab === 'password' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
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

                        <a 
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form-profile').submit();"
                            class="flex items-center space-x-3 px-5 py-4 text-xs font-bold text-red-500 hover:bg-red-50 hover:text-red-600 border-b-2 lg:border-b-0 lg:border-l-4 border-transparent whitespace-nowrap flex-grow lg:flex-grow-0"
                        >
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                            <span>Đăng xuất</span>
                        </a>
                        <form id="logout-form-profile" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
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

                    <!-- Profile Form (Real POST form) -->
                    <form 
                        action="{{ route('profile.update') }}"
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
                        class="space-y-6 pt-2"
                    >
                        @csrf
                        <!-- Avatar change row -->
                        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 pb-6 border-b border-slate-100">
                            <div class="w-20 h-20 rounded-full overflow-hidden border border-slate-200 shadow-sm bg-slate-150 flex-shrink-0">
                                <img :src="avatarUrl" alt="Avatar preview" class="w-full h-full object-cover">
                            </div>
                            <div class="text-center sm:text-left space-y-2.5">
                                <h4 class="text-xs font-bold text-slate-700">Ảnh đại diện</h4>
                                <p class="text-[10px] text-slate-400 max-w-sm leading-normal">Định dạng JPG, PNG tối đa 2MB. Tải ảnh lên và bấm Lưu thay đổi bên dưới.</p>
                                <label class="inline-flex items-center justify-center px-4 py-2 border border-slate-200 hover:border-primary text-xs font-bold rounded-xl text-slate-700 hover:text-white bg-slate-50 hover:bg-primary shadow-sm transition cursor-pointer">
                                    <i class="fa-solid fa-camera mr-2 text-xs"></i> Chọn ảnh đại diện
                                    <input type="file" name="avatar" accept="image/*" @change="previewAvatar($event)" class="hidden">
                                </label>
                                @error('avatar')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Fields Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Họ tên -->
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

                            <!-- Vai trò -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Loại tài khoản</label>
                                <div class="relative">
                                    <i class="fa-solid fa-users-gear absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <select 
                                        name="role"
                                        disabled
                                        class="w-full pl-10 pr-8 py-2.5 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl text-xs font-semibold outline-none appearance-none cursor-not-allowed transition"
                                    >
                                        <option value="Chủ nhà / Môi giới" {{ $user['role'] == 'Chủ nhà / Môi giới' ? 'selected' : '' }}>Chủ nhà / Môi giới</option>
                                        <option value="Thành viên thuê nhà" {{ $user['role'] == 'Thành viên thuê nhà' ? 'selected' : '' }}>Thành viên thuê nhà</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Form Submit -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                            <button 
                                type="submit" 
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
                            >
                                <span>Lưu thay đổi</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Saved / Favorite Listings OR Owner Properties -->
                @if(Auth::user()->role === 'owner')
                <div x-show="activeTab === 'properties'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Quản lý tin đăng</h2>
                            <p class="text-xs text-slate-400 mt-1 font-semibold">Thêm, sửa, xóa và theo dõi trạng thái các bất động sản của bạn.</p>
                        </div>
                        <button 
                            type="button" 
                            @click="activeTab = 'create_property'; window.history.pushState(null, '', '?tab=create_property');" 
                            class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98"
                        >
                            <i class="fa-solid fa-plus mr-2"></i> Đăng tin mới
                        </button>
                    </div>

                    @if($myProperties->isEmpty())
                        <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-xl">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500">Bạn chưa đăng bất kỳ bất động sản nào.</p>
                            <p class="text-[10px] text-slate-400 mt-1">Bấm nút "Đăng tin mới" ở trên để bắt đầu.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-slate-150/80 rounded-2xl shadow-sm bg-white">
                            <table class="min-w-full divide-y divide-slate-100 text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <tr>
                                        <th scope="col" class="px-5 py-4">Bất động sản</th>
                                        <th scope="col" class="px-5 py-4">Danh mục</th>
                                        <th scope="col" class="px-5 py-4">Giá thuê</th>
                                        <th scope="col" class="px-5 py-4">Diện tích</th>
                                        <th scope="col" class="px-5 py-4">Trạng thái</th>
                                        <th scope="col" class="px-5 py-4 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-semibold">
                                    @foreach($myProperties as $prop)
                                        <tr>
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
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- TAB 2.1: Create Property (Embedded) -->
                @if(Auth::user()->role === 'owner')
                <div x-show="activeTab === 'create_property'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    @include('owner.properties.create_form')
                </div>
                @endif

                <!-- TAB 2.2: Edit Property (Embedded) -->
                @if(Auth::user()->role === 'owner')
                <div x-show="activeTab === 'edit_property'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    @if(isset($property) && $property)
                        @include('owner.properties.edit_form')
                    @else
                        <div class="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                            <p class="text-xs font-bold text-slate-500">Không tìm thấy thông tin bất động sản cần chỉnh sửa.</p>
                        </div>
                    @endif
                </div>
                @endif
                @elseif(Auth::user()->role === 'tenant')
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
                @endif

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

                <!-- TAB 4: Change Password (Giai đoạn 7) -->
                <div x-show="activeTab === 'password'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Đổi mật khẩu tài khoản</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Để bảo mật tài khoản, vui lòng thiết lập mật khẩu có độ dài tối thiểu 6 ký tự.</p>
                    </div>

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

                        <!-- Submit Button -->
                        <div class="pt-4 flex justify-end">
                            <button 
                                type="submit" 
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
                            >
                                <span>Cập nhật mật khẩu</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
            
        </div>
    </div>
</div>
@endsection
