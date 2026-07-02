@extends('layouts.app')

@section('title', 'Tìm Kiếm Bất Động Sản Cho Thuê Nhanh Chóng | BDS Rental')

@section('content')
    <!-- Hero Banner Component (Tích hợp Thanh tìm kiếm) -->
    @include('components.hero')



    <!-- Section 2: Danh sách Bất động sản và Bộ lọc -->
    <section id="listings" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <!-- Section Header -->
        <div class="mb-10 text-left">
            <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Dành riêng cho bạn</span>
            <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin Đăng Cho Thuê Nổi Bật</h2>
        </div>

        @php
            // Dữ liệu $properties được truyền trực tiếp từ Route web.php
        @endphp

        <!-- Grid of Property Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
            @foreach($properties as $property)
                @include('components.property-card', ['property' => $property])
            @endforeach
        </div>

        <!-- Pagination Section -->
        <div class="flex justify-center mt-12">
            <nav class="inline-flex space-x-1 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm" aria-label="Pagination">
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary text-white font-bold shadow-md shadow-primary/20">1</a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">2</a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">3</a>
                <span class="inline-flex items-center justify-center w-10 h-10 text-slate-400">...</span>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-primary transition font-bold">12</a>
                <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </nav>
        </div>
    </section>

    <!-- Section 2.5: Tin Đăng Mới Nhất (Giai đoạn 3) -->
    <section class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div class="text-left">
                    <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Cập nhật liên tục</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tin Đăng Cho Thuê Mới Nhất</h2>
                </div>
                <a href="/listings" class="inline-flex items-center text-sm font-bold text-primary hover:text-primary-hover hover:underline transition">
                    Xem tất cả tin mới <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <!-- Grid of Latest Property Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @php
                    $displayLatest = isset($latestProperties) && count($latestProperties) > 0 ? $latestProperties : collect($properties)->sortByDesc('created_at')->take(4);
                @endphp
                @foreach($displayLatest as $property)
                    @include('components.property-card', ['property' => $property])
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 2.7: Dự án nổi bật (Featured Projects) -->
    <section class="py-16 bg-white border-t border-slate-100 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div class="text-left">
                    <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Dự án trọng điểm</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Dự Án Nổi Bật</h2>
                </div>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center text-sm font-bold text-primary hover:text-primary-hover hover:underline transition">
                    Xem tất cả dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>

            <!-- Grid of Projects -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @if(isset($featuredProjects) && $featuredProjects->count() > 0)
                    @foreach($featuredProjects as $project)
                        <div class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl hover:shadow-slate-100/50 transition-all duration-300 flex flex-col h-full">
                            <!-- Project Image -->
                            <div class="relative aspect-video bg-slate-100 overflow-hidden flex-shrink-0">
                                @if(is_array($project->images) && count($project->images) > 0)
                                    <img 
                                        src="{{ $project->images[0] }}" 
                                        alt="{{ $project->title }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i class="fa-regular fa-image text-4xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4 z-10">
                                    @if($project->status === 'selling')
                                        <span class="bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đang mở bán</span>
                                    @elseif($project->status === 'upcoming')
                                        <span class="bg-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Sắp mở bán</span>
                                    @else
                                        <span class="bg-blue-600 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đã bàn giao</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Project Content -->
                            <div class="p-6 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-xs font-black uppercase text-primary tracking-widest block mb-2">{{ $project->investor }}</span>
                                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-primary transition duration-150 mb-3 line-clamp-1">
                                        <a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a>
                                    </h3>
                                    <p class="text-slate-500 text-sm mb-5 line-clamp-3 leading-relaxed">
                                        {{ $project->description }}
                                    </p>
                                </div>

                                <!-- Highlights info -->
                                <div class="pt-5 border-t border-slate-50 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                        <span class="truncate">{{ $project->price_range ?? 'Liên hệ' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-ruler-combined text-primary"></i>
                                        <span class="truncate">{{ $project->scale ?? 'Đang cập nhật' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 col-span-2">
                                        <i class="fa-solid fa-location-dot text-primary"></i>
                                        <span class="truncate">{{ $project->location }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- View details CTA -->
                            <div class="px-6 pb-6 pt-2">
                                <a 
                                    href="{{ route('projects.show', $project->slug) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border border-slate-100 text-sm font-extrabold rounded-2xl text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200"
                                >
                                    Xem chi tiết dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback / Mock Projects if database is empty -->
                    @php
                        $mocks = [
                            [
                                'title' => 'Vinhomes Grand Park',
                                'investor' => 'Vingroup',
                                'description' => 'Đại đô thị thông minh đẳng cấp quốc tế tại trung tâm Quận 9, TP. Hồ Chí Minh với quy mô lên đến 271 ha.',
                                'price_range' => '35 - 55 triệu/m²',
                                'scale' => '44.000 căn hộ',
                                'location' => 'Quận 9, TP. Hồ Chí Minh',
                                'status' => 'selling',
                                'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg'
                            ],
                            [
                                'title' => 'Masteri Centre Point',
                                'investor' => 'Masterise Homes',
                                'description' => 'Khu căn hộ compound cao cấp bậc nhất nằm tại trung tâm đại đô thị Vinhomes Grand Park Quận 9.',
                                'price_range' => '50 - 70 triệu/m²',
                                'scale' => '5.000 căn hộ',
                                'location' => 'Quận 9, TP. Hồ Chí Minh',
                                'status' => 'upcoming',
                                'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg'
                            ],
                            [
                                'title' => 'Eco Green Saigon',
                                'investor' => 'Xuân Mai Corp',
                                'description' => 'Tổ hợp thương mại dịch vụ và căn hộ cao cấp tọa lạc ngay mặt tiền đại lộ Nguyễn Văn Linh, Quận 7.',
                                'price_range' => '45 - 60 triệu/m²',
                                'scale' => '4.000 căn hộ',
                                'location' => 'Quận 7, TP. Hồ Chí Minh',
                                'status' => 'handed_over',
                                'image' => 'https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg'
                            ]
                        ];
                    @endphp
                    @foreach($mocks as $mock)
                        <div class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl hover:shadow-slate-100/50 transition-all duration-300 flex flex-col h-full">
                            <!-- Project Image -->
                            <div class="relative aspect-video bg-slate-100 overflow-hidden flex-shrink-0">
                                <img 
                                    src="{{ $mock['image'] }}" 
                                    alt="{{ $mock['title'] }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                >
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4 z-10">
                                    @if($mock['status'] === 'selling')
                                        <span class="bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đang mở bán</span>
                                    @elseif($mock['status'] === 'upcoming')
                                        <span class="bg-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Sắp mở bán</span>
                                    @else
                                        <span class="bg-blue-600 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider">Đã bàn giao</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Project Content -->
                            <div class="p-6 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-xs font-black uppercase text-primary tracking-widest block mb-2">{{ $mock['investor'] }}</span>
                                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-primary transition duration-150 mb-3 line-clamp-1">
                                        <a href="/projects">{{ $mock['title'] }}</a>
                                    </h3>
                                    <p class="text-slate-500 text-sm mb-5 line-clamp-3 leading-relaxed">
                                        {{ $mock['description'] }}
                                    </p>
                                </div>

                                <!-- Highlights info -->
                                <div class="pt-5 border-t border-slate-50 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                        <span class="truncate">{{ $mock['price_range'] }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-ruler-combined text-primary"></i>
                                        <span class="truncate">{{ $mock['scale'] }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 col-span-2">
                                        <i class="fa-solid fa-location-dot text-primary"></i>
                                        <span class="truncate">{{ $mock['location'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- View details CTA -->
                            <div class="px-6 pb-6 pt-2">
                                <a 
                                    href="/projects"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border border-slate-100 text-sm font-extrabold rounded-2xl text-slate-700 bg-slate-50 hover:bg-primary hover:text-white hover:border-transparent transition-all duration-200"
                                >
                                    Xem chi tiết dự án <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Section 3: Tìm kiếm theo nhu cầu (Demands/Needs) -->
    <section class="py-16 bg-slate-50 border-t border-b border-slate-100 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Lựa chọn đa dạng</span>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Tìm Kiếm Theo Nhu Cầu</h2>
                <p class="text-slate-500 mt-2 max-w-xl mx-auto">Nhanh chóng lựa chọn phân khúc bất động sản phù hợp nhất với nhu cầu tài chính và phong cách sống của bạn.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Need 1: Chung cư giá tốt -->
                <a href="/listings?type=Căn+hộ+chung+cư&price_max=7000000" class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg hover:shadow-slate-100/80 transition-all duration-300 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-primary group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-building text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition mb-0.5">Chung cư giá tốt dưới 7Tr</h4>
                        <p class="text-xs text-slate-400 font-semibold">Căn hộ tiện nghi, giá cả phải chăng</p>
                    </div>
                </a>

                <!-- Need 2: Phòng trọ sinh viên -->
                <a href="/listings?type=Phòng+trọ&price_max=3000000" class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg hover:shadow-slate-100/80 transition-all duration-300 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-primary group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-graduation-cap text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition mb-0.5">Phòng trọ sinh viên dưới 3Tr</h4>
                        <p class="text-xs text-slate-400 font-semibold">Gần các trường đại học lớn</p>
                    </div>
                </a>

                <!-- Need 3: Nhà riêng nguyên căn -->
                <a href="/listings?type=Nhà+riêng" class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg hover:shadow-slate-100/80 transition-all duration-300 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-primary group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition mb-0.5">Nhà nguyên căn hộ gia đình</h4>
                        <p class="text-xs text-slate-400 font-semibold">Không gian riêng tư, rộng rãi</p>
                    </div>
                </a>

                <!-- Need 4: Căn hộ dịch vụ tiện nghi -->
                <a href="/listings?type=Căn+hộ+dịch+vụ" class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg hover:shadow-slate-100/80 transition-all duration-300 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-primary group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-bell-concierge text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition mb-0.5">Căn hộ dịch vụ đầy đủ tiện nghi</h4>
                        <p class="text-xs text-slate-400 font-semibold">Dịch vụ dọn dẹp, giặt ủi trọn gói</p>
                    </div>
                </a>

                <!-- Need 5: Văn phòng mặt bằng kinh doanh -->
                <a href="/listings?type=Văn+phòng" class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg hover:shadow-slate-100/80 transition-all duration-300 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-primary group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-briefcase text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition mb-0.5">Văn phòng / Mặt bằng kinh doanh</h4>
                        <p class="text-xs text-slate-400 font-semibold">Vị trí đắc địa, giao thương thuận lợi</p>
                    </div>
                </a>

                <!-- Need 6: Phòng trọ giá rẻ cực sốc -->
                <a href="/listings?type=Phòng+trọ&price_max=2000000" class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-xs hover:shadow-lg hover:shadow-slate-100/80 transition-all duration-300 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-primary group-hover:text-white transition duration-300">
                        <i class="fa-solid fa-tags text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition mb-0.5">Nhà trọ giá rẻ dưới 2Tr</h4>
                        <p class="text-xs text-slate-400 font-semibold">Tiết kiệm tối đa chi phí</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Section 3.5: Nhu cầu cộng đồng (Community Demands) -->
    <section 
        x-data="{ 
            scrollContainer: null,
            scrollLeft() {
                if (this.scrollContainer) {
                    this.scrollContainer.scrollBy({ left: -320, behavior: 'smooth' });
                }
            },
            scrollRight() {
                if (this.scrollContainer) {
                    this.scrollContainer.scrollBy({ left: 320, behavior: 'smooth' });
                }
            },
            createDemandModalOpen: false,
            // Form state
            form: {
                title: '',
                type: 'rent',
                location: '',
                budget: '',
                description: '',
                contact_name: '{{ Auth::check() ? Auth::user()->name : '' }}',
                contact_phone: '{{ Auth::check() ? Auth::user()->phone : '' }}'
            },
            isSubmitting: false,
            submitMessage: '',
            submitSuccess: false,
            submitForm() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;
                this.submitMessage = '';
                
                fetch('{{ route('demands.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                })
                .then(response => response.json())
                .then(data => {
                    this.isSubmitting = false;
                    if (data.success) {
                        this.submitSuccess = true;
                        this.submitMessage = data.message;
                        
                        // Push new demand card to the list dynamically
                        const container = document.getElementById('demands-list-container');
                        if (container) {
                            const newCard = document.createElement('div');
                            newCard.className = 'w-72 bg-white p-5 rounded-3xl border border-slate-100 hover:shadow-lg hover:shadow-slate-100/50 transition duration-300 flex flex-col justify-between flex-shrink-0 h-64 text-left';
                            
                            const initials = data.demand.contact_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                            const typeText = data.demand.type === 'rent' ? 'CẦN THUÊ' : 'CẦN MUA';
                            
                            newCard.innerHTML = `
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-black">
                                            ${initials}
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black bg-primary/10 text-primary uppercase">
                                            ${typeText}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 line-clamp-3 mb-2 leading-snug">
                                        ${data.demand.title}
                                    </h4>
                                    ${data.demand.budget ? `<span class="inline-block text-[11px] font-extrabold text-primary bg-primary-light px-2 py-0.5 rounded-md mb-3">${data.demand.budget}</span>` : ''}
                                </div>
                                <div class="pt-4 border-t border-slate-50 flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                    <span>${data.demand.time_ago}</span>
                                    <span class="truncate max-w-[150px]"><i class="fa-solid fa-location-dot mr-1"></i>${data.demand.location}</span>
                                </div>
                            `;
                            
                            // Insert card right after the '+' Create Demand card (which is index 0)
                            if (container.children.length > 1) {
                                container.insertBefore(newCard, container.children[1]);
                            } else {
                                container.appendChild(newCard);
                            }
                        }
                        
                        // Close modal after delay
                        setTimeout(() => {
                            this.createDemandModalOpen = false;
                            // Reset form
                            this.form.title = '';
                            this.form.location = '';
                            this.form.budget = '';
                            this.form.description = '';
                            this.submitSuccess = false;
                            this.submitMessage = '';
                        }, 1500);
                    } else {
                        this.submitSuccess = false;
                        this.submitMessage = 'Có lỗi xảy ra, vui lòng kiểm tra lại.';
                    }
                })
                .catch(error => {
                    this.isSubmitting = false;
                    this.submitSuccess = false;
                    this.submitMessage = 'Lỗi hệ thống, vui lòng thử lại sau.';
                    console.error('Error:', error);
                });
            }
        }"
        class="py-16 bg-white text-left relative overflow-hidden border-b border-slate-100"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Slider Arrows -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="text-xs font-bold text-primary tracking-widest uppercase mb-1.5 block">Hỗ trợ cộng đồng</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">Nhu Cầu Tìm Kiếm</h2>
                    <p class="text-slate-500 text-sm mt-1">Khám phá nhu cầu mua, bán, thuê mới nhất từ cộng đồng hoặc chia sẻ nhu cầu của riêng bạn.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button 
                        @click="scrollLeft()" 
                        class="w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition flex items-center justify-center cursor-pointer active:scale-95 shadow-sm"
                        title="Trượt sang trái"
                    >
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button 
                        @click="scrollRight()" 
                        class="w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition flex items-center justify-center cursor-pointer active:scale-95 shadow-sm"
                        title="Trượt sang phải"
                    >
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Horizontal Scrollable Container -->
            <div 
                x-init="scrollContainer = $el"
                id="demands-list-container"
                class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory scroll-smooth"
                style="-ms-overflow-style: none; scrollbar-width: none;"
            >
                <!-- Card 1: Tạo nhu cầu -->
                <div 
                    @click="createDemandModalOpen = true"
                    class="w-72 bg-white rounded-3xl border border-dashed border-slate-200 hover:border-primary/50 hover:shadow-md hover:shadow-slate-50/50 transition-all duration-300 flex flex-col items-center justify-center p-6 h-64 text-center cursor-pointer flex-shrink-0 group active:scale-98"
                >
                    <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-110 transition duration-300 mb-4">
                        <i class="fa-solid fa-plus text-lg"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 mb-1 group-hover:text-primary transition">Đăng nhu cầu</h4>
                    <p class="text-xs text-slate-400 font-semibold max-w-[180px]">Chia sẻ thông tin căn hộ, phòng trọ bạn đang cần tìm kiếm</p>
                </div>

                <!-- Seeded Demands list -->
                @if(isset($demands) && count($demands) > 0)
                    @foreach($demands as $demand)
                        @php
                            $words = explode(' ', $demand->contact_name);
                            $initials = '';
                            foreach ($words as $w) {
                                $initials .= mb_substr($w, 0, 1, 'UTF-8');
                            }
                            $initials = mb_strtoupper(mb_substr($initials, 0, 2, 'UTF-8'), 'UTF-8');
                        @endphp
                        <div class="w-72 bg-white p-5 rounded-3xl border border-slate-100 hover:shadow-lg hover:shadow-slate-100/50 transition duration-300 flex flex-col justify-between flex-shrink-0 h-64 text-left snap-start">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-black">
                                        {{ $initials }}
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black {{ $demand->type === 'rent' ? 'bg-sky-50 text-sky-600' : 'bg-emerald-50 text-emerald-600' }} uppercase">
                                        {{ $demand->type === 'rent' ? 'CẦN THUÊ' : 'CẦN MUA' }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 line-clamp-3 mb-2 leading-snug" title="{{ $demand->title }}">
                                    {{ $demand->title }}
                                </h4>
                                @if($demand->budget)
                                    <span class="inline-block text-[11px] font-extrabold text-primary bg-primary-light px-2 py-0.5 rounded-md mb-3">
                                        {{ $demand->budget }}
                                    </span>
                                @endif
                            </div>
                            <div class="pt-4 border-t border-slate-50 flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                <span>{{ $demand->created_at->diffForHumans() }}</span>
                                <span class="truncate max-w-[150px]"><i class="fa-solid fa-location-dot mr-1"></i>{{ $demand->location }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Create Demand Modal overlay -->
        <template x-teleport="body">
            <div 
                x-show="createDemandModalOpen" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 sm:p-6"
                x-transition
                x-cloak
            >
                <div 
                    @click.away="if(!isSubmitting) createDemandModalOpen = false" 
                    class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 text-left max-h-[90vh] overflow-y-auto"
                >
                    <!-- Close button -->
                    <button 
                        @click="createDemandModalOpen = false" 
                        class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer"
                        :disabled="isSubmitting"
                    >
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>

                    <h3 class="text-xl font-bold text-slate-900 mb-1.5 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-primary"></i>
                        <span>Đăng Nhu Cầu Tìm Kiếm</span>
                    </h3>
                    <p class="text-xs text-slate-400 font-medium mb-6">Điền thông tin chi tiết nhu cầu thuê/mua bất động sản để cộng đồng kết nối tốt nhất.</p>

                    <!-- Form submission -->
                    <form @submit.prevent="submitForm()" class="space-y-4">
                        
                        <!-- Title input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tôi đang cần tìm... (Tiêu đề ngắn)</label>
                            <input 
                                type="text" 
                                x-model="form.title" 
                                required 
                                placeholder="Ví dụ: Cần thuê căn hộ dịch vụ Quận 1 gần Đại học Hoa Sen" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                            >
                        </div>

                        <!-- Type & Budget inputs -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Loại nhu cầu</label>
                                <select 
                                    x-model="form.type" 
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary transition"
                                >
                                    <option value="rent">Cần Thuê</option>
                                    <option value="buy">Cần Mua</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Ngân sách dự kiến</label>
                                <input 
                                    type="text" 
                                    x-model="form.budget" 
                                    placeholder="Ví dụ: 5 - 8 triệu/tháng" 
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                                >
                            </div>
                        </div>

                        <!-- Location input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Khu vực mong muốn (Địa chỉ/Quận/Huyện)</label>
                            <input 
                                type="text" 
                                x-model="form.location" 
                                required
                                placeholder="Ví dụ: Quận 1, TP. Hồ Chí Minh" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                            >
                        </div>

                        <!-- Contact details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tên liên hệ</label>
                                <input 
                                    type="text" 
                                    x-model="form.contact_name" 
                                    required 
                                    placeholder="Họ và tên của bạn" 
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Số điện thoại liên hệ</label>
                                <input 
                                    type="text" 
                                    x-model="form.contact_phone" 
                                    required 
                                    placeholder="Ví dụ: 0912345678" 
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                                >
                            </div>
                        </div>

                        <!-- Description input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Mô tả thêm chi tiết nhu cầu</label>
                            <textarea 
                                x-model="form.description" 
                                rows="3" 
                                placeholder="Càng chi tiết (tiện ích xung quanh, diện tích tối thiểu, ngày dọn vào...) sẽ nhận được phản hồi chính xác nhất." 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Feedback messages -->
                        <div x-show="submitMessage" class="p-3.5 rounded-xl text-xs font-semibold" :class="submitSuccess ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                            <span x-text="submitMessage"></span>
                        </div>

                        <!-- Submit button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full py-3.5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-bold shadow-md shadow-primary/20 hover:shadow-primary/35 transition cursor-pointer flex items-center justify-center gap-2"
                                :disabled="isSubmitting"
                            >
                                <template x-if="isSubmitting">
                                    <i class="fa-solid fa-spinner animate-spin text-sm"></i>
                                </template>
                                <span>Gửi Yêu Cầu Đăng</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </section>

    <!-- Section 4: Video Nhà Đất (Real Estate Video Showcase) -->
    <section 
        x-data="{ 
            videoModalOpen: false, 
            activeVideoUrl: '', 
            openVideo(id) { 
                this.activeVideoUrl = 'https://www.youtube.com/embed/' + id + '?autoplay=1'; 
                this.videoModalOpen = true; 
            },
            closeVideo() {
                this.videoModalOpen = false;
                this.activeVideoUrl = '';
            }
        }"
        class="py-20 bg-white text-left"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Trải nghiệm thực tế</span>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Video Review Nhà Đất</h2>
                <p class="text-slate-500 mt-2 max-w-xl mx-auto">Cùng khám phá các dự án căn hộ, nhà phố và không gian sống thực tế qua các thước phim sống động.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Featured Video (8 cols) -->
                <div class="lg:col-span-8">
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group relative rounded-3xl overflow-hidden aspect-video shadow-lg border border-slate-100 cursor-pointer"
                    >
                        <!-- Video Thumbnail -->
                        <img 
                            src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" 
                            alt="Featured Real Estate Tour" 
                            class="w-full h-full object-cover group-hover:scale-103 transition duration-500"
                        >
                        <!-- Dark overlay -->
                        <div class="absolute inset-0 bg-slate-950/30 group-hover:bg-slate-950/40 transition duration-300"></div>
                        
                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/45 group-hover:scale-110 active:scale-95 transition duration-300">
                                <i class="fa-solid fa-play text-xl ml-1"></i>
                            </div>
                        </div>

                        <!-- Video info -->
                        <div class="absolute bottom-6 left-6 right-6 text-left">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-primary text-white mb-3">
                                <i class="fa-solid fa-eye mr-1"></i> XEM REVIEW CHI TIẾT
                            </span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 leading-tight">
                                Khám Phá Căn Hộ Penthouse Cao Cấp Giữa Lòng Thành Phố
                            </h3>
                            <p class="text-xs text-slate-200 line-clamp-1 font-medium">
                                Review thực tế căn hộ Penthouse 3 phòng ngủ sang trọng bậc nhất với tầm nhìn Landmark 81 cực đỉnh.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Side video playlist (4 cols) -->
                <div class="lg:col-span-4 flex flex-col justify-between gap-4">
                    <!-- Video item 1 -->
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group flex items-center space-x-4 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="relative w-28 h-20 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/careoe841i7otf8cv8yl.jpg" alt="Video 1" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <div class="w-7 h-7 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-md">
                                    <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <h4 class="text-xs font-extrabold text-slate-800 group-hover:text-primary transition line-clamp-2 leading-snug mb-1">
                                Tour Căn Hộ Studio Tiện Nghi Quận 1 Giá Chỉ 8 Triệu
                            </h4>
                            <span class="text-[10px] text-slate-400 font-semibold block">Thời lượng: 5:45</span>
                        </div>
                    </div>

                    <!-- Video item 2 -->
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group flex items-center space-x-4 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="relative w-28 h-20 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/wdowpvg4qnnnivn8t0yu.jpg" alt="Video 2" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <div class="w-7 h-7 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-md">
                                    <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <h4 class="text-xs font-extrabold text-slate-800 group-hover:text-primary transition line-clamp-2 leading-snug mb-1">
                                Cận Cảnh Biệt Thự Sân Vườn Đáng Sống Tại Khu Phú Mỹ Hưng
                            </h4>
                            <span class="text-[10px] text-slate-400 font-semibold block">Thời lượng: 12:30</span>
                        </div>
                    </div>

                    <!-- Video item 3 -->
                    <div 
                        @click="openVideo('dQw4w9WgXcQ')" 
                        class="group flex items-center space-x-4 p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="relative w-28 h-20 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101763/mvt1mwpuj5vo4qm538rb.jpg" alt="Video 3" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <div class="w-7 h-7 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-md">
                                    <i class="fa-solid fa-play text-[9px] ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-left flex-1 min-w-0">
                            <h4 class="text-xs font-extrabold text-slate-800 group-hover:text-primary transition line-clamp-2 leading-snug mb-1">
                                Đánh Giá Chung Cư Cao Cấp Đầy Đủ Nội Thất Ở Đà Nẵng
                            </h4>
                            <span class="text-[10px] text-slate-400 font-semibold block">Thời lượng: 8:15</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Modal Overlay -->
        <template x-teleport="body">
            <div 
                x-show="videoModalOpen" 
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 p-4 sm:p-6"
                x-transition
                x-cloak
            >
                <div 
                    @click.away="closeVideo()" 
                    class="relative w-full max-w-4xl bg-black rounded-3xl overflow-hidden shadow-2xl border border-slate-800"
                >
                    <!-- Close button -->
                    <button 
                        @click="closeVideo()" 
                        class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center transition border border-white/10"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>

                    <!-- Iframe Container -->
                    <div class="aspect-video w-full bg-black">
                        <template x-if="videoModalOpen">
                            <iframe 
                                :src="activeVideoUrl" 
                                class="w-full h-full border-0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                            ></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </section>

    <!-- Section 5: Call to Action (CTA) -->
    <section class="pb-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl bg-slate-900 overflow-hidden shadow-xl py-12 px-6 sm:px-12 md:py-16 md:px-16 text-left border border-slate-800">
            <!-- Background effects -->
            <div class="absolute inset-0 opacity-10">
                <img src="https://res.cloudinary.com/dj8t18pke/image/upload/v1782101764/ewjyvlwq88ixefrpstmu.jpg" alt="Landlord CTA" class="w-full h-full object-cover">
            </div>
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-primary/30 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-primary/10 blur-3xl"></div>

            <div class="relative z-10 max-w-2xl flex flex-col justify-center h-full">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl leading-tight">
                    Bạn có bất động sản <br class="hidden sm:inline">
                    <span class="text-primary">muốn cho thuê?</span>
                </h2>
                <p class="mt-4 text-base text-slate-300 leading-relaxed">
                    Đăng tin ngay hôm nay để tiếp cận hơn 100,000 khách thuê tiềm năng truy cập mỗi tháng. Hoàn toàn miễn phí, nhanh chóng và dễ dàng.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="#" class="inline-flex items-center justify-center px-6 py-3.5 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/20 hover:shadow-primary/35 transition">
                        <i class="fa-solid fa-circle-plus mr-2"></i> Đăng tin cho thuê ngay
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-6 py-3.5 border border-slate-700 hover:border-slate-500 text-sm font-semibold rounded-xl text-slate-100 hover:text-white bg-slate-900/50 hover:bg-slate-900 transition">
                        Liên hệ tư vấn môi giới
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
