@php
    // Dữ liệu mock phục vụ hiển thị giao diện ngay lập tức
    $mockLeads = [
        [
            'id' => '1',
            'name' => 'Nguyễn Minh Quân',
            'phone' => '0912345678',
            'email' => 'minhquan.nguyen@gmail.com',
            'demand_type' => 'rent',
            'budget_min' => 10,
            'budget_max' => 15,
            'preferred_location' => 'Quận 10, TP.HCM',
            'preferred_category' => 'Căn hộ chung cư',
            'status' => 'new',
            'source' => 'chatbot',
            'created_at' => '10 phút trước',
            'match_score' => 95,
            'notes' => 'Khách thích căn hộ tầng cao, hướng Đông Nam thoáng mát, ban công rộng.',
            'chat_history' => [
                ['role' => 'user', 'content' => 'Tôi muốn tìm thuê căn hộ ở Quận 10 tầm giá dưới 15 triệu'],
                ['role' => 'assistant', 'content' => 'Chào bạn! BDS Rental có nhiều căn hộ Quận 10 tầm giá 10tr - 15tr phù hợp. Bạn muốn thuê mấy phòng ngủ ạ?'],
                ['role' => 'user', 'content' => 'Tầm 2 phòng ngủ nhé, nhà có 2 vợ chồng và 1 con nhỏ.'],
                ['role' => 'assistant', 'content' => 'Dạ, tôi đã lọc được 3 căn hộ 2 phòng ngủ rất đẹp ở Quận 10. Để lưu bộ lọc tìm kiếm này và nhận thông báo khi có căn mới, bạn cho tôi xin Tên và Số điện thoại nhé.'],
                ['role' => 'user', 'content' => 'Tôi tên Quân, SĐT của tôi là 0912345678.'],
                ['role' => 'assistant', 'content' => 'Cảm ơn anh Quân! Tôi đã ghi nhận nhu cầu của anh. Các căn hộ đề xuất đã được gửi kèm phía dưới. Nhân viên tư vấn của chúng tôi cũng sẽ liên hệ lại anh ngay nhé!']
            ],
            'matched_properties' => [
                ['title' => 'Căn hộ Hà Đô Centrosa 2PN Full nội thất', 'price' => '14.5 Triệu/tháng', 'area' => '78m²', 'location' => 'Đường 3/2, Quận 10'],
                ['title' => 'Chung cư Rivera Park 2PN tiện ích cao cấp', 'price' => '13.0 Triệu/tháng', 'area' => '74m²', 'location' => 'Thành Thái, Quận 10']
            ]
        ],
        [
            'id' => '2',
            'name' => 'Lê Thị Thu Thảo',
            'phone' => '0987654321',
            'email' => 'thuthao.le@yahoo.com',
            'demand_type' => 'rent',
            'budget_min' => 5,
            'budget_max' => 8,
            'preferred_location' => 'Quận Gò Vấp, TP.HCM',
            'preferred_category' => 'Phòng trọ / Mini',
            'status' => 'contacting',
            'source' => 'chatbot',
            'created_at' => '3 giờ trước',
            'match_score' => 88,
            'notes' => 'Khách là sinh viên, cần tìm phòng trọ dịch vụ hoặc chung cư mini an ninh tốt.',
            'chat_history' => [
                ['role' => 'user', 'content' => 'Tìm phòng trọ ở Gò Vấp giá tầm 6 triệu đổ lại.'],
                ['role' => 'assistant', 'content' => 'Chào bạn! Mình có danh sách phòng trọ dịch vụ sạch sẽ tại Gò Vấp từ 5tr - 8tr. Bạn cần ở từ ngày nào ạ?'],
                ['role' => 'user', 'content' => 'Khoảng giữa tháng này mình dọn qua.'],
                ['role' => 'assistant', 'content' => 'Vâng ạ, bạn để lại Tên và Số điện thoại để mình chuyển cho chủ nhà hỗ trợ bạn đi xem phòng trực tiếp nhé.'],
                ['role' => 'user', 'content' => 'Thảo, sđt 0987654321 nhé.'],
            ],
            'matched_properties' => [
                ['title' => 'Phòng trọ Studio Full NT gần ĐH Công Nghiệp', 'price' => '5.5 Triệu/tháng', 'area' => '30m²', 'location' => 'Phan Văn Trị, Gò Vấp'],
                ['title' => 'Căn hộ Mini mới xây có gác lửng', 'price' => '6.0 Triệu/tháng', 'area' => '35m²', 'location' => 'Quang Trung, Gò Vấp']
            ]
        ],
        [
            'id' => '3',
            'name' => 'Phạm Minh Trí',
            'phone' => '0933445566',
            'email' => 'minhtri.pham@outlook.com',
            'demand_type' => 'sale',
            'budget_min' => 3000,
            'budget_max' => 4500,
            'preferred_location' => 'Quận Bình Thạnh, TP.HCM',
            'preferred_category' => 'Nhà riêng / Phố',
            'status' => 'qualified',
            'source' => 'web',
            'created_at' => '1 ngày trước',
            'match_score' => 90,
            'notes' => 'Khách tìm mua nhà hẻm xe hơi Bình Thạnh, ngân sách tối đa 4.5 tỷ, sổ hồng riêng.',
            'chat_history' => [
                ['role' => 'user', 'content' => '[Khách gửi thông tin từ form đăng ký mua nhà đất] Hẻm xe hơi Bình Thạnh, ngân sách 3 - 4.5 tỷ. Sổ hồng sẵn sàng sang tên.']
            ],
            'matched_properties' => [
                ['title' => 'Nhà trệt 2 lầu hẻm xe hơi Lê Quang Định', 'price' => '4.2 Tỷ', 'area' => '45m²', 'location' => 'Lê Quang Định, Bình Thạnh']
            ]
        ],
        [
            'id' => '4',
            'name' => 'Hoàng Ngọc Ánh',
            'phone' => '0909887766',
            'email' => 'ngocanh.h@company.com',
            'demand_type' => 'rent',
            'budget_min' => 15,
            'budget_max' => 20,
            'preferred_location' => 'Quận 2, TP.HCM',
            'preferred_category' => 'Căn hộ chung cư',
            'status' => 'closed',
            'source' => 'chatbot',
            'created_at' => '3 ngày trước',
            'match_score' => 92,
            'notes' => 'Đã chốt thuê căn Masteri Thảo Điền 2PN. Khách rất hài lòng với hỗ trợ từ AI.',
            'chat_history' => [],
            'matched_properties' => []
        ]
    ];

    $leadsList = isset($leads) && count($leads) > 0 ? $leads : $mockLeads;
