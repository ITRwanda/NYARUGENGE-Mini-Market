@extends('admin.layouts.app')
@section('title', 'Inspection Results')
@section('subtitle', 'Hygiene inspection records for your stalls')

@section('content')

{{-- Vendor header --}}
@if($vendor)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6 flex flex-wrap items-center gap-4">
    <div class="w-12 h-12 rounded-2xl bg-green-600 flex items-center justify-center
                text-white font-extrabold text-lg shrink-0">
        {{ strtoupper(substr($vendor->business_name ?? 'V', 0, 2)) }}
    </div>
    <div class="flex-1">
        <h2 class="font-extrabold text-gray-900">{{ $vendor->business_name }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">
            <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $vendor->vendor_code }}</span>
            &bull; {{ $vendor->stalls->count() }} stall{{ $vendor->stalls->count() !== 1 ? 's' : '' }}
        </p>
    </div>
    <div class="text-right">
        <p class="text-xs text-gray-400">Total inspections on your stalls</p>
        <p class="text-2xl font-extrabold text-gray-900">{{ $inspections->total() }}</p>
    </div>
</div>
@endif

{{-- Stats --}}
@php
    $stallIds = $vendor?->stalls->pluck('id') ?? collect();
    $completed = \App\Models\Inspection::whereIn('stall_id', $stallIds)->where('status','completed')->count();
    $pending   = \App\Models\Inspection::whereIn('stall_id', $stallIds)->where('status','pending')->count();
    $followUp  = \App\Models\Inspection::whereIn('stall_id', $stallIds)->where('status','follow_up')->count();
@endphp

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 text-center">
        <p class="text-2xl font-bold text-green-700">{{ $completed }}</p>
        <p class="text-xs text-green-600 font-medium mt-0.5">✅ Passed</p>
    </div>
    <div class="bg-yellow-50 rounded-2xl p-4 border border-yellow-100 text-center">
        <p class="text-2xl font-bold text-yellow-700">{{ $pending }}</p>
        <p class="text-xs text-yellow-600 font-medium mt-0.5">⏳ Pending</p>
    </div>
    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-center">
        <p class="text-2xl font-bold text-orange-700">{{ $followUp }}</p>
        <p class="text-xs text-orange-600 font-medium mt-0.5">🔄 Follow-up</p>
    </div>
</div>

{{-- Info banner --}}
<div class="mb-5 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-3
            text-sm text-blue-700">
    <i data-feather="info" class="w-4 h-4 shrink-0 mt-0.5 text-blue-500"></i>
    <p>These are hygiene inspections conducted by certified market inspectors on your stalls.
       Review the checklist results and general notes to understand what needs improvement.</p>
</div>

{{-- Inspections list --}}
<div class="space-y-4">
    @forelse($inspections as $inspection)
    @php
        $total    = $inspection->items->count();
        $passed   = $inspection->items->where('status','pass')->count();
        $failed   = $inspection->items->where('status','fail')->count();
        $passRate = $total > 0 ? round(($passed / $total) * 100) : 0;
        $rateColor = $passRate >= 80 ? 'text-green-600' : ($passRate >= 60 ? 'text-yellow-600' : 'text-red-600');
        $bgColor   = $passRate >= 80 ? 'bg-green-50 border-green-200' : ($passRate >= 60 ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200');
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden
                hover:shadow-md transition-shadow">

        {{-- Card header --}}
        <div class="px-6 py-4 flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-start gap-4">
                {{-- Pass rate ring --}}
                <div class="relative w-14 h-14 shrink-0">
                    <svg class="w-14 h-14 -rotate-90" viewBox="0 0 56 56">
                        <circle cx="28" cy="28" r="22" fill="none" stroke="#f3f4f6" stroke-width="5"/>
                        <circle cx="28" cy="28" r="22" fill="none"
                                stroke="{{ $passRate >= 80 ? '#22c55e' : ($passRate >= 60 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="5"
                                stroke-dasharray="{{ round(2 * 3.14159 * 22) }}"
                                stroke-dashoffset="{{ round(2 * 3.14159 * 22 * (1 - $passRate / 100)) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center
                                 text-xs font-extrabold {{ $rateColor }}">
                        {{ $passRate }}%
                    </span>
                </div>

                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-extrabold text-green-800 bg-green-100 px-3 py-0.5
                                     rounded-xl text-sm border border-green-200">
                            Stall {{ $inspection->stall?->stall_number }}
                        </span>
                        @if($inspection->status === 'completed')
                            <span class="badge badge-green text-xs">✅ Completed</span>
                        @elseif($inspection->status === 'pending')
                            <span class="badge badge-yellow text-xs">⏳ Pending</span>
                        @else
                            <span class="badge badge-orange text-xs">🔄 Follow-up Required</span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mt-1">
                        <i data-feather="calendar" class="w-3.5 h-3.5 inline text-gray-400"></i>
                        {{ $inspection->inspection_date->format('l, F d Y — H:i') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <i data-feather="user" class="w-3 h-3 inline text-gray-400"></i>
                        Inspector: <strong>{{ $inspection->inspector?->name ?? '—' }}</strong>
                    </p>
                </div>
            </div>

            {{-- Summary pills --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1 text-xs font-semibold
                             px-3 py-1.5 rounded-full bg-green-100 text-green-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $passed }} Passed
                </span>
                @if($failed > 0)
                <span class="inline-flex items-center gap-1 text-xs font-semibold
                             px-3 py-1.5 rounded-full bg-red-100 text-red-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ $failed }} Failed
                </span>
                @endif
                <span class="text-xs text-gray-400">/ {{ $total }} items</span>

                <a href="{{ route('admin.my-inspections.show', $inspection) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold
                          px-3 py-1.5 rounded-xl bg-green-600 hover:bg-green-700
                          text-white transition-colors ml-2">
                    <i data-feather="eye" class="w-3.5 h-3.5"></i>
                    Full Report
                </a>
            </div>
        </div>

        {{-- General notes --}}
        @if($inspection->general_notes)
        <div class="mx-6 mb-4 p-3.5 {{ $bgColor }} rounded-xl border text-sm">
            <p class="font-semibold text-gray-700 mb-1 flex items-center gap-1.5">
                <i data-feather="message-square" class="w-3.5 h-3.5"></i>
                Inspector's Notes:
            </p>
            <p class="text-gray-600 leading-relaxed">{{ $inspection->general_notes }}</p>
        </div>
        @endif

        {{-- Failed items preview --}}
        @if($failed > 0)
        <div class="px-6 pb-5">
            <p class="text-xs font-bold text-red-700 mb-2 flex items-center gap-1.5">
                <i data-feather="alert-triangle" class="w-3.5 h-3.5"></i>
                Items requiring attention:
            </p>
            <div class="space-y-1.5">
                @foreach($inspection->items->where('status','fail') as $item)
                <div class="flex items-start gap-2.5 p-3 bg-red-50 border border-red-100 rounded-xl">
                    <div class="w-5 h-5 rounded-full bg-red-200 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-feather="x" class="w-3 h-3 text-red-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-red-800">{{ $item->item }}</p>
                        @if($item->notes)
                        <p class="text-xs text-red-600 mt-0.5">{{ $item->notes }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-16 text-gray-400">
        <i data-feather="clipboard" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
        <h3 class="font-semibold text-gray-500 mb-1">No inspections yet</h3>
        <p class="text-sm">Your stalls have not been inspected yet. An inspector will be assigned soon.</p>
    </div>
    @endforelse
</div>

@if($inspections->hasPages())
<div class="mt-5 flex justify-center">
    {{ $inspections->links() }}
</div>
@endif

@endsection
