@php
    $isSale = $property->price_label && stripos($property->price_label, 'tháng') === false;
    $purpose = $isSale ? 'sale' : 'rent';
@endphp

<!-- MapLibre GL JS CSS -->
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.css">

<div class="pb-5 border-b border-slate-100 mb-8">
    <h1 class="text-xl font-bold text-slate-800">{{ $isSale ? 'Chỉnh sửa tin đăng bán' : 'Chỉnh sửa tin đăng cho thuê' }}</h1>
    <p class="text-xs text-slate-400 mt-1 font-semibold">Cập nhật thông tin chi tiết về bất động sản của bạn. Lưu ý: Tin đăng sẽ cần kiểm duyệt lại sau khi chỉnh sửa.</p>
</div>

<form 
    action="{{ route('properties.update', $property->id) }}" 
    method="POST" 
    enctype="multipart/form-data"
    x-data="propertyEditForm()"
    class="space-y-6 text-left"
>
    @csrf
    @method('PUT')
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
                value="{{ old('title', $property->title) }}"
                required 
                placeholder="Ví dụ: Căn hộ Studio Vinhomes Ocean Park Full Nội Thất..." 
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            >
            @error('title')
                <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Grid: Danh mục & Loại hình -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Danh mục -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Danh mục <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select 
                        name="category_id" 
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition"
                    >
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $property->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
                @error('category_id')
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
                        <option value="Căn hộ chung cư" {{ old('type', $property->type) == 'Căn hộ chung cư' ? 'selected' : '' }}>Căn hộ chung cư</option>
                        <option value="Nhà nguyên căn" {{ old('type', $property->type) == 'Nhà nguyên căn' ? 'selected' : '' }}>Nhà nguyên căn</option>
                        <option value="Biệt thự / Villa" {{ old('type', $property->type) == 'Biệt thự / Villa' ? 'selected' : '' }}>Biệt thự / Villa</option>
                        <option value="Văn phòng cho thuê" {{ old('type', $property->type) == 'Văn phòng cho thuê' ? 'selected' : '' }}>Văn phòng cho thuê</option>
                        <option value="Phòng trọ cho thuê" {{ old('type', $property->type) == 'Phòng trọ cho thuê' ? 'selected' : '' }}>Phòng trọ cho thuê</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
                @error('type')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Grid: Giá & Diện tích & Phòng ngủ/tắm -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- Giá thuê / Giá bán -->
            <div class="space-y-1 sm:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">{{ $isSale ? 'Giá bán (VND)' : 'Giá thuê (VND / Tháng)' }} <span class="text-red-500">*</span></label>
                <input 
                    type="number" 
                    name="price" 
                    value="{{ old('price', $property->price) }}"
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
                    value="{{ old('area', $property->area) }}"
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
                    value="{{ old('bedroom', $property->bedroom) }}"
                    min="0" 
                    placeholder="Ví dụ: 1" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
            </div>
        </div>

        <!-- Grid: Hướng & Phòng tắm & Pháp lý -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Phòng tắm -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Số phòng tắm</label>
                <input 
                    type="number" 
                    name="bathroom" 
                    value="{{ old('bathroom', $property->bathroom) }}"
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
                    value="{{ old('direction', $property->direction) }}"
                    placeholder="Ví dụ: Đông Nam, Tây Bắc..." 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
                >
            </div>

            <!-- Pháp lý -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Pháp lý</label>
                <input 
                    type="text" 
                    name="legal" 
                    value="{{ old('legal', $property->legal) }}"
                    placeholder="Ví dụ: Sổ hồng, cọc 2 tháng..." 
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
                value="{{ old('furniture', $property->furniture) }}"
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
                placeholder="Nhập thông tin chi tiết về tiện ích căn hộ..." 
                class="w-full p-4 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            >{{ old('description', $property->description) }}</textarea>
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
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select 
                        x-model="selectedProvince"
                        @change="
                            cityText = selectedProvince;
                            selectedDistrict = '';
                            districtText = '';
                            selectedWard = '';
                            wardText = '';
                            geocodeAddress();
                        "
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition text-left"
                    >
                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        <template x-for="p in provinces" :key="p.Id">
                            <option :value="p.Name" x-text="p.Name"></option>
                        </template>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
                <input type="hidden" name="city" :value="cityText">
                @error('city')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Quận/Huyện -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Quận/Huyện <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select 
                        x-model="selectedDistrict"
                        @change="
                            districtText = selectedDistrict;
                            selectedWard = '';
                            wardText = '';
                            geocodeAddress();
                        "
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition text-left"
                    >
                        <option value="">-- Chọn Quận/Huyện --</option>
                        <template x-for="d in (provinces.find(p => p.Name === selectedProvince)?.Districts || [])" :key="d.Id">
                            <option :value="d.Name" x-text="d.Name"></option>
                        </template>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                </div>
                <input type="hidden" name="district" :value="getDistrictCode(districtText)">
                @error('district')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Phường/Xã -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 px-1">Phường/Xã <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select 
                        x-model="selectedWard"
                        @change="
                            wardText = selectedWard;
                            geocodeAddress();
                        "
                        required 
                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none appearance-none cursor-pointer transition text-left"
                    >
                        <option value="">-- Chọn Phường/Xã --</option>
                        <template x-for="w in (provinces.find(p => p.Name === selectedProvince)?.Districts.find(d => d.Name === selectedDistrict)?.Wards || [])" :key="w.Id">
                            <option :value="w.Name" x-text="w.Name"></option>
                        </template>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
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
                    id="location-input-edit"
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
            <p class="text-[10px] text-slate-400 font-semibold px-1"><i class="fa-solid fa-circle-info mr-1"></i>Kéo thả điểm đánh dấu (Marker) màu đỏ hoặc bấm trực tiếp lên bản đồ để chọn tọa độ mới.</p>
            
            <!-- Map View Container -->
            <div id="picker-map-edit" style="height: 300px; min-height: 300px;" class="w-full rounded-2xl border border-slate-150 shadow-inner bg-slate-200 overflow-hidden relative"></div>
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
                    value="{{ old('phone', $property->phone) }}"
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
                    value="{{ old('zalo', $property->zalo) }}"
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
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Ảnh đại diện chính</label>
                
                <div class="flex items-start space-x-4">
                    <div class="w-24 h-20 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-inner flex items-center justify-center flex-shrink-0">
                        <img :src="mainPreview ? mainPreview : '{{ asset($property->image) }}'" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-2">
                        <p class="text-[9px] text-slate-400 leading-normal">Chọn ảnh mới nếu muốn thay đổi ảnh đại diện hiện tại.</p>
                        <label class="inline-flex items-center justify-center px-3 py-2 border border-slate-200 hover:border-primary text-[10px] font-bold rounded-xl text-slate-700 hover:text-white bg-slate-50 hover:bg-primary shadow-sm transition cursor-pointer">
                            <i class="fa-solid fa-camera mr-1.5"></i> Thay ảnh đại diện
                            <input type="file" name="image" accept="image/*" @change="previewMainImage($event)" class="hidden">
                        </label>
                    </div>
                </div>
                @error('image')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Thêm ảnh phụ (Gallery) -->
            <div class="space-y-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Tải lên thêm ảnh phụ</label>
                
                <div class="space-y-2">
                    <p class="text-[9px] text-slate-400 leading-normal">Chọn các ảnh phụ mới để tải thêm lên thư viện ảnh bất động sản.</p>
                    <label class="inline-flex items-center justify-center px-3 py-2 border border-slate-200 hover:border-primary text-[10px] font-bold rounded-xl text-slate-700 hover:text-white bg-slate-50 hover:bg-primary shadow-sm transition cursor-pointer">
                        <i class="fa-solid fa-images mr-1.5"></i> Chọn ảnh phụ mới
                        <input type="file" name="images[]" multiple accept="image/*" @change="previewGalleryImages($event)" class="hidden">
                    </label>
                </div>
            </div>
        </div>

        <!-- Existing gallery with Delete flags -->
        @if(!empty($property->images))
            <div class="space-y-2 pt-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">Thư viện ảnh hiện tại (Bấm vào nút đỏ để xóa ảnh)</label>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    @foreach($property->images as $img)
                        <div 
                            x-show="!deletedImages.includes('{{ $img }}')"
                            class="aspect-video bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm relative group"
                        >
                            <img src="{{ asset($img) }}" class="w-full h-full object-cover">
                            <button 
                                type="button" 
                                @click="toggleDeleteImage('{{ $img }}')"
                                class="absolute top-1 right-1 w-5 h-5 bg-red-500 hover:bg-red-650 text-white rounded-full flex items-center justify-center shadow transition cursor-pointer"
                                title="Xóa ảnh này"
                            >
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                            <!-- Hidden input to submit deletion request -->
                            <input type="hidden" name="delete_images[]" :value="deletedImages.includes('{{ $img }}') ? '{{ $img }}' : ''">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Newly chosen gallery previews -->
        <template x-if="galleryPreviews.length > 0">
            <div class="space-y-2 pt-2">
                <p class="text-[10px] font-bold text-slate-500 px-1">Ảnh phụ mới đã chọn để upload (<span x-text="galleryPreviews.length"></span> ảnh):</p>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    <template x-for="(src, index) in galleryPreviews" :key="index">
                        <div class="aspect-video bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm relative group">
                            <img :src="src" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-[9px] text-white font-bold font-sans">Sẵn sàng tải lên</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Submit buttons -->
    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-8">
        <button 
            type="button"
            @click="activeTab = 'properties'; window.history.pushState(null, '', '?tab=properties');" 
            class="inline-flex items-center justify-center px-5 py-3 border border-slate-200 text-xs font-bold rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer"
        >
            Hủy bỏ
        </button>
        <button 
            type="submit" 
            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-xs font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer active:scale-98 min-w-[130px]"
        >
            <span>Lưu thay đổi</span>
        </button>
    </div>
