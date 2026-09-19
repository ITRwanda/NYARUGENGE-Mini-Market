@extends('admin.layouts.app')
@section('title', 'Sensor Readings')
@section('subtitle', 'Live IoT data from all devices')

@section('content')

{{-- Live summary --}}
@php
    $latest = \App\Models\SensorReading::latest('recorded_at')->first();
    $last24 = \App\Models\SensorReading::where('recorded_at','>=', now()->subHours(24));
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-900">{{ number_format($readings->total()) }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Total Records</p>
    </div>
    <div class="bg-orange-50 rounded-2xl p-5 border border-orange-100 shadow-sm text-center">
        <p class="text-2xl font-bold text-orange-600">{{ round((clone $last24)->avg('temperature') ?? 0, 1) }}°C</p>
        <p class="text-xs text-orange-500 mt-0.5">Avg Temp (24h)</p>
    </div>
    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 shadow-sm text-center">
        <p class="text-2xl font-bold text-blue-600">{{ round((clone $last24)->avg('humidity') ?? 0, 1) }}%</p>
        <p class="text-xs text-blue-500 mt-0.5">Avg Humidity (24h)</p>
    </div>
    <div class="bg-purple-50 rounded-2xl p-5 border border-purple-100 shadow-sm text-center">
        <p class="text-2xl font-bold text-purple-600">{{ round((clone $last24)->avg('gas_level') ?? 0, 1) }} ppm</p>
        <p class="text-xs text-purple-500 mt-0.5">Avg Gas (24h)</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900">Readings Log</h3>
            <p class="text-xs text-gray-400 mt-0.5">Most recent first</p>
        </div>
        @if($latest)
        <div class="text-xs text-gray-500 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-green-500 inline-block animate-pulse"></span>
            Last received: {{ $latest->recorded_at?->diffForHumans() }}
        </div>
        @endif
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
                    $humOk  = $reading->humidity === null || ($reading->humidity >= 20 && $reading->humidity <= 80);
                    $gasOk  = $reading->gas_level === null || $reading->gas_level <= 400;
                    $allOk  = $tempOk && $humOk && $gasOk;
                @endphp
                <tr>
                    <td class="whitespace-nowrap">
                        <p class="text-sm font-medium text-gray-900">{{ $reading->recorded_at?->format('M d, H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $reading->recorded_at?->diffForHumans() }}</p>
                    </td>
                    <td>
                        <p class="text-sm font-semibold text-gray-900">{{ $reading->device?->device_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $reading->device?->device_uid ?? '' }}</p>
                    </td>
                    <td>
                        <span class="text-xs font-bold bg-green-50 text-green-800 px-2 py-0.5 rounded-lg">
                            {{ $reading->stall?->stall_number ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if($reading->temperature !== null)
                        <span class="font-mono font-bold {{ !$tempOk ? 'text-red-600' : 'text-orange-600' }}">
                            {{ $reading->temperature }}°C
                        </span>
                        @if(!$tempOk) <span class="ml-1 text-xs text-red-500">⚠</span> @endif
                        @else <span class="text-gray-300">—</span> @endif
                    </td>
                    <td>
                        @if($reading->humidity !== null)
                        <span class="font-mono font-bold {{ !$humOk ? 'text-red-600' : 'text-blue-600' }}">
                            {{ $reading->humidity }}%
                        </span>
                        @if(!$humOk) <span class="ml-1 text-xs text-red-500">⚠</span> @endif
                        @else <span class="text-gray-300">—</span> @endif
                    </td>
                    <td>
                        @if($reading->gas_level !== null)
                        <span class="font-mono font-bold {{ !$gasOk ? 'text-red-600' : 'text-purple-600' }}">
                            {{ $reading->gas_level }} ppm
                        </span>
                        @if(!$gasOk) <span class="ml-1 text-xs text-red-500">⚠</span> @endif
                        @else <span class="text-gray-300">—</span> @endif
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
                        <p>No sensor readings yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ number_format($readings->total()) }} readings total</p>
        {{ $readings->links() }}
    </div>
</div>

@endsection
