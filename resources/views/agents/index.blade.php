@extends('layouts.app')

@section('title', request('type') === 'company' ? 'Danh bạ Doanh Nghiệp Bất Động Sản Uy Tín | BDS Rental' : 'Danh bạ Nhà Môi Giới Chuyên Nghiệp | BDS Rental')
@section('description', request('type') === 'company' ? 'Tìm kiếm và kết nối với các doanh nghiệp, chủ đầu tư bất động sản uy tín hàng đầu Việt Nam.' : 'Kết nối với hơn 1000 nhà môi giới và chủ nhà chuyên nghiệp trên toàn quốc.')

@section('content')

{{-- ===================== HERO ===================== --}}
<div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 pt-28 pb-12 overflow-hidden">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            @if(request('type') === 'company')
                <div class="inline-flex items-center gap-2 border text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-wider" style="background:rgba(0,119,187,0.2);border-color:rgba(0,119,187,0.4);color:#60c8ff">
                    <i class="fa-solid fa-building"></i> Đối tác doanh nghiệp
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-3">
                    Danh Bạ <span style="color:#60c8ff">Doanh Nghiệp</span> BĐS
                </h1>
                <p class="text-slate-300 text-lg max-w-2xl mx-auto">
                    Kết nối với các chủ đầu tư, công ty phân phối và đơn vị phát triển dự án bất động sản uy tín hàng đầu.
                </p>
            @else
                <div class="inline-flex items-center gap-2 border text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-wider" style="background:rgba(0,119,187,0.2);border-color:rgba(0,119,187,0.4);color:#60c8ff">
                    <i class="fa-solid fa-user-tie"></i> Chuyên viên môi giới
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-3">
                    Danh Bạ <span style="color:#60c8ff">Nhà Môi Giới</span> Uy Tín
                </h1>
                <p class="text-slate-300 text-lg max-w-2xl mx-auto">
                    Kết nối trực tiếp với các nhà môi giới và chủ nhà chuyên nghiệp, giao dịch an toàn và tối ưu nhất.
                </p>
            @endif
        </div>

        {{-- Stats Row --}}
        <div class="flex justify-center gap-8 mb-8">
            <div class="text-center">
                <div class="text-3xl font-black text-white">{{ $agents->total() }}+</div>
                <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mt-0.5">
                    {{ request('type') === 'company' ? 'Doanh nghiệp' : 'Môi giới' }}
                </div>
            </div>
            <div class="w-px bg-white/10"></div>
            <div class="text-center">
                <div class="text-3xl font-black text-white">100%</div>
                <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mt-0.5">Đã xác thực</div>
            </div>
            <div class="w-px bg-white/10"></div>
            <div class="text-center">
                <div class="text-3xl font-black text-white">63+</div>
                <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider mt-0.5">Tỉnh thành</div>
            </div>
        </div>

        {{-- Search Bar --}}
        <form action="{{ route('agents.index') }}" method="GET" class="max-w-4xl mx-auto">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="bg-white rounded-2xl shadow-2xl shadow-black/30 flex flex-col md:flex-row overflow-hidden">
                <div class="flex-grow flex items-center px-5 py-4 border-b md:border-b-0 md:border-r border-slate-100">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 mr-3 text-sm"></i>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="{{ request('type') === 'company' ? 'Tìm tên doanh nghiệp, công ty, chủ đầu tư...' : 'Tìm tên môi giới, công ty...' }}"
                        class="w-full text-slate-800 placeholder-slate-400 focus:outline-none text-sm font-medium"
                    >
                </div>
                <div class="flex items-center px-5 py-4 border-b md:border-b-0 md:border-r border-slate-100 min-w-[200px]">
                    <i class="fa-solid fa-location-dot text-slate-400 mr-3 text-sm"></i>
                    <input
                        type="text"
                        name="location"
                        value="{{ request('location') }}"
                        placeholder="Khu vực hoạt động..."
                        class="w-full text-slate-800 placeholder-slate-400 focus:outline-none text-sm font-medium"
                    >
                </div>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold px-8 py-4 transition duration-150 whitespace-nowrap text-sm cursor-pointer">
                    <i class="fa-solid fa-search mr-2"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== TAB NAVIGATION ===================== --}}
