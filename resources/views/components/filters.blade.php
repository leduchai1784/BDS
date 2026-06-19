<form 
    action="/listings" 
    method="GET"
    x-data="{ 
        provinces: [],
        districts: [],
        wards: [],
        selectedProvince: '{{ request('province') ?: '' }}',
        selectedDistrict: '{{ request('district') ?: '' }}',
        selectedWard: '{{ request('ward') ?: '' }}',
        purpose: '{{ request('purpose') ?: '' }}',
        price: '{{ request('price') ?: '' }}',
        property_type: '{{ request('property_type') ?: '' }}',
        area: '{{ request('area') ?: '' }}',
        bedrooms: '{{ request('bedrooms') ?: '' }}',
        bathrooms: '{{ request('bathrooms') ?: '' }}',
        furniture: '{{ request('furniture') ?: '' }}',
        direction: '{{ request('direction') ?: '' }}',
        keyword: '{{ request('keyword') ?: '' }}',
        showAdvanced: false,
        
        async init() {
            try {
                const response = await fetch('/vietnam_provinces.json');
                this.provinces = await response.json();
                
                if (this.selectedProvince) {
                    const provObj = this.provinces.find(p => p.Name.includes(this.selectedProvince) || this.selectedProvince.includes(p.Name));
                    if (provObj) {
                        this.selectedProvince = provObj.Name;
                        this.districts = provObj.Districts;
                        if (this.selectedDistrict) {
                            const distObj = this.districts.find(d => d.Name.includes(this.selectedDistrict) || this.selectedDistrict.includes(d.Name) || d.Id === this.selectedDistrict);
                            if (distObj) {
                                this.selectedDistrict = distObj.Name;
                                this.wards = distObj.Wards;
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Failed to load locations:', error);
            }
        },
        
        updateDistricts() {
            this.selectedDistrict = '';
            this.selectedWard = '';
            this.districts = [];
            this.wards = [];
            
            const provObj = this.provinces.find(p => p.Name === this.selectedProvince);
            if (provObj) {
                this.districts = provObj.Districts;
            }
        },
        
        updateWards() {
            this.selectedWard = '';
            this.wards = [];
            
            const distObj = this.districts.find(d => d.Name === this.selectedDistrict);
            if (distObj) {
                this.wards = distObj.Wards;
            }
        }
    }" 
    class="bg-white rounded-3xl p-6 shadow-md border border-slate-100 mb-8 block text-left"
>
    <!-- Filter Top Bar (Always Visible) -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Heading & Quick Results -->
        <div>
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-primary"></i>
                <span>Bộ Lọc Tìm Kiếm</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Tìm kiếm chi tiết bất động sản phù hợp với nhu cầu của bạn</p>
        </div>

        <!-- Right Quick Filters & Advanced Toggle -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Toggle Advanced Filters Button -->
            <button 
                type="button" 
                @click="showAdvanced = !showAdvanced"
                :class="showAdvanced ? 'bg-primary text-white border-transparent' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'"
                class="flex items-center space-x-2 px-4 py-2.5 rounded-xl border text-sm font-bold transition duration-200 cursor-pointer"
            >
                <i class="fa-solid fa-circle-chevron-down transform transition duration-300" :class="showAdvanced ? 'rotate-180' : ''"></i>
                <span>Bộ lọc chi tiết</span>
            </button>

            <!-- Reset Button -->
            <a 
                href="/listings" 
                class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-xl border border-slate-200 text-sm font-semibold transition text-center"
            >
                Đặt lại
            </a>
        </div>
    </div>

    <!-- Collapsible Advanced Search Fields -->
    <div 
        x-show="showAdvanced"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-[800px]"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 max-h-[800px]"
        x-transition:leave-end="opacity-0 max-h-0"
        class="mt-6 pt-6 border-t border-slate-100 overflow-hidden"
        x-cloak
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Keyword -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Từ khóa tìm kiếm</label>
                <input 
                    type="text" 
                    name="keyword" 
                    x-model="keyword"
                    placeholder="Địa điểm, tên dự án..." 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition"
                >
            </div>

            <!-- Transaction Type -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Loại giao dịch</label>
                <select 
                    name="purpose" 
                    x-model="purpose"
                    @change="price = ''"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả</option>
                    <option value="rent">Cho thuê</option>
                    <option value="sale">Bán</option>
                </select>
            </div>

            <!-- Property Type -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Loại bất động sản</label>
                <select 
                    name="property_type" 
                    x-model="property_type"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả loại hình</option>
                    <option value="apartment">Căn hộ</option>
                    <option value="house">Nhà riêng</option>
                    <option value="room">Phòng trọ</option>
                    <option value="land">Đất nền</option>
                    <option value="premises">Mặt bằng</option>
                    <option value="office">Văn phòng</option>
                    <option value="warehouse">Kho xưởng</option>
                </select>
            </div>

            <!-- Province -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Tỉnh / Thành phố</label>
                <select 
                    name="province" 
                    x-model="selectedProvince"
                    @change="updateDistricts()"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Chọn Tỉnh/Thành phố</option>
                    <template x-for="p in provinces" :key="p.Id">
                        <option :value="p.Name" x-text="p.Name"></option>
                    </template>
                </select>
            </div>

            <!-- District -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Quận / Huyện</label>
                <select 
                    name="district" 
                    x-model="selectedDistrict"
                    @change="updateWards()"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                    :disabled="!selectedProvince"
                >
                    <option value="">Chọn Quận/Huyện</option>
                    <template x-for="d in districts" :key="d.Id">
                        <option :value="d.Name" x-text="d.Name"></option>
                    </template>
                </select>
            </div>

            <!-- Ward -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phường / Xã</label>
                <select 
                    name="ward" 
                    x-model="selectedWard"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                    :disabled="!selectedDistrict"
                >
                    <option value="">Chọn Phường/Xã</option>
                    <template x-for="w in wards" :key="w.Id">
                        <option :value="w.Name" x-text="w.Name"></option>
                    </template>
                </select>
            </div>

            <!-- Price -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Khoảng giá</label>
                <select 
                    name="price" 
                    x-model="price"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả mức giá</option>
                    <!-- Rent Options -->
                    <option value="under_3" x-show="purpose === 'rent' || purpose === ''">Dưới 3 triệu</option>
                    <option value="3_5" x-show="purpose === 'rent' || purpose === ''">3 - 5 triệu</option>
                    <option value="5_10" x-show="purpose === 'rent' || purpose === ''">5 - 10 triệu</option>
                    <option value="10_20" x-show="purpose === 'rent' || purpose === ''">10 - 20 triệu</option>
                    <option value="above_20" x-show="purpose === 'rent' || purpose === ''">Trên 20 triệu</option>
                    <!-- Sale Options -->
                    <option value="under_1b" x-show="purpose === 'sale'">Dưới 1 tỷ</option>
                    <option value="1b_3b" x-show="purpose === 'sale'">1 - 3 tỷ</option>
                    <option value="3b_5b" x-show="purpose === 'sale'">3 - 5 tỷ</option>
                    <option value="5b_10b" x-show="purpose === 'sale'">5 - 10 tỷ</option>
                    <option value="above_10b" x-show="purpose === 'sale'">Trên 10 tỷ</option>
                </select>
            </div>

            <!-- Area -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Diện tích</label>
                <select 
                    name="area" 
                    x-model="area"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả diện tích</option>
                    <option value="under_30">Dưới 30 m²</option>
                    <option value="30_50">30 - 50 m²</option>
                    <option value="50_80">50 - 80 m²</option>
                    <option value="80_120">80 - 120 m²</option>
                    <option value="above_120">Trên 120 m²</option>
                </select>
            </div>

            <!-- Bedrooms -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phòng ngủ</label>
                <select 
                    name="bedrooms" 
                    x-model="bedrooms"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả</option>
                    <option value="1">1+ phòng ngủ</option>
                    <option value="2">2+ phòng ngủ</option>
                    <option value="3">3+ phòng ngủ</option>
                    <option value="4">4+ phòng ngủ</option>
                </select>
            </div>

            <!-- Bathrooms -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Phòng vệ sinh</label>
                <select 
                    name="bathrooms" 
                    x-model="bathrooms"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả</option>
                    <option value="1">1+ phòng vệ sinh</option>
                    <option value="2">2+ phòng vệ sinh</option>
                    <option value="3">3+ phòng vệ sinh</option>
                </select>
            </div>

            <!-- Furniture -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Nội thất</label>
                <select 
                    name="furniture" 
                    x-model="furniture"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả</option>
                    <option value="full">Đầy đủ nội thất</option>
                    <option value="basic">Nội thất cơ bản</option>
                    <option value="none">Không nội thất</option>
                </select>
            </div>

            <!-- Direction -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 px-0.5">Hướng</label>
                <select 
                    name="direction" 
                    x-model="direction"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-sm font-semibold outline-none transition cursor-pointer"
                >
                    <option value="">Tất cả hướng</option>
                    <option value="east">Đông</option>
                    <option value="west">Tây</option>
                    <option value="south">Nam</option>
                    <option value="north">Bắc</option>
                    <option value="southeast">Đông Nam</option>
                    <option value="southwest">Tây Nam</option>
                    <option value="northeast">Đông Bắc</option>
                    <option value="northwest">Tây Bắc</option>
                </select>
            </div>
        </div>

        <!-- Filter Action Button -->
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
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
                Tìm kiếm
            </button>
        </div>
    </div>
</form>
