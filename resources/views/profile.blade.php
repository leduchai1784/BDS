@extends('layouts.app')

@section('title', 'Dashboard Quản Lý Thành Viên | BDS Rental')

@section('content')
<!-- Dashboard Wrapper with AlpineJS -->
<div 
    x-data="{ 
        activeTab: '{{ session('success') || $errors->has('current_password') || $errors->has('new_password') ? ($errors->any() ? 'password' : 'profile') : 'profile' }}', 
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Dashboard Sidebar Navigation (3/12 cols) -->
            <div class="lg:col-span-3 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-left">
                <!-- User Summary Header -->
                <div class="p-6 text-center border-b border-slate-100 bg-slate-50/50">
                    <div class="relative w-20 h-20 mx-auto mb-3.5">
                        <img 
                            src="{{ $user['avatar'] }}" 
                            alt="{{ $user['name'] }}" 
                            class="w-full h-full rounded-full object-cover border-2 border-primary/20 shadow-md"
                        >
                        <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full bg-green-500 border-2 border-white flex items-center justify-center" title="Đang trực tuyến"></span>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 leading-none mb-1.5">{{ $user['name'] }}</h4>
                    <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">{{ $user['role'] }}</span>
                    <span class="text-[9px] text-slate-400 font-medium">Thành viên từ: {{ $user['join_date'] }}</span>
                </div>

                <!-- Navigation List (AlpineJS Tab Switchers) -->
                <nav class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible scrollbar-none border-b lg:border-b-0 border-slate-100">
                    <button 
                        @click="activeTab = 'profile'" 
                        :class="activeTab === 'profile' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                        class="flex items-center space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                    >
                        <i class="fa-solid fa-user-gear text-sm"></i>
                        <span>Thông tin cá nhân</span>
                    </button>
                    
                    <button 
                        @click="activeTab = 'favorites'" 
                        :class="activeTab === 'favorites' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                        class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                    >
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-heart text-sm"></i>
                            <span>Tin yêu thích</span>
                        </div>
                        <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">3</span>
                    </button>

                    <button 
                        @click="activeTab = 'appointments'" 
                        :class="activeTab === 'appointments' ? 'bg-primary-light text-primary border-primary' : 'text-slate-600 border-transparent hover:bg-slate-50 hover:text-primary'"
                        class="flex items-center justify-between space-x-3 px-5 py-4 text-xs font-bold border-b-2 lg:border-b-0 lg:border-l-4 whitespace-nowrap flex-grow lg:flex-grow-0 cursor-pointer transition focus:outline-none"
                    >
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-calendar-days text-sm"></i>
                            <span>Lịch hẹn xem nhà</span>
                        </div>
                        <span class="hidden lg:inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px]">2</span>
                    </button>

                    <button 
                        @click="activeTab = 'password'" 
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
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                        <span>Trang quản trị (Admin)</span>
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
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Stat Item 1 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-primary-light text-primary flex items-center justify-center text-lg">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin đã đăng</span>
                                <span class="text-xl font-black text-slate-800">4 tin</span>
                            </div>
                        </div>

                        <!-- Stat Item 2 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin yêu thích</span>
                                <span class="text-xl font-black text-slate-800">12 tin</span>
                            </div>
                        </div>

                        <!-- Stat Item 3 -->
                        <div class="bg-slate-50 border border-slate-100/50 p-5 rounded-2xl flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn xem nhà</span>
                                <span class="text-xl font-black text-slate-800">2 cuộc</span>
                            </div>
                        </div>
                    </div>

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

                <!-- TAB 2: Saved / Favorite Listings (Giai đoạn 7) -->
                <div x-show="activeTab === 'favorites'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Tin yêu thích đã lưu</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Xem danh sách các căn hộ, biệt thự bạn đã lưu yêu thích để dễ dàng tham khảo lại.</p>
                    </div>

                    <!-- Favorites Property Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                            // Simulating favorites by showing first 3 mock properties
                            $favoriteProperties = collect($properties)->take(3);
                        @endphp
                        @foreach($favoriteProperties as $property)
                            @include('components.property-card', ['property' => $property])
                        @endforeach
                    </div>
                </div>

                <!-- TAB 3: Viewing Appointments (Giai đoạn 7) -->
                <div x-show="activeTab === 'appointments'" x-transition:enter="transition duration-150" class="space-y-6" x-cloak>
                    <div class="pb-5 border-b border-slate-100 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Lịch hẹn xem nhà</h2>
                        <p class="text-xs text-slate-400 mt-1 font-semibold">Theo dõi danh sách và trạng thái lịch đi xem nhà trực tiếp của bạn.</p>
                    </div>

                    <!-- Appointments Table -->
                    <div class="overflow-x-auto border border-slate-150/80 rounded-2xl shadow-sm bg-white">
                        <table class="min-w-full divide-y divide-slate-100 text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th scope="col" class="px-5 py-4">Mã số</th>
                                    <th scope="col" class="px-5 py-4">Ngày hẹn</th>
                                    <th scope="col" class="px-5 py-4">Bất động sản</th>
                                    <th scope="col" class="px-5 py-4">Môi giới</th>
                                    <th scope="col" class="px-5 py-4">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-semibold">
                                <!-- Row 1 -->
                                <tr>
                                    <td class="px-5 py-4 text-slate-900 font-bold">#BK-8302</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="block">10/06/2026</span>
                                        <span class="text-[10px] text-slate-400">14:00 - 16:00</span>
                                    </td>
                                    <td class="px-5 py-4 max-w-[200px] truncate">
                                        <a href="/property/1" class="hover:text-primary font-bold text-slate-800">Căn hộ Vinhomes Ocean Park Studio</a>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">Nguyễn Hải Đăng</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                            Đã xác nhận
                                        </span>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr>
                                    <td class="px-5 py-4 text-slate-900 font-bold">#BK-9271</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="block">15/06/2026</span>
                                        <span class="text-[10px] text-slate-400">08:00 - 10:00</span>
                                    </td>
                                    <td class="px-5 py-4 max-w-[200px] truncate">
                                        <a href="/property/3" class="hover:text-primary font-bold text-slate-800">Biệt thự sân vườn hiện đại Ciputra</a>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">Lê Hoàng Long</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Đang chờ duyệt
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

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
