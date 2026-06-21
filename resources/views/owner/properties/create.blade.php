@extends('layouts.app')

@section('title', (request()->query('purpose', 'rent') === 'sale' ? 'Đăng Tin Bán Bất Động Sản' : 'Đăng Tin Cho Thuê Bất Động Sản') . ' | BDS Rental')

@section('content')
<div class="bg-slate-50 pt-28 pb-16 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-6 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('properties.choose-type') }}" class="hover:text-primary transition">Đăng tin mới</a>
            <span>/</span>
            <span class="text-slate-800">{{ request()->query('purpose', 'rent') === 'sale' ? 'Đăng tin bán' : 'Đăng tin cho thuê' }}</span>
        </nav>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-6 sm:p-8 text-left">
            @include('owner.properties.create_form')
        </div>
    </div>
</div>
@endsection
