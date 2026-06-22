@php
    $purpose = $purpose ?? request()->query('purpose', 'rent');
    $isSale = $purpose === 'sale';
@endphp

<!-- MapLibre GL JS CSS -->
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.css">

<div class="pb-5 border-b border-slate-100 mb-8">
    <h1 class="text-xl font-bold text-slate-800">{{ $isSale ? 'Đăng tin bán mới' : 'Đăng tin cho thuê mới' }}</h1>
    <p class="text-xs text-slate-400 mt-1 font-semibold">{{ $isSale ? 'Nhập đầy đủ thông tin chi tiết về bất động sản để thu hút người mua phù hợp.' : 'Nhập đầy đủ thông tin chi tiết về bất động sản để thu hút người thuê phù hợp.' }}</p>
</div>

<form 
    action="{{ route('properties.store') }}" 
    method="POST" 
    enctype="multipart/form-data"
    x-data="propertyCreateForm('{{ $purpose }}', {{ isset($isModal) && $isModal ? 'true' : 'false' }})"
    class="space-y-6 text-left"
>
    @csrf
    <input type="hidden" name="purpose" value="{{ $purpose }}">

    <!-- Section 1: Thông tin cơ bản -->
    <div class="space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-primary">1. Thông tin cơ bản</h3>
        
        <!-- Tiêu đề -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tiêu đề tin đăng <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                name="title" 
                value="{{ old('title') }}"
                required 
                placeholder="Ví dụ: Căn hộ Studio Vinhomes Ocean Park Full Nội Thất..." 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            >
            @error('title')
                <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Loại hình -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Loại hình <span class="text-red-500">*</span></label>
            <div class="relative">
                <select 
                    name="type" 
                    required 
                    class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                >
                    <option value="">-- Chọn loại hình --</option>
                    <option value="Căn hộ chung cư" {{ old('type') == 'Căn hộ chung cư' ? 'selected' : '' }}>Căn hộ chung cư</option>
                    <option value="Nhà nguyên căn" {{ old('type') == 'Nhà nguyên căn' ? 'selected' : '' }}>Nhà nguyên căn</option>
                    <option value="Phòng trọ" {{ old('type') == 'Phòng trọ' ? 'selected' : '' }}>Phòng trọ</option>
                    <option value="Đất" {{ old('type') == 'Đất' ? 'selected' : '' }}>Đất</option>
                    <option value="Mặt bằng" {{ old('type') == 'Mặt bằng' ? 'selected' : '' }}>Mặt bằng</option>
                    <option value="Văn phòng" {{ old('type') == 'Văn phòng' ? 'selected' : '' }}>Văn phòng</option>
                    <option value="Kho, nhà xưởng" {{ old('type') == 'Kho, nhà xưởng' ? 'selected' : '' }}>Kho, nhà xưởng</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
            </div>
            @error('type')
                <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Grid: Giá & Diện tích & Phòng ngủ/tắm -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- Giá thuê / Giá bán -->
            <div class="space-y-1 sm:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">{{ $isSale ? 'Giá bán (VND)' : 'Giá thuê (VND / Tháng)' }} <span class="text-red-500">*</span></label>
                <input 
                    type="number" 
                    name="price" 
                    value="{{ old('price') }}"
                    required 
                    min="0" 
                    placeholder="Ví dụ: 6500000" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
                @error('price')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Diện tích -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Diện tích (m²) <span class="text-red-500">*</span></label>
                <input 
                    type="number" 
                    name="area" 
                    value="{{ old('area') }}"
                    required 
                    min="0" 
                    placeholder="Ví dụ: 35" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
                @error('area')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Phòng ngủ -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng ngủ</label>
                <input 
                    type="number" 
                    name="bedroom" 
                    value="{{ old('bedroom', 0) }}"
                    min="0" 
                    placeholder="Ví dụ: 1" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
            </div>
        </div>

        <!-- Dynamic Fields for Sale vs Rent -->
        @if($isSale)
            <!-- Grid: Thông số nhà đất (Chỉ cho tin Bán) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Mặt tiền -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mặt tiền (m)</label>
                    <input 
                        type="number" 
                        step="0.01"
                        name="frontage" 
                        value="{{ old('frontage') }}"
                        placeholder="Ví dụ: 5.5" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    >
                    @error('frontage')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Đường vào -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Đường rộng (m)</label>
                    <input 
                        type="number" 
                        step="0.01"
                        name="road_width" 
                        value="{{ old('road_width') }}"
                        placeholder="Ví dụ: 12.0" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    >
                    @error('road_width')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Số tầng -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số tầng</label>
                    <input 
                        type="number" 
                        name="floors" 
                        value="{{ old('floors') }}"
                        min="0"
                        placeholder="Ví dụ: 3" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    >
                    @error('floors')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @else
            <!-- Grid: Thông số thuê (Chỉ cho tin Thuê) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tiền đặt cọc -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tiền đặt cọc (VND)</label>
                    <input 
                        type="number" 
                        name="deposit" 
                        value="{{ old('deposit') }}"
                        min="0"
                        placeholder="Ví dụ: 10000000 (10 triệu)" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    >
                    @error('deposit')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thời hạn thuê tối thiểu -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Thời hạn hợp đồng tối thiểu</label>
                    <input 
                        type="text" 
                        name="lease_term" 
                        value="{{ old('lease_term') }}"
                        placeholder="Ví dụ: Tối thiểu 1 năm, 6 tháng..." 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                    >
                    @error('lease_term')
                        <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        <!-- Grid: Hướng & Phòng tắm & Pháp lý -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Phòng tắm -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng tắm</label>
                <input 
                    type="number" 
                    name="bathroom" 
                    value="{{ old('bathroom', 0) }}"
                    min="0" 
                    placeholder="Ví dụ: 1" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
            </div>

            <!-- Hướng nhà -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Hướng nhà</label>
                <input 
                    type="text" 
                    name="direction" 
                    value="{{ old('direction') }}"
                    placeholder="Ví dụ: Đông Nam, Tây Bắc..." 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
            </div>

            <!-- Pháp lý -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Pháp lý / Giấy tờ</label>
                <input 
                    type="text" 
                    name="legal" 
                    value="{{ old('legal') }}"
                    placeholder="Ví dụ: Sổ đỏ chính chủ, Sổ hồng riêng, Hợp đồng công chứng..." 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
            </div>
        </div>

        <!-- Nội thất -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tình trạng nội thất</label>
            <input 
                type="text" 
                name="furniture" 
                value="{{ old('furniture') }}"
                placeholder="Ví dụ: Full nội thất (Tivi, Tủ lạnh, Sofa...)" 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            >
        </div>

        <!-- Mô tả chi tiết -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Mô tả chi tiết <span class="text-red-500">*</span></label>
            <textarea 
                name="description" 
                required 
                rows="5" 
                placeholder="Nhập thông tin chi tiết về tiện ích căn hộ, khu dân cư, giao thông, ưu đãi..." 
                class="w-full p-4 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="border-t border-slate-100 my-6"></div>

    <!-- Section 2: Địa chỉ & Tọa độ -->
    <div class="space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-primary">2. Vị trí bất động sản</h3>
        
        <!-- Grid: Tỉnh/Thành phố, Quận/Huyện, Phường/Xã, Địa chỉ chính xác -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Tỉnh/Thành phố -->
            <div class="space-y-1" x-data="{ open: false }" @click.outside="if(!provinces.find(p => p.Name.toLowerCase() === provinceSearch.toLowerCase())) { provinceSearch = selectedProvince; } open = false">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="-- Chọn Tỉnh/Thành phố --"
                        x-model="provinceSearch"
                        @focus="open = true"
                        @input="open = true"
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition text-left"
                    >
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    
                    <!-- Dropdown Panel -->
                    <div 
                        x-show="open" 
                        class="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left"
                        x-cloak
                    >
                        <template x-for="p in provinces.filter(prov => !provinceSearch || prov.Name.toLowerCase().includes(provinceSearch.toLowerCase()))" :key="p.Id">
                            <div 
                                @click="
                                    selectedProvince = p.Name;
                                    provinceSearch = p.Name;
                                    cityText = p.Name;
                                    selectedDistrict = '';
                                    districtSearch = '';
                                    districtText = '';
                                    selectedWard = '';
                                    wardSearch = '';
                                    wardText = '';
                                    open = false;
                                    geocodeAddress();
                                "
                                class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                                x-text="p.Name"
                            ></div>
                        </template>
                        <div x-show="provinces.filter(prov => !provinceSearch || prov.Name.toLowerCase().includes(provinceSearch.toLowerCase())).length === 0" class="px-4 py-2.5 text-xs text-slate-400 font-semibold">
                            Không tìm thấy kết quả
                        </div>
                    </div>
                </div>
                <input type="hidden" name="city" :value="cityText">
                @error('city')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Quận/Huyện -->
            <div class="space-y-1" x-data="{ open: false }" @click.outside="if(!(provinces.find(p => p.Name === selectedProvince)?.Districts || []).find(d => d.Name.toLowerCase() === districtSearch.toLowerCase())) { districtSearch = selectedDistrict; } open = false">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quận/Huyện <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="-- Chọn Quận/Huyện --"
                        x-model="districtSearch"
                        @focus="open = true"
                        @input="open = true"
                        :disabled="!selectedProvince"
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition text-left disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    
                    <!-- Dropdown Panel -->
                    <div 
                        x-show="open" 
                        class="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left"
                        x-cloak
                    >
                        <template x-for="d in (provinces.find(p => p.Name === selectedProvince)?.Districts || []).filter(dist => !districtSearch || dist.Name.toLowerCase().includes(districtSearch.toLowerCase()))" :key="d.Id">
                            <div 
                                @click="
                                    selectedDistrict = d.Name;
                                    districtSearch = d.Name;
                                    districtText = d.Name;
                                    selectedWard = '';
                                    wardSearch = '';
                                    wardText = '';
                                    open = false;
                                    geocodeAddress();
                                "
                                class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                                x-text="d.Name"
                            ></div>
                        </template>
                        <div x-show="(provinces.find(p => p.Name === selectedProvince)?.Districts || []).filter(dist => !districtSearch || dist.Name.toLowerCase().includes(districtSearch.toLowerCase())).length === 0" class="px-4 py-2.5 text-xs text-slate-400 font-semibold">
                            Không tìm thấy kết quả
                        </div>
                    </div>
                </div>
                <input type="hidden" name="district" :value="getDistrictCode(districtText)">
                @error('district')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Phường/Xã -->
            <div class="space-y-1" x-data="{ open: false }" @click.outside="if(!(provinces.find(p => p.Name === selectedProvince)?.Districts.find(d => d.Name === selectedDistrict)?.Wards || []).find(w => w.Name.toLowerCase() === wardSearch.toLowerCase())) { wardSearch = selectedWard; } open = false">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường/Xã <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="-- Chọn Phường/Xã --"
                        x-model="wardSearch"
                        @focus="open = true"
                        @input="open = true"
                        :disabled="!selectedDistrict"
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none cursor-pointer transition text-left disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    
                    <!-- Dropdown Panel -->
                    <div 
                        x-show="open" 
                        class="absolute z-50 w-full mt-1 bg-white border border-slate-150 rounded-xl shadow-lg max-h-60 overflow-y-auto text-left"
                        x-cloak
                    >
                        <template x-for="w in (provinces.find(p => p.Name === selectedProvince)?.Districts.find(d => d.Name === selectedDistrict)?.Wards || []).filter(ward => !wardSearch || ward.Name.toLowerCase().includes(wardSearch.toLowerCase()))" :key="w.Id">
                            <div 
                                @click="
                                    selectedWard = w.Name;
                                    wardSearch = w.Name;
                                    wardText = w.Name;
                                    open = false;
                                    geocodeAddress();
                                "
                                class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-light hover:text-primary cursor-pointer transition"
                                x-text="w.Name"
                            ></div>
                        </template>
                        <div x-show="(provinces.find(p => p.Name === selectedProvince)?.Districts.find(d => d.Name === selectedDistrict)?.Wards || []).filter(ward => !wardSearch || ward.Name.toLowerCase().includes(wardSearch.toLowerCase())).length === 0" class="px-4 py-2.5 text-xs text-slate-400 font-semibold">
                            Không tìm thấy kết quả
                        </div>
                    </div>
                </div>
                <input type="hidden" name="ward" :value="wardText">
                @error('ward')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Địa chỉ chi tiết -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="address" 
                    id="location-input-create"
                    x-model="locationText"
                    @input.debounce.800ms="geocodeAddress()"
                    @change="geocodeAddress()"
                    required 
                    placeholder="Ví dụ: Số 15, Ngõ 44, Đường Duy Tân" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition text-left"
                >
                @error('address')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Hidden Inputs for Form Submission -->
        <input type="hidden" name="latitude" :value="lat">
        <input type="hidden" name="longitude" :value="lng">

        <!-- Single Coordinates Input Box -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tọa độ bản đồ (Vĩ độ, Kinh độ) <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                x-model="coordsInput"
                @input="parseCoords()"
                required
                placeholder="Ví dụ: 21.03, 105.81" 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            >
            <p class="text-[9px] text-slate-400 font-semibold px-1"><i class="fa-solid fa-circle-info mr-1"></i>Định dạng: vĩ_độ, kinh_độ (Ví dụ copy từ Google Maps: 21.0285, 105.8521)</p>
        </div>

        <!-- Map Selector Section -->
        <div class="space-y-2">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Chọn vị trí trên bản đồ <span class="text-red-500">*</span></label>
            <p class="text-[10px] text-slate-400 font-semibold px-1"><i class="fa-solid fa-circle-info mr-1"></i>Kéo thả điểm đánh dấu (Marker) màu đỏ hoặc bấm trực tiếp lên bản đồ để chọn tọa độ chính xác.</p>
            
            <!-- Map View Container -->
            <div :id="'picker-map-create-' + purpose" style="height: 300px; min-height: 300px;" class="w-full rounded-2xl border border-slate-150 shadow-inner bg-slate-200 overflow-hidden relative"></div>
        </div>
    </div>

    <div class="border-t border-slate-100 my-6"></div>

    <!-- Section 3: Thông tin liên hệ -->
    <div class="space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-primary">3. Thông tin liên hệ</h3>
        
        <!-- Grid: Số điện thoại & Zalo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Số điện thoại -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone', Auth::user()?->phone) }}"
                    required 
                    placeholder="Ví dụ: 0987654321" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
                @error('phone')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Zalo -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Link Zalo (Tùy chọn)</label>
                <input 
                    type="text" 
                    name="zalo" 
                    value="{{ old('zalo') }}"
                    placeholder="Ví dụ: https://zalo.me/0987654321" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
                @error('zalo')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 my-6"></div>

    <!-- Section 4: Hình ảnh bất động sản -->
    <div class="space-y-4">
        <h3 class="text-xs font-black uppercase tracking-wider text-primary">4. Hình ảnh bất động sản</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Ảnh đại diện chính -->
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Ảnh đại diện chính <span class="text-red-500">*</span></label>
                    <div class="flex bg-slate-100 p-0.5 rounded-lg text-[9px] font-bold select-none">
                        <button type="button" @click="mainImageType = 'file'; mainPreview = ''; image_url = '';" :class="mainImageType === 'file' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md transition cursor-pointer">Upload file</button>
                        <button type="button" @click="mainImageType = 'url'; mainPreview = '';" :class="mainImageType === 'url' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md transition cursor-pointer">Nhập link</button>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-24 h-20 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-inner flex items-center justify-center flex-shrink-0">
                        <template x-if="mainPreview">
                            <img :src="mainPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!mainPreview">
                            <i class="fa-regular fa-image text-slate-300 text-2xl"></i>
                        </template>
                    </div>
                    <div class="flex-grow space-y-2">
                        <div x-show="mainImageType === 'file'" class="space-y-2">
                            <p class="text-[9px] text-slate-400 leading-normal">Ảnh đại diện sẽ hiển thị làm ảnh bìa chính trên trang danh sách tìm kiếm.</p>
                            <label class="inline-flex items-center justify-center px-3 py-2 border border-slate-200 hover:border-primary text-[10px] font-bold rounded-xl text-slate-700 hover:text-white bg-slate-50 hover:bg-primary shadow-sm transition cursor-pointer">
                                <i class="fa-solid fa-camera mr-1.5"></i> Chọn ảnh chính
                                <input type="file" name="image" :required="mainImageType === 'file'" accept="image/*" @change="previewMainImage($event)" class="hidden">
                            </label>
                        </div>
                        <div x-show="mainImageType === 'url'" class="space-y-1 text-left">
                            <p class="text-[9px] text-slate-400 leading-normal">Nhập liên kết ảnh chính trực tiếp từ URL:</p>
                            <input 
                                type="text" 
                                name="image_url" 
                                x-model="image_url"
                                :required="mainImageType === 'url'"
                                placeholder="Dán link ảnh đại diện vào đây..." 
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                            >
                            @error('image_url')
                                <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @error('image')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Nhiều ảnh phụ (Gallery) -->
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Thư viện ảnh phụ (Gallery)</label>
                    <div class="flex bg-slate-100 p-0.5 rounded-lg text-[9px] font-bold select-none">
                        <button type="button" @click="galleryImageType = 'file'; galleryPreviews = []; gallery_urls = '';" :class="galleryImageType === 'file' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md transition cursor-pointer">Upload file</button>
                        <button type="button" @click="galleryImageType = 'url'; galleryPreviews = [];" :class="galleryImageType === 'url' ? 'bg-white text-primary shadow-sm' : 'text-slate-500'" class="px-2 py-1 rounded-md transition cursor-pointer">Nhập link</button>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div x-show="galleryImageType === 'file'" class="space-y-2">
                        <p class="text-[9px] text-slate-400 leading-normal">Tải lên tối đa 9 ảnh phụ mô tả chi tiết phòng khách, phòng ngủ, nhà bếp, ban công...</p>
                        <label class="inline-flex items-center justify-center px-3 py-2 border border-slate-200 hover:border-primary text-[10px] font-bold rounded-xl text-slate-700 hover:text-white bg-slate-50 hover:bg-primary shadow-sm transition cursor-pointer">
                            <i class="fa-solid fa-images mr-1.5"></i> Chọn nhiều ảnh phụ
                            <input type="file" name="images[]" multiple accept="image/*" @change="previewGalleryImages($event)" class="hidden">
                        </label>
                    </div>
                    <div x-show="galleryImageType === 'url'" class="space-y-1 text-left">
                        <p class="text-[9px] text-slate-400 leading-normal">Dán link ảnh phụ vào đây (mỗi dòng một link hoặc phân tách bằng dấu phẩy):</p>
                        <textarea 
                            name="gallery_urls" 
                            x-model="gallery_urls"
                            rows="2"
                            placeholder="Ví dụ:&#10;https://cloudinary.com/image1.jpg&#10;https://cloudinary.com/image2.jpg" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                        ></textarea>
                        @error('gallery_urls')
                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery previews list -->
        <template x-if="galleryPreviews.length > 0">
            <div class="space-y-2 pt-2">
                <p class="text-[10px] font-bold text-slate-500 px-1">Ảnh phụ đã chọn (<span x-text="galleryPreviews.length"></span> ảnh):</p>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    <template x-for="(src, index) in galleryPreviews" :key="index">
                        <div class="aspect-video bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm relative group">
                            <img :src="src" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-[9px] text-white font-bold">Xem trước</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Submit buttons -->
    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-8">
        <a 
            href="{{ route('properties.choose-type') }}" 
            class="inline-flex items-center justify-center px-5 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer"
        >
            Hủy bỏ
        </a>
        <button 
            type="submit" 
            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
        >
            <span>Đăng tin ngay</span>
        </button>
    </div>
