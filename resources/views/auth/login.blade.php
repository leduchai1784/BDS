@extends('layouts.app')

@section('title', 'Đăng Nhập Tài Khoản Thành Viên | BDS Rental')

@section('content')
<div class="bg-slate-50 pt-24 min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 flex flex-col md:flex-row min-h-[550px]">
        
        <!-- Left Side: Image Illustration -->
        <div class="md:w-1/2 relative bg-slate-900 overflow-hidden hidden md:block">
            <img 
                src="{{ asset('images/hero_bg.png') }}" 
                alt="Login illustration" 
                class="w-full h-full object-cover opacity-60 scale-105"
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
        <div class="md:w-1/2 p-8 sm:p-12 flex flex-col justify-center text-left">
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

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
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
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
 
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
