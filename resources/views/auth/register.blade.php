@extends('layouts.app')

@section('title', 'Đăng Ký Thành Viên Mới | BDS Rental')

@section('content')
<div class="bg-slate-50 pt-24 min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col md:flex-row min-h-[620px]">
        
        <!-- Left Side: Image Illustration -->
        <div class="md:w-1/2 relative bg-slate-900 overflow-hidden hidden md:block">
            <img 
                src="{{ asset('images/apartment_2.png') }}" 
                alt="Register illustration" 
                class="w-full h-full object-cover opacity-60 scale-105"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-10 left-10 right-10 text-left text-white z-10 space-y-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-primary/20 backdrop-blur-md text-cyan-300 border border-primary/20">
                    BDS Rental
                </span>
                <h2 class="text-2xl font-black leading-snug">Gia nhập cộng đồng cho thuê nhà lớn nhất Việt Nam</h2>
                <p class="text-xs text-slate-300 font-medium">Bắt đầu đăng tin miễn phí hoặc lưu lại những ngôi nhà bạn quan tâm một cách dễ dàng.</p>
            </div>
        </div>

        <!-- Right Side: Form Register -->
        <div class="md:w-1/2 p-8 sm:p-12 flex flex-col justify-center text-left">
            <div class="mb-6">
                <!-- Mini Logo -->
                <div class="flex items-center space-x-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                        <i class="fa-solid fa-house-chimney text-sm"></i>
                    </div>
                    <span class="font-bold text-lg tracking-tight text-slate-800">
                        BDS<span class="text-primary">Rental</span>
                    </span>
                </div>
                <h3 class="text-xl font-bold text-slate-800 leading-tight">Đăng ký thành viên mới</h3>
                <p class="text-xs text-slate-400 mt-1 font-semibold">Tạo tài khoản miễn phí và trải nghiệm tính năng tìm kiếm thông minh.</p>
            </div>

            <!-- Register Form -->
            <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                @csrf
                <!-- Input Full Name -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Họ và tên</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="text" 
                            name="name" 
                            required 
                            placeholder="Nhập họ và tên..."
                            value="{{ old('name') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                        >
                    </div>
                    @error('name')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Email -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Địa chỉ Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            placeholder="email@example.com"
                            value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Phone -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại</label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="tel" 
                            name="phone" 
                            required 
                            placeholder="09xx.xxx.xxx"
                            value="{{ old('phone') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                        >
                    </div>
                    @error('phone')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Choose Role -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 px-1">Loại tài khoản</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-between p-3.5 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl cursor-pointer transition select-none">
                            <div class="flex items-center space-x-2.5">
                                <i class="fa-solid fa-house-user text-xs text-slate-500"></i>
                                <span class="text-xs font-bold text-slate-700">Người thuê</span>
                            </div>
                            <input type="radio" name="role" value="tenant" {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary border-slate-200 cursor-pointer">
                        </label>
                        <label class="flex items-center justify-between p-3.5 bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl cursor-pointer transition select-none">
                            <div class="flex items-center space-x-2.5">
                                <i class="fa-solid fa-house-chimney-user text-xs text-slate-500"></i>
                                <span class="text-xs font-bold text-slate-700">Chủ nhà</span>
                            </div>
                            <input type="radio" name="role" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary border-slate-200 cursor-pointer">
                        </label>
                    </div>
                    @error('role')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Password & Confirm -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1" x-data="{ show: false }">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mật khẩu</label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password" 
                                required 
                                placeholder="Tối thiểu 8 ký tự..."
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
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
                    <div class="space-y-1" x-data="{ showConfirm: false }">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Xác nhận mật khẩu</label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input 
                                :type="showConfirm ? 'text' : 'password'" 
                                name="password_confirmation" 
                                required 
                                placeholder="Nhập lại mật khẩu..."
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                            >
                            <button 
                                type="button" 
                                @click="showConfirm = !showConfirm"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition focus:outline-none cursor-pointer"
                            >
                                <i :class="showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Accept Terms -->
                <div class="flex items-center pt-1">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" required class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200 cursor-pointer">
                        <span class="ml-2 text-xs font-bold text-slate-500 leading-normal">Tôi đồng ý với <a href="#" class="text-primary hover:underline">Điều khoản sử dụng</a> và <a href="#" class="text-primary hover:underline">Quy chế hoạt động</a></span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-3 px-4 rounded-xl shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 mt-2"
                >
                    Đăng ký tài khoản mới
                </button>
            </form>

            <!-- Redirect to Login -->
            <p class="text-xs text-slate-500 mt-6 font-semibold text-center">
                Bạn đã có tài khoản thành viên? <a href="/login" class="text-primary hover:underline font-bold">Đăng nhập ngay</a>
            </p>
        </div>

    </div>
</div>
@endsection
