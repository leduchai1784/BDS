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
        filterSource: 'all',
        searchTerm: '',
        openLead(lead) {
            this.selectedLead = lead;
            this.drawerOpen = true;
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


            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Nguồn:</span>
                <select 
                    x-model="filterSource"
                    class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-600 focus:border-primary focus:outline-none transition cursor-pointer"
                >
                    <option value="all">Tất cả</option>
                    <option value="chatbot">AI Chatbot</option>
                    <option value="web">Form Web</option>
                </select>
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
        class="fixed inset-0 z-50 overflow-hidden" 
        aria-labelledby="slide-over-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background Backdrop overlay -->
            <div 
                x-show="drawerOpen"
                x-transition:enter="ease-in-out duration-350"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                @click="drawerOpen = false"
            ></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <!-- Slide-over panel -->
                <div 
                    x-show="drawerOpen"
                    x-transition:enter="transform transition ease-in-out duration-350"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-md animate-duration-300"
                >
                    <div class="flex h-full flex-col bg-white shadow-2xl overflow-y-auto border-l border-slate-100">
                        <!-- Drawer Header -->
                        <div class="px-5 py-5 bg-gradient-to-r from-primary to-primary-hover text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center border border-white/20 text-sm font-bold" x-text="selectedLead ? getInitials(selectedLead.name) : ''"></div>
                                <div>
                                    <h3 class="font-bold text-sm" x-text="selectedLead ? selectedLead.name : ''"></h3>
                                    <p class="text-[10px] text-white/80 font-semibold mt-0.5" x-text="selectedLead ? selectedLead.phone : ''"></p>
                                </div>
                            </div>
                            <button 
                                type="button" 
                                @click="drawerOpen = false"
                                class="w-8 h-8 rounded-lg hover:bg-white/10 text-white flex items-center justify-center transition cursor-pointer focus:outline-none"
                            >
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <!-- Drawer Content Body -->
                        <div class="flex-grow p-5 space-y-6">
                            <template x-if="selectedLead">
                                <div class="space-y-6">
                                    <!-- Contact Info Card -->
                                    <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                                        <h4 class="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Thông tin liên hệ</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Họ tên:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.name"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Số điện thoại:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.phone || 'Chưa cung cấp'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Email:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.email || 'Chưa cung cấp'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Zalo:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.zalo || 'Chưa cung cấp'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Job & Company Card -->
                                    <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                                        <h4 class="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Thông tin công việc</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Công ty:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.company || 'Chưa cung cấp'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Chức vụ:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.position || 'Chưa cung cấp'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400 font-semibold">Quy mô công ty:</span>
                                                <span class="font-bold text-slate-700" x-text="selectedLead.comsize || 'Chưa cung cấp'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Demand Card -->
                                    <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                                        <h4 class="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Yêu cầu & Nhu cầu</h4>
                                        <div class="space-y-1">
                                            <span class="block text-slate-400 font-semibold mb-1">Chi tiết nhu cầu:</span>
                                            <p class="font-semibold text-slate-700 leading-relaxed bg-white border border-slate-150 p-2.5 rounded-xl whitespace-pre-line" x-text="selectedLead.demand || '-'"></p>
                                        </div>
                                    </div>

                                    <!-- Notes Card -->
                                    <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                                        <h4 class="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Ghi chú hệ thống</h4>
                                        <div class="space-y-1">
                                            <p class="font-medium text-slate-600 leading-relaxed bg-white border border-slate-150 p-2.5 rounded-xl whitespace-pre-line" x-text="selectedLead.notes || 'Chưa có ghi chú nào từ khách hàng.'"></p>
                                        </div>
                                    </div>

                                    <!-- Meta Info -->
                                    <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-cloud-arrow-up text-emerald-500 text-sm"></i>
                                            <div>
                                                <span class="block font-bold text-slate-700">Trạng thái đồng bộ</span>
                                                <span class="text-[10px] text-slate-400 font-semibold" x-text="'Ngày nhận: ' + selectedLead.created_at"></span>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-bold text-slate-400 bg-slate-200/50 px-2 py-0.5 rounded" x-text="'ID: ' + selectedLead.id"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
