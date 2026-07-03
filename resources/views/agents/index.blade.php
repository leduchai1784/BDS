@extends('layouts.app')

@section('title', request('type') === 'company' ? 'Danh bạ doanh nghiệp đối tác uy tín | BDS Rental' : 'Danh bạ nhà môi giới uy tín | BDS Rental')

@section('content')
<div class="bg-gradient-to-br from-slate-900 via-slate-800 to-primary/20 pt-28 pb-16 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4">
            @if(request('type') === 'company')
                Danh Bạ <span class="text-primary-hover">Doanh Nghiệp</span> Đối Tác
            @else
                Danh Bạ <span class="text-primary-hover">Nhà Môi Giới</span> Uy Tín
            @endif
        </h1>
        <p class="text-slate-300 max-w-2xl mx-auto text-lg mb-8">
            @if(request('type') === 'company')
                Kết nối với các doanh nghiệp, chủ đầu tư và đơn vị phân phối bất động sản uy tín hàng đầu.
            @else
                Kết nối trực tiếp với các nhà môi giới và chủ nhà chuyên nghiệp để tìm kiếm giao dịch an toàn và tối ưu nhất.
            @endif
        </p>

        <!-- Search Bar -->
        <form action="{{ route('agents.index') }}" method="GET" class="max-w-3xl mx-auto bg-white/10 backdrop-blur-md p-2 rounded-3xl border border-white/20 shadow-2xl flex flex-col md:flex-row gap-2">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="flex-grow flex items-center px-4 py-2">
                <i class="fa-solid fa-user-tie text-slate-400 mr-3"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}"
                    placeholder="{{ request('type') === 'company' ? 'Tìm tên doanh nghiệp, công ty...' : 'Tìm tên môi giới, công ty...' }}" 
                    class="bg-transparent w-full text-white placeholder-slate-400 focus:outline-none text-base"
                >
            </div>
            
            <div class="flex-shrink-0 flex items-center px-4 py-2 border-t md:border-t-0 md:border-l border-white/10">
                <i class="fa-solid fa-location-dot text-slate-400 mr-3"></i>
                <input 
                    type="text" 
                    name="location" 
                    value="{{ request('location') }}"
                    placeholder="Khu vực hoạt động..." 
                    class="bg-transparent w-full text-white placeholder-slate-400 focus:outline-none text-base"
                >
            </div>

            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-extrabold px-8 py-3 rounded-2xl transition duration-150 shadow-lg shadow-primary/25 cursor-pointer">
                Tìm kiếm
            </button>
        </form>
    </div>
</div>

<!-- Agents Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex justify-between items-center mb-10 pb-6 border-b border-slate-100">
        <h2 class="text-2xl font-black text-slate-900 text-left">
            @if(request('type') === 'company')
                Doanh nghiệp đối tác nổi bật
            @else
                Chuyên viên môi giới nổi bật
            @endif
        </h2>
        <p class="text-sm font-semibold text-slate-500">
            Tổng số: <span class="text-slate-800 font-bold">{{ $agents->total() }}</span> {{ request('type') === 'company' ? 'doanh nghiệp' : 'môi giới' }}
        </p>
    </div>

    @if($agents->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i class="fa-solid fa-users-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Không tìm thấy nhà môi giới nào</h3>
            <p class="text-slate-500 text-sm">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($agents as $agent)
                <div class="bg-white rounded-3xl border border-slate-100 hover:border-primary/20 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col items-center text-center group h-full">
                    <!-- Avatar & Status -->
                    <div class="relative w-24 h-24 mb-4">
                        <img 
                            src="{{ $agent->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($agent->name) . '&background=0077bb&color=fff' }}" 
                            alt="{{ $agent->name }}" 
                            class="w-full h-full rounded-full object-cover border-4 border-slate-50 group-hover:border-primary/10 transition-colors"
                        >
                        <!-- KYC verified badge (tick xanh) -->
                        @if(!empty($agent->id_number))
                            <span class="absolute bottom-0 right-0 w-6 h-6 bg-blue-500 rounded-full border-2 border-white flex items-center justify-center text-white text-[10px] shadow" title="Đã xác thực danh tính">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        @endif
                    </div>

                    <!-- Name & Title -->
                    <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-primary transition line-clamp-1 mb-1">
                        <a href="{{ route('agents.show', $agent->id) }}">{{ $agent->name }}</a>
                    </h3>
                    
                    <p class="text-xs font-bold text-primary tracking-wide uppercase mb-3 truncate max-w-full">
                        {{ $agent->company ?? 'Môi giới độc lập' }}
                    </p>

                    <!-- Active counts & details -->
                    <div class="w-full bg-slate-50 rounded-2xl p-3 mb-6 grid grid-cols-2 gap-1 text-xs font-semibold text-slate-600">
                        <div class="border-r border-slate-200">
                            <span class="block text-slate-400 text-[10px] uppercase font-bold tracking-wider mb-0.5">Tin đăng</span>
                            <span class="text-slate-800 font-black text-sm">{{ $agent->properties()->where('status', 'approved')->count() }} tin</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] uppercase font-bold tracking-wider mb-0.5">Khu vực</span>
                            <span class="text-slate-800 font-black text-sm truncate block px-1" title="{{ $agent->add_province ?? 'Toàn quốc' }}">
                                {{ $agent->add_province ? str_replace(['Tỉnh ', 'Thành phố '], '', $agent->add_province) : 'Toàn quốc' }}
                            </span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="mt-auto w-full space-y-2">
                        <a 
                            href="{{ route('agents.show', $agent->id) }}"
                            class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl border border-slate-100 text-xs font-extrabold text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200"
                        >
                            Xem trang cá nhân
                        </a>
                        
                        <div class="flex gap-2">
                            <a 
                                href="tel:{{ $agent->phone }}" 
                                class="flex-1 inline-flex items-center justify-center py-2 px-3 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-extrabold transition shadow shadow-primary/10"
                            >
                                <i class="fa-solid fa-phone mr-1.5"></i> Gọi điện
                            </a>
                            @if($agent->phone)
                                <a 
                                    href="https://zalo.me/{{ $agent->phone }}" 
                                    target="_blank"
                                    class="flex-1 inline-flex items-center justify-center py-2 px-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-xs font-extrabold transition shadow shadow-blue-500/10"
                                >
                                    <i class="fa-solid fa-comment mr-1.5"></i> Zalo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $agents->links() }}
        </div>
    @endif
</div>
@endsection
