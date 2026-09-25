@extends('admin.layouts.app')
@section('title', 'My Stalls')
@section('subtitle', 'Live sensor data across all your stalls')

@section('content')

@if(!$vendor)
    @include('admin.vendor.no-profile')
@else

{{-- Vendor banner --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6
            flex flex-wrap items-center gap-4">
    <div class="w-14 h-14 rounded-2xl bg-green-600 flex items-center justify-center
                text-white font-extrabold text-xl shrink-0">
        {{ strtoupper(substr($vendor->business_name ?? 'V', 0, 2)) }}
    </div>
    <div class="flex-1">
        <h2 class="text-lg font-extrabold text-gray-900">{{ $vendor->business_name }}</h2>
        <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-500">
            <span class="flex items-center gap-1">
                <i data-feather="tag" class="w-3.5 h-3.5 text-gray-400"></i>
                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $vendor->vendor_code }}</span>
            </span>
            <span class="flex items-center gap-1">
                <i data-feather="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                {{ $vendor->market?->name }}
            </span>
            <span class="flex items-center gap-1">
                <i data-feather="shopping-bag" class="w-3.5 h-3.5 text-gray-400"></i>
                {{ $vendor->food_category ?? 'General' }}
            </span>
            @if($vendor->phone)
            <span class="flex items-center gap-1">
                <i data-feather="phone" class="w-3.5 h-3.5 text-gray-400"></i>
                {{ $vendor->phone }}
            </span>
            @endif
        </div>
    </div>
    <div class="flex gap-6 text-center">
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $vendor->stalls->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Stalls</p>
        </div>
        <div class="border-l border-gray-100 pl-6">
            <p class="text-2xl font-extrabold text-gray-900">
                {{ $vendor->stalls->sum(fn($s) => $s->devices->count()) }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">Sensors</p>
        </div>
    </div>
</div>

{{-- Stalls --}}
@forelse($vendor->stalls as $stall)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">

    {{-- Stall header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="text-base font-extrabold text-green-800 bg-green-100 border border-green-200
                         px-3 py-1 rounded-xl">
                {{ $stall->stall_number }}
            </span>
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $stall->section ?? 'No section' }}</p>
                @if($stall->description)
                <p class="text-xs text-gray-400">{{ $stall->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500">
                {{ $stall->devices->count() }} sensor{{ $stall->devices->count() !== 1 ? 's' : '' }}
            </span>
            <span class="badge {{ $stall->is_active ? 'badge-green' : 'badge-gray' }}">
                {{ $stall->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    {{-- Devices --}}
    @if($stall->devices->count() > 0)
    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stall->devices as $device)
        @php $reading = $device->latestReading ?? null; @endphp
        <div class="rounded-2xl border border-gray-100 p-5 bg-gray-50 hover:bg-white
                    hover:shadow-md transition-all duration-150">

            {{-- Device header --}}
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="font-bold text-sm text-gray-900">
                        {{ $device->device_name ?? 'Sensor' }}
                    </p>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $device->device_uid }}</p>
                </div>
                @if($device->status === 'online')
                    <span class="badge badge-green text-xs">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block animate-pulse"></span>
                        Online
                    </span>
                @elseif($device->status === 'offline')
                    <span class="badge badge-red text-xs">Offline</span>
                @else
                    <span class="badge badge-yellow text-xs">{{ ucfirst($device->status) }}</span>
                @endif
            </div>

            {{-- Reading values --}}
            @if($reading)
            <div class="grid grid-cols-3 gap-2 text-center">
                @php
                    $tempTh = $thresholds['temperature'] ?? null;
                    $humTh  = $thresholds['humidity']    ?? null;
                    $gasTh  = $thresholds['gas_level']   ?? null;
                    $tempOk = $reading->temperature === null || (
                        ($tempTh===null||$tempTh->minimum_value===null||$reading->temperature>=$tempTh->minimum_value) &&
                        ($tempTh===null||$tempTh->maximum_value===null||$reading->temperature<=$tempTh->maximum_value)
                    );
                    $humOk = $reading->humidity === null || (
                        ($humTh===null||$humTh->minimum_value===null||$reading->humidity>=$humTh->minimum_value) &&
                        ($humTh===null||$humTh->maximum_value===null||$reading->humidity<=$humTh->maximum_value)
                    );
                    $gasOk = $reading->gas_level === null || (
                        ($gasTh===null||$gasTh->minimum_value===null||$reading->gas_level>=$gasTh->minimum_value) &&
                        ($gasTh===null||$gasTh->maximum_value===null||$reading->gas_level<=$gasTh->maximum_value)
                    );
                @endphp

                <div class="rounded-xl p-3 {{ !$tempOk ? 'bg-red-50 border border-red-200' : 'bg-orange-50' }}">
                    <p class="text-lg font-extrabold {{ !$tempOk ? 'text-red-600' : 'text-orange-600' }}">
                        {{ $reading->temperature !== null ? $reading->temperature : '—' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">°C</p>
                    @if(!$tempOk)<p class="text-xs text-red-500 mt-0.5">⚠ High</p>@endif
                </div>

                <div class="rounded-xl p-3 {{ !$humOk ? 'bg-red-50 border border-red-200' : 'bg-blue-50' }}">
                    <p class="text-lg font-extrabold {{ !$humOk ? 'text-red-600' : 'text-blue-600' }}">
                        {{ $reading->humidity !== null ? $reading->humidity : '—' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">%</p>
                    @if(!$humOk)<p class="text-xs text-red-500 mt-0.5">⚠ High</p>@endif
                </div>

                <div class="rounded-xl p-3 {{ !$gasOk ? 'bg-red-50 border border-red-200' : 'bg-purple-50' }}">
                    <p class="text-lg font-extrabold {{ !$gasOk ? 'text-red-600' : 'text-purple-600' }}">
                        {{ $reading->gas_level !== null ? $reading->gas_level : '—' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">ppm</p>
                    @if(!$gasOk)<p class="text-xs text-red-500 mt-0.5">⚠ High</p>@endif
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3 text-right flex items-center justify-end gap-1">
                <i data-feather="clock" class="w-3 h-3"></i>
                {{ $reading->recorded_at?->diffForHumans() }}
            </p>
            @else
            <div class="text-center py-6 text-gray-400">
                <i data-feather="wifi-off" class="w-7 h-7 mx-auto mb-2 text-gray-300"></i>
                <p class="text-xs">No readings yet</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="px-6 py-8 text-center text-gray-400">
        <i data-feather="cpu" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
        <p class="text-sm">No sensors installed in this stall yet.</p>
    </div>
    @endif
</div>
@empty
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-16 text-gray-400">
    <i data-feather="box" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
    <h3 class="font-semibold text-gray-500 mb-1">No stalls assigned</h3>
    <p class="text-sm">Contact the administrator to assign stalls to your vendor profile.</p>
</div>
@endforelse

@endif
@endsection
