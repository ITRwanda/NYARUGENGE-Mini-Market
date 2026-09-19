@extends('admin.layouts.app')
@section('title', 'IoT Devices')
@section('subtitle', 'ESP8266 sensor units and connectivity status')

@section('content')

@php
    $online      = \App\Models\Device::where('status','online')->count();
    $offline     = \App\Models\Device::where('status','offline')->count();
    $maintenance = \App\Models\Device::where('status','maintenance')->count();
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-bold text-gray-900">{{ $devices->total() }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Total</p>
    </div>
    <div class="bg-green-50 rounded-xl p-4 border border-green-100 text-center">
        <p class="text-2xl font-bold text-green-700">{{ $online }}</p>
        <p class="text-xs text-green-600 mt-0.5">Online</p>
    </div>
    <div class="bg-red-50 rounded-xl p-4 border border-red-100 text-center">
        <p class="text-2xl font-bold text-red-700">{{ $offline }}</p>
        <p class="text-xs text-red-500 mt-0.5">Offline</p>
    </div>
    <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100 text-center">
        <p class="text-2xl font-bold text-yellow-700">{{ $maintenance }}</p>
        <p class="text-xs text-yellow-600 mt-0.5">Maintenance</p>
    </div>
</div>

<div class="flex justify-end mb-4">
    <a href="{{ route('admin.devices.create') }}" class="btn-primary">
        <i data-feather="plus" class="w-4 h-4"></i>Register Device
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>UID</th>
                    <th>Market / Stall</th>
                    <th>Hardware</th>
                    <th>Firmware</th>
                    <th>Last Seen</th>
                    <th>Status</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                {{ $device->status === 'online' ? 'bg-green-100' : ($device->status === 'offline' ? 'bg-red-100' : 'bg-yellow-100') }}">
                                <i data-feather="cpu" class="w-4 h-4
                                    {{ $device->status === 'online' ? 'text-green-600' : ($device->status === 'offline' ? 'text-red-500' : 'text-yellow-600') }}">
                                </i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $device->device_name ?? 'Unnamed' }}</p>
                                <p class="text-xs text-gray-400">{{ $device->microcontroller }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono text-xs text-gray-600 max-w-[100px] truncate">{{ $device->device_uid }}</td>
                    <td>
                        <p class="text-sm font-medium text-gray-700">{{ $device->market?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">Stall: {{ $device->stall?->stall_number ?? 'N/A' }}</p>
                    </td>
                    <td class="text-xs text-gray-600 space-y-0.5">
                        <div>📡 {{ $device->communication_module }}</div>
                        <div>🌡 {{ $device->temperature_sensor }}</div>
                        <div>💨 {{ $device->gas_sensor }}</div>
                    </td>
                    <td class="font-mono text-xs text-gray-500">v{{ $device->firmware_version ?? '—' }}</td>
                    <td class="text-xs text-gray-500 whitespace-nowrap">
                        @if($device->last_seen_at)
                            <span title="{{ $device->last_seen_at->format('Y-m-d H:i:s') }}">
                                {{ $device->last_seen_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-gray-300">Never</span>
                        @endif
                    </td>
                    <td>
                        @if($device->status === 'online')
                            <span class="badge badge-green"><span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>Online</span>
                        @elseif($device->status === 'offline')
                            <span class="badge badge-red"><span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>Offline</span>
                        @elseif($device->status === 'maintenance')
                            <span class="badge badge-yellow">Maintenance</span>
                        @else
                            <span class="badge badge-gray">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right pr-4">
                        <a href="{{ route('admin.devices.edit', $device) }}"
                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                            <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i data-feather="cpu" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No devices registered.</p>
                        <a href="{{ route('admin.devices.create') }}" class="btn-primary inline-flex">
                            <i data-feather="plus" class="w-4 h-4"></i>Register Device
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">Showing {{ $devices->firstItem() }}–{{ $devices->lastItem() }} of {{ $devices->total() }}</p>
        {{ $devices->links() }}
    </div>
</div>

@endsection
