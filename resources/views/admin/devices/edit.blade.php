@extends('admin.layouts.app')
@section('title', 'Edit Device')
@section('subtitle', 'Update IoT sensor configuration')
@section('breadcrumb')
    <a href="{{ route('admin.devices') }}" class="hover:text-green-600">Devices</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $device->device_name ?? $device->device_uid }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $device->status === 'online' ? 'bg-green-100' : ($device->status === 'offline' ? 'bg-red-100' : 'bg-yellow-100') }}">
                <i data-feather="cpu" class="w-5 h-5 {{ $device->status === 'online' ? 'text-green-600' : ($device->status === 'offline' ? 'text-red-500' : 'text-yellow-600') }}"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ $device->device_name ?? 'Unnamed Device' }}</h3>
                <p class="text-xs text-gray-400 font-mono">UID: {{ $device->device_uid }}</p>
            </div>
            <div class="ml-auto">
                @if($device->status === 'online')
                    <span class="badge badge-green"><span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>Online</span>
                @elseif($device->status === 'offline')
                    <span class="badge badge-red">Offline</span>
                @else
                    <span class="badge badge-yellow">{{ ucfirst($device->status) }}</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Device Name</label>
                    <input type="text" name="device_name" value="{{ old('device_name', $device->device_name) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                    <select name="market_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('market_id', $device->market_id) == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assigned Stall</label>
                    <select name="stall_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— No stall —</option>
                        @foreach($stalls as $stall)
                            <option value="{{ $stall->id }}" {{ old('stall_id', $device->stall_id) == $stall->id ? 'selected' : '' }}>
                                {{ $stall->stall_number }} — {{ $stall->market?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach(['online','offline','maintenance','inactive'] as $s)
                            <option value="{{ $s }}" {{ old('status', $device->status) === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hardware --}}
            <div class="pt-4 border-t border-dashed border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i data-feather="settings" class="w-4 h-4 text-gray-400"></i>Hardware Configuration
                </p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach([
                        ['microcontroller',      'Microcontroller',    $device->microcontroller],
                        ['communication_module', 'Communication',      $device->communication_module],
                        ['temperature_sensor',   'Temp Sensor',        $device->temperature_sensor],
                        ['humidity_sensor',      'Humidity Sensor',    $device->humidity_sensor],
                        ['gas_sensor',           'Gas Sensor',         $device->gas_sensor],
                        ['firmware_version',     'Firmware Version',   $device->firmware_version],
                    ] as [$name, $label, $value])
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                        <input type="text" name="{{ $name }}" value="{{ old($name, $value) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 {{ $name === 'firmware_version' ? 'font-mono' : '' }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $device->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Device is active</label>
            </div>

            @if($device->last_seen_at)
            <div class="text-xs text-gray-400 flex items-center gap-1.5">
                <i data-feather="clock" class="w-3.5 h-3.5"></i>
                Last seen: {{ $device->last_seen_at->format('M d, Y H:i:s') }} ({{ $device->last_seen_at->diffForHumans() }})
            </div>
            @endif

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.devices.destroy', $device) }}"
                      onsubmit="return confirm('Delete device {{ addslashes($device->device_uid) }}? All sensor readings will be removed.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i data-feather="trash-2" class="w-4 h-4"></i>Delete Device
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('admin.devices') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i data-feather="save" class="w-4 h-4"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
