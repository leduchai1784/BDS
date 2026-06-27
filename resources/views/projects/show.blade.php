@extends('layouts.app')

@section('title', $project->title . ' - Chi tiết dự án | BDS Rental')

@section('content')
<div class="pt-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm font-semibold text-slate-500">
            <a href="/" class="hover:text-primary">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('projects.index') }}" class="hover:text-primary">Dự án</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800 font-bold truncate">{{ $project->title }}</span>
        </nav>

        <!-- Project Title Header -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-left">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-primary/10 text-primary text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">{{ $project->investor }}</span>
                    @if($project->status === 'selling')
                        <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">Đang mở bán</span>
                    @elseif($project->status === 'upcoming')
                        <span class="bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1 rounded-full">Sắp mở bán</span>
                    @else
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">Đã bàn giao</span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">{{ $project->title }}</h1>
                <p class="text-sm text-slate-500 mt-1 flex items-center">
                    <i class="fa-solid fa-location-dot mr-1.5 text-primary"></i> {{ $project->location }}
                </p>
            </div>
            
            <div class="flex flex-col text-right">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Giá bán dự kiến</span>
                <span class="text-2xl md:text-3xl font-black text-orange-500 mt-0.5">{{ $project->price_range ?? 'Liên hệ' }}</span>
            </div>
        </div>

        <!-- Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Images & Detailed Info -->
            <div class="lg:col-span-2 space-y-8 text-left">
                <!-- Gallery Carousel / Display -->
                @if(!empty($project->images) && count($project->images) > 0)
                    <div 
                        x-data="{ activeImage: 0, images: {{ json_encode($project->images) }} }"
                        class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm p-4"
                    >
                        <!-- Main Large Image -->
                        <div class="relative aspect-[16/9] bg-slate-100 rounded-2xl overflow-hidden mb-4">
                            <img :src="images[activeImage]" alt="{{ $project->title }}" class="w-full h-full object-cover">
                        </div>

                        <!-- Thumbnail list -->
                        <div class="flex gap-3 overflow-x-auto pb-1">
                            <template x-for="(img, idx) in images" :key="idx">
                                <button 
                                    @click="activeImage = idx"
                                    class="w-24 aspect-[16/10] rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
                                    :class="activeImage === idx ? 'border-primary shadow-md' : 'border-transparent opacity-70 hover:opacity-100'"
                                >
                                    <img :src="img" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>
                @endif

                <!-- Description & Overview -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
                    <h2 class="text-xl font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-50">Mô tả dự án</h2>
                    <div class="prose max-w-none text-slate-600 leading-relaxed text-sm md:text-base space-y-4">
                        <p class="whitespace-pre-line">{{ $project->description }}</p>
                    </div>
                </div>

                <!-- Map Coordinates if available -->
                @if($project->latitude && $project->longitude)
                    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
                        <h2 class="text-xl font-extrabold text-slate-900 mb-4">Vị trí dự án</h2>
                        <div class="aspect-[21/9] w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative">
                            <!-- Basic embed map using standard OpenStreetMap/Leaflet frame or MapLibre mock -->
                            <iframe 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0" 
                                src="https://maps.google.com/maps?q={{ $project->latitude }},{{ $project->longitude }}&hl=vi&z=14&amp;output=embed"
                            ></iframe>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: Quick Facts & Contact Info -->
            <div class="space-y-8 text-left">
                <!-- Project Details Panel -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-50">Thông tin tổng quan</h3>
                    
                    <dl class="space-y-4 text-sm">
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-400 font-semibold">Chủ đầu tư:</dt>
                            <dd class="text-slate-800 font-extrabold text-right">{{ $project->investor }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-400 font-semibold">Quy mô:</dt>
                            <dd class="text-slate-800 font-extrabold text-right">{{ $project->scale ?? 'Đang cập nhật' }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-400 font-semibold">Trạng thái:</dt>
                            <dd class="text-slate-800 font-extrabold text-right">
                                @if($project->status === 'selling')
                                    Đang mở bán
                                @elseif($project->status === 'upcoming')
                                    Sắp mở bán
                                @else
                                    Đã bàn giao
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-400 font-semibold">Địa chỉ:</dt>
                            <dd class="text-slate-800 font-extrabold text-right max-w-[180px] truncate" title="{{ $project->location }}">{{ $project->location }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Call to action card -->
                <div class="bg-gradient-to-br from-primary to-primary-hover rounded-3xl p-6 text-white shadow-xl shadow-primary/20">
                    <h3 class="text-lg font-extrabold mb-2">Quan tâm dự án này?</h3>
                    <p class="text-xs text-white/80 leading-relaxed mb-6">
                        Để lại thông tin liên hệ hoặc gọi điện cho chúng tôi để nhận bảng giá chính thức, tài liệu mặt bằng và chính sách bán hàng mới nhất của dự án.
                    </p>
                    <a 
                        href="tel:19001888" 
                        class="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-2xl bg-white text-primary font-black hover:bg-slate-55 transition text-sm shadow-md"
                    >
                        <i class="fa-solid fa-phone mr-2"></i> Gọi ngay: 1900 1888
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Properties section -->
        <div class="mt-16 text-left">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Bất động sản thuộc dự án này</h2>
            <p class="text-sm text-slate-500 mb-8">Danh sách tin đăng mua bán, cho thuê thực tế đang hoạt động tại dự án {{ $project->title }}</p>

            @if($properties->isEmpty())
                <div class="text-center py-16 bg-white rounded-3xl border border-slate-100 shadow-sm max-w-full">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <i class="fa-solid fa-house-circle-xmark text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Chưa có tin đăng liên quan</h3>
                    <p class="text-slate-500 text-sm">Hiện chưa có chủ nhà hoặc nhà môi giới nào đăng tin mua bán/cho thuê thuộc dự án này.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($properties as $property)
                        @include('components.property-card', ['property' => $property])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