<div class="bg-white border-b border-slate-100 shadow-sm sticky top-[70px] z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
            {{-- Tab: Nhà Môi Giới --}}
            <a href="{{ route('agents.index') }}"
               class="flex items-center gap-2 px-7 py-4 text-sm font-bold border-b-2 transition-colors duration-150
               {{ request('type') !== 'company' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                <i class="fa-solid fa-user-tie {{ request('type') !== 'company' ? 'text-primary' : 'text-slate-400' }}"></i>
                Nhà Môi Giới
            </a>

            {{-- Divider --}}
            <div class="w-px h-5 bg-slate-200 mx-2 self-center"></div>

            {{-- Tab: Doanh Nghiệp --}}
            <a href="{{ route('agents.index', ['type' => 'company']) }}"
               class="flex items-center gap-2 px-7 py-4 text-sm font-bold border-b-2 transition-colors duration-150
               {{ request('type') === 'company' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                <i class="fa-solid fa-building {{ request('type') === 'company' ? 'text-primary' : 'text-slate-400' }}"></i>
                Doanh Nghiệp
            </a>
        </div>
    </div>
</div>

{{-- ===================== MAIN CONTENT ===================== --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex gap-8">

            {{-- ===== SIDEBAR TRÁI ===== --}}
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden sticky top-36">
                    <div class="bg-slate-50 px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-filter text-primary text-xs"></i> Bộ lọc nhanh
                        </h3>
                    </div>

                    <form action="{{ route('agents.index') }}" method="GET" class="p-5 space-y-5">
                        @if(request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        {{-- Khu vực --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-map-marker-alt text-primary mr-1"></i> Khu vực
                            </label>
                            <select name="location" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-slate-50 cursor-pointer">
                                <option value="">Tất cả khu vực</option>
                                <option value="Hà Nội" {{ request('location') === 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                                <option value="Hồ Chí Minh" {{ request('location') === 'Hồ Chí Minh' ? 'selected' : '' }}>Hồ Chí Minh</option>
                                <option value="Đà Nẵng" {{ request('location') === 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                                <option value="Bình Dương" {{ request('location') === 'Bình Dương' ? 'selected' : '' }}>Bình Dương</option>
                                <option value="Đồng Nai" {{ request('location') === 'Đồng Nai' ? 'selected' : '' }}>Đồng Nai</option>
                                <option value="Cần Thơ" {{ request('location') === 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                            </select>
                        </div>

                        {{-- Số tin đăng --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-list text-primary mr-1"></i> Số tin đăng
                            </label>
                            <div class="space-y-1.5">
                                @foreach(['' => 'Tất cả', '1' => 'Có tin đăng'] as $val => $label)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="has_listings" value="{{ $val }}"
                                               {{ request('has_listings', '') === $val ? 'checked' : '' }}
                                               class="accent-primary">
                                        <span class="text-sm text-slate-600 group-hover:text-slate-900 font-medium">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Xác thực --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-shield-halved text-primary mr-1"></i> Xác thực
                            </label>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" name="verified" value="1"
                                           {{ request('verified') ? 'checked' : '' }}
                                           class="accent-primary">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-900 font-medium">Đã xác thực danh tính</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white text-sm font-bold py-2.5 rounded-xl transition cursor-pointer">
                            Áp dụng bộ lọc
                        </button>

                        @if(request()->hasAny(['location','has_listings','verified','q']))
                            <a href="{{ route('agents.index', request('type') ? ['type' => request('type')] : []) }}"
                               class="block text-center text-xs text-slate-400 hover:text-primary font-semibold mt-1">
                                <i class="fa-solid fa-rotate-left mr-1"></i> Xóa bộ lọc
                            </a>
                        @endif
                    </form>
                </div>
            </aside>

            {{-- ===== DANH SÁCH ===== --}}
            <div class="flex-1 min-w-0">

                {{-- Header row --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            @if(request('type') === 'company')
                                <i class="fa-solid fa-building text-primary mr-2"></i>Doanh nghiệp đối tác
                            @else
                                <i class="fa-solid fa-user-tie text-primary mr-2"></i>Chuyên viên môi giới
                            @endif
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Tìm thấy <span class="font-bold text-slate-800">{{ $agents->total() }}</span>
                            {{ request('type') === 'company' ? 'doanh nghiệp' : 'môi giới' }}
                            @if(request('location')) tại <span class="text-primary font-semibold">{{ request('location') }}</span> @endif
                        </p>
                    </div>
                    {{-- Active filters badges --}}
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        @if(request('q'))
                            <span class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">
                                <i class="fa-solid fa-search text-[10px]"></i> "{{ request('q') }}"
                            </span>
                        @endif
                        @if(request('location'))
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                <i class="fa-solid fa-location-dot text-[10px]"></i> {{ request('location') }}
                            </span>
                        @endif
                    </div>
                </div>

                @if($agents->isEmpty())
                    <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <i class="fa-solid fa-building-circle-xmark text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Không tìm thấy kết quả</h3>
                        <p class="text-slate-500 text-sm">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm.</p>
                        <a href="{{ route('agents.index', request('type') ? ['type' => request('type')] : []) }}"
                           class="inline-flex items-center gap-2 mt-4 text-sm text-primary font-bold hover:underline">
                            <i class="fa-solid fa-rotate-left"></i> Xem tất cả
                        </a>
                    </div>
                @else

                    {{-- Company mode: HORIZONTAL CARDS --}}
                    @if(request('type') === 'company')
                        <div class="space-y-4">
                            @foreach($agents as $i => $agent)
                                <div class="group bg-white rounded-2xl border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                                    <div class="flex items-stretch">
                                        {{-- Logo / Avatar block --}}
                                        <a href="{{ route('agents.show', $agent->id) }}"
                                           class="flex-shrink-0 w-36 bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-5 border-r border-slate-100 group-hover:from-primary/5 group-hover:to-blue-50 transition-colors">
                                            <img
                                                src="{{ $agent->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($agent->company ?? $agent->name) . '&background=0077bb&color=fff&bold=true&size=128' }}"
                                                alt="{{ $agent->company ?? $agent->name }}"
                                                class="w-20 h-20 rounded-2xl object-cover shadow-sm border-2 border-white"
                                            >
                                        </a>

                                        {{-- Info block --}}
                                        <div class="flex-1 p-5 flex flex-col justify-between min-w-0">
                                            <div>
                                                <div class="flex items-start justify-between gap-3 mb-1">
                                                    <div class="min-w-0">
                                                        {{-- Featured badge for first 3 --}}
                                                        @if($i < 3 && $agents->currentPage() === 1)
                                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider mb-1">
                                                                <i class="fa-solid fa-star text-[8px]"></i> Nổi bật
                                                            </span>
                                                        @endif
                                                        <h3 class="text-base font-extrabold text-slate-900 group-hover:text-primary transition line-clamp-1">
                                                            <a href="{{ route('agents.show', $agent->id) }}">
                                                                {{ $agent->company ?? $agent->name }}
                                                            </a>
                                                        </h3>
                                                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                                                            <i class="fa-solid fa-user mr-1 text-slate-400"></i>Đại diện: {{ $agent->name }}
                                                        </p>
                                                    </div>

                                                    {{-- KYC badge --}}
                                                    @if(!empty($agent->id_number))
                                                        <span class="flex-shrink-0 inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                                            <i class="fa-solid fa-shield-halved text-[10px]"></i> Đã xác thực
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Meta info row --}}
                                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-slate-500">
                                                    <span class="flex items-center gap-1">
                                                        <i class="fa-solid fa-location-dot text-primary/70"></i>
                                                        {{ $agent->add_province ?? 'Toàn quốc' }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fa-solid fa-newspaper text-primary/70"></i>
                                                        {{ $agent->properties()->where('status', 'approved')->count() }} tin đăng
                                                    </span>
                                                    @if($agent->phone)
                                                        <span class="flex items-center gap-1">
                                                            <i class="fa-solid fa-phone text-primary/70"></i>
                                                            {{ $agent->phone }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- CTA Buttons --}}
                                            <div class="flex items-center gap-2 mt-4">
                                                <a href="{{ route('agents.show', $agent->id) }}"
                                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-hover transition shadow-sm shadow-primary/20">
                                                    <i class="fa-solid fa-building"></i> Xem trang DN
                                                </a>
                                                @if($agent->phone)
                                                    <a href="tel:{{ $agent->phone }}"
                                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                                                        <i class="fa-solid fa-phone"></i> Gọi ngay
                                                    </a>
                                                    <a href="https://zalo.me/{{ $agent->phone }}" target="_blank"
                                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-xl transition">
                                                        <i class="fa-solid fa-comment"></i> Zalo
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Right stats block --}}
                                        <div class="hidden md:flex flex-col items-center justify-center w-28 bg-slate-50 border-l border-slate-100 p-4 gap-3 group-hover:bg-primary/5 transition-colors">
                                            <div class="text-center">
                                                <div class="text-2xl font-black text-slate-900">
                                                    {{ $agent->properties()->where('status', 'approved')->count() }}
                                                </div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tin đăng</div>
                                            </div>
                                            <div class="w-8 border-t border-slate-200"></div>
                                            <div class="text-center">
                                                <div class="text-lg font-black text-green-600">✓</div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Uy tín</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    {{-- Agent mode: VERTICAL GRID CARDS --}}
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach($agents as $agent)
                                <div class="bg-white rounded-2xl border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
                                    {{-- Top banner --}}
                                    <div class="h-16 bg-gradient-to-r from-slate-800 to-slate-700 relative">
                                        <div class="absolute inset-0 opacity-20" style="background-image: url(\"data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E\");"></div>
                                    </div>

                                    <div class="px-5 pb-5">
                                        {{-- Avatar overlapping banner --}}
                                        <div class="relative -mt-8 mb-3 flex justify-center">
                                            <div class="relative">
                                                <img
                                                    src="{{ $agent->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($agent->name) . '&background=0077bb&color=fff&bold=true&size=128' }}"
                                                    alt="{{ $agent->name }}"
                                                    class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md"
                                                >
                                                @if(!empty($agent->id_number))
                                                    <span class="absolute bottom-0 right-0 w-5 h-5 bg-blue-500 rounded-full border-2 border-white flex items-center justify-center" title="Đã xác thực">
                                                        <i class="fa-solid fa-check text-white text-[8px]"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Name --}}
                                        <div class="text-center mb-3">
                                            <h3 class="font-extrabold text-slate-900 group-hover:text-primary transition text-sm line-clamp-1">
                                                <a href="{{ route('agents.show', $agent->id) }}">{{ $agent->name }}</a>
                                            </h3>
                                            <p class="text-xs text-primary font-bold mt-0.5 truncate">
                                                {{ $agent->company ?? 'Môi giới độc lập' }}
                                            </p>
                                            <p class="text-xs text-slate-400 mt-0.5">
                                                <i class="fa-solid fa-location-dot mr-1"></i>
                                                {{ $agent->add_province ? str_replace(['Tỉnh ', 'Thành phố '], '', $agent->add_province) : 'Toàn quốc' }}
                                            </p>
                                        </div>

                                        {{-- Stats --}}
                                        <div class="grid grid-cols-2 gap-2 mb-4">
                                            <div class="bg-slate-50 rounded-xl p-2 text-center">
                                                <div class="text-base font-black text-slate-900">{{ $agent->properties()->where('status', 'approved')->count() }}</div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tin đăng</div>
                                            </div>
                                            <div class="bg-slate-50 rounded-xl p-2 text-center">
                                                <div class="text-base font-black text-green-600">✓</div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Uy tín</div>
                                            </div>
                                        </div>

                                        {{-- CTA --}}
                                        <div class="space-y-2">
                                            <a href="{{ route('agents.show', $agent->id) }}"
                                               class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-sm shadow-primary/20">
                                                <i class="fa-solid fa-id-card"></i> Xem trang cá nhân
                                            </a>
                                            <div class="flex gap-2">
                                                <a href="tel:{{ $agent->phone }}"
                                                   class="flex-1 inline-flex items-center justify-center gap-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                                    <i class="fa-solid fa-phone"></i> Gọi
                                                </a>
                                                @if($agent->phone)
                                                    <a href="https://zalo.me/{{ $agent->phone }}" target="_blank"
                                                       class="flex-1 inline-flex items-center justify-center gap-1 py-2 px-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold transition">
                                                        <i class="fa-solid fa-comment"></i> Zalo
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    <div class="mt-10">
                        {{ $agents->links() }}
                    </div>

                @endif
            </div>{{-- end main col --}}
        </div>{{-- end flex --}}
    </div>
</div>
@endsection
