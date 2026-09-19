@extends('admin.layouts.app')
@section('title', 'My Dashboard')
@section('subtitle', 'Live sensor data for your stall')

@section('content')

{{-- Vendor identity banner --}}
<div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-6 flex items-center gap-5">
    <div class="w-14 h-14 rounded-2xl bg-green-600 flex items-center justify-center text-white font-extrabold text-xl shrink-0">
        {{ strtoupper(substr($vendor->business_name ?? 'V', 0, 2)) }}
    </div>
    <div class="flex-1">
        <h2 class="text-lg font-extrabold text-gray-900">{{ $vendor->business_name ?? 'My Business' }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">
            <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $vendor->vendor_code }}</span>
            &bull; {{ $vendor->market?->name ?? '—' }} &bull; {{ $vendor->food_category ?? '—' }}
        </p>
    </div>
    <div class="hidden md:flex gap-4 text-center">
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['stalls'] }}</p>
            <p class="text-xs text-gray-400">Stalls</p>
        </div>
        <div class="border-l border-gray-100 pl-4">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['devices'] }}</p>
            <p class="text-xs text-gray-400">Sensors</p>
        </div>
        <div class="border-l border-gray-100 pl-4">
            <p class="text-2xl font-bold {{ $stats['open_alerts'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $stats['open_alerts'] }}</p>
            <p class="text-xs text-gray-400">Open Alerts</p>
        </div>
    </div>
</div>

{{-- Latest readings --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @forelse($latestReadings as $reading)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="font-bold text-sm text-gray-900">{{ $reading->device?->device_name ?? 'Sensor' }}</p>
                <p class="text-xs text-gray-400">Stall {{ $reading->stall?->stall_number ?? '—' }}</p>
            </div>
            <span class="badge {{ $reading->device?->status === 'online' ? 'badge-green' : 'badge-red' }} text-xs">
                <span class="w-1.5 h-1.5 rounded-full inline-block {{ $reading->device?->status === 'online' ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                {{ ucfirst($reading->device?->status ?? 'unknown') }}
            </span>
        </div>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-orange-50 rounded-xl p-3">
                <p class="text-lg font-bold text-orange-600">{{ $reading->temperature ?? '—' }}<span class="text-xs font-normal">°C</span></p>
                <p class="text-xs text-gray-400 mt-0.5">Temp</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-3">
                <p class="text-lg font-bold text-blue-600">{{ $reading->humidity ?? '—' }}<span class="text-xs font-normal">%</span></p>
                <p class="text-xs text-gray-400 mt-0.5">Humidity</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-3">
                <p class="text-lg font-bold text-purple-600">{{ $reading->gas_level ?? '—' }}<span class="text-xs font-normal text-gray-400">ppm</span></p>
                <p class="text-xs text-gray-400 mt-0.5">Gas</p>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3 text-right">Last update: {{ $reading->recorded_at?->diffForHumans() ?? '—' }}</p>
    </div>
    @empty
    <div class="col-span-3 text-center py-10 text-gray-400 bg-white rounded-2xl border border-gray-100">
        <i data-feather="cpu" class="w-8 h-8 mx-auto mb-2"></i>
        <p>No sensor data yet. Make sure your devices are active.</p>
    </div>
    @endforelse
</div>

{{-- Sensor trend --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">
    <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-900">24-Hour Sensor Trend</h3>
                <p class="text-xs text-gray-400">Averages for your stall devices</p>
            </div>
            <a href="{{ route('admin.my-readings') }}" class="btn-secondary text-xs py-1.5">Full history</a>
        </div>
        <canvas id="vendorSensorChart" height="110"></canvas>
    </div>

    {{-- Recent alerts --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900">Recent Alerts</h3>
            <a href="{{ route('admin.my-alerts') }}" class="text-xs text-green-600 font-semibold hover:underline">View all</a>
        </div>
        <div class="space-y-2">
            @forelse($recentAlerts as $alert)
            <div class="p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex items-start gap-2">
                    <div class="w-2 h-2 rounded-full mt-1.5 shrink-0
                        {{ $alert->severity === 'critical' ? 'bg-red-500' : ($alert->severity === 'warning' ? 'bg-yellow-400' : 'bg-blue-400') }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $alert->message }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $alert->created_at->diffForHumans() }}</p>
                    </div>
                    @if($alert->status === 'open')
                        <span class="badge badge-red text-xs shrink-0">Open</span>
                    @else
                        <span class="badge badge-green text-xs shrink-0">Resolved</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                <p class="text-sm">No active alerts</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = 'Inter, sans-serif';
const data = @json($sensorChart);
new Chart(document.getElementById('vendorSensorChart'), {
    type: 'line',
    data: {
        labels: data.map(d => d.label),
        datasets: [
            { label: 'Temp (°C)', data: data.map(d => d.temperature), borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.07)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 },
            { label: 'Humidity (%)', data: data.map(d => d.humidity), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.05)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 },
            { label: 'Gas (ppm)', data: data.map(d => d.gas_level), borderColor: '#a855f7', backgroundColor: 'rgba(168,85,247,0.05)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 },
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 10, padding: 10 }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            y: { grid: { color: '#f3f4f6' } }
        }
    }
});
</script>
@endpush
