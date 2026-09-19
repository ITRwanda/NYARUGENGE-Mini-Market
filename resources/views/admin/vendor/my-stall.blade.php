@extends('admin.layouts.app')
@section('title', 'My Stall')
@section('subtitle', 'Stall details and live sensor status')

@section('content')

@if(!$vendor)
    <div class="text-center py-20 text-gray-400">
        <i data-feather="alert-circle" class="w-12 h-12 mx-auto mb-3 text-yellow-400"></i>
        <h3 class="text-lg font-semibold text-gray-700 mb-1">No vendor profile linked</h3>
        <p class="text-sm">Contact the administrator to set up your vendor profile.</p>
    </div>
@else

{{-- Vendor info --}}
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-5 flex flex-wrap items-center gap-5">
    <div class="w-14 h-14 rounded-2xl bg-green-600 flex items-center justify-center text-white font-extrabold text-xl shrink-0">
        {{ strtoupper(substr($vendor->business_name ?? 'V', 0, 2)) }}
    </div>
    <div class="flex-1">
        <h2 class="text-lg font-extrabold text-gray-900">{{ $vendor->business_name }}</h2>
        <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-500">
            <span class="flex items-center gap-1"><i data-feather="tag" class="w-3.5 h-3.5"></i>{{ $vendor->vendor_code }}</span>
            <span class="flex items-center gap-1"><i data-feather="map-pin" class="w-3.5 h-3.5"></i>{{ $vendor->market?->name }}</span>
            <span class="flex items-center gap-1"><i data-feather="shopping-bag" class="w-3.5 h-3.5"></i>{{ $vendor->food_category ?? '—' }}</span>
            @if($vendor->phone)<span class="flex items-center gap-1"><i data-feather="phone" class="w-3.5 h-3.5"></i>{{ $vendor->phone }}</span>@endif
        </div>
    </div>
    <span class="badge {{ $vendor->is_active ? 'badge-green' : 'badge-gray' }} text-sm px-3 py-1.5">
        {{ $vendor->is_active ? '✅ Active' : 'Inactive' }}
    </span>
</div>

{{-- Stalls with devices --}}
@forelse($vendor->stalls as $stall)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-lg font-extrabold text-green-700 bg-green-50 px-3 py-1 rounded-xl">
                {{ $stall->stall_number }}
            </span>
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $stall->section ?? 'No section' }}</p>
                <p class="text-xs text-gray-400">{{ $stall->devices->count() }} sensor{{ $stall->devices->count() !== 1 ? 's' : '' }} installed</p>
            </div>
        </div>
        <span class="badge {{ $stall->is_active ? 'badge-green' : 'badge-gray' }}">
            {{ $stall->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>

    @if($stall->devices->count() > 0)
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stall->devices as $device)
        @php
            $reading = $device->sensorReadings->first();
        @endphp
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="font-semibold text-sm text-gray-900">{{ $device->device_name ?? 'Sensor' }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $device->device_uid }}</p>
                </div>
                @if($device->status === 'online')
                    <span class="badge badge-green text-xs"><span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block animate-pulse"></span>Online</span>
                @elseif($device->status === 'offline')
                    <span class="badge badge-red text-xs">Offline</span>
                @else
                    <span class="badge badge-yellow text-xs">{{ ucfirst($device->status) }}</span>
                @endif
            </div>
            @if($reading)
            <div class="grid grid-cols-3 gap-2 text-center mt-2">
                <div class="bg-orange-50 rounded-lg p-2">
                    <p class="text-base font-bold text-orange-600">{{ $reading->temperature ?? '—' }}</p>
                    <p class="text-xs text-gray-400">°C</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-2">
                    <p class="text-base font-bold text-blue-600">{{ $reading->humidity ?? '—' }}</p>
                    <p class="text-xs text-gray-400">%</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-2">
                    <p class="text-base font-bold text-purple-600">{{ $reading->gas_level ?? '—' }}</p>
                    <p class="text-xs text-gray-400">ppm</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 text-right">{{ $reading->recorded_at?->diffForHumans() }}</p>
            @else
            <div class="text-center py-4 text-gray-400 text-xs">No readings yet</div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="px-6 py-6 text-center text-gray-400 text-sm">
        <i data-feather="cpu" class="w-6 h-6 mx-auto mb-1"></i>
        No devices installed in this stall yet.
    </div>
    @endif
</div>
@empty
<div class="text-center py-16 text-gray-400">
    <i data-feather="box" class="w-10 h-10 mx-auto mb-3"></i>
    <p class="text-sm">No stalls assigned to your vendor profile.</p>
</div>
@endforelse

@endif
@endsection
