@extends('layouts.app')

@section('title', 'Quản Lý Thành Viên')
@section('breadcrumb', 'Thành viên')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Danh sách thành viên</h1>
            <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Quản lý và khóa/mở khóa tài khoản khách thuê, chủ nhà hoặc quản trị viên.</p>
        </div>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-left">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <!-- Search Keyword -->
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-505 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Tìm kiếm theo tên, email hoặc SĐT..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-850 dark:text-white text-xs font-semibold outline-none transition"
                >
            </div>

            <!-- Role Filter -->
            <div class="sm:col-span-3">
                <select 
                    name="role" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="tenant" {{ request('role') === 'tenant' ? 'selected' : '' }}>Khách thuê (tenant)</option>
                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Chủ nhà (owner)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (admin)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <select 
                    name="status" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
                    <option value="">-- Trạng thái --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đang khóa</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl shadow-md shadow-primary/20 transition cursor-pointer">
                    Lọc
                </button>
                <a href="{{ route('admin.users.index') }}" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center border border-slate-200 dark:border-slate-700">
                    Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
        <div class="overflow-x-auto">
            @if($users->count() > 0)
            <table class="min-w-full text-left text-xs text-slate-600 dark:text-slate-300 font-semibold">
                <thead class="bg-slate-50 dark:bg-slate-955/50 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-4">Thành viên</th>
                        <th scope="col" class="px-6 py-4">Liên hệ</th>
                        <th scope="col" class="px-6 py-4">Vai trò</th>
                        <th scope="col" class="px-6 py-4">Trạng thái</th>
                        <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <!-- Avatar & Name -->
                        <td class="px-6 py-4 flex items-center space-x-3.5">
                            <img 
                                src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0077bb&color=fff' }}" 
                                alt="{{ $user->name }}" 
                                class="w-9 h-9 rounded-full object-cover border border-slate-100 dark:border-slate-800 shadow-sm animate-fade-in"
                            >
                            <div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-slate-800 dark:text-slate-200 hover:text-primary transition text-xs leading-none">
                                    {{ $user->name }}
                                </a>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-1">ID: #{{ $user->id }}</span>
                            </div>
                        </td>
                        <!-- Email & Phone -->
                        <td class="px-6 py-4">
                            <span class="block text-slate-750 dark:text-slate-200 font-semibold leading-none">{{ $user->email }}</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-1">{{ $user->phone ?? 'Chưa cập nhật SĐT' }}</span>
                        </td>
                        <!-- Role -->
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40">Admin</span>
                            @elseif($user->role === 'owner')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900/40">Chủ nhà</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-900/40">Khách thuê</span>
                            @endif
                        </td>
                        <!-- Status -->
                        <td class="px-6 py-4">
                            @if($user->status === 'locked')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40">
                                    <i class="fa-solid fa-lock mr-1.5 text-[8px]"></i> Đã khóa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900/40">
                                    <i class="fa-solid fa-circle-check mr-1.5 text-[8px]"></i> Hoạt động
                                </span>
                            @endif
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Lock/Unlock Button -->
                                @if($user->id !== Auth::id())
                                    <form id="toggle-status-form-{{ $user->id }}" action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @if($user->status === 'locked')
                                            <button 
                                                type="button" 
                                                @click="triggerConfirm('Mở khóa tài khoản', 'Mở khóa tài khoản cho {{ addslashes($user->name) }}? Người dùng này sẽ lại có quyền truy cập hệ thống bình thường.', 'Mở khóa', 'bg-green-500 hover:bg-green-600', () => { document.getElementById('toggle-status-form-{{ $user->id }}').submit(); })"
                                                class="px-2.5 py-1.5 rounded-xl bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-950/45 border border-green-200 dark:border-green-900/40 text-[10px] font-extrabold cursor-pointer transition flex items-center gap-1 leading-none shadow-sm"
                                            >
                                                <i class="fa-solid fa-unlock-keyhole text-[9px]"></i> Mở khóa
                                            </button>
                                        @else
                                            <button 
                                                type="button" 
                                                @click="triggerConfirm('Khóa tài khoản', 'Khóa tài khoản thành viên của {{ addslashes($user->name) }}? Người dùng này sẽ không thể đăng nhập cho đến khi được mở lại.', 'Khóa lại', 'bg-red-500 hover:bg-red-650', () => { document.getElementById('toggle-status-form-{{ $user->id }}').submit(); })"
                                                class="px-2.5 py-1.5 rounded-xl bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/45 border border-red-200 dark:border-red-900/40 text-[10px] font-extrabold cursor-pointer transition flex items-center gap-1 leading-none shadow-sm"
                                            >
                                                <i class="fa-solid fa-user-slash text-[9px]"></i> Khóa tài khoản
                                            </button>
                                        @endif
                                    </form>
                                  @endif

                                <!-- Delete Button -->
                                @if($user->id !== Auth::id())
                                    <form id="delete-user-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="button" 
                                            @click="triggerConfirm('Xóa tài khoản', 'Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản của {{ addslashes($user->name) }}? Hành động này sẽ loại bỏ tài khoản, tin đăng và lịch hẹn liên quan, không thể khôi phục.', 'Xóa vĩnh viễn', 'bg-red-650 hover:bg-red-700', () => { document.getElementById('delete-user-form-{{ $user->id }}').submit(); })"
                                            class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-red-950/20 text-rose-600 dark:text-rose-450 hover:bg-rose-100 dark:hover:bg-red-950/45 border border-rose-200 dark:border-rose-900/40 flex items-center justify-center transition cursor-pointer" 
                                            title="Xóa tài khoản"
                                        >
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- View Details -->
                                <a href="{{ route('admin.users.show', $user->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-primary dark:hover:text-primary border border-slate-200 dark:border-slate-700 flex items-center justify-center transition" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="py-16 text-center text-slate-400 dark:text-slate-500 font-semibold">
                <i class="fa-solid fa-users-slash text-3xl mb-3 block text-slate-350 dark:text-slate-700"></i>
                Không tìm thấy thành viên nào thỏa mãn bộ lọc.
            </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/30 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
