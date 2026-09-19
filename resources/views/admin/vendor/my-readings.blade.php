@extends('admin.layouts.app')
@section('title', 'My Sensor Data')
@section('subtitle', 'Live readings from your stall devices')

@section('content')

@php
    $last24 = \App\Models\SensorReading::whereIn('stall_id',
        auth()->user()->vendor?->stalls->pluck('id') ?? collect()
    )->where('recorded_at', '>=', now()->subHours(24));
@endphp

{{-- 24h averages --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-orange-50 rounded-2xl p-5 border border-orange-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-orange-600 font-medium">Avg Temperature (24h)</p>
            <p class="text-2xl font-bold text-orange-700 mt-0.5">
                {{ round((clone $last24)->avg('temperature') ?? 0, 1) }}<span class="text-base font-normal">°C</span>
            </p>
        </div>
    </div>

    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 2C8 7 5 10.5 5 14a7 7 0 0014 0c0-3.5-3-7-7-12z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-blue-600 font-medium">Avg Humidity (24h)</p>
            <p class="text-2xl font-bold text-blue-700 mt-0.5">
                {{ round((clone $last24)->avg('humidity') ?? 0, 1) }}<span class="text-base font-normal">%</span>
            </p>
        </div>
    </div>

    <div class="bg-purple-50 rounded-2xl p-5 border border-purple-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-purple-600 font-medium">Avg Gas Level (24h)</p>
            <p class="text-2xl font-bold text-purple-700 mt-0.5">
                {{ round((clone $last24)->avg('gas_level') ?? 0, 1) }}<span class="text-base font-normal"> ppm</span>
            </p>
        </div>
    </div>
</div>

{{-- Readings table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Sensor Reading Log</h3>
        <p class="text-xs text-gray-400 mt-0.5">
            {{ $vendor?->business_name }} &bull; {{ number_format($readings->total()) }} total readings
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Device</th>
                    <th>Stall</th>
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Gas Level</th>
                    <th>Health</th>
                </tr>
            </thead>
            <tbody>
                @forelse($readings as $reading)
                @php
                    $tempOk = $reading->temperature === null || ($reading->temperature >= 5 && $reading->temperature <= 35);
                    $humOk  = $reading->humidity === null   || ($reading->humidity >= 20 && $reading->humidity <= 80);
                    $gasOk  = $reading->gas_level === null  || $reading->gas_level <= 400;
                    $allOk  = $tempOk && $humOk && $gasOk;
                @endphp
                <tr>
                    <td class="whitespace-nowrap">
                        <p class="text-sm font-medium text-gray-800">{{ $reading->recorded_at?->format('M d, H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $reading->recorded_at?->diffForHumans() }}</p>
                    </td>
                    <td>
                        <p class="font-medium text-sm text-gray-900">{{ $reading->device?->device_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $reading->device?->device_uid ?? '' }}</p>
                    </td>
                    <td>
                        <span class="font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-lg text-xs">
                            {{ $reading->stall?->stall_number ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if($reading->temperature !== null)
                            <span class="font-mono font-bold {{ !$tempOk ? 'text-red-600' : 'text-orange-600' }}">
                                {{ $reading->temperature }}°C
                            </span>
                            @if(!$tempOk)<span class="ml-1 text-xs text-red-500">⚠</span>@endif
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        @if($reading->humidity !== null)
                            <span class="font-mono font-bold {{ !$humOk ? 'text-red-600' : 'text-blue-600' }}">
                                {{ $reading->humidity }}%
                            </span>
                            @if(!$humOk)<span class="ml-1 text-xs text-red-500">⚠</span>@endif
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        @if($reading->gas_level !== null)
                            <span class="font-mono font-bold {{ !$gasOk ? 'text-red-600' : 'text-purple-600' }}">
                                {{ $reading->gas_level }} ppm
                            </span>
                            @if(!$gasOk)<span class="ml-1 text-xs text-red-500">⚠</span>@endif
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        @if($allOk)
                            <span class="badge badge-green text-xs">✅ Normal</span>
                        @else
                            <span class="badge badge-red text-xs">⚠️ Breach</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i data-feather="activity" class="w-8 h-8 mx-auto mb-2"></i>
                        <p>No sensor readings yet for your stall.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ number_format($readings->total()) }} total readings</p>
        {{ $readings->links() }}
    </div>
</div>

@endsection
