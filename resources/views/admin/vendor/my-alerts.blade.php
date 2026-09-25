@extends('admin.layouts.app')
@section('title', 'My Alerts')
@section('subtitle', 'Sensor threshold alerts for your stall')

@section('content')

@php
    // $openCount and $criticalCount are passed from controller
    // scoped to this vendor's stalls only — counts across ALL pages
@endphp

{{-- Status banner --}}
@if($openCount > 0)
<div class="mb-5 p-4 {{ $criticalCount > 0 ? 'bg-red-50 border-red-300' : 'bg-orange-50 border-orange-300' }} border rounded-2xl flex items-start gap-3">
    <i data-feather="{{ $criticalCount > 0 ? 'alert-octagon' : 'alert-triangle' }}"
       class="w-5 h-5 shrink-0 {{ $criticalCount > 0 ? 'text-red-500' : 'text-orange-500' }} mt-0.5"></i>
    <div>
        <p class="text-sm font-bold {{ $criticalCount > 0 ? 'text-red-700' : 'text-orange-700' }}">
            You have {{ $openCount }} open alert{{ $openCount !== 1 ? 's' : '' }}
            @if($criticalCount > 0)
                &mdash; including {{ $criticalCount }} critical!
            @endif
        </p>
        <p class="text-xs {{ $criticalCount > 0 ? 'text-red-500' : 'text-orange-500' }} mt-0.5">
            Your sensor readings have exceeded safety thresholds. Contact your market admin immediately.
        </p>
    </div>
</div>
@else
<div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3 text-green-700">
    <i data-feather="check-circle" class="w-5 h-5 shrink-0"></i>
    <p class="text-sm font-semibold">All clear &mdash; no open alerts on your stall right now.</p>
</div>
@endif

{{-- Alert history table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900">Alert History</h3>
            <p class="text-xs text-gray-400 mt-0.5">All sensor alerts for {{ $vendor?->business_name }}</p>
        </div>
        <div class="flex gap-2">
            @if($openCount > 0)
            <span class="badge badge-red text-xs">{{ $openCount }} Open</span>
            @endif
            <span class="text-xs text-gray-400">{{ $alerts->total() }} total</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Device / Stall</th>
                    <th>Parameter</th>
                    <th>Reading</th>
                    <th>Safe Limit</th>
                    <th>Severity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr class="
                    @if($alert->status === 'open' && $alert->severity === 'critical') bg-red-50/50
                    @elseif($alert->status === 'open' && $alert->severity === 'warning') bg-orange-50/40
                    @endif
                ">
                    <td class="whitespace-nowrap">
                        <p class="text-sm font-medium text-gray-800">{{ $alert->created_at->format('M d, H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $alert->created_at->diffForHumans() }}</p>
                    </td>

                    <td>
                        <p class="text-sm font-semibold text-gray-900">{{ $alert->device?->device_name ?? '—' }}</p>
                        <span class="font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-lg text-xs">
                            {{ $alert->stall?->stall_number ?? '—' }}
                        </span>
                    </td>

                    <td>
                        @php
                            $icon = match($alert->parameter) {
                                'temperature' => '🌡️',
                                'humidity'    => '💧',
                                'gas_level'   => '💨',
                                default       => '📊',
                            };
                        @endphp
                        <span class="text-sm font-semibold text-gray-800">
                            {{ $icon }} {{ ucfirst(str_replace('_', ' ', $alert->parameter)) }}
                        </span>
                    </td>

                    <td>
                        @php
                            $unit = match($alert->parameter) {
                                'temperature' => '°C',
                                'humidity'    => '%',
                                'gas_level'   => ' ppm',
                                default       => '',
                            };
                        @endphp
                        <span class="font-mono font-bold text-lg
                            {{ $alert->severity === 'critical' ? 'text-red-600' : 'text-orange-500' }}">
                            {{ $alert->measured_value }}{{ $unit }}
                        </span>
                    </td>

                    <td class="font-mono text-sm text-gray-500">
                        {{ $alert->threshold_value ?? '—' }}{{ $alert->threshold_value ? $unit : '' }}
                    </td>

                    <td>
                        @if($alert->severity === 'critical')
                            <span class="badge badge-red">Critical</span>
                        @elseif($alert->severity === 'warning')
                            <span class="badge badge-yellow">Warning</span>
                        @else
                            <span class="badge badge-blue">Info</span>
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
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i data-feather="bell-off" class="w-8 h-8 mx-auto mb-2"></i>
                        <p>No alerts recorded for your stall yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ $alerts->total() }} total alerts</p>
        {{ $alerts->links() }}
    </div>
</div>

@endsection
