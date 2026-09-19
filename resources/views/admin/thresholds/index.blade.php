@extends('admin.layouts.app')
@section('title', 'Thresholds')
@section('subtitle', 'Safety limits that trigger automatic alerts')

@section('content')

<div class="flex items-center justify-between mb-5">
    <span class="badge badge-blue">{{ $thresholds->total() }} configured</span>
    <a href="{{ route('admin.thresholds.create') }}" class="btn-primary">
        <i data-feather="plus" class="w-4 h-4"></i>Add Threshold
    </a>
</div>

<div class="mb-5 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-3 text-sm text-blue-700">
    <i data-feather="info" class="w-4 h-4 shrink-0 mt-0.5 text-blue-500"></i>
    <p>Thresholds define safe operating ranges. When a sensor reading falls outside these limits, an alert is created automatically. Market-level thresholds apply to all devices unless a device-specific threshold exists.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Market</th>
                    <th>Device</th>
                    <th>Parameter</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($thresholds as $threshold)
                <tr>
                    <td class="font-medium text-gray-900">{{ $threshold->market?->name ?? '—' }}</td>
                    <td>
                        @if($threshold->device)
                            <span class="text-xs font-mono bg-teal-50 text-teal-700 px-2 py-0.5 rounded-lg">
                                {{ $threshold->device->device_name ?? $threshold->device->device_uid }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">All devices</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $paramIcons  = ['temperature' => '🌡️', 'humidity' => '💧', 'gas_level' => '💨'];
                            $paramColors = ['temperature' => 'bg-orange-100 text-orange-800', 'humidity' => 'bg-blue-100 text-blue-800', 'gas_level' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $paramColors[$threshold->parameter] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $paramIcons[$threshold->parameter] ?? '📊' }}
                            {{ ucfirst(str_replace('_', ' ', $threshold->parameter)) }}
                        </span>
                    </td>
                    <td class="font-mono font-semibold text-gray-700 text-sm">
                        {{ $threshold->minimum_value !== null ? $threshold->minimum_value : '—' }}
                    </td>
                    <td class="font-mono font-semibold text-gray-700 text-sm">
                        {{ $threshold->maximum_value !== null ? $threshold->maximum_value : '—' }}
                    </td>
                    <td class="text-gray-500 text-sm">{{ $threshold->unit ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $threshold->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $threshold->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.thresholds.edit', $threshold) }}"
                               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Edit
                            </a>
                            <form method="POST" action="{{ route('admin.thresholds.destroy', $threshold) }}"
                                  onsubmit="return confirm('Delete this threshold?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i data-feather="trash-2" class="w-3.5 h-3.5"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i data-feather="sliders" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No thresholds configured yet.</p>
                        <a href="{{ route('admin.thresholds.create') }}" class="btn-primary inline-flex">
                            <i data-feather="plus" class="w-4 h-4"></i>Add First Threshold
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ $thresholds->total() }} thresholds total</p>
        {{ $thresholds->links() }}
    </div>
</div>

@endsection
