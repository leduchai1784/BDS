@extends('layouts.app')

@section('title', 'Tin Đăng Đã Lưu | BDS Rental')

@section('content')
    <div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[70vh]">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="text-slate-500 hover:text-primary font-medium transition">Trang chủ</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-350 text-[10px] mx-1"></i>
                        <span class="text-slate-900 font-bold ml-1">Tin đã lưu</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-12">
            <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Danh sách của bạn</span>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">Tin Đăng Đã Lưu</h1>
            <p class="text-slate-500 mt-2 max-w-2xl">Lưu trữ các căn hộ, phòng trọ, dự án bạn đang quan tâm để dễ dàng so sánh và đặt lịch hẹn xem nhà.</p>
        </div>

        <!-- Wishlist Container -->
        <div 
            x-data="{ 
                loading: {{ Auth::check() ? 'false' : 'true' }},
                hasFavorites: {{ Auth::check() && $properties->count() > 0 ? 'true' : 'false' }},
                propertiesHtml: '',
                init() {
                    @guest
                        const wishlist = JSON.parse(localStorage.getItem('bds_wishlist') || '[]');
                        if (wishlist.length === 0) {
                            this.loading = false;
                            this.hasFavorites = false;
                            return;
                        }
                        this.hasFavorites = true;
                        this.fetchCards(wishlist);
                    @endguest

                    window.addEventListener('wishlist-updated', (e) => {
                        if (!e.detail.liked) {
                            @auth
                                // For authenticated users, reload to sync database state
                                window.location.reload();
                            @else
                                // For guest users, update the list locally
                                const updatedWishlist = JSON.parse(localStorage.getItem('bds_wishlist') || '[]');
                                if (updatedWishlist.length === 0) {
                                    this.hasFavorites = false;
                                    this.propertiesHtml = '';
                                } else {
                                    this.fetchCards(updatedWishlist);
                                }
                            @endauth
                        }
                    });
                },
                fetchCards(ids) {
                    fetch('{{ route('wishlist.render') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        if (data.success && data.html && data.html.trim().length > 0) {
                            this.propertiesHtml = data.html;
                            this.hasFavorites = true;
                        } else {
                            this.hasFavorites = false;
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        this.hasFavorites = false;
                        console.error('Error loading wishlist:', error);
                    });
                }
            }"
            class="w-full"
        >
            <!-- 1. Loading Spinner -->
            <div x-show="loading" class="flex flex-col items-center justify-center py-20" x-cloak>
                <div class="w-12 h-12 border-4 border-slate-200 border-t-primary rounded-full animate-spin"></div>
                <p class="text-sm text-slate-500 mt-4 font-semibold">Đang tải danh sách tin yêu thích...</p>
            </div>

            <!-- 2. Grid of Property Cards -->
            <div x-show="!loading && hasFavorites" class="w-full" x-cloak>
                @auth
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                        @foreach($properties as $property)
                            @include('components.property-card', ['property' => $property])
                        @endforeach
                    </div>
                @else
                    <template x-if="propertiesHtml !== ''">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12" x-html="propertiesHtml"></div>
                    </template>
                @endauth
            </div>

            <!-- 3. Premium Empty State (Glassmorphism card) -->
            <div x-show="!loading && !hasFavorites" class="max-w-md mx-auto py-16 text-center" x-cloak>
                <div class="relative inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-slate-50 border border-slate-100 shadow-sm mb-6 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-primary/5 to-primary-hover/10 blur-xl opacity-60"></div>
                    <i class="fa-solid fa-heart text-4xl text-slate-300 relative z-10 animate-pulse"></i>
                </div>
                
                <h3 class="text-xl font-bold text-slate-800 mb-2">Chưa có tin đăng yêu thích</h3>
                <p class="text-sm text-slate-500 leading-relaxed mb-8 max-w-sm mx-auto">
                    Hãy nhấn vào biểu tượng <i class="fa-solid fa-heart text-red-400 mx-0.5"></i> trên mỗi tin đăng khi khám phá dự án để lưu lại những lựa chọn bạn yêu thích nhất tại đây.
                </p>
                
                <a 
                    href="/listings" 
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 hover:shadow-primary/35 transform hover:-translate-y-0.5 transition duration-200"
                >
                    <i class="fa-solid fa-compass mr-2"></i> Khám phá tin đăng ngay
                </a>
            </div>
        </div>
    </div>
@endsection
