<section class="relative z-30 min-h-[90vh] flex items-center justify-center bg-slate-900 text-white pt-24 pb-16 overflow-visible">
    <!-- Background Wrapper to contain decorative elements and images -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        <!-- Background Image with Dark Overlay -->
        <img 
            src="{{ asset('images/hero_bg.png') }}" 
            alt="Real estate banner" 
            class="w-full h-full object-cover object-center opacity-40 transform scale-105"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-slate-950/40"></div>

        <!-- Background Decorative Elements -->
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-primary/20 blur-3xl z-0"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl z-0"></div>
    </div>

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

        <!-- Search Bar Widget -->
        <div 
            class="bg-white rounded-full p-2 pl-6 pr-2 shadow-2xl text-slate-800 max-w-4xl mx-auto border border-slate-100/50 backdrop-blur-md relative"
            x-data="{
                query: '',
                suggestions: [],
                isOpen: false,
                activeIndex: -1,
                locating: false,
                async fetchSuggestions() {
                    if (this.query.trim().length < 2) {
                        this.suggestions = [];
                        this.isOpen = false;
                        return;
                    }
                    try {
                        const res = await fetch(`/api/properties/autocomplete?q=${encodeURIComponent(this.query)}`);
                        this.suggestions = await res.json();
                        this.isOpen = this.suggestions.length > 0;
                        this.activeIndex = -1;
                    } catch (error) {
                        console.error('Error fetching suggestions:', error);
                    }
                },
                selectSuggestion(sug) {
                    this.query = sug.label;
                    this.isOpen = false;
                    if (sug.type === 'property' && sug.id) {
                        window.location.href = `/property/${sug.id}`;
                    } else if (sug.type === 'city') {
                        window.location.href = `/map?city=${encodeURIComponent(sug.value)}`;
                    } else if (sug.type === 'district') {
                        window.location.href = `/map?district=${encodeURIComponent(sug.value)}`;
                    } else if (sug.type === 'ward') {
                        window.location.href = `/map?ward=${encodeURIComponent(sug.value)}`;
                    } else {
                        window.location.href = `/map?keyword=${encodeURIComponent(sug.value)}`;
                    }
                },
                selectActiveIndex() {
                    if (this.activeIndex >= 0 && this.activeIndex < this.suggestions.length) {
                        this.selectSuggestion(this.suggestions[this.activeIndex]);
                    } else {
                        this.$refs.searchForm.submit();
                    }
                },
                getUserLocation() {
                    if (!navigator.geolocation) {
                        alert('Trình duyệt của bạn không hỗ trợ định vị vị trí.');
                        return;
                    }
                    this.locating = true;
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.locating = false;
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            window.location.href = `/map?lat=${lat}&lng=${lng}`;
                        },
                        (error) => {
                            this.locating = false;
                            console.error('Error getting location:', error);
                            alert('Không thể lấy vị trí hiện tại của bạn. Vui lòng cấp quyền truy cập vị trí cho trang web.');
                        },
                        { enableHighAccuracy: true, timeout: 6000 }
                    );
                }
            }"
            @click.outside="isOpen = false"
        >
            <form action="/map" method="GET" x-ref="searchForm">
                <div class="flex flex-col sm:flex-row items-center w-full gap-2">
                    <!-- Location Icon and Input -->
                    <div class="relative flex-grow w-full text-left flex items-center">
                        <button 
                            type="button" 
                            @click="getUserLocation()" 
                            class="hover:scale-110 active:scale-95 transition text-primary hover:text-primary-hover flex-shrink-0 cursor-pointer focus:outline-none relative"
                            title="Định vị vị trí hiện tại"
                            :disabled="locating"
                        >
                            <template x-if="locating">
                                <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <template x-if="!locating">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </template>
                        </button>
                        <div class="h-6 w-px bg-slate-200 ml-2.5 mr-3 flex-shrink-0"></div>
                        <input 
                            type="text" 
                            name="keyword" 
                            x-model="query"
                            @input.debounce.250ms="fetchSuggestions()"
                            @focus="isOpen = suggestions.length > 0"
                            @keydown.arrow-down.prevent="activeIndex = (activeIndex + 1) % suggestions.length"
                            @keydown.arrow-up.prevent="activeIndex = (activeIndex - 1 + suggestions.length) % suggestions.length"
                            @keydown.enter.prevent="selectActiveIndex()"
                            @keydown.escape="isOpen = false"
                            placeholder="Nhập địa chỉ hoặc khu vực tìm kiếm" 
                            autocomplete="off"
                            class="w-full py-3 bg-transparent text-sm font-semibold outline-none appearance-none transition h-12"
                        >
                        <button 
                            type="button" 
                            x-show="query.length > 0"
                            @click="query = ''; suggestions = []; isOpen = false;"
                            class="absolute right-2 text-slate-400 hover:text-slate-650 transition cursor-pointer"
                            style="display: none;"
                        >
                            <i class="fa-solid fa-circle-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="w-full sm:w-auto flex-shrink-0">
                        <button 
                            type="submit" 
                            class="w-full sm:w-40 bg-primary hover:bg-primary-hover text-white font-extrabold py-3 px-6 rounded-full flex items-center justify-center space-x-2 shadow-lg shadow-primary/20 hover:shadow-primary/35 transition duration-200 h-12 cursor-pointer text-sm"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Tìm kiếm</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Autocomplete Suggestion Dropdown -->
            <div 
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute left-4 right-4 md:left-5 md:right-5 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden text-left"
                style="display: none;"
            >
                <div class="max-h-[350px] overflow-y-auto py-2">
                    <template x-for="(sug, index) in suggestions" :key="index">
                        <div 
                            @click="selectSuggestion(sug)"
                            @mouseenter="activeIndex = index"
                            :class="{ 'bg-slate-50 text-primary': activeIndex === index }"
                            class="px-4 py-3 cursor-pointer flex items-center justify-between border-b border-slate-50 last:border-0 hover:bg-slate-50 transition duration-150"
                        >
                            <div class="flex items-center space-x-3">
                                <!-- Dynamic Icon -->
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 transition">
                                    <template x-if="sug.type === 'city' || sug.type === 'district' || sug.type === 'ward' || sug.type === 'address'">
                                        <i class="fa-solid fa-location-dot text-sm text-[#0077bb]"></i>
                                    </template>
                                    <template x-if="sug.type === 'property'">
                                        <i class="fa-solid fa-building text-sm text-amber-500"></i>
                                    </template>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800" x-text="sug.label"></div>
                                    <div class="text-[10px] text-slate-400" x-text="sug.sublabel"></div>
                                </div>
                            </div>
                            <span 
                                class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full"
                                :class="{
                                    'bg-blue-50 text-blue-600': sug.type === 'city',
                                    'bg-indigo-50 text-indigo-600': sug.type === 'district',
                                    'bg-purple-50 text-purple-600': sug.type === 'ward',
                                    'bg-teal-50 text-teal-600': sug.type === 'address',
                                    'bg-amber-50 text-amber-600': sug.type === 'property'
                                }"
                                x-text="sug.type === 'city' ? 'Tỉnh thành' : (sug.type === 'district' ? 'Quận huyện' : (sug.type === 'ward' ? 'Phường xã' : (sug.type === 'address' ? 'Địa chỉ' : 'Bất động sản')))"
                            ></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</section>
