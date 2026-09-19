@extends('admin.layouts.app')
@section('title', 'Edit Threshold')
@section('subtitle', 'Update safety limit configuration')
@section('breadcrumb')
    <a href="{{ route('admin.thresholds') }}" class="hover:text-green-600">Thresholds</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Edit</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                <i data-feather="sliders" class="w-5 h-5 text-orange-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">
                    {{ ucfirst(str_replace('_',' ',$threshold->parameter)) }} Threshold
                </h3>
                <p class="text-xs text-gray-400">{{ $threshold->market?->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.thresholds.update', $threshold) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                    <select name="market_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('market_id', $threshold->market_id) == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Device <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                    <select name="device_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— All devices —</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ old('device_id', $threshold->device_id) == $device->id ? 'selected' : '' }}>
                                {{ $device->device_name ?? $device->device_uid }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Parameter *</label>
                    <select name="parameter" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="temperature" {{ old('parameter', $threshold->parameter) === 'temperature' ? 'selected' : '' }}>🌡️ Temperature</option>
                        <option value="humidity"    {{ old('parameter', $threshold->parameter) === 'humidity'    ? 'selected' : '' }}>💧 Humidity</option>
                        <option value="gas_level"   {{ old('parameter', $threshold->parameter) === 'gas_level'   ? 'selected' : '' }}>💨 Gas Level</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $threshold->unit) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum Value</label>
                    <input type="number" name="minimum_value" value="{{ old('minimum_value', $threshold->minimum_value) }}" step="0.01"
                           placeholder="No minimum"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Maximum Value</label>
                    <input type="number" name="maximum_value" value="{{ old('maximum_value', $threshold->maximum_value) }}" step="0.01"
                           placeholder="No maximum"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $threshold->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Threshold is active</label>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.thresholds.destroy', $threshold) }}"
                      onsubmit="return confirm('Delete this threshold?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i data-feather="trash-2" class="w-4 h-4"></i>Delete
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('admin.thresholds') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i data-feather="save" class="w-4 h-4"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
