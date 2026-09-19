@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Real-time market hygiene overview')

@section('content')

{{-- ===== STAT CARDS ROW ===== --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">

    {{-- Markets --}}
    <div class="stat-card">
        <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-feather="map-pin" class="w-5 h-5 text-green-600"></i>
            </div>
            <span class="badge badge-green">Active</span>
        </div>
        <p class="stat-card-value">{{ $stats['markets'] }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Markets</p>
    </div>

    {{-- Vendors --}}
    <div class="stat-card">
        <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-feather="users" class="w-5 h-5 text-blue-600"></i>
            </div>
        </div>
        <p class="stat-card-value">{{ $stats['vendors'] }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Vendors</p>
    </div>

    {{-- Stalls --}}
    <div class="stat-card">
        <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                <i data-feather="box" class="w-5 h-5 text-purple-600"></i>
            </div>
        </div>
        <p class="stat-card-value">{{ $stats['stalls'] }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Stalls</p>
    </div>

    {{-- Devices --}}
    <div class="stat-card">
        <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                <i data-feather="cpu" class="w-5 h-5 text-teal-600"></i>
            </div>
            <span class="badge badge-green text-xs">{{ $stats['devices_online'] }} online</span>
        </div>
        <p class="stat-card-value">{{ $stats['devices'] }}</p>
        <p class="text-sm text-gray-500 mt-0.5">IoT Devices</p>
    </div>

    {{-- Open Alerts --}}
    <div class="stat-card border-red-100 {{ $stats['open_alerts'] > 0 ? 'bg-red-50/50' : '' }}">
        <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl {{ $stats['open_alerts'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center">
                <i data-feather="bell" class="w-5 h-5 {{ $stats['open_alerts'] > 0 ? 'text-red-600' : 'text-gray-400' }}"></i>
            </div>
            @if($stats['critical_alerts'] > 0)
                <span class="badge badge-red">{{ $stats['critical_alerts'] }} critical</span>
            @endif
        </div>
        <p class="stat-card-value {{ $stats['open_alerts'] > 0 ? 'text-red-700' : '' }}">{{ $stats['open_alerts'] }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Open Alerts</p>
    </div>

</div>

{{-- ===== SENSOR AVERAGES CARDS ===== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    {{-- Temperature --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #f97316, #ef4444);">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
            </svg>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Avg Temperature <span class="text-xs">(24h)</span></p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['avg_temp'] }}<span class="text-base font-medium text-gray-500">°C</span></p>
            <p class="text-xs text-gray-400 mt-0.5">Threshold: 5–35°C</p>
        </div>
    </div>

    {{-- Humidity --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #06b6d4, #3b82f6);">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 2C8 7 5 10.5 5 14a7 7 0 0014 0c0-3.5-3-7-7-12z"/>
            </svg>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Avg Humidity <span class="text-xs">(24h)</span></p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['avg_humidity'] }}<span class="text-base font-medium text-gray-500">%</span></p>
            <p class="text-xs text-gray-400 mt-0.5">Threshold: 20–80%</p>
        </div>
    </div>

    {{-- Gas Level --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #a855f7, #ec4899);">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Avg Gas Level <span class="text-xs">(24h)</span></p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['avg_gas'] }}<span class="text-base font-medium text-gray-500">ppm</span></p>
            <p class="text-xs text-gray-400 mt-0.5">Max threshold: 400 ppm</p>
        </div>
    </div>

</div>

{{-- ===== CHARTS ROW ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

    {{-- Sensor trend chart (large) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-900">Sensor Readings — 7 Day Trend</h3>
                <p class="text-xs text-gray-400 mt-0.5">Daily averages across all active devices</p>
            </div>
            <div class="flex gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>Temp (°C)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>Humidity (%)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-purple-400 inline-block"></span>Gas (ppm)</span>
            </div>
        </div>
        <canvas id="sensorTrendChart" height="110"></canvas>
    </div>

    {{-- Device status doughnut --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="mb-5">
            <h3 class="font-bold text-gray-900">Device Status</h3>
            <p class="text-xs text-gray-400 mt-0.5">Current connectivity overview</p>
        </div>
        <div class="flex justify-center">
            <div class="w-48 h-48">
                <canvas id="deviceStatusChart"></canvas>
            </div>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>Online</span>
                <span class="font-semibold text-gray-900">{{ $deviceStatus['online'] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Offline</span>
                <span class="font-semibold text-gray-900">{{ $deviceStatus['offline'] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>Maintenance</span>
                <span class="font-semibold text-gray-900">{{ $deviceStatus['maintenance'] }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ===== ALERTS + INSPECTION + MARKETS ROW ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

    {{-- Alert chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="mb-5">
            <h3 class="font-bold text-gray-900">Alerts — 7 Days</h3>
            <p class="text-xs text-gray-400 mt-0.5">Open vs resolved alerts per day</p>
        </div>
        <canvas id="alertChart" height="160"></canvas>
    </div>

    {{-- Inspection donut --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="mb-5">
            <h3 class="font-bold text-gray-900">Inspections Status</h3>
            <p class="text-xs text-gray-400 mt-0.5">All-time by status</p>
        </div>
        <div class="flex justify-center">
            <div class="w-40 h-40">
                <canvas id="inspectionChart"></canvas>
            </div>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>Completed</span>
                <span class="font-semibold">{{ $inspectionStats['completed'] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>Pending</span>
                <span class="font-semibold">{{ $inspectionStats['pending'] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>Follow-up</span>
                <span class="font-semibold">{{ $inspectionStats['follow_up'] }}</span>
            </div>
        </div>
    </div>

    {{-- Market overview --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-900">Markets Overview</h3>
                <p class="text-xs text-gray-400 mt-0.5">Assets by market</p>
            </div>
            <a href="{{ route('admin.markets') }}" class="text-xs text-green-600 font-semibold hover:underline">View all</a>
        </div>
        <div class="space-y-3">
            @foreach($markets as $market)
            <div class="p-3 rounded-xl bg-gray-50 hover:bg-green-50/50 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-sm text-gray-900 truncate">{{ $market->name }}</span>
                    <span class="badge badge-green text-xs">Active</span>
                </div>
                <div class="flex gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <i data-feather="users" class="w-3 h-3"></i>
                        {{ $market->vendors_count }} vendors
                    </span>
                    <span class="flex items-center gap-1">
                        <i data-feather="box" class="w-3 h-3"></i>
                        {{ $market->stalls_count }} stalls
                    </span>
                    <span class="flex items-center gap-1">
                        <i data-feather="cpu" class="w-3 h-3"></i>
                        {{ $market->devices_count }} devices
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ===== RECENT ALERTS TABLE ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div>
            <h3 class="font-bold text-gray-900">Recent Open Alerts</h3>
            <p class="text-xs text-gray-400 mt-0.5">Alerts requiring attention</p>
        </div>
        <a href="{{ route('admin.alerts') }}" class="btn-secondary text-xs py-1.5">
            <i data-feather="arrow-right" class="w-3.5 h-3.5"></i>
            View all
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Stall</th>
                    <th>Parameter</th>
                    <th>Measured</th>
                    <th>Threshold</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAlerts as $alert)
                <tr>
                    <td class="font-medium text-gray-900">{{ $alert->device?->device_name ?? '—' }}</td>
                    <td>{{ $alert->stall?->stall_number ?? '—' }}</td>
                    <td class="capitalize">{{ str_replace('_', ' ', $alert->parameter) }}</td>
                    <td class="font-mono font-semibold">{{ $alert->measured_value }}</td>
                    <td class="font-mono text-gray-500">{{ $alert->threshold_value ?? '—' }}</td>
                    <td>
                        @if($alert->severity === 'critical')
                            <span class="badge badge-red"><span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>Critical</span>
                        @elseif($alert->severity === 'warning')
                            <span class="badge badge-yellow"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 inline-block"></span>Warning</span>
                        @else
                            <span class="badge badge-blue"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>Info</span>
                        @endif
                    </td>
                    <td>
                        @if($alert->status === 'open')
                            <span class="badge badge-red">Open</span>
                        @elseif($alert->status === 'acknowledged')
                            <span class="badge badge-yellow">Acknowledged</span>
                        @else
                            <span class="badge badge-green">Resolved</span>
                        @endif
                    </td>
                    <td class="text-gray-400 text-xs whitespace-nowrap">{{ $alert->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-400">
                        <i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                        <p>No open alerts — all clear!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
const sensorData = @json($sensorChart);
const alertData  = @json($alertChart);

// Chart defaults
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6b7280';

// ── Sensor Trend Line Chart ──────────────────────────────────────────
const sensorCtx = document.getElementById('sensorTrendChart').getContext('2d');
new Chart(sensorCtx, {
    type: 'line',
    data: {
        labels: sensorData.map(d => d.label),
        datasets: [
            {
                label: 'Temperature (°C)',
                data: sensorData.map(d => d.temperature),
                borderColor: '#f97316',
                backgroundColor: 'rgba(249,115,22,0.08)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#f97316',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Humidity (%)',
                data: sensorData.map(d => d.humidity),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.06)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Gas Level (ppm)',
                data: sensorData.map(d => d.gas_level),
                borderColor: '#a855f7',
                backgroundColor: 'rgba(168,85,247,0.06)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#a855f7',
                tension: 0.4,
                fill: true,
            },
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#f9fafb',
                bodyColor: '#d1d5db',
                cornerRadius: 10,
                padding: 12,
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 } } },
        }
    }
});

// ── Device Status Doughnut ──────────────────────────────────────────
const devCtx = document.getElementById('deviceStatusChart').getContext('2d');
new Chart(devCtx, {
    type: 'doughnut',
    data: {
        labels: ['Online', 'Offline', 'Maintenance'],
        datasets: [{
            data: [{{ $deviceStatus['online'] }}, {{ $deviceStatus['offline'] }}, {{ $deviceStatus['maintenance'] }}],
            backgroundColor: ['#22c55e', '#ef4444', '#f59e0b'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 10, padding: 10 }
        }
    }
});

// ── Alert Bar Chart ──────────────────────────────────────────────────
const alertCtx = document.getElementById('alertChart').getContext('2d');
new Chart(alertCtx, {
    type: 'bar',
    data: {
        labels: alertData.map(d => d.label),
        datasets: [
            {
                label: 'Open',
                data: alertData.map(d => d.open),
                backgroundColor: '#ef4444',
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Resolved',
                data: alertData.map(d => d.resolved),
                backgroundColor: '#22c55e',
                borderRadius: 6,
                borderSkipped: false,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 10, padding: 10 }
        },
        scales: {
            x: { stacked: false, grid: { display: false }, ticks: { font: { size: 10 } } },
            y: { grid: { color: '#f3f4f6' }, ticks: { stepSize: 1, font: { size: 11 } } },
        }
    }
});

// ── Inspection Doughnut ──────────────────────────────────────────────
const insCtx = document.getElementById('inspectionChart').getContext('2d');
new Chart(insCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Pending', 'Follow-up'],
        datasets: [{
            data: [{{ $inspectionStats['completed'] }}, {{ $inspectionStats['pending'] }}, {{ $inspectionStats['follow_up'] }}],
            backgroundColor: ['#22c55e', '#f59e0b', '#f97316'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 10, padding: 10 }
        }
    }
});
</script>
@endpush
