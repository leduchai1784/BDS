@extends('layouts.admin')

@section('title', 'Báo Cáo Thống Kê')
@section('breadcrumb', 'Báo cáo thống kê')

@section('content')
<div class="space-y-8">
    <!-- Page Header Info -->
    <div class="text-left">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Thống kê hoạt động hệ thống</h2>
        <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-semibold">Theo dõi sự tăng trưởng và tương tác của người dùng trong 6 tháng gần nhất.</p>
    </div>

    <!-- Charts Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Chart 1: Properties Growth -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm p-6 flex flex-col justify-between text-left">
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/20 text-emerald-500 dark:text-emerald-400 flex items-center justify-center">
                        <i class="fa-solid fa-hotel text-xs"></i>
                    </div>
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-200 tracking-wider uppercase">Tin đăng mới theo tháng</h3>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-1">Biểu thị số lượng tin đăng bất động sản mới được cập nhật lên hệ thống.</p>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="propertiesChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Users Growth -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm p-6 flex flex-col justify-between text-left">
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/20 text-blue-500 dark:text-blue-400 flex items-center justify-center">
                        <i class="fa-solid fa-users text-xs"></i>
                    </div>
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-200 tracking-wider uppercase">Thành viên mới đăng ký</h3>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-1">Số lượng khách thuê và chủ nhà đăng ký tài khoản mới.</p>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="usersChart"></canvas>
            </div>
        </div>

        <!-- Chart 3: Appointments Statistics -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm p-6 flex flex-col justify-between lg:col-span-2 text-left">
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-950/20 text-violet-500 dark:text-violet-400 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-xs"></i>
                    </div>
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-200 tracking-wider uppercase">Lịch hẹn xem phòng phát sinh</h3>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-1">Thống kê số cuộc hẹn kết nối giữa khách thuê và chủ nhà hàng tháng.</p>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Stats Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top 10 BĐS nhiều lượt xem -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider"><i class="fa-solid fa-fire mr-1.5 text-amber-500 animate-pulse"></i>Top 10 Bất động sản nhiều lượt xem nhất</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs text-slate-650 dark:text-slate-300 font-semibold">
                    <thead class="bg-slate-50 dark:bg-slate-955/50 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-5 py-3">Tiêu đề tin</th>
                            <th scope="col" class="px-5 py-3">Chủ nhà</th>
                            <th scope="col" class="px-5 py-3">Lượt xem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($topProperties as $p)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 max-w-[240px] truncate">
                                <a href="{{ route('admin.properties.show', $p->id) }}" class="text-slate-800 dark:text-slate-200 font-bold hover:text-primary transition truncate block leading-tight">
                                    {{ $p->title }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 truncate text-slate-600 dark:text-slate-400">{{ $p->agent->name }}</td>
                            <td class="px-5 py-3.5 font-mono font-bold text-primary"><i class="fa-solid fa-eye mr-1 text-[9px] opacity-75"></i>{{ number_format($p->views) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top 10 chủ nhà đăng nhiều tin nhất -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden text-left">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider"><i class="fa-solid fa-crown mr-1.5 text-indigo-500"></i>Top chủ nhà tích cực nhất</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-xs text-slate-650 dark:text-slate-300 font-semibold">
                    <thead class="bg-slate-50 dark:bg-slate-955/50 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-5 py-3">Chủ nhà</th>
                            <th scope="col" class="px-5 py-3">Liên hệ</th>
                            <th scope="col" class="px-5 py-3">Số lượng tin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($topOwners as $owner)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-slate-200 flex items-center space-x-2.5">
                                <img src="{{ $owner->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($owner->name) . '&background=0077bb&color=fff' }}" class="w-6 h-6 rounded-full object-cover">
                                <span>{{ $owner->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 truncate text-slate-500 dark:text-slate-450">{{ $owner->phone ?? $owner->email }}</td>
                            <td class="px-5 py-3.5 font-mono font-bold text-indigo-500 dark:text-indigo-400">{{ $owner->properties_count }} tin đăng</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartLabels = {!! json_encode($chartLabels) !!};
        const propertiesData = {!! json_encode($propertiesData) !!};
        const usersData = {!! json_encode($usersData) !!};
        const appointmentsData = {!! json_encode($appointmentsData) !!};

        const isDark = () => document.documentElement.classList.contains('dark');
        const getTextColor = () => isDark() ? '#94a3b8' : '#64748b';
        const getGridColor = () => isDark() ? '#334155' : '#f1f5f9';

        // 1. Properties Chart (Emerald Green Bar with Gradients)
        const ctxProperties = document.getElementById('propertiesChart').getContext('2d');
        const gradProperties = ctxProperties.createLinearGradient(0, 0, 0, 240);
        gradProperties.addColorStop(0, 'rgba(16, 185, 129, 0.85)');
        gradProperties.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

        const propertiesChart = new Chart(ctxProperties, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Tin đăng mới',
                    data: propertiesData,
                    backgroundColor: gradProperties,
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: getTextColor(), font: { size: 10, weight: 'bold', family: 'Outfit' } } },
                    y: { grid: { color: getGridColor() }, min: 0, ticks: { color: getTextColor(), precision: 0, font: { size: 10, family: 'Outfit' } } }
                }
            }
        });

        // 2. Users Chart (Blue Smooth Line Chart with Area Fill)
        const ctxUsers = document.getElementById('usersChart').getContext('2d');
        const gradUsers = ctxUsers.createLinearGradient(0, 0, 0, 240);
        gradUsers.addColorStop(0, 'rgba(59, 130, 246, 0.45)');
        gradUsers.addColorStop(1, 'rgba(59, 130, 246, 0.01)');

        const usersChart = new Chart(ctxUsers, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Thành viên mới',
                    data: usersData,
                    fill: true,
                    backgroundColor: gradUsers,
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 3,
                    tension: 0.35,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: getTextColor(), font: { size: 10, weight: 'bold', family: 'Outfit' } } },
                    y: { grid: { color: getGridColor() }, min: 0, ticks: { color: getTextColor(), precision: 0, font: { size: 10, family: 'Outfit' } } }
                }
            }
        });

        // 3. Appointments Chart (Violet Smooth Line + Points with high visibility)
        const ctxAppointments = document.getElementById('appointmentsChart').getContext('2d');
        const gradAppointments = ctxAppointments.createLinearGradient(0, 0, 0, 260);
        gradAppointments.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
        gradAppointments.addColorStop(1, 'rgba(139, 92, 246, 0.02)');

        const appointmentsChart = new Chart(ctxAppointments, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Lịch hẹn mới',
                    data: appointmentsData,
                    fill: true,
                    backgroundColor: gradAppointments,
                    borderColor: 'rgb(139, 92, 246)',
                    borderWidth: 3,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgb(139, 92, 246)',
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: getTextColor(), font: { size: 10, weight: 'bold', family: 'Outfit' } } },
                    y: { grid: { color: getGridColor() }, min: 0, ticks: { color: getTextColor(), precision: 0, font: { size: 10, family: 'Outfit' } } }
                }
            }
        });

        // Watch for Dark Mode class mutation to update charts text & grid colors
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const textColor = getTextColor();
                    const gridColor = getGridColor();
                    
                    [propertiesChart, usersChart, appointmentsChart].forEach(chart => {
                        chart.options.scales.x.ticks.color = textColor;
                        chart.options.scales.y.grid.color = gridColor;
                        chart.options.scales.y.ticks.color = textColor;
                        chart.update();
                    });
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    });
</script>
@endpush
