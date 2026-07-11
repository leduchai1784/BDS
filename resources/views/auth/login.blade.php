@extends('layouts.app')

@section('title', 'Đăng Nhập Tài Khoản Thành Viên | BDS Rental')

@section('content')
@php
    $rememberedAccounts = [];
    $cookieValue = request()->cookie('remembered_accounts');
    if ($cookieValue) {
        $rememberedAccounts = json_decode($cookieValue, true) ?: [];
    }
@endphp
<div class="bg-slate-50 pt-24 min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col md:flex-row min-h-[550px]">
        
        <!-- Left Side: Image Illustration -->
        <div class="md:w-1/2 relative bg-slate-900 overflow-hidden hidden md:block">
            <img 
                src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" 
                alt="Login illustration" 
                class="w-full h-full object-cover opacity-80 scale-105"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-10 left-10 right-10 text-left text-white z-10 space-y-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-primary/20 backdrop-blur-md text-cyan-300 border border-primary/20">
                    BDS Rental
                </span>
                <h2 class="text-2xl font-black leading-snug">Tìm kiếm không gian sống mơ ước nhanh chóng và tin cậy</h2>
                <p class="text-xs text-slate-300 font-medium">Hàng ngàn tin đăng chính chủ xác thực mỗi ngày đang chờ bạn khám phá.</p>
            </div>
        </div>

        <!-- Right Side: Form Login -->
        <div class="md:w-1/2 p-8 sm:p-12 flex flex-col justify-center text-left" x-data="{
            accounts: {{ json_encode($rememberedAccounts) }},
            selectedEmail: '{{ !empty($rememberedAccounts) ? $rememberedAccounts[0]['email'] : '' }}',
            selectedName: '{{ !empty($rememberedAccounts) ? $rememberedAccounts[0]['name'] : '' }}',
            selectedAvatar: '{{ !empty($rememberedAccounts) ? $rememberedAccounts[0]['avatar'] : '' }}',
            showForm: true,
            selectAccount(email, name, avatar) {
                this.selectedEmail = email;
                this.selectedName = name;
                this.selectedAvatar = avatar;
                this.showForm = true;
                this.$nextTick(() => {
                    if (this.$refs.emailInput) this.$refs.emailInput.value = email;
                    if (this.$refs.passwordInput) this.$refs.passwordInput.focus();
                });
            },
            useDifferentAccount() {
                this.selectedEmail = '';
                this.selectedName = '';
                this.selectedAvatar = '';
                this.showForm = true;
                this.$nextTick(() => {
                    if (this.$refs.emailInput) {
                        this.$refs.emailInput.value = '';
                        this.$refs.emailInput.focus();
                    }
                });
            },
            removeAccount(email) {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('login.forget-account') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.accounts = this.accounts.filter(a => a.email !== email);
                        if (this.selectedEmail === email) {
                            if (this.accounts.length > 0) {
                                this.selectAccount(this.accounts[0].email, this.accounts[0].name, this.accounts[0].avatar);
                            } else {
                                this.useDifferentAccount();
                            }
                        }
                    }
                })
                .catch(err => console.error(err));
            }
        }">
            <!-- Form Header -->
            <div class="mb-8">
                <!-- Mini Logo -->
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                        <i class="fa-solid fa-house-chimney text-sm"></i>
                    </div>
                    <span class="font-bold text-lg tracking-tight text-slate-800">
                        BDS<span class="text-primary">Rental</span>
                    </span>
                </div>
                <h3 class="text-xl font-bold text-slate-800 leading-tight">Chào mừng quay trở lại!</h3>
                <p class="text-xs text-slate-400 mt-1 font-semibold">Đăng nhập tài khoản của bạn để tiếp tục sử dụng dịch vụ.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50/70 border-l-4 border-emerald-500 rounded-2xl text-emerald-700 text-xs font-bold flex items-start space-x-3 shadow-sm border border-emerald-100">
                    <i class="fa-solid fa-circle-check text-base text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-black text-emerald-800">Thành công</p>
                        <p class="text-[11px] font-semibold text-emerald-700/95 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Login Form view -->
            <div x-show="showForm" x-cloak>
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Selected Account Banner with Dropdown -->
                    <template x-if="selectedEmail">
                        <div class="relative mb-5" x-data="{ openDropdown: false }" @click.away="openDropdown = false">
                            <div class="flex items-center space-x-3.5 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-left">
                                <img :src="selectedAvatar" :alt="selectedName" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0">
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-xs font-extrabold text-slate-800 truncate" x-text="selectedName"></h4>
                                    <p class="text-[10px] font-semibold text-slate-400 truncate" x-text="selectedEmail"></p>
                                </div>
                                <button type="button" @click="openDropdown = !openDropdown" class="text-xs font-bold text-primary hover:underline cursor-pointer flex items-center gap-1 select-none">
                                    Thay đổi <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200" :class="openDropdown ? 'rotate-180' : ''"></i>
                                </button>
                            </div>

                            <!-- Dropdown accounts list -->
                            <div x-show="openDropdown" x-transition class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-2 space-y-1 max-h-[220px] overflow-y-auto">
                                <template x-for="acc in accounts.filter(a => a.email !== selectedEmail)" :key="acc.email">
                                    <div class="flex items-center justify-between p-2.5 hover:bg-slate-50 border border-transparent hover:border-slate-100 rounded-xl transition cursor-pointer" @click="selectAccount(acc.email, acc.name, acc.avatar); openDropdown = false">
                                        <div class="flex items-center space-x-3 min-w-0">
                                            <img :src="acc.avatar" :alt="acc.name" class="w-8 h-8 rounded-full object-cover border border-slate-100 flex-shrink-0">
                                            <div class="min-w-0 text-left">
                                                <h5 class="text-[11px] font-extrabold text-slate-800 truncate" x-text="acc.name"></h5>
                                                <p class="text-[9px] font-semibold text-slate-400 truncate" x-text="acc.email"></p>
                                            </div>
                                        </div>
                                        <button type="button" @click.stop="removeAccount(acc.email)" class="flex items-center justify-center w-6 h-6 text-slate-400 hover:text-red-650 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Xóa ghi nhớ">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </button>
                                    </div>
                                </template>
                                <div class="border-t border-slate-100 my-1"></div>
                                <div class="flex items-center p-2.5 hover:bg-slate-50 border border-transparent hover:border-slate-100 rounded-xl transition cursor-pointer text-slate-600 hover:text-primary text-left" @click="useDifferentAccount(); openDropdown = false">
                                    <i class="fa-solid fa-user-plus text-[10px] mr-2 text-primary"></i>
                                    <span class="text-[11px] font-bold">Sử dụng tài khoản khác</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Input Email (Hidden if selected account is active) -->
                    <div class="space-y-1" x-show="!selectedEmail">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 px-1">Địa chỉ Email</label>
                            <template x-if="accounts.length > 0">
                                <div class="relative" x-data="{ openSelector: false }" @click.away="openSelector = false">
                                    <button type="button" @click="openSelector = !openSelector" class="text-[10px] font-bold text-primary hover:underline cursor-pointer">
                                        Chọn tài khoản đã lưu <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200" :class="openSelector ? 'rotate-180' : ''"></i>
                                    </button>
                                    <!-- Popover account list -->
                                    <div x-show="openSelector" x-transition class="absolute right-0 top-full mt-1 w-64 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-2 space-y-1">
                                        <template x-for="acc in accounts" :key="acc.email">
                                            <div class="flex items-center justify-between p-2 hover:bg-slate-50 border border-transparent hover:border-slate-100 rounded-xl transition cursor-pointer" @click="selectAccount(acc.email, acc.name, acc.avatar); openSelector = false">
                                                <div class="flex items-center space-x-3 min-w-0">
                                                    <img :src="acc.avatar" :alt="acc.name" class="w-8 h-8 rounded-full object-cover border border-slate-100 flex-shrink-0">
                                                    <div class="min-w-0 text-left">
                                                        <h5 class="text-[11px] font-extrabold text-slate-800 truncate" x-text="acc.name"></h5>
                                                        <p class="text-[9px] font-semibold text-slate-400 truncate" x-text="acc.email"></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click.stop="removeAccount(acc.email)" class="flex items-center justify-center w-6 h-6 text-slate-400 hover:text-red-650 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Xóa ghi nhớ">
                                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input 
                                type="email" 
                                name="email" 
                                x-ref="emailInput"
                                :required="!selectedEmail" 
                                placeholder="email@example.com"
                                value="{{ old('email') }}"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                            >
                        </div>
                        @error('email')
                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Email (Hidden fallback always submitting selected email) -->
                    <template x-if="selectedEmail">
                        <input type="hidden" name="email" :value="selectedEmail">
                    </template>
     
                    <!-- Input Password -->
                    <div class="space-y-1" x-data="{ show: false }">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 px-1">Mật khẩu</label>
                            <a href="#" class="text-[10px] font-bold text-primary hover:underline">Quên mật khẩu?</a>
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password" 
                                x-ref="passwordInput"
                                required 
                                placeholder="Nhập mật khẩu..."
                                class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                            >
                            <button 
                                type="button" 
                                @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition focus:outline-none cursor-pointer"
                            >
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-xs"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
     
                    <!-- Remember Me & Submit -->
                    <div class="flex items-center justify-between pt-1" x-show="!selectedEmail">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200 cursor-pointer" {{ old('remember') ? 'checked' : '' }}>
                            <span class="ml-2 text-xs font-bold text-slate-500">Ghi nhớ đăng nhập</span>
                        </label>
                    </div>
     
                    <button 
                        type="submit" 
                        class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-3.5 px-4 rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 mt-2"
                    >
                        Đăng nhập tài khoản
                    </button>
                </form>
            </div>

            <!-- Social Login Options -->
            <div class="mt-8">
                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="flex-shrink mx-4 text-[10px] text-slate-400 font-bold uppercase tracking-wider">Hoặc đăng nhập bằng</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <button class="inline-flex justify-center items-center px-4 py-2.5 border border-slate-200 hover:border-slate-350 bg-white rounded-xl text-xs font-bold text-slate-600 transition cursor-pointer">
                        <i class="fa-brands fa-google text-red-500 mr-2 text-sm"></i> Google
                    </button>
                    <button class="inline-flex justify-center items-center px-4 py-2.5 border border-slate-200 hover:border-slate-350 bg-white rounded-xl text-xs font-bold text-slate-600 transition cursor-pointer">
                        <i class="fa-brands fa-facebook-f text-blue-600 mr-2 text-sm"></i> Facebook
                    </button>
                </div>
            </div>

            <!-- Redirect to Register -->
            <p class="text-xs text-slate-500 mt-8 font-semibold text-center">
                Bạn chưa có tài khoản? <a href="/register" class="text-primary hover:underline font-bold">Đăng ký thành viên ngay</a>
            </p>
        </div>

    </div>
</div>
@endsection
