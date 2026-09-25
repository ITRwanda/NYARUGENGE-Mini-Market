<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $label }} -- Nyarugenge Mini Market</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 9pt; color: #1f2937; background: #fff; }
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    /* ── Header ─────────────────────────────────── */
    .header {
        background: linear-gradient(135deg, #0a1a0a 0%, #15803d 100%);
        color: white;
        padding: 24px 28px 20px;
        margin-bottom: 0;
    }
    .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .header h1 { font-size: 16pt; font-weight: 800; letter-spacing: -0.5px; }
    .header h2 { font-size: 11pt; font-weight: 600; margin-top: 4px; color: #86efac; }
    .header-meta { font-size: 8pt; color: #bbf7d0; text-align: right; line-height: 1.6; }
    .header-badge {
        display: inline-block; background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        padding: 4px 10px; border-radius: 6px;
        font-size: 8.5pt; color: white; font-weight: 600; margin-top: 8px;
    }

    /* ── Period bar ─────────────────────────────── */
    .period-bar {
        background: #f0fdf4; border-left: 4px solid #16a34a;
        padding: 10px 20px; font-size: 8.5pt; color: #166534;
        display: flex; justify-content: space-between;
    }

    /* ── Section heading ────────────────────────── */
    .section-heading {
        font-size: 9.5pt; font-weight: 700; color: #111827;
        border-bottom: 2px solid #16a34a;
        padding-bottom: 4px; margin: 18px 0 10px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* ── KPI grid ───────────────────────────────── */
    .kpi-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .kpi-card {
        flex: 1 1 calc(16.66% - 8px); min-width: 90px;
        border: 1px solid #e5e7eb; border-radius: 8px;
        padding: 10px 12px; text-align: center;
    }
    .kpi-value { font-size: 14pt; font-weight: 800; color: #111827; }
    .kpi-label { font-size: 7pt; color: #6b7280; margin-top: 2px; }
    .kpi-card.green  { background: #f0fdf4; border-color: #bbf7d0; }
    .kpi-card.orange { background: #fff7ed; border-color: #fed7aa; }
    .kpi-card.blue   { background: #eff6ff; border-color: #bfdbfe; }
    .kpi-card.purple { background: #faf5ff; border-color: #e9d5ff; }
    .kpi-card.red    { background: #fef2f2; border-color: #fecaca; }
    .kpi-card.teal   { background: #f0fdfa; border-color: #99f6e4; }

    /* ── Tables ─────────────────────────────────── */
    table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 7.5pt; }
    thead tr { background: #f9fafb; }
    thead th { padding: 6px 8px; text-align: left; font-weight: 700; color: #374151;
               border-bottom: 2px solid #e5e7eb; font-size: 7pt;
               text-transform: uppercase; letter-spacing: 0.3px; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; color: #374151; }
    tbody tr:nth-child(even) { background: #fafafa; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 12px; font-size: 6.5pt; font-weight: 600; }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-orange { background: #ffedd5; color: #9a3412; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }

    /* ── Stall table ────────────────────────────── */
    .stall-num { background: #f0fdf4; color: #166534; font-weight: 700;
                 padding: 2px 7px; border-radius: 6px; display: inline-block; }
    .bar-wrap { display: inline-block; width: 60px; height: 6px;
                background: #e5e7eb; border-radius: 3px; vertical-align: middle; margin-right: 4px; }
    .bar-fill { height: 6px; border-radius: 3px; display: inline-block; }

    /* ── Alert status ───────────────────────────── */
    .dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 3px; vertical-align: middle; }
    .dot-red    { background: #ef4444; }
    .dot-yellow { background: #f59e0b; }
    .dot-green  { background: #22c55e; }
    .dot-blue   { background: #3b82f6; }

    /* ── Footer ─────────────────────────────────── */
    .footer {
        margin-top: 20px; padding-top: 10px;
        border-top: 1px solid #e5e7eb;
        font-size: 7pt; color: #9ca3af;
        display: flex; justify-content: space-between;
    }

    /* ── Page breaks ────────────────────────────── */
    .page-break { page-break-before: always; }

    .content { padding: 16px 24px 20px; }
</style>
</head>
<body>

{{-- ══ HEADER ═══════════════════════════════════════════════════════ --}}
<div class="header">
    <div class="header-top">
        <div>
            <h1>Nyarugenge Mini Market</h1>
            <h2>Hygiene Monitoring Report</h2>
            <div class="header-badge">{{ strtoupper($type) }} REPORT</div>
        </div>
        <div class="header-meta">
            <div style="font-size:9pt; font-weight:700; color:white;">{{ $label }}</div>
            <div>{{ $start->format('d M Y') }} – {{ $end->format('d M Y') }}</div>
            <div style="margin-top:6px;">Generated by: {{ auth()->user()->name }}</div>
            <div>{{ now()->format('d M Y, H:i') }}</div>
            @if($stall)
            <div style="margin-top:4px; color:#86efac; font-weight:600;">Stall: {{ $stall->stall_number }}</div>
            @endif
        </div>
    </div>
</div>

<div class="period-bar">
    <span><strong>Market:</strong> {{ $market->name }} &nbsp;·&nbsp; {{ $market->district }}, {{ $market->city }}, {{ $market->country }}</span>
    <span><strong>Period:</strong> {{ $start->format('d M Y, H:i') }} – {{ $end->format('d M Y, H:i') }}</span>
</div>

<div class="content">

{{-- ══ KPI CARDS ═════════════════════════════════════════════════════ --}}
<div class="section-heading">Executive Summary</div>
<div class="kpi-grid">
    <div class="kpi-card teal">
        <div class="kpi-value">{{ number_format($summary['total_readings']) }}</div>
        <div class="kpi-label">Total Readings</div>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-value">{{ $summary['avg_temp'] }}°C</div>
        <div class="kpi-label">Avg Temp</div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-value">{{ $summary['avg_humidity'] }}%</div>
        <div class="kpi-label">Avg Humidity</div>
    </div>
    <div class="kpi-card purple">
        <div class="kpi-value">{{ $summary['avg_gas'] }}</div>
        <div class="kpi-label">Avg Gas (ppm)</div>
    </div>
    <div class="kpi-card {{ $summary['total_alerts'] > 0 ? 'red' : 'green' }}">
        <div class="kpi-value">{{ $summary['total_alerts'] }}</div>
        <div class="kpi-label">Total Alerts</div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-value">{{ $summary['avg_pass_rate'] }}%</div>
        <div class="kpi-label">Avg Pass Rate</div>
    </div>
</div>

{{-- Detail metrics --}}
<table style="margin-bottom:16px;">
    <tbody>
        <tr>
            <td style="width:25%; font-weight:600; color:#374151;">Temperature Range</td>
            <td>{{ $summary['min_temp'] }}°C – {{ $summary['max_temp'] }}°C</td>
            <td style="width:25%; font-weight:600; color:#374151;">Humidity Range</td>
            <td>{{ $summary['min_humidity'] }}% – {{ $summary['max_humidity'] }}%</td>
        </tr>
        <tr>
            <td style="font-weight:600; color:#374151;">Open Alerts</td>
            <td>{{ $summary['open_alerts'] }}</td>
            <td style="font-weight:600; color:#374151;">Critical Alerts</td>
            <td>{{ $summary['critical_alerts'] }}</td>
        </tr>
        <tr>
            <td style="font-weight:600; color:#374151;">Total Inspections</td>
            <td>{{ $summary['total_inspections'] }}</td>
            <td style="font-weight:600; color:#374151;">Completed Inspections</td>
            <td>{{ $summary['completed_inspections'] }}</td>
        </tr>
    </tbody>
</table>

{{-- ══ PER-STALL BREAKDOWN ════════════════════════════════════════════ --}}
@if($stallStats->count() > 0)
<div class="section-heading">Per-Stall Performance</div>
<table>
    <thead>
        <tr>
            <th>Stall</th><th>Vendor</th><th>Readings</th>
            <th>Avg Temp</th><th>Avg Humidity</th><th>Avg Gas</th>
            <th>Alerts</th><th>Inspections</th><th>Pass Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stallStats as $s)
        <tr>
            <td><span class="stall-num">{{ $s['stall']->stall_number }}</span></td>
            <td>{{ $s['stall']->vendor?->business_name ?? '—' }}</td>
            <td>{{ $s['readings'] }}</td>
            <td style="{{ $s['avg_temp']>35?'color:#dc2626;font-weight:700;':'' }}">{{ $s['avg_temp'] }}°C</td>
            <td style="{{ $s['avg_humidity']>80?'color:#dc2626;font-weight:700;':'' }}">{{ $s['avg_humidity'] }}%</td>
            <td style="{{ $s['avg_gas']>400?'color:#dc2626;font-weight:700;':'' }}">{{ $s['avg_gas'] }} ppm</td>
            <td>
                @if($s['open_alerts']>0)
                    <span class="badge badge-red">{{ $s['open_alerts'] }} open</span>
                @else
                    <span class="badge badge-green">{{ $s['alerts'] }}</span>
                @endif
            </td>
            <td>{{ $s['inspections'] }}</td>
            <td>
                @if($s['pass_rate']!==null)
                    <span class="bar-wrap"><span class="bar-fill" style="width:{{ $s['pass_rate'] }}%;background:{{ $s['pass_rate']>=80?'#22c55e':($s['pass_rate']>=60?'#f59e0b':'#ef4444') }};"></span></span>
                    <strong style="font-size:7.5pt; color:{{ $s['pass_rate']>=80?'#16a34a':($s['pass_rate']>=60?'#854d0e':'#991b1b') }}">{{ $s['pass_rate'] }}%</strong>
                @else — @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ ALERTS ══════════════════════════════════════════════════════ --}}
@if($alerts->count() > 0)
<div class="page-break"></div>
<div class="section-heading">Alerts ({{ $alerts->count() }})</div>
<table>
    <thead>
        <tr>
            <th>Time</th><th>Stall</th><th>Parameter</th>
            <th>Measured</th><th>Threshold</th><th>Severity</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alerts->take(60) as $a)
        <tr>
            <td>{{ $a->created_at->format('d/m H:i') }}</td>
            <td><span class="stall-num">{{ $a->stall?->stall_number??'—' }}</span></td>
            <td>{{ ucfirst(str_replace('_',' ',$a->parameter)) }}</td>
            <td style="font-weight:700; color:{{ $a->severity==='critical'?'#dc2626':'#d97706' }}">{{ $a->measured_value }}</td>
            <td>{{ $a->threshold_value??'—' }}</td>
            <td>
                @if($a->severity==='critical') <span class="badge badge-red">Critical</span>
                @elseif($a->severity==='warning') <span class="badge badge-yellow">Warning</span>
                @else <span class="badge badge-blue">Info</span> @endif
            </td>
            <td>
                @if($a->status==='open') <span class="badge badge-red">Open</span>
                @elseif($a->status==='acknowledged') <span class="badge badge-yellow">Ack</span>
                @else <span class="badge badge-green">Resolved</span> @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ INSPECTIONS ═════════════════════════════════════════════════ --}}
@if($inspections->count() > 0)
<div class="section-heading">Inspections ({{ $inspections->count() }})</div>
<table>
    <thead>
        <tr>
            <th>Date</th><th>Stall</th><th>Inspector</th>
            <th>Items</th><th>Passed</th><th>Failed</th><th>Pass Rate</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inspections as $ins)
        @php
            $t=$ins->items->count(); $p=$ins->items->where('status','pass')->count();
            $f=$ins->items->where('status','fail')->count();
            $rate=$t>0?round(($p/$t)*100):0;
        @endphp
        <tr>
            <td>{{ $ins->inspection_date->format('d M Y') }}</td>
            <td><span class="stall-num">{{ $ins->stall?->stall_number??'—' }}</span></td>
            <td>{{ $ins->inspector?->name??'—' }}</td>
            <td>{{ $t }}</td>
            <td style="color:#16a34a; font-weight:600;">{{ $p }}</td>
            <td style="{{ $f>0?'color:#dc2626;font-weight:600;':'' }}">{{ $f }}</td>
            <td>
                <span class="bar-wrap"><span class="bar-fill" style="width:{{ $rate }}%;background:{{ $rate>=80?'#22c55e':($rate>=60?'#f59e0b':'#ef4444') }};"></span></span>
                <strong style="font-size:7.5pt; color:{{ $rate>=80?'#16a34a':($rate>=60?'#854d0e':'#991b1b') }}">{{ $rate }}%</strong>
            </td>
            <td>
                @if($ins->status==='completed') <span class="badge badge-green">Completed</span>
                @elseif($ins->status==='pending') <span class="badge badge-yellow">Pending</span>
                @else <span class="badge badge-orange">Follow-up</span> @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══ FOOTER ══════════════════════════════════════════════════════ --}}
<div class="footer">
    <span>Nyarugenge Mini Market — Hygiene Monitoring System &bull; Kigali, Rwanda</span>
    <span>Generated: {{ now()->format('d M Y, H:i:s') }} by {{ auth()->user()->name }}</span>
</div>

{{-- Print toolbar (hidden when printing) --}}
<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:999;display:flex;gap:8px;">
    <button onclick="window.print()"
            style="background:#15803d;color:white;border:none;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
        🖨️ Print / Save as PDF
    </button>
    <button onclick="window.close()"
            style="background:#6b7280;color:white;border:none;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
        ✕ Close
    </button>
</div>

<script>
    // Auto-trigger print dialog after page loads
    window.addEventListener('load', function() {
        setTimeout(function() { window.print(); }, 600);
    });
</script>

</div><!-- /content -->
</body>
</html>
