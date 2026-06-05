<div 
    x-data="{ showAdvanced: false }" 
    class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-8"
>
    <!-- Filter Top Bar (Always Visible) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Heading & Quick Results -->
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-primary"></i>
                <span>Tìm kiếm tin đăng bất động sản</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Tìm thấy <span class="font-semibold text-primary">1,240</span> bất động sản phù hợp</p>
        </div>

        <!-- Right Quick Filters & Advanced Toggle -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Quick Sort Dropdown -->
            <div class="relative min-w-[160px]">
                <select 
                    name="sort" 
                    class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm outline-none appearance-none cursor-pointer transition"
                >
                    <option value="latest">Mới nhất</option>
                    <option value="price_asc">Giá tăng dần</option>
                    <option value="price_desc">Giá giảm dần</option>
                    <option value="area_desc">Diện tích lớn nhất</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
            </div>

            <!-- Toggle Advanced Filters Button -->
            <button 
                type="button" 
                @click="showAdvanced = !showAdvanced"
                :class="showAdvanced ? 'bg-primary text-white border-transparent' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'"
                class="flex items-center space-x-2 px-4 py-2.5 rounded-xl border text-sm font-bold transition duration-200 cursor-pointer"
            >
                <i class="fa-solid fa-circle-chevron-down transform transition duration-300" :class="showAdvanced ? 'rotate-180' : ''"></i>
                <span>Bộ lọc nâng cao</span>
            </button>

            <!-- Reset Button -->
            <a 
                href="/" 
                class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-xl border border-slate-200 text-sm font-semibold transition text-center"
            >
                Đặt lại
            </a>
        </div>
    </div>

    <!-- Advanced Filter Drawer (Collapsible) -->
    <div 
        x-show="showAdvanced"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-[500px]"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 max-h-[500px]"
        x-transition:leave-end="opacity-0 max-h-0"
        class="mt-6 pt-6 border-t border-slate-100 overflow-hidden"
        x-cloak
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Filter: Area (Diện tích) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Diện tích</label>
                <div class="relative">
                    <select 
                        name="area" 
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">Tất cả diện tích</option>
                        <option value="under_30">Dưới 30 m²</option>
                        <option value="30_50">30 - 50 m²</option>
                        <option value="50_80">50 - 80 m²</option>
                        <option value="above_80">Trên 80 m²</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Filter: Bedrooms (Phòng ngủ) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Số phòng ngủ</label>
                <div class="relative">
                    <select 
                        name="bedrooms" 
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">Tất cả</option>
                        <option value="1">1 phòng ngủ</option>
                        <option value="2">2 phòng ngủ</option>
                        <option value="3">3+ phòng ngủ</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Filter: Bathrooms (Phòng vệ sinh) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Số phòng vệ sinh</label>
                <div class="relative">
                    <select 
                        name="bathrooms" 
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">Tất cả</option>
                        <option value="1">1 phòng vệ sinh</option>
                        <option value="2">2+ phòng vệ sinh</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>

            <!-- Filter: Direction (Hướng nhà) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Hướng bất động sản</label>
                <div class="relative">
                    <select 
                        name="direction" 
                        class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">Tất cả các hướng</option>
                        <option value="east">Đông</option>
                        <option value="west">Tây</option>
                        <option value="south">Nam</option>
                        <option value="north">Bắc</option>
                        <option value="southeast">Đông Nam</option>
                        <option value="southwest">Tây Nam</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Filter Action Button -->
        <div class="flex justify-end gap-3 mt-6">
            <button 
                type="button" 
                @click="showAdvanced = false"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition"
            >
                Hủy
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl text-sm shadow-md shadow-primary/20 hover:shadow-primary/30 transition cursor-pointer"
            >
                Áp dụng bộ lọc
            </button>
        </div>
    </div>
</div>
