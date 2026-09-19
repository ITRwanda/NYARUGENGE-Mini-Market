@extends('admin.layouts.app')
@section('title', 'Register Device')
@section('subtitle', 'Add a new IoT sensor unit')
@section('breadcrumb')
    <a href="{{ route('admin.devices') }}" class="hover:text-green-600">Devices</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Register</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                <i data-feather="cpu" class="w-5 h-5 text-teal-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">New IoT Device</h3>
                <p class="text-xs text-gray-400">Register an ESP8266 sensor unit</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.devices.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- Identity --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Device UID *</label>
                    <input type="text" name="device_uid" value="{{ old('device_uid') }}" required
                           placeholder="e.g. ESP-A1B2C3D4"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 @error('device_uid') border-red-400 @enderror">
                    @error('device_uid')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Device Name</label>
                    <input type="text" name="device_name" value="{{ old('device_name') }}"
                           placeholder="e.g. Sensor Unit 1"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                    <select name="market_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white @error('market_id') border-red-400 @enderror">
                        <option value="">— Select market —</option>
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('market_id') == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('market_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign to Stall</label>
                    <select name="stall_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— No stall assignment —</option>
                        @foreach($stalls as $stall)
                            <option value="{{ $stall->id }}" {{ old('stall_id') == $stall->id ? 'selected' : '' }}>
                                {{ $stall->stall_number }} — {{ $stall->market?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hardware --}}
            <div class="pt-4 border-t border-dashed border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i data-feather="settings" class="w-4 h-4 text-gray-400"></i>
                    Hardware Configuration
                </p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Microcontroller</label>
                        <input type="text" name="microcontroller" value="{{ old('microcontroller', 'Arduino Uno') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Communication</label>
                        <input type="text" name="communication_module" value="{{ old('communication_module', 'ESP8266') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Temp Sensor</label>
                        <input type="text" name="temperature_sensor" value="{{ old('temperature_sensor', 'DHT22') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Humidity Sensor</label>
                        <input type="text" name="humidity_sensor" value="{{ old('humidity_sensor', 'DHT22') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Gas Sensor</label>
                        <input type="text" name="gas_sensor" value="{{ old('gas_sensor', 'MQ-135') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Firmware Version</label>
                        <input type="text" name="firmware_version" value="{{ old('firmware_version', '1.0.0') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Initial Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="offline"     {{ old('status') === 'offline'     ? 'selected' : '' }}>Offline</option>
                        <option value="online"      {{ old('status') === 'online'      ? 'selected' : '' }}>Online</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="inactive"    {{ old('status') === 'inactive'    ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end pb-1">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl w-full">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Active device</label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.devices') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>Register Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
