<!-- SIDEBAR CSS OVERRIDES FOR ROBUST DESKTOP FLOW -->
<style>
    @media (min-width: 768px) {
        .admin-sidebar {
            position: sticky !important;
            top: 4rem !important;
            height: calc(100vh - 4rem) !important;
            bottom: auto !important;
            left: auto !important;
            transform: none !important;
        }
    }
</style>

<nav 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="admin-sidebar fixed md:sticky md:top-16 md:bottom-auto md:left-auto inset-y-0 left-0 w-64 md:h-[calc(100vh-4rem)] bg-slate-900 text-slate-300 z-35 transition-transform duration-300 ease-in-out flex flex-col justify-between flex-shrink-0 border-r border-slate-800 shadow-xl overflow-y-auto"
>
    <div class="flex flex-col">
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950">
            <a href="/admin" class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                    <i class="fa-solid fa-house-chimney text-sm"></i>
                </div>
                <span class="font-extrabold text-lg text-white tracking-wider">
                    BDS<span class="text-primary">Admin</span>
                </span>
            </a>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white focus:outline-none cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 space-y-1.5 flex-1 text-left">
            <a 
                href="{{ route('admin.dashboard') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition {{ Request::routeIs('admin.dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' }}"
            >
                <i class="fa-solid fa-chart-line text-sm"></i>
                <span>Dashboard</span>
            </a>
            
            <a 
                href="{{ route('admin.users.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition {{ Request::routeIs('admin.users.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' }}"
            >
                <i class="fa-solid fa-users text-sm"></i>
                <span>Quản lý thành viên</span>
            </a>

            <a 
                href="{{ route('admin.properties.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition {{ Request::routeIs('admin.properties.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' }}"
            >
                <i class="fa-solid fa-hotel text-sm"></i>
                <span>Quản lý tin đăng</span>
            </a>

            <a 
                href="{{ route('admin.appointments.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition {{ Request::routeIs('admin.appointments.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' }}"
            >
                <i class="fa-solid fa-calendar-days text-sm"></i>
                <span>Quản lý lịch hẹn</span>
            </a>

            <a 
                href="{{ route('admin.categories.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition {{ Request::routeIs('admin.categories.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' }}"
            >
                <i class="fa-solid fa-tags text-sm"></i>
                <span>Quản lý danh mục</span>
            </a>

            <a 
                href="{{ route('admin.reports') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition {{ Request::routeIs('admin.reports') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-800 hover:text-white' }}"
            >
                <i class="fa-solid fa-chart-pie text-sm"></i>
                <span>Báo cáo thống kê</span>
            </a>
        </nav>
    </div>

</nav>
