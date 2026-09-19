@extends('admin.layouts.app')
@section('title', 'Alerts')
@section('subtitle', 'Sensor threshold breach notifications')

@section('content')

@php
    $openCount     = \App\Models\Alert::where('status','open')->count();
    $ackCount      = \App\Models\Alert::where('status','acknowledged')->count();
    $resolvedCount = \App\Models\Alert::where('status','resolved')->count();
    $criticalCount = \App\Models\Alert::where('severity','critical')->where('status','open')->count();
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-red-50 rounded-2xl p-4 border border-red-100 text-center">
        <p class="text-2xl font-bold text-red-700">{{ $openCount }}</p>
        <p class="text-xs text-red-500 font-medium mt-0.5">Open</p>
    </div>
    <div class="bg-yellow-50 rounded-2xl p-4 border border-yellow-100 text-center">
        <p class="text-2xl font-bold text-yellow-700">{{ $ackCount }}</p>
        <p class="text-xs text-yellow-600 font-medium mt-0.5">Acknowledged</p>
    </div>
    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 text-center">
        <p class="text-2xl font-bold text-green-700">{{ $resolvedCount }}</p>
        <p class="text-xs text-green-600 font-medium mt-0.5">Resolved</p>
    </div>
    <div class="bg-red-100 rounded-2xl p-4 border border-red-200 text-center">
        <p class="text-2xl font-bold text-red-900">{{ $criticalCount }}</p>
        <p class="text-xs text-red-700 font-medium mt-0.5">Critical Open</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">All Alerts</h3>
        <p class="text-xs text-gray-400 mt-0.5">{{ $alerts->total() }} total — sorted by newest</p>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Device</th>
                    <th>Stall</th>
                    <th>Parameter</th>
                    <th>Measured</th>
                    <th>Threshold</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr class="{{ $alert->severity === 'critical' && $alert->status === 'open' ? 'bg-red-50/40' : '' }}">
                    <td class="text-xs text-gray-500 whitespace-nowrap">
                        {{ $alert->created_at->format('M d, H:i') }}
                    </td>
                    <td class="font-medium text-gray-900 text-sm">{{ $alert->device?->device_name ?? '—' }}</td>
                    <td>
                        <span class="text-xs font-bold bg-green-50 text-green-800 px-2 py-0.5 rounded-lg">
                            {{ $alert->stall?->stall_number ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @php $icons = ['temperature'=>'🌡','humidity'=>'💧','gas_level'=>'💨']; @endphp
                        <span class="text-sm">{{ ($icons[$alert->parameter] ?? '📊') }} {{ ucfirst(str_replace('_',' ',$alert->parameter)) }}</span>
                    </td>
                    <td class="font-mono font-bold text-sm {{ $alert->severity === 'critical' ? 'text-red-600' : 'text-orange-500' }}">
                        {{ $alert->measured_value }}
                    </td>
                    <td class="font-mono text-gray-500 text-xs">{{ $alert->threshold_value ?? '—' }}</td>
                    <td>
                        @if($alert->severity === 'critical') <span class="badge badge-red">🔴 Critical</span>
                        @elseif($alert->severity === 'warning') <span class="badge badge-yellow">⚠️ Warning</span>
                        @else <span class="badge badge-blue">ℹ️ Info</span>
                        @endif
                    </td>
                    <td>
                        @if($alert->status === 'open') <span class="badge badge-red">Open</span>
                        @elseif($alert->status === 'acknowledged') <span class="badge badge-yellow">Acknowledged</span>
                        @else <span class="badge badge-green">Resolved</span>
                        @endif
                    </td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($alert->status === 'open')
                            <form method="POST" action="{{ route('admin.alerts.acknowledge', $alert) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs text-yellow-700 hover:text-yellow-900 font-semibold px-2.5 py-1.5 rounded-lg hover:bg-yellow-50 transition-colors whitespace-nowrap">
                                    Acknowledge
                                </button>
                            </form>
                            @endif
                            @if($alert->status !== 'resolved')
                            <form method="POST" action="{{ route('admin.alerts.resolve', $alert) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs text-green-700 hover:text-green-900 font-semibold px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                    Resolve
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-12 text-gray-400">
                        <i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                        <p>No alerts found — all systems normal.</p>
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
