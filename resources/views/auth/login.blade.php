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
            selectedEmail: '',
            selectedName: '',
            selectedAvatar: '',
            showForm: {{ (count($errors) > 0 || old('email')) ? 'true' : (empty($rememberedAccounts) ? 'true' : 'false') }},
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
                fetch('{{ route('login.forget-account') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.accounts = this.accounts.filter(a => a.email !== email);
                        if (this.accounts.length === 0) {
                            this.showForm = true;
                        }
                    }
                });
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
                <p class="text-xs text-slate-400 mt-1 font-semibold" x-text="showForm ? 'Đăng nhập tài khoản của bạn để tiếp tục sử dụng dịch vụ.' : 'Chọn một tài khoản đã ghi nhớ trên thiết bị này.'"></p>
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

            <!-- Account Chooser view -->
            <div x-show="!showForm" class="space-y-4">
                <div class="space-y-2.5 max-h-[300px] overflow-y-auto pr-1">
                    <template x-for="acc in accounts" :key="acc.email">
                        <div class="group/item flex items-center justify-between p-3.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 hover:border-primary/20 rounded-2xl transition cursor-pointer" @click="selectAccount(acc.email, acc.name, acc.avatar)">
                            <div class="flex items-center space-x-3.5 min-w-0">
                                <img :src="acc.avatar" :alt="acc.name" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0">
                                <div class="min-w-0 text-left">
                                    <h4 class="text-xs font-extrabold text-slate-800 truncate" x-text="acc.name"></h4>
                                    <p class="text-[10px] font-semibold text-slate-400 truncate" x-text="acc.email"></p>
                                </div>
                            </div>
                            <button type="button" @click.stop="removeAccount(acc.email)" class="flex items-center justify-center w-7 h-7 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-200/50 transition cursor-pointer" title="Xóa ghi nhớ">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="useDifferentAccount()" class="w-full inline-flex justify-center items-center py-3.5 px-4 border border-slate-200 hover:border-slate-350 bg-white rounded-xl text-xs font-bold text-slate-600 transition cursor-pointer">
                    <i class="fa-solid fa-user-plus mr-2 text-primary"></i> Sử dụng tài khoản khác
                </button>
            </div>

            <!-- Login Form view -->
            <div x-show="showForm" x-cloak>
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Selected Account Banner -->
                    <template x-if="selectedEmail">
                        <div class="flex items-center space-x-3.5 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl mb-4 text-left">
                            <img :src="selectedAvatar" :alt="selectedName" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0">
                            <div class="flex-grow min-w-0">
                                <h4 class="text-xs font-extrabold text-slate-800 truncate" x-text="selectedName"></h4>
                                <p class="text-[10px] font-semibold text-slate-400 truncate" x-text="selectedEmail"></p>
                            </div>
                            <button type="button" @click="showForm = false; selectedEmail = ''; selectedName = ''; selectedAvatar = ''" class="text-xs font-bold text-primary hover:underline cursor-pointer">Thay đổi</button>
                        </div>
                    </template>

                    <!-- Input Email (Hidden if selected account is active) -->
                    <div class="space-y-1" x-show="!selectedEmail">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Địa chỉ Email</label>
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
                    <div class="flex items-center justify-between pt-1">
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

                <!-- Back button to list if accounts list is not empty -->
                <template x-if="accounts.length > 0">
                    <button type="button" @click="showForm = false; selectedEmail = ''" class="w-full inline-flex justify-center items-center py-3 px-4 border border-slate-200 hover:border-slate-350 bg-white rounded-xl text-xs font-bold text-slate-600 transition cursor-pointer mt-3">
                        <i class="fa-solid fa-arrow-left-long mr-2"></i> Quay lại chọn tài khoản
                    </button>
                </template>
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
