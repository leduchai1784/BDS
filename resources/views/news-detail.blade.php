@extends('layouts.app')

@section('title', $article['title'] . ' | BDS Rental')

@section('content')
<!-- Header Banner Section -->
<div class="bg-slate-50 border-b border-slate-100 pt-28 pb-10 text-left">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-400 mb-4 space-x-2">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('news') }}" class="hover:text-primary transition">Tin tức</a>
            <span>/</span>
            <span class="text-slate-600 line-clamp-1 max-w-xs sm:max-w-md">{{ $article['title'] }}</span>
        </nav>
        
        <div class="max-w-4xl space-y-4">
            <span class="inline-block bg-primary text-white text-[9px] font-black px-3 py-1 rounded-[6px] uppercase tracking-wider">
                {{ $article['category_label'] }}
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight tracking-tight">
                {{ $article['title'] }}
            </h1>
            <div class="flex items-center text-xs text-slate-400 font-bold space-x-4">
                <span>Đăng ngày: {{ $article['date'] }}</span>
                <span>•</span>
                <span>Tác giả: Ban Biên Tập NKS</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Detail Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        
        <!-- Left Content Column -->
        <div class="lg:col-span-2 space-y-8 text-left">
            <!-- Article Banner Image -->
            <div class="aspect-[16/9] sm:aspect-[21/9] rounded-[36px] overflow-hidden shadow-2xs border border-slate-150/40">
                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
            </div>

            <!-- Rich HTML Text Content -->
            <div class="prose max-w-none text-slate-700 text-sm md:text-base leading-relaxed space-y-6">
                {!! $article['content'] !!}
            </div>
            
            <hr class="border-slate-100 my-8">
            
            <!-- Back to News -->
            <div class="flex">
                <a href="{{ route('news') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-primary transition">
                    <i class="fa-solid fa-arrow-left-long"></i> Quay lại trang tin tức
                </a>
            </div>
        </div>

        <!-- Right Sidebar Column -->
        <div class="space-y-8">
            
            <!-- Widget 1: Search Box Widget -->
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-left">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
                <form action="{{ route('news') }}" method="GET" class="flex gap-2">
                    <div class="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3.5 py-2.5 flex items-center gap-2 shadow-2xs">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                        <input type="text" 
                               name="q"
                               placeholder="Nhập từ khóa tìm kiếm..." 
                               class="w-full bg-transparent border-0 p-0 text-slate-700 placeholder-slate-400 font-bold focus:outline-none focus:ring-0 text-xs">
                    </div>
                    <button type="submit" 
                            class="bg-primary hover:bg-primary-hover text-white font-bold px-4 py-2.5 rounded-2xl transition-all text-xs cursor-pointer">
                        Tìm
                    </button>
                </form>
            </div>

            <!-- Widget 2: Related Articles -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6 text-left">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest pb-3 border-b border-slate-50">
                    Bài viết liên quan
                </h3>
                
                <div class="space-y-4">
                    @foreach($related as $rel)
                        <div class="flex gap-3 items-start group cursor-pointer" @click="window.location.href = '{{ route('news.show', $rel['slug']) }}'">
                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                <img src="{{ $rel['image'] }}" 
                                     alt="{{ $rel['title'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-300">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    {{ $rel['title'] }}
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">{{ $rel['date'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    /* Styling headings in rich article content */
    .prose h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
        text-align: left;
    }
    .prose p {
        color: #475569;
        margin-bottom: 1.25rem;
        text-align: justify;
    }
</style>
@endsection
