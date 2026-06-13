@extends('layouts.app')

@php
    $isSale = $property->price_label && stripos($property->price_label, 'tháng') === false;
@endphp

@section('title', $isSale ? 'Chỉnh Sửa Tin Đăng Bán | BDS Rental' : 'Chỉnh Sửa Tin Đăng Cho Thuê | BDS Rental')

@section('content')
<div class="bg-slate-50 pt-28 pb-16 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-6 space-x-2 text-left" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="/profile" class="hover:text-primary transition">Trang cá nhân</a>
            <span>/</span>
            <span class="text-slate-800">Chỉnh sửa tin đăng</span>
        </nav>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-xl shadow-slate-100/50 p-6 sm:p-8">
            @include('owner.properties.edit_form')
        </div>
    </div>
</div>
@endsection