</form>

@push('scripts')
<!-- MapLibre GL JS SDK -->
<script src="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.js"></script>

<script>
    function propertyCreateForm(purpose, isModal = false) {
        return {
            purpose: purpose,
            lat: {{ old('latitude', 21.0285) }},
            lng: {{ old('longitude', 105.8521) }},
            coordsInput: '{{ old('latitude', 21.0285) }}, {{ old('longitude', 105.8521) }}',
            locationText: '{{ old('address') }}',
            wardText: '{{ old('ward') }}',
            districtText: '{{ old('district') }}',
            cityText: '{{ old('city', 'Thành phố Hà Nội') }}',
            provinceSearch: '',
            districtSearch: '',
            wardSearch: '',
            provinces: [],
            selectedProvince: '',
            selectedDistrict: '',
            selectedWard: '',
            mainPreview: '',
            galleryPreviews: [],
            mainImageType: 'file',
            image_url: '',
            galleryImageType: 'file',
            gallery_urls: '',
            map: null,
            marker: null,

            init() {
                fetch('/vietnam_provinces.json')
                    .then(res => res.json())
                    .then(data => {
                        this.provinces = data;
                        this.initializeDropdowns();
                    })
                    .catch(err => console.error("Error loading provinces:", err));

                this.$watch('image_url', value => {
                    if (this.mainImageType === 'url') {
                        this.mainPreview = value;
                    }
                });
                this.$watch('gallery_urls', value => {
                    if (this.galleryImageType === 'url') {
                        if (!value) {
                            this.galleryPreviews = [];
                        } else {
                            this.galleryPreviews = value.split(/[\n,\r]+/)
                                .map(u => u.trim())
                                .filter(u => u.length > 0);
                        }
                    }
                });

                if (isModal) {
                    this.$watch('activeModal', value => {
                        if (value === this.purpose && !this.map) {
                            this.$nextTick(() => {
                                this.initMap();
                                this.detectCurrentLocation();
                            });
                        }
                    });
                    if (typeof activeModal !== 'undefined' && activeModal === this.purpose) {
                        this.$nextTick(() => {
                            this.initMap();
                            this.detectCurrentLocation();
                        });
                    }
                } else {
                    const isTabbed = typeof this.activeTab !== 'undefined';
                    if (isTabbed) {
                        this.$watch('activeTab', value => {
                            if (value === 'create_property' && !this.map) {
                                this.$nextTick(() => {
                                    this.initMap();
                                    this.detectCurrentLocation();
                                });
                            }
                        });
                        if (this.activeTab === 'create_property') {
                            this.$nextTick(() => {
                                this.initMap();
                                this.detectCurrentLocation();
                            });
                        }
                    } else {
                        this.$nextTick(() => {
                            this.initMap();
                            this.detectCurrentLocation();
                        });
                    }
                }
            },

            initMap(retryCount = 0) {
                const mapId = 'picker-map-create-' + this.purpose;
                const el = document.getElementById(mapId);
                if (!el) {
                    if (retryCount < 10) {
                        setTimeout(() => this.initMap(retryCount + 1), 100);
                    }
                    return;
                }
                if (this.map) return;
                this.map = new maplibregl.Map({
                    container: mapId,
                    style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
                    center: [this.lng, this.lat],
                    zoom: 13
                });

                // Create draggable marker
                this.marker = new maplibregl.Marker({
                    draggable: true,
                    color: '#ff4433' // Red marker
                })
                .setLngLat([this.lng, this.lat])
                .addTo(this.map);

                // Update inputs on drag end
                this.marker.on('dragend', () => {
                    const lngLat = this.marker.getLngLat();
                    this.lat = parseFloat(lngLat.lat.toFixed(6));
                    this.lng = parseFloat(lngLat.lng.toFixed(6));
                    this.coordsInput = `${this.lat}, ${this.lng}`;
                });

                // Update marker position and inputs on map click
                this.map.on('click', (e) => {
                    const lngLat = e.lngLat;
                    this.lat = parseFloat(lngLat.lat.toFixed(6));
                    this.lng = parseFloat(lngLat.lng.toFixed(6));
                    this.marker.setLngLat(lngLat);
                    this.coordsInput = `${this.lat}, ${this.lng}`;
                });
            },

            detectCurrentLocation() {
                @if(!old('latitude') && !old('longitude'))
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        this.lat = parseFloat(position.coords.latitude.toFixed(6));
                        this.lng = parseFloat(position.coords.longitude.toFixed(6));
                        this.coordsInput = `${this.lat}, ${this.lng}`;
                        if (this.marker && this.map) {
                            this.marker.setLngLat([this.lng, this.lat]);
                            this.map.setCenter([this.lng, this.lat]);
                        }
                    }, err => {
                        console.log("Geolocation error or permission denied:", err);
                    });
                }
                @endif
            },

            geocodeAddress() {
                let parts = [];
                if (this.locationText && this.locationText.trim().length >= 3) {
                    parts.push(this.locationText.trim());
                }
                if (this.wardText && this.wardText.trim().length >= 3) {
                    parts.push(this.wardText.trim());
                }
                if (this.districtText && this.districtText.trim().length >= 3) {
                    parts.push(this.districtText.trim());
                }
                if (this.cityText && this.cityText.trim().length >= 3) {
                    parts.push(this.cityText.trim());
                }

                if (parts.length === 0) return;

                const query = parts.join(', ');
                this.geocodeQuery(query);
            },

            initializeDropdowns() {
                if (!this.provinces || !this.provinces.length) return;
                
                // 1. Match city
                if (this.cityText) {
                    const matchCity = this.provinces.find(p => 
                        p.Name.toLowerCase() === this.cityText.toLowerCase() ||
                        p.Name.toLowerCase().replace(/^(thành phố|tỉnh)\s+/i, '') === this.cityText.toLowerCase()
                    );
                    if (matchCity) {
                        this.selectedProvince = matchCity.Name;
                    }
                }
                
                // 2. Match district
                if (this.districtText && this.selectedProvince) {
                    const prov = this.provinces.find(p => p.Name === this.selectedProvince);
                    if (prov) {
                        const codeMap = {
                            'GL': 'Gia Lâm', 'BD': 'Ba Đình', 'TH': 'Tây Hồ', 'CG': 'Cầu Giấy',
                            'DD': 'Đống Đa', 'HK': 'Hoàn Kiếm', 'HBT': 'Hai Bà Trưng', 'TX': 'Thanh Xuân',
                            'NTL': 'Nam Từ Liêm', 'BTL': 'Bắc Từ Liêm', 'Q1': 'Quận 1', 'Q3': 'Quận 3',
                            'BT': 'Bình Thạnh', 'TD': 'Thủ Đức', 'Q10': 'Quận 10'
                        };
                        const searchDistrictName = codeMap[this.districtText] || this.districtText;
                        
                        const matchDist = prov.Districts.find(d => 
                            d.Name.toLowerCase() === searchDistrictName.toLowerCase() ||
                            d.Name.toLowerCase().replace(/^(quận|huyện|thị xã|thành phố)\s+/i, '') === searchDistrictName.toLowerCase()
                        );
                        if (matchDist) {
                            this.selectedDistrict = matchDist.Name;
                        }
                    }
                }
                
                // 3. Match ward
                if (this.wardText && this.selectedDistrict && this.selectedProvince) {
                    const prov = this.provinces.find(p => p.Name === this.selectedProvince);
                    const dist = prov ? prov.Districts.find(d => d.Name === this.selectedDistrict) : null;
                    if (dist) {
                        const matchWard = dist.Wards.find(w => 
                            w.Name.toLowerCase() === this.wardText.toLowerCase() ||
                            w.Name.toLowerCase().replace(/^(phường|xã|thị trấn)\s+/i, '') === this.wardText.toLowerCase()
                        );
                        if (matchWard) {
                            this.selectedWard = matchWard.Name;
                        }
                    }
                }
            },

            getDistrictCode(name) {
                if (!name) return '';
                const cleanName = name.replace(/^(Quận|Huyện|Thị xã|Thành phố)\s+/i, '').trim();
                const mapping = {
                    'Gia Lâm': 'GL',
                    'Ba Đình': 'BD',
                    'Tây Hồ': 'TH',
                    'Cầu Giấy': 'CG',
                    'Đống Đa': 'DD',
                    'Hoàn Kiếm': 'HK',
                    'Hai Bà Trưng': 'HBT',
                    'Thanh Xuân': 'TX',
                    'Nam Từ Liêm': 'NTL',
                    'Bắc Từ Liêm': 'BTL',
                    'Quận 1': 'Q1',
                    'Quận 3': 'Q3',
                    'Bình Thạnh': 'BT',
                    'Thủ Đức': 'TD',
                    'Quận 10': 'Q10'
                };
                if (mapping[cleanName]) return mapping[cleanName];
                for (const key in mapping) {
                    if (cleanName.toLowerCase().indexOf(key.toLowerCase()) !== -1) {
                        return mapping[key];
                    }
                }
                return cleanName.substring(0, 10);
            },

            geocodeQuery(query) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const result = data[0];
                            this.lat = parseFloat(parseFloat(result.lat).toFixed(6));
                            this.lng = parseFloat(parseFloat(result.lon).toFixed(6));
                            this.coordsInput = `${this.lat}, ${this.lng}`;
                            if (this.marker && this.map) {
                                this.marker.setLngLat([this.lng, this.lat]);
                                this.map.setCenter([this.lng, this.lat]);
                            }
                        } else {
                            // Fallback: strip the first part of the query (e.g. detailed street address) and try again
                            const parts = query.split(',');
                            if (parts.length > 1) {
                                parts.shift();
                                const fallbackQuery = parts.join(',').trim();
                                if (fallbackQuery.length >= 3) {
                                    this.geocodeQuery(fallbackQuery);
                                }
                            }
                        }
                    })
                    .catch(err => console.error("Geocoding error:", err));
            },

            parseCoords() {
                const input = this.coordsInput.trim();
                const parts = input.split(/[\s,]+/);
                if (parts.length >= 2) {
                    const latVal = parseFloat(parts[0]);
                    const lngVal = parseFloat(parts[1]);
                    if (!isNaN(latVal) && !isNaN(lngVal)) {
                        this.lat = latVal;
                        this.lng = lngVal;
                        if (this.marker && this.map) {
                            this.marker.setLngLat([lngVal, latVal]);
                            this.map.setCenter([lngVal, latVal]);
                        }
                    }
                }
            },

            previewMainImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.mainPreview = URL.createObjectURL(file);
                }
            },

            previewGalleryImages(event) {
                const files = event.target.files;
                this.galleryPreviews = [];
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    this.galleryPreviews.push(URL.createObjectURL(file));
                }
            }
        }
    }
</script>
@endpush
