@extends('admin.layouts.app')
@section('title', 'Add Threshold')
@section('subtitle', 'Define a new safety limit for sensor parameters')
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
                <p class="text-xs text-gray-400">Alerts trigger automatically when a reading breaches these limits</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.thresholds.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Device
                        <span class="text-gray-400 font-normal text-xs">(leave blank = applies to all)</span>
                    </label>
                    <select name="device_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— All devices in market —</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                {{ $device->device_name ?? $device->device_uid }} ({{ $device->market?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Parameter *</label>
                    <select name="parameter" required id="paramSelect"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white @error('parameter') border-red-400 @enderror">
                        <option value="">— Select parameter —</option>
                        <option value="temperature" {{ old('parameter') === 'temperature' ? 'selected' : '' }}>🌡️ Temperature</option>
                        <option value="humidity"    {{ old('parameter') === 'humidity'    ? 'selected' : '' }}>💧 Humidity</option>
                        <option value="gas_level"   {{ old('parameter') === 'gas_level'   ? 'selected' : '' }}>💨 Gas Level</option>
                    </select>
                    @error('parameter')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit</label>
                    <input type="text" name="unit" id="unitInput" value="{{ old('unit') }}"
                           placeholder="e.g. °C, %, ppm"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum Value</label>
                    <input type="number" name="minimum_value" value="{{ old('minimum_value') }}" step="0.01"
                           placeholder="Leave blank if no minimum"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    <p class="text-xs text-gray-400 mt-1">Alert fires when reading drops below this</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Maximum Value</label>
                    <input type="number" name="maximum_value" value="{{ old('maximum_value') }}" step="0.01"
                           placeholder="Leave blank if no maximum"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    <p class="text-xs text-gray-400 mt-1">Alert fires when reading exceeds this</p>
                </div>
            </div>

            {{-- Preset hints --}}
            <div id="presetHint" class="hidden p-4 bg-green-50 border border-green-100 rounded-xl text-xs text-green-700">
                <p id="presetText"></p>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Threshold is active</label>
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
    temperature: { unit: '°C',  hint: 'Recommended safe range: 5°C – 35°C' },
    humidity:    { unit: '%',   hint: 'Recommended safe range: 20% – 80%' },
    gas_level:   { unit: 'ppm', hint: 'Recommended maximum: 400 ppm (MQ-135 sensor)' },
};
document.getElementById('paramSelect').addEventListener('change', function () {
    const p = presets[this.value];
    const hint = document.getElementById('presetHint');
    const unitInput = document.getElementById('unitInput');
    if (p) {
        unitInput.value = unitInput.value || p.unit;
        document.getElementById('presetText').textContent = '💡 ' + p.hint;
        hint.classList.remove('hidden');
    } else {
        hint.classList.add('hidden');
    }
});
</script>
@endpush
