@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục BĐS')
@section('breadcrumb', 'Danh mục')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="text-left">
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Danh sách danh mục</h1>
            <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Tạo mới, chỉnh sửa hoặc xóa các danh mục cho thuê bất động sản chính chủ.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-xs font-extrabold rounded-xl text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/25 hover:shadow-primary/35 transform hover:-translate-y-0.5 transition duration-200 whitespace-nowrap cursor-pointer">
            <i class="fa-solid fa-circle-plus mr-1.5 text-sm"></i> Thêm danh mục mới
        </a>
    </div>

    <!-- Categories Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
        <div class="overflow-x-auto">
            @if($categories->count() > 0)
            <table class="min-w-full text-left text-xs text-slate-600 dark:text-slate-300 font-semibold">
                <thead class="bg-slate-50 dark:bg-slate-955/50 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-4">ID</th>
                        <th scope="col" class="px-6 py-4">Tên danh mục</th>
                        <th scope="col" class="px-6 py-4">Đường dẫn thân thiện (slug)</th>
                        <th scope="col" class="px-6 py-4">Mô tả</th>
                        <th scope="col" class="px-6 py-4">Số tin đăng</th>
                        <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4.5 text-slate-400 dark:text-slate-550">#{{ $cat->id }}</td>
                        <td class="px-6 py-4.5 font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $cat->name }}</td>
                        <td class="px-6 py-4.5 font-mono text-[10px] text-slate-450 dark:text-slate-500">{{ $cat->slug }}</td>
                        <td class="px-6 py-4.5 text-slate-500 dark:text-slate-400 font-medium max-w-[280px] truncate" title="{{ $cat->description }}">{{ $cat->description ?? 'Không có mô tả' }}</td>
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-300 font-bold text-[10px]">
                                {{ $cat->properties_count }} tin
                            </span>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Edit -->
                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-primary dark:hover:text-primary border border-slate-200 dark:border-slate-700 flex items-center justify-center transition" title="Chỉnh sửa danh mục">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>

                                <!-- Delete -->
                                <form id="delete-cat-form-{{ $cat->id }}" action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="button" 
                                        @click="$root.triggerConfirm('Xóa danh mục', 'Bạn có chắc chắn muốn xóa danh mục {{ addslashes($cat->name) }}? Bất động sản trong danh mục này sẽ tạm thời chuyển thành chưa phân loại.', 'Xóa danh mục', 'bg-red-650 hover:bg-red-750', () => { document.getElementById('delete-cat-form-{{ $cat->id }}').submit(); })"
                                        class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/45 border border-red-200 dark:border-red-900/40 flex items-center justify-center transition cursor-pointer" 
                                        title="Xóa danh mục"
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
            <div class="py-16 text-center text-slate-400 dark:text-slate-500 font-semibold">
                <i class="fa-solid fa-tags text-3xl mb-3 block text-slate-350 dark:text-slate-750"></i>
                Chưa có danh mục nào được tạo.
            </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-955/35 border-t border-slate-100 dark:border-slate-800">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
