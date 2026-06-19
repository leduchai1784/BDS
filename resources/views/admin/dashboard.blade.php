@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header Summary -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Xin chào, {{ Auth::user()->name }}!</h1>
        <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Chào mừng quay trở lại trang quản trị của hệ thống cho thuê bất động sản BDS Rental.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5">
        <!-- Stat item 1 -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center justify-between hover:shadow-md dark:hover:shadow-slate-950 transition duration-200">
            <div class="space-y-1 text-left">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Thành viên</span>
                <span class="text-xl font-black text-slate-800 dark:text-white">{{ $totalAccounts }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/35 text-blue-500 dark:text-blue-400 flex items-center justify-center text-lg shadow-sm">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Stat item 2 -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center justify-between hover:shadow-md dark:hover:shadow-slate-950 transition duration-200">
            <div class="space-y-1 text-left">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Chủ nhà</span>
                <span class="text-xl font-black text-slate-800 dark:text-white">{{ $totalOwners }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/35 text-indigo-500 dark:text-indigo-400 flex items-center justify-center text-lg shadow-sm">
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>

        <!-- Stat item 3 -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center justify-between hover:shadow-md dark:hover:shadow-slate-950 transition duration-200">
            <div class="space-y-1 text-left">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Khách thuê</span>
                <span class="text-xl font-black text-slate-800 dark:text-white">{{ $totalTenants }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/35 text-teal-500 dark:text-teal-400 flex items-center justify-center text-lg shadow-sm">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <!-- Stat item 4 -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center justify-between hover:shadow-md dark:hover:shadow-slate-950 transition duration-200">
            <div class="space-y-1 text-left">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tin đăng BĐS</span>
                <span class="text-xl font-black text-slate-800 dark:text-white">{{ $totalProperties }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/35 text-amber-500 dark:text-amber-400 flex items-center justify-center text-lg shadow-sm">
                <i class="fa-solid fa-hotel"></i>
            </div>
        </div>

        <!-- Stat item 5 -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center justify-between hover:shadow-md dark:hover:shadow-slate-950 transition duration-200">
            <div class="space-y-1 text-left">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lịch hẹn xem</span>
                <span class="text-xl font-black text-slate-800 dark:text-white">{{ $totalAppointments }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/35 text-rose-500 dark:text-rose-400 flex items-center justify-center text-lg shadow-sm">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <!-- Stat item 6 -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center justify-between hover:shadow-md dark:hover:shadow-slate-950 transition duration-200">
            <div class="space-y-1 text-left">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lượt xem tin</span>
                <span class="text-xl font-black text-slate-800 dark:text-white">{{ number_format($totalViews) }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/35 text-purple-500 dark:text-purple-400 flex items-center justify-center text-lg shadow-sm">
                <i class="fa-solid fa-eye"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Tin đăng -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-left">
            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4"><i class="fa-solid fa-hotel mr-1.5 text-primary"></i>Tin đăng theo tháng</h4>
            <div class="h-64">
                <canvas id="chartProperties"></canvas>
            </div>
        </div>
        <!-- Chart Thành viên mới -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-left">
            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4"><i class="fa-solid fa-users mr-1.5 text-indigo-500"></i>Thành viên mới theo tháng</h4>
            <div class="h-64">
                <canvas id="chartUsers"></canvas>
            </div>
        </div>
        <!-- Chart Lịch hẹn -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm text-left">
            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4"><i class="fa-solid fa-calendar-check mr-1.5 text-teal-500"></i>Lịch hẹn theo tháng</h4>
            <div class="h-64">
                <canvas id="chartAppointments"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Pending Listings (Left: 2/3 width) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider"><i class="fa-solid fa-clock-rotate-left mr-2 text-primary"></i>Tin đăng mới chờ duyệt</h3>
                <span class="bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-900/60">
                    {{ count($pendingProperties) }} tin đăng
                </span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 overflow-x-auto">
                @if(count($pendingProperties) > 0)
                <table class="min-w-full text-left text-xs text-slate-600 dark:text-slate-300 font-semibold">
                    <thead class="bg-slate-50 dark:bg-slate-950/50 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4">Bất động sản</th>
                            <th scope="col" class="px-6 py-4">Chủ nhà</th>
                            <th scope="col" class="px-6 py-4">Giá / diện tích</th>
                            <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($pendingProperties as $p)
                        <tr class="hover:bg-slate-50/55 dark:hover:bg-slate-800/40 transition">
                            <!-- Title & Location -->
                            <td class="px-6 py-4.5 max-w-[280px]">
                                <a href="{{ route('admin.properties.show', $p->id) }}" class="block font-bold text-slate-800 dark:text-slate-200 hover:text-primary transition leading-tight truncate">
                                    {{ $p->title }}
                                </a>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-1"><i class="fa-solid fa-location-dot mr-1"></i>{{ $p->location }}</span>
                            </td>
                            <!-- Owner -->
                            <td class="px-6 py-4.5">
                                <span class="block text-slate-800 dark:text-slate-200 font-bold leading-none">{{ $p->agent->name }}</span>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-1">{{ $p->agent->phone }}</span>
                            </td>
                            <!-- Price & Area -->
                            <td class="px-6 py-4.5">
                                <span class="block font-bold text-primary">{{ number_format($p->price / 1000000, 1) }} tr/tháng</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5">{{ $p->area }} m²</span>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Approve Button -->
                                    <form id="approve-form-{{ $p->id }}" action="{{ route('admin.properties.status', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button 
                                            type="button" 
                                            @click="triggerConfirm('Duyệt tin đăng', 'Bạn có chắc muốn phê duyệt tin đăng: {{ addslashes($p->title) }}?', 'Phê duyệt', 'bg-green-500 hover:bg-green-600', () => { document.getElementById('approve-form-{{ $p->id }}').submit(); })"
                                            class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-950/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-950/45 flex items-center justify-center transition border border-green-200 dark:border-green-900/40 cursor-pointer" 
                                            title="Phê duyệt đăng tin"
                                        >
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <!-- Reject Button -->
                                    <form id="reject-form-{{ $p->id }}" action="{{ route('admin.properties.status', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button 
                                            type="button" 
                                            @click="triggerConfirm('Từ chối tin đăng', 'Bạn có chắc muốn từ chối tin đăng: {{ addslashes($p->title) }}?', 'Từ chối', 'bg-red-500 hover:bg-red-600', () => { document.getElementById('reject-form-{{ $p->id }}').submit(); })"
                                            class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/45 flex items-center justify-center transition border border-red-200 dark:border-red-900/40 cursor-pointer" 
                                            title="Từ chối tin đăng"
                                        >
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                    <!-- View Details Button -->
                                    <a href="{{ route('admin.properties.show', $p->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center transition border border-slate-200 dark:border-slate-700" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="py-12 text-center text-slate-400 dark:text-slate-500 font-semibold">
                    <i class="fa-solid fa-folder-open text-2xl mb-2.5 block text-slate-350 dark:text-slate-700"></i>
                    Không có tin đăng nào chờ phê duyệt.
                </div>
                @endif
            </div>
            <div class="px-6 py-4.5 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 text-right">
                <a href="{{ route('admin.properties.index', ['status' => 'pending']) }}" class="text-xs font-bold text-primary hover:underline">
                    Xem tất cả tin chờ duyệt <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Recent Appointments (Right: 1/3 width) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider"><i class="fa-solid fa-calendar-check mr-2 text-primary"></i>Lịch hẹn gần đây</h3>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @if(count($recentAppointments) > 0)
                    @foreach($recentAppointments as $app)
                    <div class="p-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition flex flex-col space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $app->name }}</span>
                            <!-- Status Badges -->
                            @if($app->status === 'confirmed')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900/40">Đã duyệt</span>
                            @elseif($app->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40">Đã hủy</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">Đang chờ</span>
                            @endif
                        </div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold space-y-0.5 leading-relaxed">
                            <p class="truncate"><i class="fa-solid fa-location-dot mr-1 text-slate-350 dark:text-slate-650"></i>{{ $app->property->title }}</p>
                            <p><i class="fa-solid fa-clock mr-1 text-slate-350 dark:text-slate-650"></i>{{ Carbon\Carbon::parse($app->date)->format('d/m/Y') }} lúc {{ $app->time }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="py-12 text-center text-slate-400 dark:text-slate-500 font-semibold">
                    <i class="fa-solid fa-calendar text-2xl mb-2.5 block text-slate-300 dark:text-slate-700"></i>
                    Chưa có lịch hẹn xem nhà nào.
                </div>
                @endif
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('admin.appointments.index') }}" class="text-xs font-bold text-primary hover:underline">
                    Xem tất cả lịch hẹn <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = {!! json_encode($chartLabels) !!};
        const isDark = () => document.documentElement.classList.contains('dark');
        const getTextColor = () => isDark() ? '#94a3b8' : '#64748b';
        const getGridColor = () => isDark() ? '#334155' : '#f1f5f9';

        // Chart Properties
        const chartProperties = new Chart(document.getElementById('chartProperties'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tin đăng mới',
                    data: {!! json_encode($propertiesData) !!},
                    backgroundColor: 'rgba(0, 119, 187, 0.85)',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: getGridColor() },
                        ticks: { color: getTextColor(), stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: getTextColor() }
                    }
                }
            }
        });

        // Chart Users
        const chartUsers = new Chart(document.getElementById('chartUsers'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Người dùng mới',
                    data: {!! json_encode($usersData) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: getGridColor() },
                        ticks: { color: getTextColor(), stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: getTextColor() }
                    }
                }
            }
        });

        // Chart Appointments
        const chartAppointments = new Chart(document.getElementById('chartAppointments'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Lịch hẹn',
                    data: {!! json_encode($appointmentsData) !!},
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: getGridColor() },
                        ticks: { color: getTextColor(), stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: getTextColor() }
                    }
                }
            }
        });

        // Watch for Dark Mode change to update charts text colors
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const textColor = getTextColor();
                    const gridColor = getGridColor();
                    
                    [chartProperties, chartUsers, chartAppointments].forEach(chart => {
                        chart.options.scales.y.grid.color = gridColor;
                        chart.options.scales.y.ticks.color = textColor;
                        chart.options.scales.x.ticks.color = textColor;
                        chart.update();
                    });
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    });
</script>
@endpush
