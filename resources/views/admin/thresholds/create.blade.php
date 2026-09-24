@extends('admin.layouts.app')
@section('title', 'Add Threshold')
@section('subtitle', 'Define a safety limit that triggers automatic alerts')
@section('breadcrumb')
    <a href="{{ route('admin.thresholds') }}" class="hover:text-green-600">Thresholds</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Create</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                <i data-feather="sliders" class="w-5 h-5 text-orange-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">New Threshold</h3>
                <p class="text-xs text-gray-400">
                    <strong>{{ $market->name }}</strong> &bull;
                    Alerts fire automatically when a reading breaches these limits
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.thresholds.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Parameter *</label>
                    <div class="grid grid-cols-3 gap-3" id="paramButtons">
                        @foreach([
                            ['temperature', '🌡️', 'Temperature', 'orange'],
                            ['humidity',    '💧', 'Humidity',    'blue'],
                            ['gas_level',   '💨', 'Gas Level',   'purple'],
                        ] as [$val, $icon, $label, $color])
                        <label class="param-btn flex flex-col items-center gap-1.5 p-4 rounded-xl border-2
                                      cursor-pointer transition-all text-center
                                      {{ old('parameter') === $val
                                          ? "border-{$color}-500 bg-{$color}-50"
                                          : 'border-gray-100 hover:border-gray-300' }}"
                               id="param-{{ $val }}">
                            <input type="radio" name="parameter" value="{{ $val }}" required
                                   {{ old('parameter') === $val ? 'checked' : '' }}
                                   class="sr-only" onchange="updateParam('{{ $val }}', '{{ $color }}')">
                            <span class="text-2xl">{{ $icon }}</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('parameter')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Device
                        <span class="text-gray-400 font-normal text-xs">(blank = all devices)</span>
                    </label>
                    <select name="device_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— Market-wide (all devices) —</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                {{ $device->device_name ?? $device->device_uid }}
                                ({{ $device->stall?->stall_number ?? 'No stall' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit</label>
                    <input type="text" name="unit" id="unitInput" value="{{ old('unit') }}"
                           placeholder="e.g. °C, %, ppm"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum Value</label>
                    <input type="number" name="minimum_value" value="{{ old('minimum_value') }}" step="0.01"
                           placeholder="No minimum limit"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    <p class="text-xs text-gray-400 mt-1">Alert fires when reading drops <strong>below</strong> this</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Maximum Value</label>
                    <input type="number" name="maximum_value" value="{{ old('maximum_value') }}" step="0.01"
                           placeholder="No maximum limit"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    <p class="text-xs text-gray-400 mt-1">Alert fires when reading <strong>exceeds</strong> this</p>
                </div>
            </div>

            <div id="presetHint" class="hidden p-4 bg-green-50 border border-green-100 rounded-xl">
                <p id="presetText" class="text-xs text-green-700 font-semibold"></p>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                    Threshold is active
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.thresholds') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>Create Threshold
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const presets = {
    temperature: { unit: '°C',  hint: '💡 Recommended safe range: 5°C (min) – 35°C (max)' },
    humidity:    { unit: '%',   hint: '💡 Recommended safe range: 20% (min) – 80% (max)' },
    gas_level:   { unit: 'ppm', hint: '💡 Recommended maximum: 400 ppm (MQ-135 sensor baseline)' },
};

const colors = {
    temperature: 'orange',
    humidity:    'blue',
    gas_level:   'purple',
};

function updateParam(val, color) {
    // Reset all buttons
    document.querySelectorAll('.param-btn').forEach(btn => {
        btn.className = btn.className
            .replace(/border-\w+-500/g, 'border-gray-100')
            .replace(/bg-\w+-50/g, '');
        btn.classList.add('hover:border-gray-300');
    });

    // Highlight selected
    const selected = document.getElementById('param-' + val);
    if (selected) {
        selected.classList.remove('border-gray-100', 'hover:border-gray-300');
        selected.classList.add(`border-${color}-500`, `bg-${color}-50`);
    }

    // Preset
    const p = presets[val];
    const hint = document.getElementById('presetHint');
    const unit = document.getElementById('unitInput');
    if (p) {
        if (!unit.value) unit.value = p.unit;
        document.getElementById('presetText').textContent = p.hint;
        hint.classList.remove('hidden');
    }
}
</script>
@endpush