@endphp

<script>
    window.crmLeadsList = {!! json_encode($leadsList) !!};
</script>

<div 
    x-data="{
        leads: window.crmLeadsList || [],
        selectedLead: null,
        drawerOpen: false,
        activeDetailTab: 'info',
        filterSource: 'all',
        searchTerm: '',
        openLead(lead) {
            this.selectedLead = lead;
            this.drawerOpen = true;
            this.activeDetailTab = 'info';
        },
        getStatusLabel(status) {
            const labels = {
                'new': 'Mới nhận',
                'contacting': 'Đang liên hệ',
                'qualified': 'Tiềm năng',
                'unqualified': 'Không khớp',
                'closed': 'Đã chốt'
            };
            return labels[status] || status;
        },
        getStatusClass(status) {
            const classes = {
                'new': 'bg-blue-50 text-blue-600 border-blue-100',
                'contacting': 'bg-amber-50 text-amber-600 border-amber-100',
                'qualified': 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'unqualified': 'bg-slate-50 text-slate-500 border-slate-100',
                'closed': 'bg-rose-50 text-rose-600 border-rose-100'
            };
            return 'px-2.5 py-1 text-[10px] font-bold rounded-lg border ' + (classes[status] || 'bg-slate-100 text-slate-600');
        },
        getSourceLabel(source) {
            const labels = {
                'all': 'Tất cả',
                'chatbot': 'AI Chatbot',
                'web': 'Form Web',
                'unknown': 'Chưa xác định'
            };
            return labels[source] || 'Tất cả';
        },
        getInitials(name) {
            if (!name) return 'L';
            const parts = name.trim().split(' ');
            return parts.length === 1 
                ? name[0].toUpperCase() 
                : (parts[parts.length - 2][0] + parts[parts.length - 1][0]).toUpperCase();
        },
        get filteredLeads() {
            return this.leads.filter(lead => {
                const sourceMatch = this.filterSource === 'all' || lead.source === this.filterSource;
                const textMatch = this.searchTerm.trim() === '' || 
                    (lead.name && lead.name.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                    (lead.phone && lead.phone.includes(this.searchTerm)) ||
                    (lead.email && lead.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                    (lead.demand && lead.demand.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                    (lead.company && lead.company.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                    (lead.position && lead.position.toLowerCase().includes(this.searchTerm.toLowerCase()));
                return sourceMatch && textMatch;
            });
        }
    }"
    class="space-y-6"
>
    <!-- Header -->
    <div class="pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Quản lý Khách hàng (Leads)</h2>
            <p class="text-xs text-slate-400 mt-1 font-semibold">Theo dõi nhu cầu, tương tác của khách hàng từ chatbot AI và đồng bộ CRM.</p>
        </div>
        <div class="flex items-center gap-3">
            <button 
                type="button" 
                @click="triggerToast('Bắt đầu đồng bộ danh sách Leads từ NKS...')"
                class="px-4 py-2 border border-slate-200 hover:border-primary text-slate-600 hover:text-primary rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer bg-white"
            >
                <i class="fa-solid fa-arrows-rotate"></i> Đồng bộ Leads
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tổng số Lead</p>
                <h3 class="text-base font-extrabold text-slate-800 mt-0.5" x-text="leads.length"></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Từ Web Form</p>
                <h3 class="text-base font-extrabold text-slate-800 mt-0.5" x-text="leads.filter(l => l.source === 'web').length"></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Từ Chatbot AI</p>
                <h3 class="text-base font-extrabold text-slate-800 mt-0.5" x-text="leads.filter(l => l.source === 'chatbot').length"></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Có Số Điện Thoại</p>
                <h3 class="text-base font-extrabold text-slate-800 mt-0.5" x-text="leads.filter(l => l.phone && l.phone !== '').length"></h3>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search bar -->
        <div class="relative max-w-xs w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
                type="text" 
                x-model="searchTerm"
                placeholder="Tìm khách hàng, số điện thoại..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-primary rounded-xl text-xs font-semibold outline-none transition"
            >
        </div>

        <!-- Filter Selects -->
        <div class="flex items-center gap-3 self-end md:self-auto flex-wrap">


            <div class="flex items-center gap-2 relative" x-data="{ open: false }" @click.outside="open = false">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Nguồn:</span>
                <button 
                    type="button"
                    @click="open = !open"
                    class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-600 focus:border-primary focus:outline-none transition cursor-pointer flex items-center gap-2 min-w-[125px] justify-between"
                >
                    <span x-text="getSourceLabel(filterSource)"></span>
                    <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Custom Dropdown Menu -->
                <div 
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute z-30 right-0 top-full mt-1.5 w-44 bg-white border border-slate-100 rounded-2xl shadow-xl py-1 overflow-hidden"
                    x-cloak
                >
                    <template x-for="opt in ['all', 'chatbot', 'web', 'unknown']" :key="opt">
                        <button 
                            type="button"
                            @click="filterSource = opt; open = false"
                            :class="filterSource === opt ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50'"
                            class="w-full text-left px-4 py-2.5 text-xs font-semibold transition cursor-pointer"
                            x-text="getSourceLabel(opt)"
                        ></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads Table / Grid -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <template x-if="filteredLeads.length === 0">
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-450 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-address-book text-xl"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="font-bold text-slate-700 text-sm">Không tìm thấy khách hàng nào</h4>
                    <p class="text-xs text-slate-400 font-semibold max-w-xs mx-auto">Thử đổi từ khóa hoặc điều chỉnh bộ lọc tìm kiếm.</p>
                </div>
            </div>
        </template>

        <template x-if="filteredLeads.length">
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-55/40 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-4">Khách hàng</th>
                            <th class="px-6 py-4">Nhu cầu</th>
                            <th class="px-6 py-4">Thông tin công việc</th>
                            <th class="px-6 py-4 text-center">Nguồn</th>
                            <th class="px-6 py-4 text-center">Ngày nhận</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="lead in filteredLeads" :key="lead.id">
                            <tr class="hover:bg-slate-50/50 transition duration-150 border-b border-slate-50">
                                <!-- Khách hàng -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div 
                                            class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary to-primary-hover text-white font-black text-xs flex items-center justify-center shadow-sm"
                                            x-text="getInitials(lead.name)"
                                        ></div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-xs" x-text="lead.name"></h4>
                                            <p class="text-[10px] text-slate-400 mt-0.5 font-bold" x-text="lead.phone || 'Chưa cung cấp SĐT'"></p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Nhu cầu -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="max-w-[200px] truncate text-xs font-semibold text-slate-700" :title="lead.demand" x-text="lead.demand || '-'"></div>
                                </td>

                                <!-- Thông tin công việc -->
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                    <div x-show="lead.company || lead.position">
                                        <span class="font-bold text-slate-700" x-text="lead.position || 'Chức vụ'"></span>
                                        <span class="text-slate-400" x-show="lead.position && lead.company"> tại </span>
                                        <span class="font-semibold text-slate-500" x-text="lead.company || 'Công ty'"></span>
                                    </div>
                                    <div x-show="!lead.company && !lead.position" class="text-slate-400 font-semibold">-</div>
                                </td>

                                <!-- Nguồn -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <template x-if="lead.source === 'chatbot'">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                            <i class="fa-solid fa-robot text-[9px]"></i> AI Chatbot
                                        </span>
                                    </template>
                                    <template x-if="lead.source === 'web'">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                                            <i class="fa-solid fa-globe text-[9px]"></i> Web Form
                                        </span>
                                    </template>
                                    <template x-if="lead.source !== 'chatbot' && lead.source !== 'web'">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200">
                                            <i class="fa-solid fa-circle-question text-[9px]"></i> Chưa xác định
                                        </span>
                                    </template>
                                </td>

                                <!-- Ngày nhận -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-semibold text-slate-500" x-text="lead.created_at"></td>

                                <!-- Thao tác -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                    <button 
                                        type="button" 
                                        @click="openLead(lead)"
                                        class="px-3 py-1.5 bg-slate-50 hover:bg-primary-light border border-slate-200 hover:border-primary/20 text-slate-600 hover:text-primary rounded-lg font-bold transition cursor-pointer focus:outline-none"
                                    >
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>
    </div>

    <!-- SLIDE-OVER DRAWER (Lead Details) -->
    <div 
        x-show="drawerOpen" 
        class="fixed inset-0 z-[100] overflow-hidden" 
        aria-labelledby="slide-over-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background Backdrop overlay -->
            <div 
                x-show="drawerOpen"
                x-transition:enter="ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" 
                @click="drawerOpen = false"
            ></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <!-- Slide-over panel (720px width) -->
                <div 
                    x-show="drawerOpen"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full opacity-90"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-90"
                    class="pointer-events-auto w-screen max-w-[550px]"
                >
                    <div class="flex h-full flex-col bg-slate-50 shadow-2xl overflow-hidden border-l border-slate-200">
                        
                        <!-- HEADER (Sticky, Premium Design) -->
                        <div class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm flex-shrink-0">
                            <div class="px-6 py-5 flex items-start justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar with initials & online pulse -->
                                    <div class="relative">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-700 text-white flex items-center justify-center font-extrabold text-lg shadow-md tracking-wider" x-text="selectedLead ? getInitials(selectedLead.name) : 'LD'"></div>
                                        <span class="absolute -bottom-1 -right-1 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white"></span>
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2.5">
                                            <h3 class="font-extrabold text-slate-800 text-lg leading-snug" x-text="selectedLead ? selectedLead.name : 'Khách Hàng'"></h3>
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[10px] font-bold" x-text="selectedLead ? 'ID #' + selectedLead.id : ''"></span>
                                        </div>
                                        
                                        <!-- Badges Row -->
                                        <div class="flex flex-wrap items-center gap-2 text-xs">
                                            <span class="flex items-center gap-1 text-[11px] font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full border border-orange-100">
                                                <i class="fa-solid fa-fire text-[10px]"></i> AI Match: <span x-text="selectedLead ? selectedLead.match_score + '%' : '97%'"></span>
                                            </span>
                                            <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100" x-text="selectedLead ? 'Nguồn: ' + getSourceLabel(selectedLead.source) : ''"></span>
                                            <span class="text-[11px] font-medium text-slate-400" x-text="selectedLead ? 'Ngày tạo: ' + selectedLead.created_at : ''"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Header Quick Actions & Close -->
                                <div class="flex items-center gap-1.5">
                                    <div class="flex items-center bg-slate-100 p-1 rounded-xl">
                                        <button @click="triggerToast('Đang thực hiện cuộc gọi...')" class="group relative p-2 text-slate-600 hover:text-blue-600 hover:bg-white rounded-lg transition" title="Gọi điện">
                                            <i class="fa-solid fa-phone text-sm"></i>
                                        </button>
                                        <button @click="activeDetailTab = 'ai'" class="group relative p-2 text-slate-600 hover:text-emerald-600 hover:bg-white rounded-lg transition" title="Nhắn tin AI">
                                            <i class="fa-solid fa-message text-sm"></i>
                                        </button>
                                        <button @click="triggerToast('Đang mở form soạn Email...')" class="group relative p-2 text-slate-600 hover:text-purple-600 hover:bg-white rounded-lg transition" title="Gửi Email">
                                            <i class="fa-solid fa-envelope text-sm"></i>
                                        </button>
                                        <button @click="activeDetailTab = 'appointments'" class="group relative p-2 text-slate-600 hover:text-amber-600 hover:bg-white rounded-lg transition" title="Đặt lịch hẹn">
                                            <i class="fa-solid fa-calendar-days text-sm"></i>
                                        </button>
                                        <button @click="activeDetailTab = 'ai'" class="group relative p-2 text-slate-600 hover:text-blue-600 hover:bg-white rounded-lg transition" title="AI Phân tích">
                                            <i class="fa-solid fa-robot text-sm"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="w-px h-6 bg-slate-200 mx-1"></div>

                                    <button 
                                        type="button" 
                                        @click="drawerOpen = false"
                                        class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition focus:outline-none border border-slate-200"
                                    >
                                        <i class="fa-solid fa-xmark text-base"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- PIPELINE (Progress Stepper) -->
                            <div class="px-6 pb-4 border-t border-slate-100 pt-3 bg-white">
                                <div class="relative flex items-center justify-between w-full">
                                    <!-- Progress Line Background -->
                                    <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-slate-150 rounded-full z-0"></div>
                                    
                                    <template x-for="(st, idx) in ['new', 'contacting', 'qualified', 'unqualified', 'closed']" :key="st">
                                        <div class="relative z-10 flex flex-col items-center group cursor-pointer" @click="selectedLead.status = st; triggerToast('Đã chuyển sang trạng thái: ' + getStatusLabel(st))">
                                            <div 
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all border-2"
                                                :class="selectedLead.status === st 
                                                    ? 'bg-blue-600 text-white border-blue-600 scale-110 shadow-md shadow-blue-200' 
                                                    : 'bg-white text-slate-400 border-slate-300 group-hover:border-slate-500'"
                                            >
                                                <i x-show="selectedLead.status === st" class="fa-solid fa-check text-[8px]"></i>
                                                <span x-show="selectedLead.status !== st" x-text="idx + 1"></span>
                                            </div>
                                            <span 
                                                class="text-[9px] font-bold mt-1.5 tracking-tight transition-all"
                                                :class="selectedLead.status === st ? 'text-blue-600 font-extrabold' : 'text-slate-400 group-hover:text-slate-600'"
                                                x-text="getStatusLabel(st)"
                                            ></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- STICKY SUBTABS NAVIGATION -->
                            <div class="border-t border-slate-200 px-4 flex bg-slate-50 flex-shrink-0 overflow-x-auto scrollbar-none gap-1">
                                <button 
                                    @click="activeDetailTab = 'info'"
                                    :class="activeDetailTab === 'info' ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-3 border-b-2 text-xs transition-all focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold whitespace-nowrap flex-shrink-0"
                                >
                                    <i class="fa-solid fa-circle-info text-[11px]"></i> Thông tin
                                </button>
                                <button 
                                    @click="activeDetailTab = 'timeline'"
                                    :class="activeDetailTab === 'timeline' ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-3 border-b-2 text-xs transition-all focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold whitespace-nowrap flex-shrink-0"
                                >
                                    <i class="fa-solid fa-clock-rotate-left text-[11px]"></i> Timeline
                                </button>
                                <button 
                                    @click="activeDetailTab = 'ai'"
                                    :class="activeDetailTab === 'ai' ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-3 border-b-2 text-xs transition-all focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold whitespace-nowrap flex-shrink-0"
                                >
                                    <i class="fa-solid fa-robot text-[11px]"></i> Trợ lý AI
                                </button>
                                <button 
                                    @click="activeDetailTab = 'appointments'"
                                    :class="activeDetailTab === 'appointments' ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-3 border-b-2 text-xs transition-all focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold whitespace-nowrap flex-shrink-0"
                                >
                                    <i class="fa-solid fa-calendar-check text-[11px]"></i> Lịch hẹn
                                </button>
                                <button 
                                    @click="activeDetailTab = 'documents'"
                                    :class="activeDetailTab === 'documents' ? 'border-blue-600 text-blue-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-3 border-b-2 text-xs transition-all focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold whitespace-nowrap flex-shrink-0"
                                >
                                    <i class="fa-solid fa-folder-open text-[11px]"></i> Tài liệu
                                </button>
                            </div>
                        </div>

                        <!-- DRAWER CONTENT BODY -->
                        <div class="flex-grow p-6 space-y-6 overflow-y-auto pb-24">
                            <template x-if="selectedLead">
                                <div>
                                    <!-- TAB 1: THÔNG TIN -->
                                    <div x-show="activeDetailTab === 'info'" class="space-y-6">
                                        
                                        <!-- AI Summary Card -->
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 p-5 rounded-[18px] shadow-sm relative overflow-hidden">
                                            <div class="absolute top-0 right-0 transform translate-x-4 -translate-y-4 text-blue-100 text-8xl font-bold select-none opacity-20 pointer-events-none">AI</div>
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-md">
                                                    <i class="fa-solid fa-robot text-sm"></i>
                                                </div>
                                                <h4 class="font-extrabold text-slate-850 text-sm tracking-tight">AI Summary & Khuyến nghị</h4>
                                            </div>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-xs">
                                                    <span class="text-slate-400 block mb-0.5">Khả năng chốt</span>
                                                    <span class="text-slate-800 font-black text-sm flex items-center gap-1 text-emerald-600">
                                                        86% <i class="fa-solid fa-circle-arrow-up text-[10px]"></i>
                                                    </span>
                                                </div>
                                                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-xs">
                                                    <span class="text-slate-400 block mb-0.5">Mức độ quan tâm</span>
                                                    <span class="text-slate-800 font-black text-sm text-blue-600" x-text="selectedLead.demand ? 'Cao' : 'Trung bình'"></span>
                                                </div>
                                                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-xs col-span-2 md:col-span-1">
                                                    <span class="text-slate-400 block mb-0.5">Ngân sách dự kiến</span>
                                                    <span class="text-slate-800 font-black text-sm" x-text="selectedLead.comsize || '3 Tỷ'"></span>
                                                </div>
                                            </div>
                                            <div class="mt-4 bg-white/70 border border-blue-150 p-3 rounded-xl text-xs text-slate-700 leading-relaxed font-semibold">
                                                <span class="text-blue-700 font-bold block mb-1 flex items-center gap-1">
                                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Đề xuất hành động:
                                                </span>
                                                Khách hàng đang rất quan tâm đến căn hộ dịch vụ khu vực Thanh Xuân. Nên gọi ngay hôm nay để tư vấn căn hộ Mini đầy đủ tiện ích và chốt lịch xem nhà vào ngày mai.
                                            </div>
                                        </div>

                                        <!-- SECTION 1: Thông tin cá nhân -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-user text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Thông tin cá nhân</h4>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Họ và tên:</span>
                                                    <div class="flex items-center gap-1.5 font-bold text-slate-700">
                                                        <span x-text="selectedLead.name"></span>
                                                        <button @click="navigator.clipboard.writeText(selectedLead.name); triggerToast('Đã copy tên!')" class="p-1 text-slate-400 hover:text-blue-600"><i class="fa-solid fa-copy"></i></button>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Số điện thoại:</span>
                                                    <div class="flex items-center gap-1.5 font-bold text-slate-700">
                                                        <span x-text="selectedLead.phone || 'Chưa cung cấp'"></span>
                                                        <template x-if="selectedLead.phone">
                                                            <div class="flex items-center gap-0.5">
                                                                <button @click="triggerToast('Đang gọi điện...')" class="p-1 text-blue-600 hover:scale-110 transition"><i class="fa-solid fa-phone"></i></button>
                                                                <button @click="navigator.clipboard.writeText(selectedLead.phone); triggerToast('Đã copy sđt!')" class="p-1 text-slate-400 hover:text-blue-600"><i class="fa-solid fa-copy"></i></button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Email:</span>
                                                    <div class="flex items-center gap-1.5 font-bold text-slate-700">
                                                        <span class="truncate max-w-[150px]" x-text="selectedLead.email || 'Chưa cung cấp'"></span>
                                                        <template x-if="selectedLead.email">
                                                            <button @click="navigator.clipboard.writeText(selectedLead.email); triggerToast('Đã copy email!')" class="p-1 text-slate-400 hover:text-blue-600"><i class="fa-solid fa-copy"></i></button>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Zalo / Facebook:</span>
                                                    <div class="flex items-center gap-1.5 font-bold text-slate-700">
                                                        <span x-text="selectedLead.zalo || 'Chưa cung cấp'"></span>
                                                        <template x-if="selectedLead.zalo">
                                                            <div class="flex items-center gap-0.5">
                                                                <a :href="'https://zalo.me/' + selectedLead.zalo" target="_blank" class="p-1 text-sky-500 hover:scale-110 transition"><i class="fa-solid fa-message"></i></a>
                                                                <button @click="navigator.clipboard.writeText(selectedLead.zalo); triggerToast('Đã copy Zalo!')" class="p-1 text-slate-400 hover:text-blue-600"><i class="fa-solid fa-copy"></i></button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Địa chỉ:</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedLead.address || 'Chưa cung cấp'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">CCCD / Số CMND:</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedLead.id_number || 'Chưa xác thực'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECTION 2: Thông tin nhu cầu -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-house-chimney text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Thông tin nhu cầu</h4>
                                            </div>
                                            <div class="space-y-4">
                                                <div class="flex flex-wrap gap-2 pt-1">
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-150 flex flex-col gap-0.5 min-w-[120px]">
                                                        <span class="text-[10px] text-slate-400 font-semibold">Loại BĐS</span>
                                                        <span class="text-xs font-bold text-slate-700">Căn hộ Studio</span>
                                                    </div>
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-150 flex flex-col gap-0.5 min-w-[120px]">
                                                        <span class="text-[10px] text-slate-400 font-semibold">Khu vực quan tâm</span>
                                                        <span class="text-xs font-bold text-slate-700">Thanh Xuân, HN</span>
                                                    </div>
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-150 flex flex-col gap-0.5 min-w-[120px]">
                                                        <span class="text-[10px] text-slate-400 font-semibold">Ngân sách</span>
                                                        <span class="text-xs font-bold text-slate-700" x-text="selectedLead.comsize || '3 Tỷ'"></span>
                                                    </div>
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-150 flex flex-col gap-0.5 min-w-[120px]">
                                                        <span class="text-[10px] text-slate-400 font-semibold">Diện tích</span>
                                                        <span class="text-xs font-bold text-slate-700">45 m² - 60 m²</span>
                                                    </div>
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-150 flex flex-col gap-0.5 min-w-[120px]">
                                                        <span class="text-[10px] text-slate-400 font-semibold">Phòng ngủ</span>
                                                        <span class="text-xs font-bold text-slate-700">1 PN, 1 WC</span>
                                                    </div>
                                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-150 flex flex-col gap-0.5 min-w-[120px]">
                                                        <span class="text-[10px] text-slate-400 font-semibold">Nội thất</span>
                                                        <span class="text-xs font-bold text-slate-700">Đầy đủ (Full)</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-1 bg-slate-50/50 p-3.5 rounded-xl border border-slate-150">
                                                    <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wide mb-1">Mô tả nhu cầu chi tiết từ khách hàng:</span>
                                                    <p class="font-semibold text-slate-700 text-xs leading-relaxed whitespace-pre-line" x-text="selectedLead.demand || '-'"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECTION 3: Thông tin công việc -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-briefcase text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Thông tin công việc & Hệ thống</h4>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Công ty:</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedLead.company || 'Chưa cung cấp'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Chức vụ:</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedLead.position || 'Chưa cung cấp'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Quy mô công ty:</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedLead.comsize || 'Chưa cung cấp'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Người phụ trách:</span>
                                                    <span class="font-bold text-slate-700">Chính chủ (Tôi)</span>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                                    <span class="text-slate-400 font-semibold">Ngày nhận:</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedLead.created_at"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- AI Matching Properties Section -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-fire text-orange-500 text-sm animate-pulse"></i>
                                                    <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">AI Matching (Giỏ hàng đề xuất)</h4>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg" x-text="selectedLead.matched_properties ? selectedLead.matched_properties.length + ' BĐS phù hợp' : '0 BĐS'"></span>
                                            </div>

                                            <template x-if="!selectedLead.matched_properties || selectedLead.matched_properties.length === 0">
                                                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                                                    Không tìm thấy bất động sản nào trùng khớp trong kho hàng của bạn.
                                                </div>
                                            </template>

                                            <template x-if="selectedLead.matched_properties && selectedLead.matched_properties.length">
                                                <div class="grid grid-cols-1 gap-3.5">
                                                    <template x-for="(prop, pIdx) in selectedLead.matched_properties" :key="pIdx">
                                                        <div class="p-3.5 bg-slate-50 border border-slate-200 hover:border-blue-400 rounded-xl shadow-xs transition-all flex gap-3.5 items-center">
                                                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex-shrink-0 relative overflow-hidden border border-slate-200">
                                                                <img src="/images/apartment_1.png" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=200&q=80'">
                                                            </div>
                                                            <div class="flex-grow min-w-0 flex flex-col gap-1">
                                                                <h5 class="font-bold text-slate-800 text-xs truncate" x-text="prop.title"></h5>
                                                                <div class="flex items-center gap-3 text-[10px] text-slate-500 font-semibold">
                                                                    <span class="truncate"><i class="fa-solid fa-location-dot text-slate-400 mr-0.5"></i> <span x-text="prop.location"></span></span>
                                                                    <span class="flex-shrink-0"><i class="fa-solid fa-maximize text-slate-400 mr-0.5"></i> <span x-text="prop.area"></span></span>
                                                                </div>
                                                                <div class="flex items-center justify-between mt-1">
                                                                    <span class="text-sm font-black text-blue-600" x-text="prop.price"></span>
                                                                    <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 border border-emerald-100 rounded-lg font-bold">Độ phù hợp: 95%</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex flex-col gap-1 flex-shrink-0">
                                                                <button @click="triggerToast('Đang mở chi tiết BĐS...')" class="px-2.5 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg text-[10px] border border-slate-200 font-bold transition">Xem</button>
                                                                <button @click="triggerToast('Đã gửi thông tin cho khách!')" class="px-2.5 py-1.5 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-[10px] font-bold transition">Gửi</button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- TAB 2: TIMELINE & MESSENGER NOTES -->
                                    <div x-show="activeDetailTab === 'timeline'" class="space-y-6">
                                        <!-- Vertical Timeline -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-clock-rotate-left text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Hành trình chăm sóc</h4>
                                            </div>
                                            
                                            <!-- Vertical Stepper Timeline -->
                                            <div class="relative pl-6 border-l-2 border-blue-100 space-y-6 ml-3 py-2 text-xs">
                                                <!-- Step 1 -->
                                                <div class="relative">
                                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow-md"></span>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-bold text-slate-850">Khách hàng tạo mới trên hệ thống</span>
                                                        <span class="text-[9px] text-slate-400 font-semibold" x-text="selectedLead.created_at"></span>
                                                    </div>
                                                    <p class="text-slate-500">Khách hàng điền form thông tin đăng ký tư vấn qua Website.</p>
                                                </div>
                                                <!-- Step 2 -->
                                                <div class="relative">
                                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-blue-500 border-4 border-white shadow-md"></span>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-bold text-slate-850">AI Assistant phân tích yêu cầu</span>
                                                        <span class="text-[9px] text-slate-400 font-semibold">09:15</span>
                                                    </div>
                                                    <p class="text-slate-500">AI tự động phân loại nhu cầu: Căn hộ Studio, Ngân sách 3 tỷ, khu vực Thanh Xuân.</p>
                                                </div>
                                                <!-- Step 3 -->
                                                <div class="relative">
                                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-amber-500 border-4 border-white shadow-md"></span>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-bold text-slate-850">Môi giới gọi điện liên hệ lần 1</span>
                                                        <span class="text-[9px] text-slate-400 font-semibold">09:30</span>
                                                    </div>
                                                    <p class="text-slate-500">Gọi điện xác nhận thông tin cơ bản và hẹn lịch xem nhà trực tiếp.</p>
                                                </div>
                                                <!-- Step 4 -->
                                                <div class="relative">
                                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-purple-500 border-4 border-white shadow-md"></span>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-bold text-slate-850">Thiết lập lịch hẹn đi xem dự án</span>
                                                        <span class="text-[9px] text-slate-400 font-semibold">10:00</span>
                                                    </div>
                                                    <p class="text-slate-500">Đã chốt lịch hẹn xem căn hộ vào cuối tuần này.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes Card (Messenger Style) -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-comments text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Lịch sử ghi chú chi tiết</h4>
                                            </div>

                                            <!-- Notes List -->
                                            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                                                <!-- Dynamic notes mockup -->
                                                <div class="flex gap-3 text-xs items-start bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">Tôi</div>
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-slate-800">Chính chủ (Tôi)</span>
                                                            <span class="text-[9px] text-slate-400 font-medium">10 phút trước</span>
                                                        </div>
                                                        <p class="text-slate-650 font-semibold bg-white p-2.5 rounded-lg border border-slate-150/65" x-text="selectedLead.notes || 'Khách thích căn hộ tầng trung, ban công rộng hướng Nam để đón gió thoáng. Hẹn chủ nhật này đi xem căn hộ.'"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Messenger Note Write area -->
                                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                                <div class="flex items-center gap-2">
                                                    <textarea 
                                                        rows="2" 
                                                        x-model="selectedLead.notes"
                                                        class="flex-grow p-3 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-xs font-semibold outline-none transition-all resize-none shadow-inner"
                                                        placeholder="Viết ghi chú mới ở đây..."
                                                    ></textarea>
                                                    <button 
                                                        type="button" 
                                                        @click="triggerToast('Lưu ghi chú thành công!')"
                                                        class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-xl flex items-center justify-center transition shadow-md cursor-pointer flex-shrink-0"
                                                    >
                                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB 3: CHAT AI & PROMPT GENERATORS -->
                                    <div x-show="activeDetailTab === 'ai'" class="space-y-6">
                                        <!-- ChatGPT style interface -->
                                        <div class="bg-white border border-slate-200 rounded-[18px] shadow-sm flex flex-col overflow-hidden h-[420px]">
                                            <!-- AI Chat header -->
                                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-robot text-blue-600"></i>
                                                    <span class="text-xs font-black text-slate-800">Trợ Lý AI NKS</span>
                                                </div>
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 block animate-pulse"></span>
                                            </div>

                                            <!-- Chat Body -->
                                            <div class="flex-grow p-4 overflow-y-auto space-y-3.5 text-xs bg-slate-50/30">
                                                <div class="flex gap-2.5 max-w-[85%]">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm"><i class="fa-solid fa-robot"></i></div>
                                                    <div class="bg-white border border-slate-200 p-3 rounded-xl rounded-tl-none font-semibold text-slate-700 leading-relaxed shadow-xs">
                                                        Xin chào! Tôi có thể giúp gì để chăm sóc khách hàng <span class="text-blue-600 font-extrabold" x-text="selectedLead.name"></span> này tốt nhất?
                                                    </div>
                                                </div>
                                                
                                                <template x-if="selectedLead.chat_history && selectedLead.chat_history.length">
                                                    <template x-for="(msg, idx) in selectedLead.chat_history" :key="idx">
                                                        <div class="flex gap-2.5 max-w-[85%] mb-2" :class="msg.role === 'user' ? 'ml-auto flex-row-reverse' : ''">
                                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm font-bold"
                                                                :class="msg.role === 'user' ? 'bg-indigo-100 text-indigo-700' : 'bg-blue-600 text-white'">
                                                                <span x-text="msg.role === 'user' ? 'KH' : 'AI'"></span>
                                                            </div>
                                                            <div class="p-3 rounded-xl shadow-xs font-semibold leading-relaxed"
                                                                :class="msg.role === 'user' 
                                                                    ? 'bg-blue-600 text-white rounded-tr-none' 
                                                                    : 'bg-white text-slate-700 border border-slate-200 rounded-tl-none'"
                                                                x-text="msg.content">
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>

                                            <!-- Chat Input -->
                                            <div class="p-3 border-t border-slate-200 bg-white">
                                                <div class="flex items-center gap-2">
                                                    <input 
                                                        type="text" 
                                                        class="flex-grow p-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 focus:bg-white rounded-xl text-xs font-semibold outline-none transition" 
                                                        placeholder="Hỏi AI hoặc yêu cầu soạn kịch bản gọi điện..."
                                                    >
                                                    <button @click="triggerToast('AI đang phân tích câu hỏi...')" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-md"><i class="fa-solid fa-arrow-up"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick AI Tools / Prompt Suggestions -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-wand-magic-sparkles text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Hành động nhanh bằng AI</h4>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3 text-xs">
                                                <button @click="triggerToast('Đang tạo kịch bản gọi điện...')" class="p-3 bg-slate-50 border border-slate-150 hover:border-blue-400 hover:bg-blue-50/20 text-left rounded-xl transition font-bold text-slate-750 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-phone text-blue-600"></i> Kịch bản gọi điện
                                                </button>
                                                <button @click="triggerToast('Đang sinh nội dung Email...')" class="p-3 bg-slate-50 border border-slate-150 hover:border-blue-400 hover:bg-blue-50/20 text-left rounded-xl transition font-bold text-slate-750 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-envelope text-indigo-600"></i> Soạn Email tư vấn
                                                </button>
                                                <button @click="triggerToast('Đang sinh SMS giới thiệu...')" class="p-3 bg-slate-50 border border-slate-150 hover:border-blue-400 hover:bg-blue-50/20 text-left rounded-xl transition font-bold text-slate-750 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-comment-dots text-emerald-600"></i> Viết tin nhắn SMS
                                                </button>
                                                <button @click="triggerToast('Đang sinh kịch bản Zalo...')" class="p-3 bg-slate-50 border border-slate-150 hover:border-blue-400 hover:bg-blue-50/20 text-left rounded-xl transition font-bold text-slate-750 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-paper-plane text-sky-500"></i> Viết kịch bản Zalo
                                                </button>
                                                <button @click="triggerToast('Đang tạo dự thảo đề xuất...')" class="p-3 bg-slate-50 border border-slate-150 hover:border-blue-400 hover:bg-blue-50/20 text-left rounded-xl transition font-bold text-slate-750 flex items-center gap-2.5 col-span-2">
                                                    <i class="fa-solid fa-file-invoice text-rose-500"></i> Thiết lập Proposal gửi khách hàng
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB 4: LỊCH HẸN -->
                                    <div x-show="activeDetailTab === 'appointments'" class="space-y-6">
                                        <!-- Appointment form -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-calendar-check text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Đặt lịch hẹn mới</h4>
                                            </div>
                                            <div class="space-y-4 text-xs font-semibold text-slate-750">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-[10px] text-slate-400 uppercase tracking-wide mb-1">Thời gian</label>
                                                        <input type="datetime-local" class="w-full p-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] text-slate-400 uppercase tracking-wide mb-1">Địa điểm</label>
                                                        <input type="text" class="w-full p-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold" placeholder="VD: Tòa nhà Thanh Xuân">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-slate-400 uppercase tracking-wide mb-1">Nội dung cuộc hẹn</label>
                                                    <textarea rows="3" class="w-full p-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl outline-none resize-none font-semibold" placeholder="Chi tiết lịch đi xem nhà..."></button>
                                                </div>
                                                <button @click="triggerToast('Đã lưu cuộc hẹn mới!')" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-md">Tạo cuộc hẹn mới</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB 5: TÀI LIỆU -->
                                    <div x-show="activeDetailTab === 'documents'" class="space-y-6">
                                        <!-- Documents list & upload -->
                                        <div class="bg-white border border-slate-200 p-5 rounded-[18px] shadow-sm space-y-4">
                                            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                                                <i class="fa-solid fa-folder-open text-blue-600 text-sm"></i>
                                                <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Tài liệu và Hợp đồng</h4>
                                            </div>
                                            <div class="space-y-3">
                                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                                                    <div class="flex items-center gap-3">
                                                        <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                                                        <div>
                                                            <span class="block font-bold text-slate-700">Dự thảo hợp đồng thuê nhà.pdf</span>
                                                            <span class="text-[9px] text-slate-400">1.2 MB - 08/07/2026</span>
                                                        </div>
                                                    </div>
                                                    <button @click="triggerToast('Đang tải tài liệu...')" class="p-2 text-slate-400 hover:text-blue-600"><i class="fa-solid fa-download"></i></button>
                                                </div>
                                                
                                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                                                    <div class="flex items-center gap-3">
                                                        <i class="fa-solid fa-file-image text-blue-500 text-xl"></i>
                                                        <div>
                                                            <span class="block font-bold text-slate-700">Hình ảnh CCCD mặt trước.png</span>
                                                            <span class="text-[9px] text-slate-400">800 KB - 08/07/2026</span>
                                                        </div>
                                                    </div>
                                                    <button @click="triggerToast('Đang tải tài liệu...')" class="p-2 text-slate-400 hover:text-blue-600"><i class="fa-solid fa-download"></i></button>
                                                </div>
                                            </div>
                                            
                                            <div class="border-2 border-dashed border-slate-350 p-6 rounded-xl flex flex-col items-center justify-center gap-2 cursor-pointer bg-slate-50/50 hover:bg-slate-50 transition">
                                                <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-2xl"></i>
                                                <span class="text-xs font-bold text-slate-600">Kéo thả tệp vào đây hoặc nhấn để tải lên</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- ACTION BAR (Sticky Bottom) -->
                        <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-6 py-4 flex items-center justify-between gap-3 shadow-lg z-30">
                            <div class="flex items-center gap-2">
                                <button @click="triggerToast('Đang thực hiện cuộc gọi...')" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-xl flex items-center justify-center transition shadow-md" title="Gọi điện">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </button>
                                <button @click="triggerToast('Mở cổng chat Zalo...')" class="w-10 h-10 bg-sky-500 hover:bg-sky-600 text-white rounded-xl flex items-center justify-center transition shadow-md" title="Mở Zalo">
                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                </button>
                                <button @click="triggerToast('Mở cổng gửi Email...')" class="w-10 h-10 bg-purple-600 hover:bg-purple-700 text-white rounded-xl flex items-center justify-center transition shadow-md" title="Soạn Email">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </button>
                                <button @click="activeDetailTab = 'appointments'" class="w-10 h-10 bg-amber-500 hover:bg-amber-600 text-white rounded-xl flex items-center justify-center transition shadow-md" title="Tạo lịch hẹn">
                                    <i class="fa-solid fa-calendar-check text-xs"></i>
                                </button>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button @click="triggerToast('AI đang phân tích & gợi ý BĐS...')" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                                    <i class="fa-solid fa-robot"></i> Phân tích AI
                                </button>
                                <button @click="triggerToast('Đang tự động sinh hợp đồng mẫu...')" class="px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                                    <i class="fa-solid fa-file-contract"></i> Sinh hợp đồng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
