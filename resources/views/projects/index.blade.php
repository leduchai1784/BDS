@extends('layouts.app')

@section('title', 'Dự án Bất động sản nổi bật | BDS Rental')

@section('content')
<div class="bg-gradient-to-br from-slate-900 via-slate-800 to-primary/20 pt-28 pb-16 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4">
            Khám Phá Các <span class="text-primary-hover">Dự Án</span> Nổi Bật
        </h1>
        <p class="text-slate-300 max-w-2xl mx-auto text-lg mb-8">
            Tìm kiếm các khu đô thị sinh thái, dự án chung cư cao cấp và nhà ở xã hội quy mô lớn nhất trên toàn quốc.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('projects.index') }}" method="GET" class="max-w-3xl mx-auto bg-white/10 backdrop-blur-md p-2 rounded-3xl border border-white/20 shadow-2xl flex flex-col md:flex-row gap-2">
            <div class="flex-grow flex items-center px-4 py-2">
                <i class="fa-solid fa-magnifying-glass text-slate-400 mr-3"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}"
                    placeholder="Nhập tên dự án, chủ đầu tư hoặc vị trí..." 
                    class="bg-transparent w-full text-white placeholder-slate-400 focus:outline-none text-base"
                >
            </div>
            
            <div class="flex-shrink-0 flex items-center px-4 py-2 border-t md:border-t-0 md:border-l border-white/10">
                <i class="fa-solid fa-map-pin text-slate-400 mr-3"></i>
                <input 
                    type="text" 
                    name="city" 
                    value="{{ request('city') }}"
                    placeholder="Tỉnh/Thành phố..." 
                    class="bg-transparent w-full text-white placeholder-slate-400 focus:outline-none text-base"
                >
            </div>

            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-extrabold px-8 py-3 rounded-2xl transition duration-150 shadow-lg shadow-primary/25 cursor-pointer">
                Tìm kiếm
            </button>
        </form>
    </div>
</div>

<!-- Project Directory Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-100">
        <div class="flex flex-wrap gap-2">
            <a 
                href="{{ route('projects.index', request()->except('status')) }}" 
                class="px-5 py-2.5 rounded-full text-sm font-extrabold transition {{ !request('status') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Tất cả dự án
            </a>
            <a 
                href="{{ route('projects.index', array_merge(request()->query(), ['status' => 'selling'])) }}" 
                class="px-5 py-2.5 rounded-full text-sm font-extrabold transition {{ request('status') === 'selling' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Đang mở bán
            </a>
            <a 
                href="{{ route('projects.index', array_merge(request()->query(), ['status' => 'upcoming'])) }}" 
                class="px-5 py-2.5 rounded-full text-sm font-extrabold transition {{ request('status') === 'upcoming' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Sắp mở bán
            </a>
            <a 
                href="{{ route('projects.index', array_merge(request()->query(), ['status' => 'handed_over'])) }}" 
                class="px-5 py-2.5 rounded-full text-sm font-extrabold transition {{ request('status') === 'handed_over' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Đã bàn giao
            </a>
        </div>
        
        <p class="text-sm font-semibold text-slate-500">
            Hiển thị <span class="text-slate-800 font-bold">{{ $projects->total() }}</span> dự án
        </p>
    </div>

    <!-- Project Grid -->
    @if($projects->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i class="fa-solid fa-folder-open text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Không tìm thấy dự án nào</h3>
            <p class="text-slate-500 text-sm">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm xem sao nhé.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($projects as $project)
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group h-full">
                    <!-- Project Image -->
                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                        @if(!empty($project->images) && count($project->images) > 0)
                            <img 
                                src="{{ $project->images[0] }}" 
                                alt="{{ $project->title }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fa-regular fa-image text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4 z-10">
                            @if($project->status === 'selling')
                                <span class="bg-emerald-550 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đang mở bán</span>
                            @elseif($project->status === 'upcoming')
                                <span class="bg-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Sắp mở bán</span>
                            @else
                                <span class="bg-blue-600 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đã bàn giao</span>
                            @endif
                        </div>
                    </div>

                    <!-- Project Content -->
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-black uppercase text-primary tracking-widest block mb-2">{{ $project->investor }}</span>
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-primary transition duration-150 mb-3 line-clamp-1">
                                <a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a>
                            </h3>
                            <p class="text-slate-500 text-sm mb-5 line-clamp-3 leading-relaxed">
                                {{ $project->description }}
                            </p>
                        </div>

                        <!-- Highlights info -->
                        <div class="pt-5 border-t border-slate-50 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                <span class="truncate">{{ $project->price_range ?? 'Liên hệ' }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-ruler-combined text-primary"></i>
                                <span class="truncate">{{ $project->scale ?? 'Đang cập nhật' }}</span>
                            </div>
                            <div class="flex items-center space-x-2 col-span-2">
                                <i class="fa-solid fa-location-dot text-primary"></i>
                                <span class="truncate">{{ $project->location }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- View details CTA -->
                    <div class="px-6 pb-6 pt-2">
                        <a 
                            href="{{ route('projects.show', $project->slug) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-3 border border-slate-100 text-sm font-extrabold rounded-2xl text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200"
                        >
                            Xem chi tiết dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
