@extends('layouts.app')

@section('title', 'Chi Tiết Thành Viên - ' . $user->name)

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Breadcrumbs / Back button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('profile.index', ['tab' => 'admin_users']) }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-primary transition">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Quay lại danh sách
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- LEFT COLUMN: Profile Card (4/12 cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-center p-6">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    <img 
                        src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0077bb&color=fff' }}" 
                        alt="{{ $user->name }}" 
                        class="w-full h-full rounded-full object-cover border-2 border-primary/20 shadow-md"
                    >
                </div>
                
                <h3 class="text-lg font-black text-slate-800 leading-none mb-1.5">{{ $user->name }}</h3>
                <span class="text-[10px] text-slate-400 font-bold block mb-4">ID thành viên: #{{ $user->id }}</span>

                <!-- Status and Role Badges -->
                <div class="flex items-center justify-center gap-2 mb-6">
                    <!-- Role -->
                    @if($user->role === 'admin')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Admin</span>
                    @elseif($user->role === 'owner')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Chủ nhà</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200">Khách thuê</span>
                    @endif

                    <!-- Status -->
                    @if($user->status === 'locked')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Đã khóa</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">Đang hoạt động</span>
                    @endif
                </div>

                <!-- Profile Info List -->
                <div class="border-t border-slate-100 pt-5 space-y-3.5 text-left text-xs text-slate-600 font-semibold">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block mb-0.5">Địa chỉ Email</span>
                        <span class="text-slate-800 break-all">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block mb-0.5">Số điện thoại</span>
                        <span class="text-slate-800">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block mb-0.5">Ngày gia nhập</span>
                        <span class="text-slate-800">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Không rõ' }}</span>
                    </div>
                </div>

                <!-- Lock / Unlock Action on sidebar -->
                @if($user->id !== Auth::id())
                    <div class="border-t border-slate-100 pt-5 mt-6 space-y-3">
                        <form id="detail-toggle-status-form" action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                            @csrf
                            @if($user->status === 'locked')
                                <button 
                                    type="submit" 
                                    onclick="return confirm('Mở khóa tài khoản cho {{ addslashes($user->name) }}? Người dùng này sẽ lại có quyền truy cập hệ thống bình thường.');"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md shadow-green-500/20 transition cursor-pointer"
                                >
                                    <i class="fa-solid fa-unlock-keyhole mr-1.5"></i> Mở khóa tài khoản
                                </button>
                            @else
                                <button 
                                    type="submit" 
                                    onclick="return confirm('Khóa tài khoản thành viên của {{ addslashes($user->name) }}? Người dùng này sẽ không thể đăng nhập cho đến khi được mở lại.');"
                                    class="w-full bg-red-500 hover:bg-red-650 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md shadow-red-500/20 transition cursor-pointer"
                                >
                                    <i class="fa-solid fa-user-slash mr-1.5"></i> Khóa tài khoản này
                                </button>
                            @endif
                        </form>
                    </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: Related Properties or Appointments (8/12 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- If Owner: Show posted properties -->
                @if($user->role === 'owner')
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tin đăng bất động sản ({{ count($properties) }})</h3>
                    </div>
                    <div class="divide-y divide-slate-100 overflow-x-auto">
                        @if(count($properties) > 0)
                        <table class="min-w-full text-left text-xs text-slate-600 font-semibold">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Bất động sản</th>
                                    <th scope="col" class="px-6 py-4">Giá / diện tích</th>
                                    <th scope="col" class="px-6 py-4">Trạng thái</th>
                                    <th scope="col" class="px-6 py-4 text-right">Xem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($properties as $p)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 max-w-[280px]">
                                        <a href="/property/{{ $p->id }}" class="font-bold text-slate-800 hover:text-primary transition block truncate leading-tight">
                                            {{ $p->title }}
                                        </a>
                                        <span class="text-[9px] text-slate-400 block mt-1"><i class="fa-solid fa-tag mr-1 text-[8px]"></i>{{ $p->category->name ?? 'Không phân loại' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block text-primary font-bold">{{ number_format($p->price / 1000000, 1) }} tr/tháng</span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ $p->area }} m²</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($p->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">Hiển thị</span>
                                        @elseif($p->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                                        @elseif($p->status === 'rejected')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Từ chối</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 text-slate-700 border border-slate-200">Đã ẩn</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/property/{{ $p->id }}" class="w-7 h-7 rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-primary border border-slate-200 flex items-center justify-center transition">
                                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="py-12 text-center text-slate-400 font-semibold">
                            <i class="fa-solid fa-hotel text-2xl mb-2.5 block text-slate-350"></i>
                            Thành viên này chưa đăng tin bất động sản nào.
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- If Tenant: Show scheduled appointments -->
                @if($user->role === 'tenant')
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden text-left">
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Lịch hẹn xem nhà ({{ count($appointments) }})</h3>
                    </div>
                    <div class="divide-y divide-slate-100 overflow-x-auto">
                        @if(count($appointments) > 0)
                        <table class="min-w-full text-left text-xs text-slate-600 font-semibold">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Bất động sản</th>
                                    <th scope="col" class="px-6 py-4">Thời gian</th>
                                    <th scope="col" class="px-6 py-4">Trạng thái</th>
                                    <th scope="col" class="px-6 py-4 text-right">Xem BĐS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($appointments as $app)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 max-w-[280px]">
                                        @if($app->property)
                                            <a href="/property/{{ $app->property->id }}" class="font-bold text-slate-800 hover:text-primary transition block truncate leading-tight">
                                                {{ $app->property->title }}
                                            </a>
                                            <span class="text-[9px] text-slate-400 block mt-1"><i class="fa-solid fa-user-tie mr-1 text-[8px]"></i>Chủ nhà: {{ $app->property->agent->name ?? 'N/A' }}</span>
                                        @else
                                            <span class="text-slate-400 italic">BĐS không tồn tại</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block text-slate-800">{{ Carbon\Carbon::parse($app->date)->format('d/m/Y') }}</span>
                                        <span class="text-[10px] text-slate-450 block mt-0.5 font-normal">lúc {{ $app->time }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($app->status === 'confirmed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-200">Đã duyệt</span>
                                        @elseif($app->status === 'cancelled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-200">Đã hủy</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Chờ phản hồi</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($app->property)
                                            <a href="/property/{{ $app->property->id }}" class="w-7 h-7 rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-primary border border-slate-200 flex items-center justify-center transition">
                                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                            </a>
                                        @else
                                            <button type="button" disabled class="w-7 h-7 rounded-lg bg-slate-50 text-slate-300 border border-slate-100 flex items-center justify-center cursor-not-allowed">
                                                <i class="fa-solid fa-ban text-[10px]"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="py-12 text-center text-slate-400 font-semibold">
                            <i class="fa-solid fa-calendar-days text-2xl mb-2.5 block text-slate-350"></i>
                            Khách thuê này chưa đăng ký lịch hẹn xem nhà nào.
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Admin and General details -->
                @if($user->role === 'admin')
                <div class="bg-white p-8 rounded-3xl border border-slate-200/60 shadow-sm text-center">
                    <i class="fa-solid fa-shield-halved text-4xl text-primary mb-3.5 block"></i>
                    <h4 class="text-sm font-bold text-slate-800">Tài khoản Quản trị viên hệ thống</h4>
                    <p class="text-[11px] text-slate-400 font-semibold mt-1 max-w-sm mx-auto leading-relaxed">Đây là tài khoản quản trị có toàn quyền kiểm soát và điều phối toàn bộ các module trong hệ thống BDS Rental.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
