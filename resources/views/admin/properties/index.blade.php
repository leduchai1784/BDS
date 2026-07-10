@extends('layouts.app')

@section('title', 'Quản Lý Tin Đăng BĐS')
@section('breadcrumb', 'Tin đăng')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Danh sách tin đăng bất động sản</h1>
            <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Duyệt tin đăng mới, ẩn hoặc xóa các tin vi phạm quy chế của sàn cho thuê.</p>
        </div>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-left">
        <form action="{{ route('admin.properties.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <!-- Search Keyword -->
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Tìm kiếm theo tiêu đề tin, địa chỉ..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
            </div>

            <!-- Category Filter -->
            <div class="sm:col-span-3">
                <select 
                    name="category_id" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <select 
                    name="status" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
                    <option value="">-- Trạng thái --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl shadow-md shadow-primary/20 transition cursor-pointer">
                    Lọc
                </button>
                <a href="{{ route('admin.properties.index') }}" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center border border-slate-200 dark:border-slate-700">
                    Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Properties Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
        <div class="overflow-x-auto">
            @if($properties->count() > 0)
            <table class="min-w-full text-left text-xs text-slate-600 dark:text-slate-300 font-semibold">
                <thead class="bg-slate-50 dark:bg-slate-955/50 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-4">Bất động sản</th>
                        <th scope="col" class="px-6 py-4">Người đăng</th>
                        <th scope="col" class="px-6 py-4">Giá / diện tích</th>
                        <th scope="col" class="px-6 py-4">Xem / Vip</th>
                        <th scope="col" class="px-6 py-4">Trạng thái</th>
                        <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($properties as $property)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <!-- Image & Title -->
                        <td class="px-6 py-4 max-w-[300px]">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-150 dark:border-slate-700 flex-shrink-0">
                                    <img 
                                        src="{{ asset($property->image) }}" 
                                        alt="{{ $property->title }}" 
                                        class="w-full h-full object-cover"
                                        onerror="this.src='https://placehold.co/120x120?text=BDS'"
                                    >
                                </div>
                                <div class="truncate">
                                    <a href="{{ route('admin.properties.show', $property->id) }}" class="font-bold text-slate-800 dark:text-slate-200 hover:text-primary transition block truncate leading-tight">
                                        {{ $property->title }}
                                    </a>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-1"><i class="fa-solid fa-location-dot mr-1"></i>{{ $property->location }}</span>
                                </div>
                            </div>
                        </td>
                        <!-- Owner/Agent -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-slate-800 dark:text-slate-200 font-bold leading-none">{{ $property->agent->name }}</span>
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-1">{{ $property->agent->phone }}</span>
                        </td>
                        <!-- Price & Area -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-primary font-bold">{{ number_format($property->price / 1000000, 1) }} tr/tháng</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5">{{ $property->area }} m²</span>
                        </td>
                        <!-- Views & VIP Status -->
                        <td class="px-6 py-4 whitespace-nowrap leading-relaxed">
                            <span class="block text-[10px] text-slate-500 dark:text-slate-400"><i class="fa-solid fa-eye mr-1 text-slate-400 dark:text-slate-600"></i>{{ $property->views }} lượt xem</span>
                            @if($property->is_vip)
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40 text-[8px] font-black uppercase mt-1">Tin VIP</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-550 border border-slate-200 dark:border-slate-700 text-[8px] font-bold uppercase mt-1">Thường</span>
                            @endif
                        </td>
                        <!-- Status Badge -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($property->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900/40">Hiển thị</span>
                            @elseif($property->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">Chờ duyệt</span>
                            @elseif($property->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40">Bị từ chối</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-450 border border-slate-200 dark:border-slate-700">Đang ẩn</span>
                            @endif
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Quick Approve -->
                                @if($property->status !== 'approved')
                                    <form id="quick-approve-form-{{ $property->id }}" action="{{ route('admin.properties.status', $property->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button 
                                            type="button" 
                                            @click="triggerConfirm('Phê duyệt tin đăng', 'Phê duyệt tin đăng này để hiển thị công khai trên website?', 'Phê duyệt', 'bg-green-500 hover:bg-green-600', () => { document.getElementById('quick-approve-form-{{ $property->id }}').submit(); })"
                                            class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-950/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-950/45 border border-green-200 dark:border-green-900/40 flex items-center justify-center transition cursor-pointer" 
                                            title="Duyệt bài đăng"
                                        >
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Quick Hide -->
                                @if($property->status === 'approved')
                                    <form id="quick-hide-form-{{ $property->id }}" action="{{ route('admin.properties.status', $property->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="hidden">
                                        <button 
                                            type="button" 
                                            @click="triggerConfirm('Ẩn tin đăng', 'Ẩn tin đăng này khỏi danh sách công khai trên trang chủ và tìm kiếm?', 'Ẩn tin đăng', 'bg-slate-500 hover:bg-slate-600', () => { document.getElementById('quick-hide-form-{{ $property->id }}').submit(); })"
                                            class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition cursor-pointer" 
                                            title="Ẩn tin đăng"
                                        >
                                            <i class="fa-solid fa-eye-slash text-xs"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- View details -->
                                <a href="{{ route('admin.properties.show', $property->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-primary dark:hover:text-primary border border-slate-200 dark:border-slate-700 flex items-center justify-center transition" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                <!-- Delete -->
                                <form id="quick-delete-form-{{ $property->id }}" action="{{ route('admin.properties.destroy', $property->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="button" 
                                        @click="triggerConfirm('Xóa tin đăng', 'Bạn có chắc chắn muốn xóa tin đăng bất động sản này vĩnh viễn không? Hành động này không thể hoàn tác.', 'Xóa vĩnh viễn', 'bg-red-650 hover:bg-red-755', () => { document.getElementById('quick-delete-form-{{ $property->id }}').submit(); })"
                                        class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/45 border border-red-200 dark:border-red-900/40 flex items-center justify-center transition cursor-pointer" 
                                        title="Xóa tin đăng"
                                    >
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="py-16 text-center text-slate-400 dark:text-slate-550 font-semibold">
                <i class="fa-solid fa-hotel text-3xl mb-3 block text-slate-350 dark:text-slate-700"></i>
                Không tìm thấy tin đăng nào thỏa mãn bộ lọc.
            </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if($properties->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-955/35 border-t border-slate-100 dark:border-slate-800">
            {{ $properties->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
