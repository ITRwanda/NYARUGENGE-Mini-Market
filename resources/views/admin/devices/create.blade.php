@extends('admin.layouts.app')
@section('title', 'Register Device')
@section('subtitle', 'Add a new ESP32 IoT sensor unit')
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
                <p class="text-xs text-gray-400">
                    <strong>{{ $market->name }}</strong> &bull;
                    The device UID must match what is flashed on the ESP32
                </p>
            </div>
        </div>

        {{-- ESP32 hint --}}
        <div class="mx-6 mt-5 p-4 bg-teal-50 border border-teal-100 rounded-xl">
            <p class="text-xs font-bold text-teal-700 mb-1 flex items-center gap-1.5">
                <i data-feather="zap" class="w-3.5 h-3.5"></i>
                ESP32 Setup Instructions
            </p>
            <p class="text-xs text-teal-600 leading-relaxed">
                Set the <code class="bg-teal-100 px-1 rounded font-mono">DEVICE_UID</code> in your ESP32 firmware
                to match the UID entered here. The device posts to
                <code class="bg-teal-100 px-1 rounded font-mono">POST /api/iot/readings</code>
                with <code class="bg-teal-100 px-1 rounded font-mono">{ device_uid, temperature, humidity, gas_level }</code>.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.devices.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Device UID *
                        <span class="text-gray-400 font-normal text-xs">(must match firmware)</span>
                    </label>
                    <input type="text" name="device_uid" value="{{ old('device_uid') }}" required
                           placeholder="e.g. ESP32-A1B2C3"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500
                                  @error('device_uid') border-red-400 @enderror">
                    @error('device_uid')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Device Name</label>
                    <input type="text" name="device_name" value="{{ old('device_name') }}"
                           placeholder="e.g. Sensor #01 — Stall S-001"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Assign to Stall
                        <span class="text-gray-400 font-normal text-xs">(required for sensor data to appear per-stall)</span>
                    </label>
                    <select name="stall_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 bg-white">
                        <option value="">— No stall assignment —</option>
                        @foreach($stalls as $stall)
                            <option value="{{ $stall->id }}" {{ old('stall_id') == $stall->id ? 'selected' : '' }}>
                                {{ $stall->stall_number }}
                                @if($stall->vendor) — {{ $stall->vendor->business_name }} @endif
                                ({{ $stall->section ?? 'No section' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hardware --}}
            <div class="pt-4 border-t border-dashed border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i data-feather="settings" class="w-4 h-4 text-gray-400"></i>
                    Hardware Specification
                </p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach([
                        ['microcontroller',      'Microcontroller',    'ESP32'],
                        ['communication_module', 'Communication',      'Wi-Fi 802.11 b/g/n'],
                        ['temperature_sensor',   'Temp Sensor',        'DHT22'],
                        ['humidity_sensor',      'Humidity Sensor',    'DHT22'],
                        ['gas_sensor',           'Gas Sensor',         'MQ-135'],
                        ['firmware_version',     'Firmware Version',   '2.0.0'],
                    ] as [$name, $label, $default])
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                        <input type="text" name="{{ $name }}"
                               value="{{ old($name, $default) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500
                                      {{ $name === 'firmware_version' ? 'font-mono' : '' }}">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Initial Status</label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 bg-white">
                        <option value="offline">Offline (not yet powered)</option>
                        <option value="online">Online</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl w-full border border-gray-100">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Device is active
                        </label>
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
