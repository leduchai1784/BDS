@extends('layouts.app')

@section('title', (!empty($agent->company) ? $agent->company : $agent->name) . ' | BDS Rental')

@section('content')

{{-- ========== HERO PROFILE BANNER ========== --}}
<div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 pt-24 pb-0 overflow-hidden">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-16">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-400 font-semibold mb-8">
            <a href="/" class="hover:text-white transition">Trang chủ</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('agents.index', !empty($agent->company) ? ['type' => 'company'] : []) }}" class="hover:text-white transition">
                {{ !empty($agent->company) ? 'Doanh nghiệp' : 'Môi giới' }}
            </a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-white truncate max-w-xs">{{ !empty($agent->company) ? $agent->company : $agent->name }}</span>
        </nav>

        {{-- Profile Header --}}
        <div class="flex flex-col md:flex-row items-center md:items-end gap-6">
            {{-- Logo / Avatar --}}
            <div class="relative flex-shrink-0">
                <img
                    src="{{ $agent->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(!empty($agent->company) ? $agent->company : $agent->name) . '&background=0077bb&color=fff&bold=true&size=256' }}"
                    alt="{{ !empty($agent->company) ? $agent->company : $agent->name }}"
                    class="w-28 h-28 md:w-32 md:h-32 {{ !empty($agent->company) ? 'rounded-2xl' : 'rounded-full' }} object-cover border-4 border-white/20 shadow-2xl"
                >
                @if(!empty($agent->id_number))
                    <span class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-500 rounded-full border-3 border-white flex items-center justify-center shadow-lg" title="Đã xác thực danh tính">
                        <i class="fa-solid fa-check text-white text-xs"></i>
                    </span>
                @endif
            </div>

            {{-- Name & Meta --}}
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                    @if(!empty($agent->company))
                        <span class="inline-flex items-center gap-1 border text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider" style="background:rgba(0,119,187,0.25);border-color:rgba(0,119,187,0.4);color:#60c8ff">
                            <i class="fa-solid fa-building text-[10px]"></i> Doanh nghiệp
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 bg-blue-500/30 text-blue-300 border border-blue-400/40 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">
                            <i class="fa-solid fa-user-tie text-[10px]"></i> Nhà môi giới
                        </span>
                    @endif
                    @if(!empty($agent->id_number))
                        <span class="inline-flex items-center gap-1 bg-blue-600/30 text-blue-300 border border-blue-400/30 text-xs font-bold px-3 py-1 rounded-full">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i> Đã xác thực danh tính
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-black text-white mb-1">
                    {{ !empty($agent->company) ? $agent->company : $agent->name }}
                </h1>
                @if(!empty($agent->company))
                    <p class="text-slate-400 text-sm font-medium">
                        <i class="fa-solid fa-user mr-1.5"></i>Đại diện: <span class="text-slate-200 font-semibold">{{ $agent->name }}</span>
                    </p>
                @endif
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mt-3 text-sm text-slate-400">
                    @if($agent->add_province)
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-primary/80"></i>
                            {{ $agent->add_district ? $agent->add_district . ', ' : '' }}{{ $agent->add_province }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-newspaper text-primary/80"></i>
                        {{ $saleProperties->count() + $rentProperties->count() }} tin đăng đang hoạt động
                    </span>
                </div>
            </div>

            {{-- Action buttons (desktop) --}}
            <div class="flex-shrink-0 flex flex-col gap-2 min-w-[180px]">
                <a href="tel:{{ $agent->phone }}"
                   class="inline-flex items-center justify-center gap-2 py-3 px-6 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-xl transition shadow-lg shadow-primary/30">
                    <i class="fa-solid fa-phone"></i> {{ $agent->phone ?? 'Gọi điện' }}
                </a>
                @if($agent->phone)
                    <a href="https://zalo.me/{{ $agent->phone }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 py-3 px-6 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl transition">
                        <i class="fa-solid fa-comment"></i> Chat Zalo
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats bar at bottom of hero --}}
    <div class="relative bg-white/5 backdrop-blur-sm border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex divide-x divide-white/10">
                <div class="flex-1 py-4 text-center">
                    <div class="text-xl font-black text-white">{{ $saleProperties->count() }}</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Tin bán</div>
                </div>
                <div class="flex-1 py-4 text-center">
                    <div class="text-xl font-black text-white">{{ $rentProperties->count() }}</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Tin cho thuê</div>
                </div>
                <div class="flex-1 py-4 text-center">
                    <div class="text-xl font-black text-white">{{ $saleProperties->count() + $rentProperties->count() }}</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Tổng tin đăng</div>
                </div>
                <div class="flex-1 py-4 text-center">
                    <div class="text-xl font-black text-green-400">✓</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Uy tín</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== MAIN CONTENT ========== --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- ===== LEFT SIDEBAR ===== --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Contact Card --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-5 py-3.5 border-b border-slate-100">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-address-card text-primary mr-1.5"></i>Thông tin liên hệ
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        @if($agent->phone)
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                    <i class="fa-solid fa-phone text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Điện thoại</div>
                                    <a href="tel:{{ $agent->phone }}" class="text-sm font-extrabold text-slate-900 hover:text-primary transition">{{ $agent->phone }}</a>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email</div>
                                <span class="text-sm font-semibold text-slate-800 truncate block">{{ $agent->email }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-sm"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Khu vực</div>
                                <span class="text-sm font-semibold text-slate-800">
                                    {{ $agent->add_province ?? 'Toàn quốc' }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 space-y-2">
                            <a href="tel:{{ $agent->phone }}"
                               class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-sm font-bold transition shadow shadow-primary/20">
                                <i class="fa-solid fa-phone"></i> Gọi điện ngay
                            </a>
                            @if($agent->phone)
                                <a href="https://zalo.me/{{ $agent->phone }}" target="_blank"
                                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold transition">
                                    <i class="fa-solid fa-comment"></i> Chat qua Zalo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Intro panel --}}
                @if(!empty($agent->intro))
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider mb-3">
                            <i class="fa-solid fa-circle-info text-primary mr-1.5"></i>Giới thiệu
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $agent->intro }}</p>
                    </div>
                @endif

                {{-- Back button --}}
                <a href="{{ route('agents.index', !empty($agent->company) ? ['type' => 'company'] : []) }}"
                   class="flex items-center gap-2 text-sm text-slate-500 hover:text-primary font-semibold transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    Quay lại danh sách {{ !empty($agent->company) ? 'doanh nghiệp' : 'môi giới' }}
                </a>
            </div>

            {{-- ===== LISTINGS PANEL ===== --}}
            <div class="lg:col-span-3" x-data="{ activeTab: 'rent' }">

                {{-- Tab header --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                    <div class="flex border-b border-slate-100 bg-slate-50/60">
                        <button
                            @click="activeTab = 'rent'"
                            class="flex-1 py-4 px-5 text-sm font-extrabold rounded-none transition duration-150 cursor-pointer flex items-center justify-center gap-2"
                            :class="activeTab === 'rent' ? 'bg-white text-primary border-b-2 border-primary shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                            <i class="fa-solid fa-key"></i>
                            Cho thuê
                            <span class="bg-primary/10 text-primary text-xs px-2 py-0.5 rounded-full font-black">{{ $rentProperties->count() }}</span>
                        </button>
                        <button
                            @click="activeTab = 'sale'"
                            class="flex-1 py-4 px-5 text-sm font-extrabold rounded-none transition duration-150 cursor-pointer flex items-center justify-center gap-2"
                            :class="activeTab === 'sale' ? 'bg-white text-primary border-b-2 border-primary shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                            <i class="fa-solid fa-tags"></i>
                            Đang bán
                            <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-black">{{ $saleProperties->count() }}</span>
                        </button>
                    </div>

                    <div class="p-6 md:p-8">
                        {{-- Rent Tab --}}
                        <div x-show="activeTab === 'rent'" x-cloak>
                            @if($rentProperties->isEmpty())
                                <div class="text-center py-14 text-slate-400">
                                    <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-key text-xl"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">Chưa có tin cho thuê</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ !empty($agent->company) ? 'Doanh nghiệp' : 'Môi giới' }} này chưa đăng tin cho thuê nào.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    @foreach($rentProperties as $property)
                                        @include('components.property-card', ['property' => $property])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Sale Tab --}}
                        <div x-show="activeTab === 'sale'" x-cloak>
                            @if($saleProperties->isEmpty())
                                <div class="text-center py-14 text-slate-400">
                                    <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-tags text-xl"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">Chưa có tin đăng bán</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ !empty($agent->company) ? 'Doanh nghiệp' : 'Môi giới' }} này chưa đăng tin bán nào.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    @foreach($saleProperties as $property)
                                        @include('components.property-card', ['property' => $property])
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
