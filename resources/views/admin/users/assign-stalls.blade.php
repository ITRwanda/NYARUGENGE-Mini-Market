@extends('admin.layouts.app')
@section('title', 'Assign Stalls')
@section('subtitle', 'Choose which stalls this inspector monitors')
@section('breadcrumb')
    <a href="{{ route('admin.users') }}" class="hover:text-green-600">Inspectors</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $user->name }} — Stall Assignments</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Inspector card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center
                    text-white font-extrabold text-lg shrink-0">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
            <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500">{{ $user->email }}
                @if($user->phone) &bull; {{ $user->phone }} @endif
            </p>
        </div>
        <span class="ml-auto badge {{ $user->is_locked ? 'badge-red' : 'badge-green' }}">
            {{ $user->is_locked ? 'Locked' : 'Active' }}
        </span>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-feather="map-pin" class="w-5 h-5 text-green-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Stall Assignments</h3>
                <p class="text-xs text-gray-400">
                    {{ $market->name }} &bull; {{ $stalls->count() }} stalls total
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.save-stalls', $user) }}" class="p-6">
            @csrf

            <p class="text-sm text-gray-600 mb-4">
                Select the stalls this inspector is responsible for. They can only create inspections
                for their assigned stalls.
            </p>

            {{-- Quick select buttons --}}
            <div class="flex gap-2 mb-5">
                <button type="button" onclick="selectAll(true)"
                        class="text-xs px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700
                               font-semibold rounded-lg transition-colors border border-green-200">
                    ✓ Select All
                </button>
                <button type="button" onclick="selectAll(false)"
                        class="text-xs px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600
                               font-semibold rounded-lg transition-colors border border-gray-200">
                    ✗ Deselect All
                </button>
            </div>

            {{-- Group stalls by section --}}
            @php
                $grouped = $stalls->groupBy('section');
            @endphp

            <div class="space-y-5">
                @foreach($grouped as $section => $sectionStalls)
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-5 h-0.5 bg-green-300 inline-block"></span>
                        {{ $section ?? 'Unassigned Section' }}
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($sectionStalls as $stall)
                        @php $checked = in_array($stall->id, $assignedIds); @endphp
                        <label class="stall-card flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer
                                      transition-all duration-150 select-none
                                      {{ $checked
                                          ? 'border-green-500 bg-green-50'
                                          : 'border-gray-100 bg-white hover:border-green-300 hover:bg-green-50/40' }}"
                               id="label-{{ $stall->id }}">
                            <input type="checkbox" name="stall_ids[]" value="{{ $stall->id }}"
                                   {{ $checked ? 'checked' : '' }}
                                   class="stall-checkbox w-4 h-4 rounded border-gray-300 text-green-600
                                          focus:ring-green-500 shrink-0"
                                   onchange="updateCard(this)">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm {{ $checked ? 'text-green-800' : 'text-gray-900' }}
                                                 bg-{{ $checked ? 'green' : 'gray' }}-100 px-2 py-0.5 rounded-lg">
                                        {{ $stall->stall_number }}
                                    </span>
                                </div>
                                @if($stall->vendor)
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    {{ $stall->vendor->business_name }}
                                    <span class="text-gray-400">— {{ $stall->vendor->food_category }}</span>
                                </p>
                                @else
                                <p class="text-xs text-gray-400 mt-0.5 italic">Unassigned vendor</p>
                                @endif
                            </div>
                            <div class="shrink-0">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                                            {{ $checked ? 'border-green-500 bg-green-500' : 'border-gray-300' }}"
                                     id="check-icon-{{ $stall->id }}">
                                    @if($checked)
                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-700
                        flex items-center gap-2">
                <i data-feather="info" class="w-4 h-4 shrink-0 text-blue-500"></i>
                <span id="assignmentSummary">
                    <span id="selectedCount">{{ count($assignedIds) }}</span>
                    of {{ $stalls->count() }} stalls assigned to this inspector.
                </span>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>
                    Save Assignments
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateCard(checkbox) {
    const stallId   = checkbox.value;
    const label     = document.getElementById('label-' + stallId);
    const checkIcon = document.getElementById('check-icon-' + stallId);
    const checked   = checkbox.checked;

    // Update card style
    label.className = label.className
        .replace(/border-(green-500|gray-100)\s*/g, '')
        .replace(/bg-(green-50|white)[^\s]*/g, '');

    if (checked) {
        label.classList.add('border-green-500', 'bg-green-50');
        checkIcon.className = checkIcon.className
            .replace('border-gray-300', 'border-green-500 bg-green-500');
        checkIcon.innerHTML = '<svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
    } else {
        label.classList.remove('border-green-500', 'bg-green-50');
        label.classList.add('border-gray-100', 'bg-white');
        checkIcon.className = checkIcon.className
            .replace('border-green-500 bg-green-500', 'border-gray-300');
        checkIcon.innerHTML = '';
    }

    updateCount();
}

function selectAll(state) {
    document.querySelectorAll('.stall-checkbox').forEach(cb => {
        if (cb.checked !== state) {
            cb.checked = state;
            updateCard(cb);
        }
    });
}

function updateCount() {
    const count = document.querySelectorAll('.stall-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
}
</script>
@endpush
