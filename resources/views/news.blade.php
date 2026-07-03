@extends('layouts.app')

@section('title', 'Wiki Bất Động Sản - Tin Tức, Báo Cáo & Phong Thủy | BDS Rental')

@section('content')
<!-- Main Content Area -->
<div class="space-y-12 pb-20 bg-white" 
     x-data="{
         activeTab: (new URLSearchParams(window.location.search).get('category') || 'report'),
         searchQuery: '',
         tabData: @json($tabData),
         
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

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Main Content Area (Tabs & Grid) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Category Filtering Navigation Menu (Capsules) -->
                <div class="flex flex-wrap gap-2.5 border-b border-slate-100 pb-5">
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

                <!-- Articles Grid (Shows all 4 articles in a balanced 2x2 layout) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                    <template x-for="(article, index) in filteredArticles" :key="index">
                        <div class="space-y-4 group cursor-pointer" @click="window.location.href = '/news/' + article.slug">
                            
                            <!-- Image container with ratio and hover scaling -->
                            <div class="aspect-[16/10] rounded-[24px] overflow-hidden shadow-2xs relative border border-slate-100/60">
                                <img :src="article.image" 
                                     :alt="article.title" 
                                     class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                            </div>
                            
                            <!-- Article metadata and texts -->
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
                    <div class="text-center py-16 bg-slate-50 rounded-[24px] border border-slate-100">
                        <i class="fa-solid fa-folder-open text-slate-300 text-4xl mb-3 block"></i>
                        <span class="text-slate-500 font-bold text-sm">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</span>
                    </div>
                </template>
                
            </div>

            <!-- Sidebar Column -->
            <div class="space-y-8">
                
                <!-- Search Box Widget -->
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-left">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider block">Tìm kiếm tin tức</h3>
                    <div class="flex gap-2">
                        <div class="relative flex-grow bg-white border border-slate-200/60 rounded-2xl px-3.5 py-2.5 flex items-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
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

                <!-- Popular Articles Widget (Tin đọc nhiều - Dynamic using PHP data) -->
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6 text-left">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest pb-3 border-b border-slate-50">
                        Tin đọc nhiều
                    </h3>
                    
                    @php
                        $popularArticles = [];
                        foreach ($tabData as $cat => $articles) {
                            foreach ($articles as $art) {
                                $popularArticles[] = $art;
                            }
                        }
                        // Unique list of 4 select articles
                        $popularList = array_slice($popularArticles, 1, 4);
                    @endphp

                    <div class="space-y-4">
                        @foreach($popularList as $popular)
                            <div class="flex gap-3 items-start group cursor-pointer" @click="window.location.href = '{{ route('news.show', $popular['slug']) }}'">
                                <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                    <img src="{{ $popular['image'] }}" 
                                         alt="{{ $popular['title'] }}" 
                                         class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-300">
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                                        {{ $popular['title'] }}
                                    </h4>
                                    <span class="text-[9px] text-slate-400 font-bold block">{{ $popular['date'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
