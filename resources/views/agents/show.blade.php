@extends('layouts.app')

@section('title', (!empty($agent->company) ? $agent->company : $agent->name) . ' - BDS Rental')

@section('content')
<div class="pt-24 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm font-semibold text-slate-500">
            <a href="/" class="hover:text-primary">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('agents.index') }}" class="hover:text-primary">Môi giới</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800 font-bold truncate">{{ !empty($agent->company) ? $agent->company : $agent->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Side: Agent profile card -->
            <div class="lg:col-span-1 space-y-6 text-left">
                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm flex flex-col items-center text-center">
                    <!-- Profile avatar with verified badge -->
                    <div class="relative w-28 h-28 mb-4">
                        <img 
                            src="{{ $agent->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(!empty($agent->company) ? $agent->company : $agent->name) . '&background=0077bb&color=fff' }}" 
                            alt="{{ !empty($agent->company) ? $agent->company : $agent->name }}" 
                            class="w-full h-full {{ !empty($agent->company) ? 'rounded-2xl shadow-sm' : 'rounded-full' }} object-cover border-4 border-slate-50 shadow-inner"
                        >
                        @if(!empty($agent->id_number))
                            <span class="absolute bottom-1 right-1 w-7 h-7 bg-blue-500 rounded-full border-2 border-white flex items-center justify-center text-white text-[11px] shadow-md" title="Môi giới đã xác thực danh tính">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        @endif
                    </div>

                    <h2 class="text-xl font-extrabold text-slate-900 mb-1">
                        {{ !empty($agent->company) ? $agent->company : $agent->name }}
                    </h2>
                    <span class="bg-primary/10 text-primary text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider mb-4 block">
                        {{ !empty($agent->company) ? 'Đại diện: ' . $agent->name : 'Môi giới độc lập' }}
                    </span>

                    <hr class="w-full border-slate-100 mb-4">

                    <!-- Contact Details -->
                    <div class="w-full text-left space-y-3.5 text-sm text-slate-600 mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <span class="font-extrabold text-slate-800">{{ $agent->phone }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <span class="truncate font-semibold">{{ $agent->email }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-primary flex-shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <span class="font-semibold">{{ $agent->add_province ? $agent->add_district . ', ' . $agent->add_province : 'Toàn quốc' }}</span>
                        </div>
                    </div>

                    <!-- Instant CTA buttons -->
                    <div class="w-full space-y-2">
                        <a 
                            href="tel:{{ $agent->phone }}" 
                            class="w-full inline-flex items-center justify-center py-3 px-4 rounded-2xl bg-primary hover:bg-primary-hover text-white text-sm font-extrabold transition shadow-lg shadow-primary/20"
                        >
                            <i class="fa-solid fa-phone mr-2"></i> Gọi điện ngay
                        </a>
                        @if($agent->phone)
                            <a 
                                href="https://zalo.me/{{ $agent->phone }}" 
                                target="_blank"
                                class="w-full inline-flex items-center justify-center py-3 px-4 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-extrabold transition shadow"
                            >
                                <i class="fa-solid fa-comment mr-2"></i> Chat qua Zalo
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Introduction panel -->
                @if(!empty($agent->intro))
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                        <h3 class="text-base font-extrabold text-slate-900 mb-3">Giới thiệu bản thân</h3>
                        <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">
                            {{ $agent->intro }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Right Side: Agent's Listings with tabs -->
            <div 
                class="lg:col-span-3 text-left"
                x-data="{ activeTab: 'sale' }"
            >
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
                    <!-- Tab buttons -->
                    <div class="flex border-b border-slate-100 bg-slate-50/50 p-2">
                        <button 
                            @click="activeTab = 'sale'"
                            class="flex-1 py-3.5 px-4 text-sm font-extrabold rounded-2xl transition duration-150 cursor-pointer"
                            :class="activeTab === 'sale' ? 'bg-white text-primary shadow' : 'text-slate-500 hover:text-slate-800'"
                        >
                            <i class="fa-solid fa-tags mr-2"></i> Đang bán ({{ $saleProperties->count() }})
                        </button>
                        
                        <button 
                            @click="activeTab = 'rent'"
                            class="flex-1 py-3.5 px-4 text-sm font-extrabold rounded-2xl transition duration-150 cursor-pointer"
                            :class="activeTab === 'rent' ? 'bg-white text-primary shadow' : 'text-slate-500 hover:text-slate-800'"
                        >
                            <i class="fa-solid fa-key mr-2"></i> Cho thuê ({{ $rentProperties->count() }})
                        </button>
                    </div>

                    <!-- Listings area -->
                    <div class="p-6 md:p-8">
                        <!-- Sale Tab -->
                        <div x-show="activeTab === 'sale'" x-cloak>
                            @if($saleProperties->isEmpty())
                                <div class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-tags text-lg"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">Chưa có tin đăng bán</h4>
                                    <p class="text-xs text-slate-450 mt-1">Môi giới này chưa đăng bất kỳ tin bán nào.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($saleProperties as $property)
                                        @include('components.property-card', ['property' => $property])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Rent Tab -->
                        <div x-show="activeTab === 'rent'" x-cloak>
                            @if($rentProperties->isEmpty())
                                <div class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-key text-lg"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">Chưa có tin đăng cho thuê</h4>
                                    <p class="text-xs text-slate-450 mt-1">Môi giới này chưa đăng bất kỳ tin cho thuê nào.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($rentProperties as $property)
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
