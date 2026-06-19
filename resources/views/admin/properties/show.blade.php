@extends('layouts.admin')

@section('title', 'Chi Tiết Tin Đăng BĐS')
@section('breadcrumb', 'Chi tiết tin đăng')

@section('content')
<div class="space-y-6">
    <!-- Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.properties.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-primary transition">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Main Grid (8 + 4 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- LEFT COLUMN: Main Details (8/12 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Property Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left p-6">
                <!-- Title & Location -->
                <div class="space-y-2.5 pb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-primary-light/10 text-primary border border-primary/15">{{ $property->category->name ?? 'Không phân loại' }}</span>
                        @if($property->is_vip)
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40 uppercase">Tin VIP</span>
                        @endif
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white leading-snug tracking-tight">{{ $property->title }}</h2>
                    <p class="text-xs text-slate-450 dark:text-slate-400 font-semibold"><i class="fa-solid fa-location-dot mr-1 text-slate-400 dark:text-slate-650"></i>{{ $property->location }}</p>
                </div>

                <!-- Featured Image -->
                <div class="mt-6 aspect-video rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-850 border border-slate-150 dark:border-slate-800 relative shadow-sm">
                    <img 
                        src="{{ asset($property->image) }}" 
                        alt="{{ $property->title }}" 
                        class="w-full h-full object-cover"
                        onerror="this.src='https://placehold.co/800x450?text=BDS'"
                    >
                </div>

                <!-- Gallery Row -->
                @if(is_array($property->images) && count($property->images) > 0)
                <div class="grid grid-cols-4 gap-3.5 mt-4">
                    @foreach($property->images as $img)
                    <div class="aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-850 border border-slate-150 dark:border-slate-800 shadow-sm">
                        <img 
                            src="{{ asset($img) }}" 
                            alt="Gallery image" 
                            class="w-full h-full object-cover hover:scale-105 transition duration-200 cursor-pointer"
                            onerror="this.src='https://placehold.co/200x200?text=BDS'"
                        >
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Attributes Info -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 py-6 border-b border-t border-slate-100 dark:border-slate-800 mt-6 text-center">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Giá thuê</span>
                        <span class="text-base font-black text-primary">{{ number_format($property->price / 1000000, 1) }} tr/tháng</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Diện tích</span>
                        <span class="text-base font-black text-slate-800 dark:text-slate-200">{{ $property->area }} m²</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Phòng ngủ</span>
                        <span class="text-base font-black text-slate-800 dark:text-slate-200">{{ $property->bedrooms }} PN</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Phòng vệ sinh</span>
                        <span class="text-base font-black text-slate-800 dark:text-slate-200">{{ $property->bathrooms }} WC</span>
                    </div>
                </div>

                <!-- Detail description -->
                <div class="mt-6 space-y-3">
                    <h4 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Thông tin mô tả</h4>
                    <div class="text-xs text-slate-650 dark:text-slate-350 font-semibold leading-relaxed whitespace-pre-line">
                        {{ $property->description }}
                    </div>
                </div>

                <!-- Technical Details Table -->
                <div class="mt-8 space-y-3">
                    <h4 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Chi tiết kỹ thuật</h4>
                    <div class="border border-slate-150 dark:border-slate-800 rounded-2xl overflow-hidden bg-slate-50/50 dark:bg-slate-950/20">
                        <div class="grid grid-cols-2 divide-x divide-slate-150 dark:divide-slate-800 border-b border-slate-150 dark:border-slate-800">
                            <div class="p-3.5 text-xs text-left">
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550 uppercase block">Hướng nhà</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 mt-0.5 block">{{ $property->direction ?? 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="p-3.5 text-xs text-left">
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550 uppercase block">Pháp lý</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 mt-0.5 block">{{ $property->legal ?? 'Chưa cập nhật' }}</span>
                            </div>
                        </div>
                        <div class="p-3.5 text-xs text-left">
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550 uppercase block">Nội thất</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 mt-0.5 block">{{ $property->furniture ?? 'Chưa cập nhật' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Action & Owner Cards (4/12 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Admin Action Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm p-6 text-left space-y-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-1">Duyệt tin đăng</h3>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 font-semibold leading-relaxed">Thay đổi trạng thái tin đăng bất động sản trên hệ thống.</p>
                </div>

                <!-- Status Indicator -->
                <div class="p-3.5 rounded-xl border flex items-center justify-between text-xs font-semibold 
                    {{ $property->status === 'approved' ? 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-900/40 text-green-700 dark:text-green-400' : '' }}
                    {{ $property->status === 'pending' ? 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40 text-amber-700 dark:text-amber-400' : '' }}
                    {{ $property->status === 'rejected' ? 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-900/40 text-red-700 dark:text-red-400' : '' }}
                    {{ $property->status === 'hidden' ? 'bg-slate-50 dark:bg-slate-850/60 border-slate-200 dark:border-slate-850 text-slate-750 dark:text-slate-300' : '' }}
                ">
                    <span>Trạng thái hiện tại:</span>
                    <span class="font-bold uppercase tracking-wide">
                        @if($property->status === 'approved') Hiển thị
                        @elseif($property->status === 'pending') Chờ duyệt
                        @elseif($property->status === 'rejected') Bị từ chối
                        @else Đang ẩn
                        @endif
                    </span>
                </div>

                <!-- Status Update Buttons -->
                <div class="space-y-2">
                    @if($property->status !== 'approved')
                    <form id="approve-property-form" action="{{ route('admin.properties.status', $property->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button 
                            type="button" 
                            @click="triggerConfirm('Phê duyệt tin đăng', 'Duyệt bài đăng này để hiển thị công khai trên website?', 'Phê duyệt', 'bg-green-500 hover:bg-green-600', () => { document.getElementById('approve-property-form').submit(); })"
                            class="w-full bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md shadow-green-500/20 transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <i class="fa-solid fa-circle-check"></i> Phê duyệt tin đăng
                        </button>
                    </form>
                    @endif

                    @if($property->status === 'approved')
                    <form id="hide-property-form" action="{{ route('admin.properties.status', $property->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="hidden">
                        <button 
                            type="button" 
                            @click="triggerConfirm('Ẩn tin đăng', 'Ẩn tin đăng này khỏi trang tìm kiếm công cộng?', 'Ẩn tin', 'bg-slate-500 hover:bg-slate-600', () => { document.getElementById('hide-property-form').submit(); })"
                            class="w-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5 shadow-sm"
                        >
                            <i class="fa-solid fa-eye-slash"></i> Ẩn tin đăng này
                        </button>
                    </form>
                    @endif

                    @if($property->status === 'pending')
                    <form id="reject-property-form" action="{{ route('admin.properties.status', $property->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button 
                            type="button" 
                            @click="triggerConfirm('Từ chối tin đăng', 'Từ chối tin đăng này? Tin sẽ không được hiển thị và chủ nhà sẽ nhận được thông báo.', 'Từ chối', 'bg-amber-500 hover:bg-amber-600', () => { document.getElementById('reject-property-form').submit(); })"
                            class="w-full bg-amber-50 dark:bg-amber-950/20 hover:bg-amber-100 dark:hover:bg-amber-955/35 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40 text-xs font-bold py-2.5 px-4 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <i class="fa-solid fa-ban"></i> Từ chối tin đăng
                        </button>
                    </form>
                    @endif

                    @if($property->status !== 'pending')
                    <form id="pending-property-form" action="{{ route('admin.properties.status', $property->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="pending">
                        <button 
                            type="button" 
                            @click="triggerConfirm('Yêu cầu duyệt lại', 'Chuyển tin đăng này trở lại hàng đợi chờ kiểm duyệt?', 'Chuyển về', 'bg-slate-500 hover:bg-slate-600', () => { document.getElementById('pending-property-form').submit(); })"
                            class="w-full bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-650 dark:text-slate-350 border border-slate-200 dark:border-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5 shadow-sm"
                        >
                            <i class="fa-solid fa-clock-rotate-left"></i> Chuyển về chờ duyệt
                        </button>
                    </form>
                    @endif
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                    <form id="destroy-property-form" action="{{ route('admin.properties.destroy', $property->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="button" 
                            @click="triggerConfirm('Xóa tin đăng', 'Bạn có chắc chắn muốn xóa tin đăng bất động sản này vĩnh viễn không? Thao tác không thể hoàn tác.', 'Xóa vĩnh viễn', 'bg-red-650 hover:bg-red-700', () => { document.getElementById('destroy-property-form').submit(); })"
                            class="w-full bg-red-500 hover:bg-red-650 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md shadow-red-500/20 transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <i class="fa-solid fa-trash-can"></i> Xóa tin đăng vĩnh viễn
                        </button>
                    </form>
                </div>
            </div>

            <!-- Owner Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm p-6 text-center space-y-3">
                <h4 class="text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider text-left">Chủ nhà đăng tin</h4>
                <div class="relative w-16 h-16 mx-auto">
                    <img 
                        src="{{ $property->agent->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($property->agent->name) . '&background=0077bb&color=fff' }}" 
                        alt="{{ $property->agent->name }}" 
                        class="w-full h-full rounded-full object-cover border border-slate-150 dark:border-slate-800 shadow-sm"
                    >
                </div>
                <div>
                    <h5 class="text-xs font-extrabold text-slate-800 dark:text-slate-200 leading-tight">{{ $property->agent->name }}</h5>
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 font-bold block mt-0.5">Vai trò: Chủ nhà</span>
                </div>
                <div class="border-t border-slate-100 dark:border-slate-800 pt-3.5 mt-4 text-xs text-left space-y-2 text-slate-500 dark:text-slate-400 font-semibold leading-relaxed">
                    <p><i class="fa-solid fa-envelope mr-1.5 text-slate-350 dark:text-slate-600 text-[10px]"></i>{{ $property->agent->email }}</p>
                    <p><i class="fa-solid fa-phone mr-1.5 text-slate-350 dark:text-slate-600 text-[10px]"></i>{{ $property->agent->phone ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="pt-4">
                    <a href="{{ route('admin.users.show', $property->agent->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-slate-200 dark:border-slate-700 hover:border-primary text-xs font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-primary dark:hover:bg-primary transition shadow-sm cursor-pointer">
                        Xem trang cá nhân chủ nhà
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
