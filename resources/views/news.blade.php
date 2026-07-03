@extends('layouts.app')

@section('title', 'Wiki Bất Động Sản - Tin Tức, Báo Cáo & Phong Thủy | BDS Rental')

@section('content')
@php
    // Get synchronized articles data from HomeController
    $tabData = \App\Http\Controllers\HomeController::getNewsData();
@endphp

<!-- Main Content Area -->
<div class="space-y-12 pb-20 bg-white" 
     x-data="{
         activeTab: (new URLSearchParams(window.location.search).get('category') || 'report'),
         searchQuery: '',
         tabData: {{ json_encode($tabData) }},
         
         get filteredArticles() {
             let articles = this.tabData[this.activeTab] || [];
             if (this.searchQuery.trim() !== '') {
                 let q = this.searchQuery.toLowerCase();
                 return articles.filter(a => a.title.toLowerCase().includes(q) || a.excerpt.toLowerCase().includes(q));
             }
             return articles;
         },
         
         changeTab(tab) {
             this.activeTab = tab;
             const url = new URL(window.location);
             url.searchParams.set('category', tab);
             window.history.pushState({}, '', url);
         }
     }"
>
    <!-- Hero / Header Title Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
        <div class="space-y-2 border-b border-slate-100 pb-6 text-left">
            <span class="text-xs font-black text-primary uppercase tracking-widest">NKS WIKI TIN TỨC</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight font-sans">
                Tin Tức Bất Động Sản
            </h1>
            <p class="text-slate-400 text-xs sm:text-sm font-medium">
                Cập nhật nhanh chóng xu hướng thị trường, kiến thức đầu tư, thiết kế nội thất và cẩm nang phong thủy.
            </p>
        </div>
    </section>

    <!-- Main Container (Left Sidebar, Right Content) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-16 items-start">
            
            <!-- LEFT COLUMN: Sidebar (33% width / lg:col-span-1) -->
            <div class="space-y-8 lg:col-span-1">
                
                <!-- Search Box Widget -->
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-left">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
                    <div class="flex gap-2">
                        <div class="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3 py-2.5 flex items-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs pl-1"></i>
                            <input type="text" 
                                   x-model="searchQuery" 
                                   placeholder="Nhập từ khóa tìm kiếm..." 
                                   class="w-full bg-transparent border-0 p-0 text-slate-700 placeholder-slate-400 font-bold focus:outline-none focus:ring-0 text-xs">
                        </div>
                        <button type="button" 
                                class="bg-primary hover:bg-primary-hover text-white font-bold px-4 py-2.5 rounded-2xl transition-all text-xs cursor-pointer">
                            Tìm
                        </button>
                    </div>
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
                                     alt="Quy trình và thủ tục chuyển nhượng hợp đồng thuê nhà chuẩn pháp lý" 
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
                                     alt="Kinh nghiệm vàng giúp phân biệt sổ hồng thật và giả" 
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
                                     alt="Các loại thuế phí phải nộp khi mua bán nhà đất" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    Các loại thuế phí phải nộp khi mua bán và chuyển nhượng nhà đất
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                            </div>
                        </a>
                        
                        <a href="/news/huong-dan-dang-tin-nha-dat-chuan-seo-va-ai-len-xu-huong-nks" class="flex gap-3 items-start group cursor-pointer">
                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=200" 
                                     alt="Hướng dẫn đăng tin chuẩn SEO" 
                                     class="w-full h-full object-cover group-hover:scale-103 transition-transform">
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                    Hướng dẫn đăng tin chuẩn SEO và AI lên xu hướng NKS
                                </h4>
                                <span class="text-[9px] text-slate-400 font-bold block">26/06/2026</span>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Articles Grid (66% width / lg:col-span-2) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Category Filtering Navigation Menu -->
                <div class="flex flex-wrap gap-2 border-b border-slate-100 pb-4">
                    <button @click="changeTab('report')" 
                            :class="activeTab === 'report' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Báo cáo Thị trường BĐS
                    </button>
                    <button @click="changeTab('view')" 
                            :class="activeTab === 'view' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Góc Nhìn NKS
                    </button>
                    <button @click="changeTab('interior')" 
                            :class="activeTab === 'interior' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Nội Thất
                    </button>
                    <button @click="changeTab('fengshui')" 
                            :class="activeTab === 'fengshui' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Phong Thủy
                    </button>
                    <button @click="changeTab('news')" 
                            :class="activeTab === 'news' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Tin Tức
                    </button>
                    <button @click="changeTab('knowledge')" 
                            :class="activeTab === 'knowledge' ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" 
                            class="px-5 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer focus:outline-none">
                        Kiến Thức
                    </button>
                </div>

                <!-- Articles Grid (Shows all 4 articles in balanced cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                    <template x-for="(article, index) in filteredArticles" :key="index">
                        <div class="space-y-4 group cursor-pointer" @click="window.location.href = '/news/' + article.slug">
                            
                            <div class="h-48 rounded-[24px] overflow-hidden shadow-2xs relative border border-slate-100/60">
                                <img :src="article.image" 
                                     :alt="article.title" 
                                     class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                            </div>
                            
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="article.category_label">
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-800 group-hover:text-primary transition-colors leading-snug line-clamp-2" 
                                    x-text="article.title">
                                </h4>
                                <p class="text-xs text-slate-400 font-medium line-clamp-2" x-text="article.excerpt">
                                </p>
                                <p class="text-[10px] text-slate-400 font-bold pt-1" x-text="article.date">
                                </p>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <template x-if="filteredArticles.length === 0">
                    <div class="text-center py-16 bg-slate-50 rounded-3xl border border-slate-100">
                        <i class="fa-solid fa-folder-open text-slate-300 text-4xl mb-3 block"></i>
                        <span class="text-slate-500 font-bold text-sm">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</span>
                    </div>
                </template>
                
            </div>

        </div>
    </div>
</div>
@endsection
