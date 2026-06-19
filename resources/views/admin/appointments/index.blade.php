@extends('layouts.admin')

@section('title', 'Quản Lý Lịch Hẹn Xem Nhà')
@section('breadcrumb', 'Lịch hẹn')

@section('content')
<div class="space-y-6" x-data="{ activeAppointment: null, modalOpen: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Danh sách lịch hẹn xem nhà</h1>
            <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Theo dõi trạng thái và hủy lịch hẹn đi xem nhà trực tiếp của thành viên.</p>
        </div>
    </div>

    <!-- Search and Filters Card -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-left">
        <form action="{{ route('admin.appointments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <!-- Search Keyword -->
            <div class="sm:col-span-8 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Tìm theo tên khách, SĐT hoặc tiêu đề bất động sản..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
                <!-- Keep status tab selection when searching -->
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-4 flex gap-2">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl shadow-md shadow-primary/20 transition cursor-pointer">
                    Tìm kiếm
                </button>
                <a href="{{ route('admin.appointments.index') }}" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center border border-slate-200 dark:border-slate-700">
                    Xóa lọc / tìm
                </a>
            </div>
        </form>
    </div>

    <!-- Status Tabs Filters -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 overflow-x-auto scrollbar-none gap-2">
        <a 
            href="{{ route('admin.appointments.index', request()->only('search')) }}" 
            class="px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition {{ !request('status') ? 'border-primary text-primary bg-primary-light/10 dark:bg-primary/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-350 dark:hover:border-slate-700' }}"
        >
            Tất cả
        </a>
        <a 
            href="{{ route('admin.appointments.index', array_merge(request()->only('search'), ['status' => 'pending'])) }}" 
            class="px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition {{ request('status') === 'pending' ? 'border-primary text-primary bg-primary-light/10 dark:bg-primary/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-350 dark:hover:border-slate-700' }}"
        >
            Đang chờ duyệt
        </a>
        <a 
            href="{{ route('admin.appointments.index', array_merge(request()->only('search'), ['status' => 'confirmed'])) }}" 
            class="px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition {{ request('status') === 'confirmed' ? 'border-primary text-primary bg-primary-light/10 dark:bg-primary/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-350 dark:hover:border-slate-700' }}"
        >
            Đã xác nhận
        </a>
        <a 
            href="{{ route('admin.appointments.index', array_merge(request()->only('search'), ['status' => 'cancelled'])) }}" 
            class="px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition {{ request('status') === 'cancelled' ? 'border-primary text-primary bg-primary-light/10 dark:bg-primary/10 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-350 dark:hover:border-slate-700' }}"
        >
            Đã hủy
        </a>
    </div>

    <!-- Appointments Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
        <div class="overflow-x-auto">
            @if($appointments->count() > 0)
            <table class="min-w-full text-left text-xs text-slate-600 dark:text-slate-300 font-semibold">
                <thead class="bg-slate-50 dark:bg-slate-955/50 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-4">Mã số / Khách hẹn</th>
                        <th scope="col" class="px-6 py-4">Bất động sản</th>
                        <th scope="col" class="px-6 py-4">Thời gian hẹn</th>
                        <th scope="col" class="px-6 py-4">Lời nhắn</th>
                        <th scope="col" class="px-6 py-4">Trạng thái</th>
                        <th scope="col" class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($appointments as $app)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <!-- ID & Guest details -->
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            <span class="block text-slate-900 dark:text-slate-250 font-bold">#BK-{{ $app->id }}</span>
                            <div class="mt-1 space-y-0.5 leading-none">
                                <span class="text-slate-800 dark:text-slate-200 font-bold block">{{ $app->name }}</span>
                                <span class="text-[10px] text-slate-450 dark:text-slate-500 block mt-0.5">{{ $app->phone }}</span>
                            </div>
                        </td>
                        <!-- Property -->
                        <td class="px-6 py-4.5 max-w-[280px]">
                            <a href="{{ route('admin.properties.show', $app->property->id) }}" class="font-bold text-slate-800 dark:text-slate-200 hover:text-primary transition block truncate leading-tight">
                                {{ $app->property->title }}
                            </a>
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-1"><i class="fa-solid fa-user-tie mr-1 text-[8px]"></i>Chủ nhà: {{ $app->property->agent->name }}</span>
                        </td>
                        <!-- Date & Time -->
                        <td class="px-6 py-4.5 whitespace-nowrap leading-relaxed">
                            <span class="block text-slate-850 dark:text-slate-200 font-bold">{{ Carbon\Carbon::parse($app->date)->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-slate-450 dark:text-slate-500 block"><i class="fa-regular fa-clock mr-1 text-slate-400 dark:text-slate-600"></i>{{ $app->time }}</span>
                        </td>
                        <!-- Message -->
                        <td class="px-6 py-4.5 max-w-[200px] truncate" title="{{ $app->message }}">
                            <span class="text-slate-500 dark:text-slate-400 block truncate font-medium">{{ $app->message ?? 'Không có lời nhắn' }}</span>
                        </td>
                        <!-- Status Badge -->
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            @if($app->status === 'confirmed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900/40">Đã duyệt</span>
                            @elseif($app->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40">Đã hủy</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">Chờ duyệt</span>
                            @endif
                        </td>
                        <!-- Action -->
                        <td class="px-6 py-4.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Details Trigger Button -->
                                <button 
                                    type="button"
                                    @click="activeAppointment = { id: '{{ $app->id }}', name: '{{ addslashes($app->name) }}', phone: '{{ $app->phone }}', date: '{{ Carbon\Carbon::parse($app->date)->format('d/m/Y') }}', time: '{{ $app->time }}', message: '{{ addslashes($app->message ?? 'Không có lời nhắn') }}', status: '{{ $app->status }}', propertyTitle: '{{ addslashes($app->property->title) }}', propertyPrice: '{{ number_format($app->property->price / 1000000, 1) }} tr/tháng', propertyLocation: '{{ addslashes($app->property->location) }}', ownerName: '{{ addslashes($app->property->agent->name) }}', ownerPhone: '{{ $app->property->agent->phone }}' }; modalOpen = true"
                                    class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-primary dark:hover:text-primary border border-slate-200 dark:border-slate-700 flex items-center justify-center transition cursor-pointer"
                                    title="Xem chi tiết"
                                >
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                @if($app->status === 'pending' || $app->status === 'confirmed')
                                    <form id="cancel-app-form-{{ $app->id }}" action="{{ route('admin.appointments.cancel', $app->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button 
                                            type="button" 
                                            @click="triggerConfirm('Hủy lịch hẹn', 'Bạn có chắc chắn muốn hủy lịch hẹn xem nhà #BK-{{ $app->id }} của khách {{ addslashes($app->name) }} không?', 'Hủy lịch', 'bg-red-500 hover:bg-red-650', () => { document.getElementById('cancel-app-form-{{ $app->id }}').submit(); })"
                                            class="px-3 py-1.5 rounded-xl bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/45 border border-red-200 dark:border-red-900/40 text-[10px] font-extrabold cursor-pointer transition flex items-center gap-1 leading-none shadow-sm"
                                        >
                                            <i class="fa-solid fa-calendar-minus text-[9px]"></i> Hủy lịch
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold px-3 py-1.5 block">Không có thao tác</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="py-16 text-center text-slate-400 dark:text-slate-550 font-semibold">
                <i class="fa-solid fa-calendar-times text-3xl mb-3 block text-slate-350 dark:text-slate-700"></i>
                Không tìm thấy lịch hẹn xem nhà nào thỏa mãn bộ lọc.
            </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if($appointments->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-955/35 border-t border-slate-100 dark:border-slate-800">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>

    <!-- Appointment Detail Modal -->
    <div 
        x-show="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-955/70 backdrop-blur-sm"
        x-transition
        x-cloak
    >
        <div 
            @click.away="modalOpen = false"
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 max-w-md w-full text-left space-y-4"
        >
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Chi tiết lịch hẹn xem nhà</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            
            <div class="space-y-3.5 text-xs text-slate-600 dark:text-slate-350 font-semibold" x-show="activeAppointment">
                <!-- Appointment ID & Status -->
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-400 uppercase">Mã lịch hẹn: <span class="text-slate-800 dark:text-white font-extrabold" x-text="'#BK-' + activeAppointment?.id"></span></span>
                    <div>
                        <span 
                            class="px-2 py-0.5 rounded-full text-[9px] font-bold border"
                            :class="{
                                'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-900/40': activeAppointment?.status === 'confirmed',
                                'bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-900/40': activeAppointment?.status === 'cancelled',
                                'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900/40': activeAppointment?.status === 'pending'
                            }"
                            x-text="activeAppointment?.status === 'confirmed' ? 'Đã duyệt' : (activeAppointment?.status === 'cancelled' ? 'Đã hủy' : 'Chờ duyệt')"
                        ></span>
                    </div>
                </div>

                <!-- Tenant Info -->
                <div class="p-3 bg-slate-50 dark:bg-slate-950/25 rounded-xl space-y-2 border border-slate-100 dark:border-slate-800/80">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Thông tin khách hẹn</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[9px] text-slate-400 block">Họ và tên</span>
                            <span class="text-slate-800 dark:text-white text-[11px] block font-bold" x-text="activeAppointment?.name"></span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block">Số điện thoại</span>
                            <span class="text-slate-800 dark:text-white text-[11px] block font-bold" x-text="activeAppointment?.phone"></span>
                        </div>
                    </div>
                </div>

                <!-- Time Details -->
                <div class="p-3 bg-slate-50 dark:bg-slate-950/25 rounded-xl space-y-2 border border-slate-100 dark:border-slate-800/80">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Thời gian hẹn</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[9px] text-slate-400 block">Ngày hẹn xem</span>
                            <span class="text-slate-800 dark:text-white text-[11px] block font-bold" x-text="activeAppointment?.date"></span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block">Giờ hẹn xem</span>
                            <span class="text-slate-800 dark:text-white text-[11px] block font-bold" x-text="activeAppointment?.time"></span>
                        </div>
                    </div>
                </div>

                <!-- Property Details -->
                <div class="space-y-1.5 pt-1 text-left">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Bất động sản hẹn xem</span>
                    <div>
                        <span class="text-slate-800 dark:text-white text-xs block leading-tight font-extrabold" x-text="activeAppointment?.propertyTitle"></span>
                        <span class="text-primary font-black mt-1 block" x-text="activeAppointment?.propertyPrice"></span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold block mt-0.5" x-text="activeAppointment?.propertyLocation"></span>
                    </div>
                </div>

                <!-- Owner Details -->
                <div class="grid grid-cols-2 gap-4 border-t border-slate-100 dark:border-slate-800 pt-3 text-left">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Chủ nhà</span>
                        <span class="text-slate-800 dark:text-white font-bold mt-0.5 block" x-text="activeAppointment?.ownerName"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Liên hệ chủ nhà</span>
                        <span class="text-slate-800 dark:text-white font-bold mt-0.5 block" x-text="activeAppointment?.ownerPhone"></span>
                    </div>
                </div>

                <!-- Message -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-3 text-left">
                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Lời nhắn của khách</span>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 italic font-medium leading-relaxed" x-text="activeAppointment?.message"></p>
                </div>
            </div>
            
            <div class="pt-2">
                <button 
                    @click="modalOpen = false" 
                    class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 text-xs font-bold rounded-xl transition cursor-pointer text-center focus:outline-none"
                >
                    Đóng cửa sổ
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
