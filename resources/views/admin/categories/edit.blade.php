@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Danh Mục')
@section('breadcrumb', 'Chỉnh sửa danh mục')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-primary transition">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Form Container -->
    <div class="max-w-xl bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left p-6 sm:p-8">
        <div class="mb-6">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight">Chỉnh sửa thông tin danh mục</h3>
            <p class="text-[11px] text-slate-400 dark:text-slate-450 mt-1 font-semibold">Cập nhật thông tin của danh mục bất động sản.</p>
        </div>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            
            <!-- Category Name -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 px-1">Tên danh mục</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $category->name) }}" 
                    required 
                    placeholder="Ví dụ: Chung cư mini, Căn hộ dịch vụ..."
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition"
                >
                @error('name')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 px-1">Mô tả danh mục</label>
                <textarea 
                    name="description" 
                    rows="4" 
                    placeholder="Nhập mô tả ngắn gọn cho danh mục..."
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-primary focus:bg-white dark:focus:bg-slate-900 rounded-xl text-slate-800 dark:text-white text-xs font-semibold outline-none transition resize-none"
                >{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 mt-6">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-705 border border-slate-200 dark:border-slate-700 rounded-xl transition">
                    Hủy bỏ
                </a>
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 px-5 rounded-xl shadow-md shadow-primary/20 transition cursor-pointer">
                    Cập nhật danh mục
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