</form>

@push('scripts')
<!-- MapLibre GL JS SDK -->
<script src="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.js"></script>

<script>
    function propertyEditForm() {
        return {
            lat: {{ old('latitude', $property->latitude ?? 21.0285) }},
            lng: {{ old('longitude', $property->longitude ?? 105.8521) }},
            coordsInput: '{{ old('latitude', $property->latitude ?? 21.0285) }}, {{ old('longitude', $property->longitude ?? 105.8521) }}',
            locationText: '{{ old('address', $property->address) }}',
            wardText: '{{ old('ward', $property->ward) }}',
            districtText: '{{ old('district', $property->district) }}',
            cityText: '{{ old('city', $property->city ?? 'Thành phố Hà Nội') }}',
            provinces: [],
            selectedProvince: '',
            selectedDistrict: '',
            selectedWard: '',
            mainPreview: '',
            galleryPreviews: [],
            deletedImages: [], // Holds paths of images to be deleted
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

                this.$watch(() => this.activeTab, value => {
                    if (value === 'edit_property' && !this.map) {
                        this.$nextTick(() => {
                            this.initMap();
                        });
                    }
                });
                if (this.activeTab === 'edit_property') {
                    this.$nextTick(() => {
                        this.initMap();
                    });
                }
            },

            initMap(retryCount = 0) {
                const el = document.getElementById('picker-map-edit');
                if (!el) {
                    if (retryCount < 10) {
                        setTimeout(() => this.initMap(retryCount + 1), 100);
                    }
                    return;
                }
                if (this.map) return;
                this.map = new maplibregl.Map({
                    container: 'picker-map-edit',
                    style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
                    center: [this.lng, this.lat],
                    zoom: 13.5
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

                // Update marker position on click
                this.map.on('click', (e) => {
                    const lngLat = e.lngLat;
                    this.lat = parseFloat(lngLat.lat.toFixed(6));
                    this.lng = parseFloat(lngLat.lng.toFixed(6));
                    this.marker.setLngLat(lngLat);
                    this.coordsInput = `${this.lat}, ${this.lng}`;
                });
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
                            // Fallback: strip the first part of the address and try again
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
            },

            toggleDeleteImage(path) {
                if (!this.deletedImages.includes(path)) {
                    this.deletedImages.push(path);
                } else {
                    this.deletedImages = this.deletedImages.filter(item => item !== path);
                }
            }
        }
    }
</script>
@endpush
