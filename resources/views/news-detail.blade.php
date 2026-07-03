@extends('layouts.app')

@section('title', $article['title'] . ' | BDS Rental')

@section('content')
<!-- Header Section -->
<div class="bg-slate-50 border-b border-slate-100 pt-28 pb-10 text-left">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-400 mb-4 space-x-2">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="/news" class="hover:text-primary transition">Tin tức</a>
            <span>/</span>
            <span class="text-slate-600 truncate max-w-xs">{{ $article['title'] }}</span>
        </nav>
        
        <div class="max-w-4xl">
            <span class="inline-block bg-primary/10 text-primary text-[10px] font-black px-2.5 py-1 rounded-[6px] uppercase tracking-wider mb-3">
                {{ $article['category_label'] }}
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] leading-tight tracking-tight">
                {{ $article['title'] }}
            </h1>
            <p class="text-slate-400 text-xs font-bold mt-3">
                Ngày đăng: {{ $article['date'] }} • BDS Rental
            </p>
        </div>
    </div>
</div>

<!-- Main Body Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Grid with Left Sidebar (33%) and Right Content (66%) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-16 items-start">
        
        <!-- LEFT COLUMN: Sidebar (33% width / lg:col-span-1) -->
        <div class="space-y-8 lg:col-span-1">
            
            <!-- Search Box Widget -->
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-left">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
                <form action="/news" method="GET" class="flex gap-2">
                    <div class="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3 py-2.5 flex items-center gap-2 shadow-2xs">
                        <input type="text" 
                               name="q"
                               placeholder="Nhập từ khóa..." 
                               class="w-full bg-transparent border-0 p-0 text-slate-700 placeholder-slate-400 font-bold focus:outline-none focus:ring-0 text-xs">
                    </div>
                    <button type="submit" 
                            class="bg-primary hover:bg-primary-hover text-white font-bold px-4 py-2.5 rounded-2xl transition-all text-xs cursor-pointer">
                        Tìm
                    </button>
                </form>
            </div>

            <!-- Popular Articles Widget (Tin đọc nhiều) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6 text-left">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest pb-3 border-b border-slate-50">
                    Tin đọc nhiều
                </h3>
                
                <div class="space-y-4">
                    <a href="/news/quy-trinh-thu-tuc-chuyen-nhuong-hop-dong-thue-nha" class="flex gap-3 items-start group cursor-pointer">
                        <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                            <img src="https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&q=80&w=200" 
                                 alt="Quy trình sang nhượng hợp đồng" 
                                 class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý
                            </h4>
                            <span class="text-[9px] text-slate-400 font-bold block">02/07/2026</span>
                        </div>
                    </a>
                    
                    <a href="/news/kinh-nghiem-vang-phan-biet-so-hong-that-gia" class="flex gap-3 items-start group cursor-pointer">
                        <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                            <img src="https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?auto=format&fit=crop&q=80&w=200" 
                                 alt="Kinh nghiệm sổ hồng thật giả" 
                                 class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả khi giao dịch đặt cọc
                            </h4>
                            <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                        </div>
                    </a>
                    
                    <a href="/news/cac-loai-thue-phi-phai-nop-khi-mua-ban-nha-dat" class="flex gap-3 items-start group cursor-pointer">
                        <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                            <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=200" 
                                 alt="Thuế phí mua bán nhà đất" 
                                 class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất
                            </h4>
                            <span class="text-[9px] text-slate-400 font-bold block">17/06/2026</span>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Article Detail Content (66% width / lg:col-span-2) -->
        <div class="lg:col-span-2 space-y-8 text-left">
            <!-- Article Main Image -->
            <div class="rounded-[32px] overflow-hidden aspect-[16/9] shadow-sm relative bg-slate-100">
                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
            </div>

            <!-- Article Excerpt -->
            <div class="bg-slate-50 border-l-4 border-primary p-5 rounded-r-2xl">
                <p class="text-slate-600 font-semibold text-sm leading-relaxed italic">
                    " {{ $article['excerpt'] }} "
                </p>
            </div>

            <!-- Article HTML Body -->
            <article class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-sm md:text-base space-y-6">
                {!! $article['content'] !!}
            </article>

            <!-- Bottom Share / Back Widget -->
            <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="/news" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-primary transition">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i> Quay lại trang tin tức
                </a>
                
                <button 
                    @click="$dispatch('open-share-modal', { url: window.location.href, title: '{{ addslashes($article['title']) }}' })"
                    class="inline-flex items-center gap-1.5 px-4.5 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/60 rounded-xl text-xs font-extrabold text-slate-700 transition cursor-pointer"
                >
                    <i class="fa-solid fa-share-nodes text-primary"></i> Chia sẻ bài viết
                </button>
            </div>
        </div>

    </div>
</div>
@endsection
