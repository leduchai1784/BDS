<section class="relative min-h-[90vh] flex items-center justify-center bg-slate-900 text-white pt-24 pb-16 overflow-hidden">
    <!-- Background Image with Dark Overlay -->
    <div class="absolute inset-0 z-0">
        <!-- Generative image fallback or fallback color if file not loaded yet -->
        <img 
            src="{{ asset('images/hero_bg.png') }}" 
            alt="Real estate banner" 
            class="w-full h-full object-cover object-center opacity-40 transform scale-105"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-slate-950/40"></div>
    </div>

    <!-- Background Decorative Elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-primary/20 blur-3xl z-0"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl z-0"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Animated Badge -->
        <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/20 mb-6 animate-pulse">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-200">Hơn 50,000+ Bất động sản đang cho thuê</span>
        </div>

        <!-- Heading H1 -->
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
            Tìm Kiếm Không Gian Sống <br class="hidden sm:inline">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-primary font-black">Lý Tưởng Cho Bạn</span>
        </h1>

        <!-- Subheading -->
        <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto mb-10 font-normal leading-relaxed">
            Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn và mặt bằng kinh doanh cho thuê uy tín, cập nhật liên tục mỗi ngày với bộ lọc thông minh.
        </p>

        <!-- Tabbed Search Bar Widget -->
        <div 
            x-data="{ activeTab: 'apartment' }" 
            class="bg-white rounded-3xl p-4 sm:p-6 shadow-2xl text-slate-800 max-w-4xl mx-auto border border-slate-100/50 backdrop-blur-sm"
        >
            <!-- Tabs Header -->
            <div class="flex border-b border-slate-100 pb-3 mb-5 space-x-1 sm:space-x-3 overflow-x-auto scrollbar-none">
                <button 
                    @click="activeTab = 'apartment'"
                    :class="activeTab === 'apartment' ? 'bg-primary text-white shadow-lg shadow-primary/25 border-transparent' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-transparent'"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-sm font-bold border transition duration-200 whitespace-nowrap"
                >
                    <i class="fa-solid fa-building text-base"></i>
                    <span>Căn hộ / Chung cư</span>
                </button>
                <button 
                    @click="activeTab = 'house'"
                    :class="activeTab === 'house' ? 'bg-primary text-white shadow-lg shadow-primary/25 border-transparent' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-transparent'"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-sm font-bold border transition duration-200 whitespace-nowrap"
                >
                    <i class="fa-solid fa-house-user text-base"></i>
                    <span>Nhà riêng / Biệt thự</span>
                </button>
                <button 
                    @click="activeTab = 'office'"
                    :class="activeTab === 'office' ? 'bg-primary text-white shadow-lg shadow-primary/25 border-transparent' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-transparent'"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-sm font-bold border transition duration-200 whitespace-nowrap"
                >
                    <i class="fa-solid fa-shop text-base"></i>
                    <span>Mặt bằng / Văn phòng</span>
                </button>
            </div>

            <!-- Tabs Forms -->
            <form action="/listings" method="GET">
                <!-- Tab Specific Hidden Input -->
                <input type="hidden" name="type" :value="activeTab">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    <!-- Column 1: Keyword Input -->
                    <div class="md:col-span-4 text-left">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 px-1">Từ khóa tìm kiếm</label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input 
                                type="text" 
                                name="keyword" 
                                placeholder="Nhập địa điểm, tên dự án..." 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-2xl text-sm outline-none transition duration-200"
                            >
                        </div>
                    </div>

                    <!-- Column 2: Location Select -->
                    <div class="md:col-span-3 text-left">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 px-1">Khu vực / Địa điểm</label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select 
                                name="location" 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-2xl text-sm outline-none appearance-none transition duration-200 cursor-pointer"
                            >
                                <option value="">Toàn quốc</option>
                                <option value="HN">Hà Nội</option>
                                <option value="HCM">TP. Hồ Chí Minh</option>
                                <option value="DN">Đà Nẵng</option>
                                <option value="BD">Bình Dương</option>
                                <option value="DNai">Đồng Nai</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Column 3: Price Select -->
                    <div class="md:col-span-3 text-left">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 px-1">Khoảng giá</label>
                        <div class="relative">
                            <i class="fa-solid fa-money-bill-wave absolute left-4.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select 
                                name="price" 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-2xl text-sm outline-none appearance-none transition duration-200 cursor-pointer"
                            >
                                <option value="">Tất cả mức giá</option>
                                <option value="under_5">Dưới 5 triệu</option>
                                <option value="5_10">5 - 10 triệu</option>
                                <option value="10_20">10 - 20 triệu</option>
                                <option value="above_20">Trên 20 triệu</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <!-- Column 4: Submit Button -->
                    <div class="md:col-span-2 text-center h-full flex items-end">
                        <button 
                            type="submit" 
                            class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-primary/20 hover:shadow-primary/35 flex items-center justify-center space-x-2 transition duration-200 h-[50px] cursor-pointer mt-1"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Tìm kiếm</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
