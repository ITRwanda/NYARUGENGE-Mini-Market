@extends('admin.layouts.app')
@section('title', 'New Inspection')
@section('subtitle', 'Record a hygiene compliance inspection')
@section('breadcrumb')
    <a href="{{ route('admin.inspections') }}" class="hover:text-green-600">Inspections</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">New</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-feather="clipboard" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">New Inspection</h3>
                <p class="text-xs text-gray-400">
                    {{ $market->name }} &bull; Inspector: <strong>{{ auth()->user()->name }}</strong>
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.inspections.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- Meta --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stall *</label>
                    <select name="stall_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none
                                   focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white
                                   @error('stall_id') border-red-400 @enderror">
                        <option value="">— Select stall —</option>
                        @foreach($stalls as $stall)
                            <option value="{{ $stall->id }}" {{ old('stall_id') == $stall->id ? 'selected' : '' }}>
                                {{ $stall->stall_number }}
                                @if($stall->vendor) — {{ $stall->vendor->business_name }} @endif
                                ({{ $stall->section ?? 'No section' }})
                            </option>
                        @endforeach
                    </select>
                    @error('stall_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date & Time *</label>
                    <input type="datetime-local" name="inspection_date" required
                           value="{{ old('inspection_date', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none
                                  focus:ring-2 focus:ring-green-500/30 focus:border-green-500
                                  @error('inspection_date') border-red-400 @enderror">
                    @error('inspection_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none
                                   focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="completed"  {{ old('status','completed') === 'completed'  ? 'selected' : '' }}>✅ Completed</option>
                        <option value="pending"    {{ old('status') === 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="follow_up"  {{ old('status') === 'follow_up'  ? 'selected' : '' }}>🔄 Follow-up Required</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">General Notes</label>
                    <textarea name="general_notes" rows="2"
                              placeholder="Overall observations about this stall…"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none
                                     focus:ring-2 focus:ring-green-500/30 focus:border-green-500 resize-none">{{ old('general_notes') }}</textarea>
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
                    @php
                        $defaults = [
                            'Food storage temperature within safe range',
                            'Proper food labeling and date markings',
                            'Absence of pests or rodents',
                            'Clean and sanitized work surfaces',
                            'Proper waste disposal in place',
                            'Vendor personal hygiene compliance',
                            'Raw and cooked food properly separated',
                            'Adequate ventilation in stall',
                            'Fire safety equipment present and accessible',
                            'Valid health permit visibly displayed',
                        ];
                        $items = old('items', array_map(fn($t) => ['item'=>$t,'status'=>'pass','notes'=>''], $defaults));
                    @endphp

                    @foreach($items as $i => $item)
                    <div class="checklist-row flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex-1">
                            <input type="text" name="items[{{ $i }}][item]" value="{{ $item['item'] }}" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none
                                          focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        </div>
                        <select name="items[{{ $i }}][status]" required
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none
                                       focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                            <option value="pass"           {{ ($item['status']??'') === 'pass'           ? 'selected' : '' }}>✅ Pass</option>
                            <option value="fail"           {{ ($item['status']??'') === 'fail'           ? 'selected' : '' }}>❌ Fail</option>
                            <option value="not_applicable" {{ ($item['status']??'') === 'not_applicable' ? 'selected' : '' }}>— N/A</option>
                        </select>
                        <input type="text" name="items[{{ $i }}][notes]" value="{{ $item['notes']??'' }}"
                               placeholder="Notes…"
                               class="w-40 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none
                                      focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <button type="button" onclick="removeItem(this)"
                                class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors shrink-0">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @endforeach
                </div>

                <p class="text-xs text-gray-400 mt-2">
                    <span id="itemCount">{{ count($items) }}</span> items in checklist
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.inspections') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>Save Inspection
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let idx = document.querySelectorAll('.checklist-row').length;

function addItem() {
    const container = document.getElementById('checklistItems');
    const row = document.createElement('div');
    row.className = 'checklist-row flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100';
    row.innerHTML = `
        <div class="flex-1">
            <input type="text" name="items[${idx}][item]" required placeholder="Checklist item…"
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
        </div>
        <select name="items[${idx}][status]" required
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
            <option value="pass">✅ Pass</option>
            <option value="fail">❌ Fail</option>
            <option value="not_applicable">— N/A</option>
        </select>
        <input type="text" name="items[${idx}][notes]" placeholder="Notes…"
               class="w-40 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
        <button type="button" onclick="removeItem(this)"
                class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>`;
    container.appendChild(row);
    idx++;
    updateCount();
    row.querySelector('input[type="text"]').focus();
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
