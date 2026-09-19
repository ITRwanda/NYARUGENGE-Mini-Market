@extends('admin.layouts.app')
@section('title', 'Edit Inspection')
@section('subtitle', 'Update inspection record and checklist')
@section('breadcrumb')
    <a href="{{ route('admin.inspections') }}" class="hover:text-green-600">Inspections</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('admin.inspections.show', $inspection) }}" class="hover:text-green-600">
        {{ $inspection->inspection_date->format('M d, Y') }}
    </a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-feather="edit-2" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Edit Inspection</h3>
                <p class="text-xs text-gray-400">
                    {{ $inspection->inspection_date->format('M d, Y') }} &bull;
                    Stall {{ $inspection->stall?->stall_number }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.inspections.update', $inspection) }}" class="p-6 space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                    <select name="market_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('market_id', $inspection->market_id) == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stall *</label>
                    <select name="stall_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach($stalls as $stall)
                            <option value="{{ $stall->id }}" {{ old('stall_id', $inspection->stall_id) == $stall->id ? 'selected' : '' }}>
                                {{ $stall->stall_number }} — {{ $stall->market?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Inspector *</label>
                    <select name="inspector_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}" {{ old('inspector_id', $inspection->inspector_id) == $inspector->id ? 'selected' : '' }}>
                                {{ $inspector->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Inspection Date & Time *</label>
                    <input type="datetime-local" name="inspection_date" required
                           value="{{ old('inspection_date', $inspection->inspection_date->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="completed"  {{ old('status', $inspection->status) === 'completed'  ? 'selected' : '' }}>✅ Completed</option>
                        <option value="pending"    {{ old('status', $inspection->status) === 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="follow_up"  {{ old('status', $inspection->status) === 'follow_up'  ? 'selected' : '' }}>🔄 Follow-up Required</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">General Notes</label>
                    <textarea name="general_notes" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 resize-none">{{ old('general_notes', $inspection->general_notes) }}</textarea>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i data-feather="check-square" class="w-4 h-4 text-green-600"></i>
                        Checklist Items
                    </p>
                    <button type="button" onclick="addItem()" class="btn-secondary text-xs py-1.5">
                        <i data-feather="plus" class="w-3.5 h-3.5"></i>Add Item
                    </button>
                </div>

                <div id="checklistItems" class="space-y-2">
                    @foreach($inspection->items as $i => $item)
                    <div class="checklist-row flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex-1">
                            <input type="text" name="items[{{ $i }}][item]" value="{{ old("items.$i.item", $item->item) }}" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        </div>
                        <select name="items[{{ $i }}][status]" required
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                            <option value="pass"           {{ old("items.$i.status", $item->status) === 'pass'           ? 'selected' : '' }}>✅ Pass</option>
                            <option value="fail"           {{ old("items.$i.status", $item->status) === 'fail'           ? 'selected' : '' }}>❌ Fail</option>
                            <option value="not_applicable" {{ old("items.$i.status", $item->status) === 'not_applicable' ? 'selected' : '' }}>— N/A</option>
                        </select>
                        <input type="text" name="items[{{ $i }}][notes]" value="{{ old("items.$i.notes", $item->notes) }}"
                               placeholder="Notes..."
                               class="w-40 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <button type="button" onclick="removeItem(this)"
                                class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors shrink-0">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @endforeach
                </div>

                <p class="text-xs text-gray-400 mt-2">
                    <span id="itemCount">{{ $inspection->items->count() }}</span> items
                </p>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.inspections.destroy', $inspection) }}"
                      onsubmit="return confirm('Delete this inspection record?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i data-feather="trash-2" class="w-4 h-4"></i>Delete
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('admin.inspections.show', $inspection) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i data-feather="save" class="w-4 h-4"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = {{ $inspection->items->count() }};

function addItem() {
    const container = document.getElementById('checklistItems');
    const row = document.createElement('div');
    row.className = 'checklist-row flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100';
    row.innerHTML = `
        <div class="flex-1">
            <input type="text" name="items[${itemIndex}][item]" required placeholder="Checklist item..."
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
        </div>
        <select name="items[${itemIndex}][status]" required
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
            <option value="pass">✅ Pass</option>
            <option value="fail">❌ Fail</option>
            <option value="not_applicable">— N/A</option>
        </select>
        <input type="text" name="items[${itemIndex}][notes]" placeholder="Notes..."
               class="w-40 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
        <button type="button" onclick="removeItem(this)"
                class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>`;
    container.appendChild(row);
    itemIndex++;
    updateCount();
    feather.replace({ width: 16, height: 16 });
}

function removeItem(btn) {
    btn.closest('.checklist-row').remove();
    updateCount();
}

function updateCount() {
    document.getElementById('itemCount').textContent =
        document.querySelectorAll('.checklist-row').length;
}
</script>
@endpush
