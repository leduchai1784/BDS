@extends('layouts.app')

@section('title', 'Đăng tin mới | BDS Rental')

@section('content')
<div 
    x-data="{ 
        showWarning: false, 
        warningMessage: '',
        checkRole(role, targetUrl) {
            if (!role) {
                window.location.href = '{{ route('login') }}';
                return;
            }
            if (role !== 'owner') {
                this.warningMessage = role === 'admin' 
                    ? 'Tài khoản của bạn là Quản trị viên. Bạn cần tài khoản Chủ nhà / Môi giới để đăng tin.' 
                    : 'Tài khoản của bạn là Khách thuê. Bạn cần tài khoản Chủ nhà / Môi giới để đăng tin.';
                this.showWarning = true;
                return;
            }
            window.location.href = targetUrl;
        }
    }"
    class="bg-slate-50 min-h-screen flex flex-col font-sans pt-20"
>
    <!-- Main Selection Content -->
    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="max-w-xl w-full">
            <!-- Page Title -->
            <div class="text-center mb-8">
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight">Đăng tin mới</h1>
                <p class="text-xs lg:text-sm font-semibold text-slate-400 mt-2">Chọn hình thức giao dịch của bất động sản để bắt đầu</p>
            </div>
            <div class="flex flex-col gap-5 md:gap-6">
                
                @php
                    $saleUrl = Auth::check() ? route('properties.create', ['purpose' => 'sale']) : route('login');
                    $userRole = Auth::check() ? Auth::user()->role : '';
                @endphp
                <a 
                    href="javascript:void(0)"
                    @click="checkRole('{{ $userRole }}', '{{ $saleUrl }}')"
                    class="group relative bg-white rounded-[32px] border border-slate-100 shadow-sm shadow-slate-100/50 p-8 lg:p-10 flex items-center justify-between overflow-hidden h-44 lg:h-48 hover:shadow-2xl hover:shadow-slate-200/80 hover:-translate-y-1.5 transition-all duration-300 cursor-pointer"
                >
                    <!-- Watermark background icon (Subtle and behind text) -->
                    <div class="absolute -left-6 -bottom-8 text-slate-200 opacity-15 group-hover:opacity-25 transition-opacity duration-300 select-none pointer-events-none z-0">
                        <i class="fa-solid fa-house text-[130px] lg:text-[150px]"></i>
                    </div>

                    <!-- Card details (Left) -->
                    <div class="flex flex-col text-left relative z-10">
                        <span class="text-[10px] lg:text-xs font-black tracking-widest text-slate-400 uppercase">ĐĂNG TIN</span>
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-800 mt-2">Bán</h2>
                        <p class="text-xs lg:text-sm font-semibold text-slate-500 mt-1">Nhà, đất, căn hộ...</p>
                    </div>

                    <!-- Blue circular arrow button (Right) -->
                    <div class="w-11 h-11 lg:w-12 lg:h-12 rounded-full bg-primary hover:bg-primary-hover text-white flex items-center justify-center transition shadow-md shadow-primary/20 group-hover:scale-110 active:scale-95 flex-shrink-0 z-10">
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </div>
                </a>

                @php
                    $rentUrl = Auth::check() ? route('properties.create', ['purpose' => 'rent']) : route('login');
                @endphp
                <a 
                    href="javascript:void(0)"
                    @click="checkRole('{{ $userRole }}', '{{ $rentUrl }}')"
                    class="group relative bg-white rounded-[32px] border border-slate-100 shadow-sm shadow-slate-100/50 p-8 lg:p-10 flex items-center justify-between overflow-hidden h-44 lg:h-48 hover:shadow-2xl hover:shadow-slate-200/80 hover:-translate-y-1.5 transition-all duration-300 cursor-pointer"
                >
                    <!-- Watermark background icon (Subtle and behind text) -->
                    <div class="absolute -left-4 -bottom-6 text-slate-200 opacity-15 group-hover:opacity-25 transition-opacity duration-300 select-none pointer-events-none z-0">
                        <i class="fa-solid fa-key text-[130px] lg:text-[150px]"></i>
                    </div>

                    <!-- Card details (Left) -->
                    <div class="flex flex-col text-left relative z-10">
                        <span class="text-[10px] lg:text-xs font-black tracking-widest text-slate-400 uppercase">ĐĂNG TIN</span>
                        <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-800 mt-2">Cho thuê</h2>
                        <p class="text-xs lg:text-sm font-semibold text-slate-500 mt-1">Phòng trọ, mặt bằng...</p>
                    </div>

                    <!-- Blue circular arrow button (Right) -->
                    <div class="w-11 h-11 lg:w-12 lg:h-12 rounded-full bg-primary hover:bg-primary-hover text-white flex items-center justify-center transition shadow-md shadow-primary/20 group-hover:scale-110 active:scale-95 flex-shrink-0 z-10">
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </div>
                </a>

            </div>
        </div>
    </main>

    <!-- Account Permission Warning Modal -->
    <div 
        x-show="showWarning" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4" 
        x-cloak
    >
        <!-- Backdrop backdrop-blur -->
        <div 
            @click="showWarning = false" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>
        
        <!-- Modal dialog box -->
        <div 
            class="bg-white rounded-[32px] max-w-md w-full p-8 shadow-2xl relative z-10 border border-slate-100 text-center transform transition-all duration-300"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
            </div>
            
            <h3 class="text-xl font-extrabold text-slate-800 mb-3">Quyền hạn tài khoản</h3>
            <p class="text-sm font-semibold text-slate-500 leading-relaxed mb-6" x-text="warningMessage"></p>
            
            <div class="flex flex-col gap-2">
                <a 
                    href="{{ route('profile.index') }}" 
                    class="w-full inline-flex items-center justify-center px-5 py-3.5 bg-primary hover:bg-primary-hover text-sm font-bold rounded-2xl text-white shadow-lg shadow-primary/25 hover:shadow-primary/35 transition active:scale-98"
                >
                    Vào trang cá nhân
                </a>
                <button 
                    @click="showWarning = false" 
                    type="button" 
                    class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold rounded-2xl text-slate-500 hover:bg-slate-50 transition cursor-pointer"
                >
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
