<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Thuê Bất Động Sản Giá Tốt | Kênh Cho Thuê Nhà Đất Số 1 Việt Nam')</title>
    <meta name="description" content="@yield('meta_description', 'Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn, mặt bằng kinh doanh cho thuê uy tín, cập nhật liên tục với bộ lọc giá, diện tích thông minh.')">
    <meta name="keywords" content="cho thuê nhà, thuê căn hộ, thuê phòng trọ, thuê mặt bằng, tìm nhà đất, bất động sản">
    <meta name="robots" content="index, follow">
    <meta name="author" content="BDS Rental">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Thuê Bất Động Sản Giá Tốt | Kênh Cho Thuê Nhà Đất Số 1 Việt Nam')">
    <meta property="og:description" content="@yield('meta_description', 'Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn, mặt bằng kinh doanh cho thuê uy tín.')">
    <meta property="og:image" content="{{ asset('images/hero_bg.png') }}">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite Assets (Tailwind CSS, JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS for Interactive UI Elements (Mobile Menu, Tabs, Dropdowns) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="flex flex-col min-h-full font-sans antialiased text-slate-800">

    <!-- Header / Navbar Component -->
    @if(!isset($hideNavbar) || !$hideNavbar)
        @include('components.navbar')
    @endif

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Component -->
    @if(!isset($hideFooter) || !$hideFooter)
        @include('components.footer')
    @endif

    @stack('scripts')
</body>
</html>
