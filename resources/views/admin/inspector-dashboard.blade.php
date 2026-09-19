@extends('admin.layouts.app')
@section('title', 'Inspector Dashboard')
@section('subtitle', 'Your inspections & assigned stall alerts')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
            <i data-feather="clipboard" class="w-5 h-5 text-blue-600"></i>
        </div>
        <p class="stat-card-value">{{ $stats['total_inspections'] }}</p>
        <p class="text-sm text-gray-500">My Inspections</p>
    </div>

    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center mb-3">
            <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
        </div>
        <p class="stat-card-value text-green-700">{{ $stats['completed_inspections'] }}</p>
        <p class="text-sm text-gray-500">Completed</p>
    </div>

    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center mb-3">
            <i data-feather="clock" class="w-5 h-5 text-yellow-600"></i>
        </div>
        <p class="stat-card-value text-yellow-700">{{ $stats['pending_inspections'] }}</p>
        <p class="text-sm text-gray-500">Pending</p>
    </div>

    <div class="stat-card {{ $stats['open_alerts'] > 0 ? 'border-red-100 bg-red-50/40' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $stats['open_alerts'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center mb-3">
            <i data-feather="bell" class="w-5 h-5 {{ $stats['open_alerts'] > 0 ? 'text-red-500' : 'text-gray-400' }}"></i>
        </div>
        <p class="stat-card-value {{ $stats['open_alerts'] > 0 ? 'text-red-700' : '' }}">{{ $stats['open_alerts'] }}</p>
        <p class="text-sm text-gray-500">Open Alerts</p>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">

    {{-- Pass-rate chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="mb-4">
            <h3 class="font-bold text-gray-900">Inspection Pass Rate</h3>
            <p class="text-xs text-gray-400">Last 7 completed inspections</p>
        </div>
        <canvas id="passRateChart" height="130"></canvas>
    </div>

    {{-- Open alerts for my stalls --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-900">Open Alerts — My Stalls</h3>
                <p class="text-xs text-gray-400">From stalls I've inspected</p>
            </div>
            <a href="{{ route('admin.alerts') }}" class="text-xs text-green-600 font-semibold hover:underline">View all</a>
        </div>
        <div class="space-y-2">
            @forelse($recentAlerts as $alert)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-red-50/50 transition-colors">
                <div class="w-2 h-2 rounded-full shrink-0 {{ $alert->severity === 'critical' ? 'bg-red-500' : ($alert->severity === 'warning' ? 'bg-yellow-400' : 'bg-blue-400') }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $alert->message }}</p>
                    <p class="text-xs text-gray-400">Stall {{ $alert->stall?->stall_number ?? '—' }} · {{ $alert->created_at->diffForHumans() }}</p>
                </div>
                <span class="badge {{ $alert->severity === 'critical' ? 'badge-red' : ($alert->severity === 'warning' ? 'badge-yellow' : 'badge-blue') }} text-xs shrink-0">
                    {{ ucfirst($alert->severity) }}
                </span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                <p class="text-sm">No open alerts on your stalls</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Recent inspections --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div>
            <h3 class="font-bold text-gray-900">My Recent Inspections</h3>
            <p class="text-xs text-gray-400">Latest records</p>
        </div>
        <a href="{{ route('admin.inspections.create') }}" class="btn-primary text-xs py-2">
            <i data-feather="plus" class="w-3.5 h-3.5"></i>
            New Inspection
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Market</th>
                    <th>Stall</th>
                    <th>Items</th>
                    <th>Pass Rate</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentInspections as $inspection)
                @php
                    $total    = $inspection->items->count();
                    $passed   = $inspection->items->where('status','pass')->count();
                    $passRate = $total > 0 ? round(($passed/$total)*100) : 0;
                @endphp
                <tr>
                    <td>
                        <p class="font-semibold text-sm">{{ $inspection->inspection_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $inspection->inspection_date->format('H:i') }}</p>
                    </td>
                    <td>{{ $inspection->market?->name ?? '—' }}</td>
                    <td><span class="font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-lg text-xs">{{ $inspection->stall?->stall_number ?? '—' }}</span></td>
                    <td class="text-sm text-gray-600">
                        <span class="text-green-600 font-semibold">{{ $passed }}✓</span>
                        <span class="text-gray-400"> / {{ $total }}</span>
                    </td>
                    <td>
                        @if($total > 0)
                        <div class="flex items-center gap-2">
                            <div class="w-16 h-1.5 bg-gray-100 rounded-full">
                                <div class="h-1.5 rounded-full {{ $passRate >= 80 ? 'bg-green-500' : ($passRate >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                     style="width:{{ $passRate }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $passRate >= 80 ? 'text-green-600' : ($passRate >= 60 ? 'text-yellow-600' : 'text-red-500') }}">{{ $passRate }}%</span>
                        </div>
                        @else <span class="text-gray-300 text-xs">N/A</span> @endif
                    </td>
                    <td>
                        @if($inspection->status === 'completed') <span class="badge badge-green">Completed</span>
                        @elseif($inspection->status === 'pending') <span class="badge badge-yellow">Pending</span>
                        @else <span class="badge badge-orange">Follow-up</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.inspections.show', $inspection) }}"
                           class="text-blue-600 hover:underline text-xs font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-400">No inspections yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = 'Inter, sans-serif';
const data = @json($inspectionChart);
new Chart(document.getElementById('passRateChart'), {
    type: 'bar',
    data: {
        labels: data.map(d => d.label),
        datasets: [{
            label: 'Pass Rate (%)',
            data: data.map(d => d.pass_pct),
            backgroundColor: data.map(d => d.pass_pct >= 80 ? '#22c55e' : d.pass_pct >= 60 ? '#f59e0b' : '#ef4444'),
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 10, padding: 10 }
        },
        scales: {
            x: { grid: { display: false } },
            y: { max: 100, grid: { color: '#f3f4f6' }, ticks: { callback: v => v + '%' } }
        }
    }
});
</script>
@endpush
