@extends('admin.layouts.app')
@section('title', 'Reports')
@section('subtitle', 'Nyarugenge Mini Market — hygiene & sensor analytics')

@section('content')

{{-- ── Filter bar ─────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.reports') }}"
      class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 mb-6
             flex flex-wrap items-end gap-4">

    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Report Type</label>
        <select name="type"
                class="px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
            @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $val => $lbl)
                <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Date</label>
        <input type="date" name="date" value="{{ $date }}"
               class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Stall (optional)</label>
        <select name="stall_id"
                class="px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 min-w-[160px]">
            <option value="">All Stalls</option>
            @foreach($stalls as $stall)
                <option value="{{ $stall->id }}" {{ (string)$stallId === (string)$stall->id ? 'selected' : '' }}>
                    {{ $stall->stall_number }} — {{ $stall->vendor?->business_name ?? 'Unassigned' }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn-primary">
        <i data-feather="search" class="w-4 h-4"></i>Generate
    </button>

    {{-- Export buttons --}}
    <div class="flex gap-2 ml-auto flex-wrap">
        @php $q = http_build_query(['type'=>$type,'date'=>$date,'stall_id'=>$stallId]); @endphp

        <a href="{{ route('admin.reports.pdf') }}?{{ $q }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl transition-colors">
            <i data-feather="file-text" class="w-3.5 h-3.5"></i>PDF
        </a>

        <a href="{{ route('admin.reports.excel') }}?{{ $q }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-700 hover:bg-green-800 text-white text-xs font-semibold rounded-xl transition-colors">
            <i data-feather="grid" class="w-3.5 h-3.5"></i>Excel
        </a>

        <div class="relative" x-data="{ open: false }">
            <button type="button"
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-colors">
                <i data-feather="download" class="w-3.5 h-3.5"></i>CSV
                <i data-feather="chevron-down" class="w-3 h-3"></i>
            </button>
            <div class="hidden absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-10 min-w-[160px] overflow-hidden">
                <a href="{{ route('admin.reports.csv') }}?{{ $q }}&sheet=readings"
                   class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700">
                    <i data-feather="activity" class="w-3.5 h-3.5"></i>Sensor Readings
                </a>
                <a href="{{ route('admin.reports.csv') }}?{{ $q }}&sheet=alerts"
                   class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700">
                    <i data-feather="bell" class="w-3.5 h-3.5"></i>Alerts
                </a>
                <a href="{{ route('admin.reports.csv') }}?{{ $q }}&sheet=inspections"
                   class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700">
                    <i data-feather="clipboard" class="w-3.5 h-3.5"></i>Inspections
                </a>
            </div>
        </div>
    </div>
</form>

{{-- ── Report header ───────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 mb-5">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-gray-900">{{ $label }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $market->name }} &bull; {{ $start->format('d M Y') }} – {{ $end->format('d M Y') }}
                @if($stallId)
                    &bull; <span class="font-semibold text-green-700">Stall {{ $stalls->firstWhere('id', $stallId)?->stall_number }}</span>
                @endif
            </p>
        </div>
        <p class="text-xs text-gray-400">Generated: {{ now()->format('d M Y H:i') }}</p>
    </div>
</div>

{{-- ── KPI cards ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    @php
        $kpis = [
            ['label' => 'Readings',     'value' => number_format($summary['total_readings']),  'icon' => 'activity',   'color' => 'bg-teal-50 border-teal-100',   'text' => 'text-teal-700'],
            ['label' => 'Avg Temp',     'value' => $summary['avg_temp'].'°C',                   'icon' => 'thermometer','color' => 'bg-orange-50 border-orange-100','text' => 'text-orange-700'],
            ['label' => 'Avg Humidity', 'value' => $summary['avg_humidity'].'%',                'icon' => 'droplet',    'color' => 'bg-blue-50 border-blue-100',   'text' => 'text-blue-700'],
            ['label' => 'Avg Gas',      'value' => $summary['avg_gas'].' ppm',                  'icon' => 'wind',       'color' => 'bg-purple-50 border-purple-100','text' => 'text-purple-700'],
            ['label' => 'Alerts',       'value' => $summary['total_alerts'],                    'icon' => 'bell',       'color' => $summary['total_alerts'] > 0 ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-100', 'text' => $summary['total_alerts'] > 0 ? 'text-red-700' : 'text-gray-500'],
            ['label' => 'Pass Rate',    'value' => $summary['avg_pass_rate'].'%',               'icon' => 'check-circle','color'=> 'bg-green-50 border-green-100', 'text' => 'text-green-700'],
        ];
    @endphp

    @foreach($kpis as $kpi)
    <div class="rounded-2xl border p-4 {{ $kpi['color'] }}">
        <div class="flex items-center justify-between mb-2">
            <i data-feather="{{ $kpi['icon'] }}" class="w-4 h-4 {{ $kpi['text'] }}"></i>
        </div>
        <p class="text-xl font-extrabold {{ $kpi['text'] }}">{{ $kpi['value'] }}</p>
        <p class="text-xs {{ $kpi['text'] }} opacity-75 mt-0.5">{{ $kpi['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Charts row ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">

    {{-- Sensor trend --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-900 text-sm">Sensor Trend</h3>
                <p class="text-xs text-gray-400 mt-0.5">Temperature · Humidity · Gas</p>
            </div>
            <div class="flex gap-3 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>Temp</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-400 inline-block"></span>Humidity</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-purple-400 inline-block"></span>Gas</span>
            </div>
        </div>
        <canvas id="sensorChart" height="120"></canvas>
    </div>

    {{-- Alert trend --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="mb-4">
            <h3 class="font-bold text-gray-900 text-sm">Alert Distribution</h3>
            <p class="text-xs text-gray-400 mt-0.5">By severity &amp; status</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">By Severity</p>
                @php
                    $byS = ['critical'=>$alerts->where('severity','critical')->count(), 'warning'=>$alerts->where('severity','warning')->count(), 'info'=>$alerts->where('severity','info')->count()];
                @endphp
                @foreach($byS as $sev => $cnt)
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 rounded-full inline-block {{ $sev==='critical'?'bg-red-500':($sev==='warning'?'bg-yellow-400':'bg-blue-400') }}"></span>
                    <span class="text-xs text-gray-600 flex-1 capitalize">{{ $sev }}</span>
                    <span class="text-xs font-bold text-gray-900">{{ $cnt }}</span>
                    @if($summary['total_alerts'] > 0)
                    <div class="w-16 h-1.5 bg-gray-100 rounded-full">
                        <div class="h-1.5 rounded-full {{ $sev==='critical'?'bg-red-500':($sev==='warning'?'bg-yellow-400':'bg-blue-400') }}"
                             style="width:{{ round(($cnt/$summary['total_alerts'])*100) }}%"></div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">By Status</p>
                @php
                    $byStatus = ['open'=>$alerts->where('status','open')->count(),'acknowledged'=>$alerts->where('status','acknowledged')->count(),'resolved'=>$alerts->where('status','resolved')->count()];
                @endphp
                @foreach($byStatus as $st => $cnt)
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 rounded-full inline-block {{ $st==='open'?'bg-red-500':($st==='acknowledged'?'bg-yellow-400':'bg-green-500') }}"></span>
                    <span class="text-xs text-gray-600 flex-1 capitalize">{{ $st }}</span>
                    <span class="text-xs font-bold text-gray-900">{{ $cnt }}</span>
                </div>
                @endforeach
                <div class="mt-4 text-center">
                    <canvas id="alertPie" width="100" height="100" class="mx-auto"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Per-stall table ─────────────────────────────────────────────────── --}}
@if($stallStats->count() > 1)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900 text-sm">Per-Stall Breakdown</h3>
        <p class="text-xs text-gray-400 mt-0.5">Performance metrics per stall for this period</p>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Stall</th>
                    <th>Vendor</th>
                    <th>Section</th>
                    <th>Readings</th>
                    <th>Avg Temp</th>
                    <th>Avg Humidity</th>
                    <th>Avg Gas</th>
                    <th>Alerts</th>
                    <th>Inspections</th>
                    <th>Pass Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stallStats as $s)
                <tr>
                    <td><span class="font-bold text-green-800 bg-green-50 px-2 py-0.5 rounded-lg text-xs">{{ $s['stall']->stall_number }}</span></td>
                    <td class="text-sm text-gray-700">{{ $s['stall']->vendor?->business_name ?? '—' }}</td>
                    <td class="text-xs text-gray-500">{{ $s['stall']->section ?? '—' }}</td>
                    <td class="text-sm font-mono text-gray-700">{{ $s['readings'] }}</td>
                    <td class="font-mono text-sm {{ $s['avg_temp'] > 35 ? 'text-red-600 font-bold' : 'text-orange-600' }}">{{ $s['avg_temp'] }}°C</td>
                    <td class="font-mono text-sm {{ $s['avg_humidity'] > 80 ? 'text-red-600 font-bold' : 'text-blue-600' }}">{{ $s['avg_humidity'] }}%</td>
                    <td class="font-mono text-sm {{ $s['avg_gas'] > 400 ? 'text-red-600 font-bold' : 'text-purple-600' }}">{{ $s['avg_gas'] }} ppm</td>
                    <td>
                        @if($s['open_alerts'] > 0)
                            <span class="badge badge-red">{{ $s['open_alerts'] }} open</span>
                        @elseif($s['alerts'] > 0)
                            <span class="badge badge-yellow">{{ $s['alerts'] }}</span>
                        @else
                            <span class="badge badge-green">0</span>
                        @endif
                    </td>
                    <td class="text-sm text-gray-700 text-center">{{ $s['inspections'] }}</td>
                    <td>
                        @if($s['pass_rate'] !== null)
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-1.5 bg-gray-100 rounded-full">
                                <div class="h-1.5 rounded-full {{ $s['pass_rate']>=80?'bg-green-500':($s['pass_rate']>=60?'bg-yellow-500':'bg-red-500') }}"
                                     style="width:{{ $s['pass_rate'] }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $s['pass_rate']>=80?'text-green-600':($s['pass_rate']>=60?'text-yellow-600':'text-red-500') }}">
                                {{ $s['pass_rate'] }}%
                            </span>
                        </div>
                        @else <span class="text-gray-300 text-xs">N/A</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── Recent readings ─────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900 text-sm">Sensor Readings (latest 50)</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ number_format($summary['total_readings']) }} total in this period</p>
        </div>
        <a href="{{ route('admin.reports.csv') }}?{{ $q }}&sheet=readings"
           class="text-xs text-green-600 hover:underline font-semibold">Export CSV</a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th><th>Device</th><th>Stall</th>
                    <th>Temp</th><th>Humidity</th><th>Gas</th><th>Health</th>
                </tr>
            </thead>
            <tbody>
                @forelse($readings->take(50) as $r)
                @php
                    $tempTh = $thresholds['temperature'] ?? null;
                    $humTh  = $thresholds['humidity']    ?? null;
                    $gasTh  = $thresholds['gas_level']   ?? null;
                    $tOk = $r->temperature===null||(($tempTh===null||$tempTh->minimum_value===null||$r->temperature>=$tempTh->minimum_value)&&($tempTh===null||$tempTh->maximum_value===null||$r->temperature<=$tempTh->maximum_value));
                    $hOk = $r->humidity===null||(($humTh===null||$humTh->minimum_value===null||$r->humidity>=$humTh->minimum_value)&&($humTh===null||$humTh->maximum_value===null||$r->humidity<=$humTh->maximum_value));
                    $gOk = $r->gas_level===null||(($gasTh===null||$gasTh->minimum_value===null||$r->gas_level>=$gasTh->minimum_value)&&($gasTh===null||$gasTh->maximum_value===null||$r->gas_level<=$gasTh->maximum_value));
                    $ok  = $tOk&&$hOk&&$gOk;
                @endphp
                <tr>
                    <td class="text-xs text-gray-500 whitespace-nowrap">{{ $r->recorded_at?->format('d/m H:i') }}</td>
                    <td class="text-sm text-gray-700">{{ $r->device?->device_name??'—' }}</td>
                    <td><span class="text-xs font-bold bg-green-50 text-green-800 px-2 py-0.5 rounded-lg">{{ $r->stall?->stall_number??'—' }}</span></td>
                    <td class="font-mono text-sm {{ !$tOk?'text-red-600 font-bold':'text-orange-600' }}">{{ $r->temperature??'—' }}{{ $r->temperature!==null?'°C':'' }}</td>
                    <td class="font-mono text-sm {{ !$hOk?'text-red-600 font-bold':'text-blue-600' }}">{{ $r->humidity??'—' }}{{ $r->humidity!==null?'%':'' }}</td>
                    <td class="font-mono text-sm {{ !$gOk?'text-red-600 font-bold':'text-purple-600' }}">{{ $r->gas_level??'—' }}{{ $r->gas_level!==null?' ppm':'' }}</td>
                    <td>{{ $ok ? '✅' : '⚠️' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-400">No readings for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Recent alerts ───────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900 text-sm">Alerts This Period</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $summary['total_alerts'] }} total</p>
        </div>
        <a href="{{ route('admin.reports.csv') }}?{{ $q }}&sheet=alerts"
           class="text-xs text-green-600 hover:underline font-semibold">Export CSV</a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr><th>Time</th><th>Stall</th><th>Parameter</th><th>Measured</th><th>Severity</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($alerts->take(30) as $a)
                <tr>
                    <td class="text-xs text-gray-500 whitespace-nowrap">{{ $a->created_at->format('d/m H:i') }}</td>
                    <td><span class="text-xs font-bold bg-green-50 text-green-800 px-2 py-0.5 rounded-lg">{{ $a->stall?->stall_number??'—' }}</span></td>
                    <td class="text-sm capitalize">{{ str_replace('_',' ',$a->parameter) }}</td>
                    <td class="font-mono font-bold text-sm {{ $a->severity==='critical'?'text-red-600':'text-orange-500' }}">{{ $a->measured_value }}</td>
                    <td>
                        @if($a->severity==='critical') <span class="badge badge-red">Critical</span>
                        @elseif($a->severity==='warning') <span class="badge badge-yellow">Warning</span>
                        @else <span class="badge badge-blue">Info</span> @endif
                    </td>
                    <td>
                        @if($a->status==='open') <span class="badge badge-red">Open</span>
                        @elseif($a->status==='acknowledged') <span class="badge badge-yellow">Acknowledged</span>
                        @else <span class="badge badge-green">Resolved</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">No alerts for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Inspections ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900 text-sm">Inspections This Period</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $summary['total_inspections'] }} total &bull; avg pass rate: <strong>{{ $summary['avg_pass_rate'] }}%</strong></p>
        </div>
        <a href="{{ route('admin.reports.csv') }}?{{ $q }}&sheet=inspections"
           class="text-xs text-green-600 hover:underline font-semibold">Export CSV</a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr><th>Date</th><th>Stall</th><th>Inspector</th><th>Items</th><th>Pass Rate</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($inspections as $ins)
                @php
                    $t=$ins->items->count(); $p=$ins->items->where('status','pass')->count();
                    $rate=$t>0?round(($p/$t)*100):0;
                @endphp
                <tr>
                    <td class="text-sm text-gray-700 whitespace-nowrap">{{ $ins->inspection_date->format('d M Y') }}</td>
                    <td><span class="text-xs font-bold bg-green-50 text-green-800 px-2 py-0.5 rounded-lg">{{ $ins->stall?->stall_number??'—' }}</span></td>
                    <td class="text-sm text-gray-700">{{ $ins->inspector?->name??'—' }}</td>
                    <td class="text-sm text-gray-600"><span class="text-green-600 font-semibold">{{ $p }}✓</span> / {{ $t }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-14 h-1.5 bg-gray-100 rounded-full"><div class="h-1.5 rounded-full {{ $rate>=80?'bg-green-500':($rate>=60?'bg-yellow-500':'bg-red-500') }}" style="width:{{ $rate }}%"></div></div>
                            <span class="text-xs font-bold {{ $rate>=80?'text-green-600':($rate>=60?'text-yellow-600':'text-red-500') }}">{{ $rate }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($ins->status==='completed') <span class="badge badge-green">Completed</span>
                        @elseif($ins->status==='pending') <span class="badge badge-yellow">Pending</span>
                        @else <span class="badge badge-orange">Follow-up</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">No inspections for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = 'Inter, sans-serif';

const trend = @json($trend);

// Sensor trend line chart
new Chart(document.getElementById('sensorChart'), {
    type: 'line',
    data: {
        labels: trend.map(d => d.label),
        datasets: [
            { label: 'Temp (°C)',   data: trend.map(d => d.temp),     borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.07)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 },
            { label: 'Humidity(%)', data: trend.map(d => d.humidity), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.05)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 },
            { label: 'Gas (ppm)',   data: trend.map(d => d.gas),      borderColor: '#a855f7', backgroundColor: 'rgba(168,85,247,0.05)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 },
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 10, padding: 10 }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            y: { grid: { color: '#f3f4f6' } }
        }
    }
});

// Alert pie
new Chart(document.getElementById('alertPie'), {
    type: 'doughnut',
    data: {
        labels: ['Open', 'Acknowledged', 'Resolved'],
        datasets: [{
            data: [{{ $summary['open_alerts'] }}, {{ $alerts->where('status','acknowledged')->count() }}, {{ $alerts->where('status','resolved')->count() }}],
            backgroundColor: ['#ef4444','#f59e0b','#22c55e'],
            borderWidth: 0,
        }]
    },
    options: {
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#1f2937', cornerRadius: 8, padding: 8 }
        }
    }
});
</script>
@endpush
