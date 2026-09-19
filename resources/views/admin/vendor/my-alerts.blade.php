@extends('admin.layouts.app')
@section('title', 'My Alerts')
@section('subtitle', 'Alerts for your stall sensors')

@section('content')

@php
    $openCount = $alerts->getCollection()->where('status','open')->count();
@endphp

@if($openCount > 0)
<div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3 text-red-700">
    <i data-feather="alert-triangle" class="w-5 h-5 shrink-0 text-red-500"></i>
    <p class="text-sm font-semibold">You have <strong>{{ $openCount }}</strong> open alert{{ $openCount !== 1 ? 's' : '' }} that need attention. Contact your market admin.</p>
</div>
@else
<div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3 text-green-700">
    <i data-feather="check-circle" class="w-5 h-5 shrink-0"></i>
    <p class="text-sm font-semibold">All clear — no open alerts on your stall.</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Alert History</h3>
        <p class="text-xs text-gray-400 mt-0.5">All sensor alerts for {{ $vendor?->business_name }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Device</th>
                    <th>Stall</th>
                    <th>Parameter</th>
                    <th>Reading</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr class="{{ $alert->severity === 'critical' && $alert->status === 'open' ? 'bg-red-50/40' : '' }}">
                    <td class="text-xs text-gray-500 whitespace-nowrap">
                        {{ $alert->created_at->format('M d, H:i') }}<br>
                        <span class="text-gray-400">{{ $alert->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="text-sm font-medium text-gray-900">{{ $alert->device?->device_name ?? '—' }}</td>
                    <td><span class="font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-lg text-xs">{{ $alert->stall?->stall_number ?? '—' }}</span></td>
                    <td class="text-sm capitalize">{{ str_replace('_',' ',$alert->parameter) }}</td>
                    <td class="font-mono font-bold text-sm {{ $alert->severity === 'critical' ? 'text-red-600' : 'text-orange-500' }}">
                        {{ $alert->measured_value }}
                    </td>
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
                    <td class="text-xs text-gray-500 max-w-xs truncate">{{ $alert->message }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i data-feather="bell-off" class="w-8 h-8 mx-auto mb-2"></i>
                        <p>No alerts recorded for your stall.</p>
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
