@extends('admin.layouts.app')
@section('title', 'Inspection Details')
@section('subtitle', 'Full checklist report for your stall')
@section('breadcrumb')
    <a href="{{ route('admin.my-inspections') }}" class="hover:text-green-600">Inspection Results</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $inspection->inspection_date->format('M d, Y') }}</span>
@endsection

@section('content')

@php
    $total    = $inspection->items->count();
    $passed   = $inspection->items->where('status','pass')->count();
    $failed   = $inspection->items->where('status','fail')->count();
    $na       = $inspection->items->where('status','not_applicable')->count();
    $passRate = $total > 0 ? round(($passed / $total) * 100) : 0;
@endphp

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Top stripe --}}
        <div class="h-2 {{ $passRate >= 80 ? 'bg-green-500' : ($passRate >= 60 ? 'bg-yellow-400' : 'bg-red-500') }}"></div>

        <div class="p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
                        Stall {{ $inspection->stall?->stall_number }}
                        @if($inspection->stall?->vendor)
                        <span class="text-sm font-normal text-gray-500">
                            — {{ $inspection->stall->vendor->business_name }}
                        </span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $inspection->inspection_date->format('l, F d Y — H:i') }}
                    </p>
                </div>

                @if($inspection->status === 'completed')
                    <span class="badge badge-green text-sm px-3 py-1.5">✅ Completed</span>
                @elseif($inspection->status === 'pending')
                    <span class="badge badge-yellow text-sm px-3 py-1.5">⏳ Pending</span>
                @else
                    <span class="badge badge-orange text-sm px-3 py-1.5">🔄 Follow-up Required</span>
                @endif
            </div>

            {{-- Pass rate bar + stats --}}
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="col-span-2 md:col-span-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                        <span class="font-semibold">Overall Pass Rate</span>
                        <span class="font-bold {{ $passRate >= 80 ? 'text-green-600' : ($passRate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $passed }} / {{ $total }} items passed
                        </span>
                    </div>
                    <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-3 rounded-full transition-all {{ $passRate >= 80 ? 'bg-green-500' : ($passRate >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $passRate }}%"></div>
                    </div>
                    <p class="text-right text-lg font-extrabold mt-1
                               {{ $passRate >= 80 ? 'text-green-600' : ($passRate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $passRate }}%
                    </p>
                </div>

                <div class="bg-green-50 rounded-xl p-4 text-center border border-green-100">
                    <p class="text-2xl font-extrabold text-green-700">{{ $passed }}</p>
                    <p class="text-xs text-green-600 mt-0.5 font-medium">✅ Passed</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center border border-red-100">
                    <p class="text-2xl font-extrabold text-red-700">{{ $failed }}</p>
                    <p class="text-xs text-red-500 mt-0.5 font-medium">❌ Failed</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                    <p class="text-2xl font-extrabold text-gray-600">{{ $na }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 font-medium">— N/A</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center border border-blue-100">
                    <p class="text-2xl font-extrabold text-blue-700">{{ $total }}</p>
                    <p class="text-xs text-blue-500 mt-0.5 font-medium">Total Items</p>
                </div>
            </div>

            {{-- Inspector info --}}
            <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center
                            text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr($inspection->inspector?->name ?? 'I', 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $inspection->inspector?->name ?? 'Unknown Inspector' }}
                    </p>
                    <p class="text-xs text-gray-400">Certified Market Inspector</p>
                </div>
            </div>

            {{-- General notes --}}
            @if($inspection->general_notes)
            <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <p class="text-xs font-bold text-blue-700 mb-1.5 flex items-center gap-1.5">
                    <i data-feather="message-square" class="w-3.5 h-3.5"></i>
                    General Notes from Inspector
                </p>
                <p class="text-sm text-blue-800 leading-relaxed">{{ $inspection->general_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Checklist --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <i data-feather="check-square" class="w-5 h-5 text-green-600"></i>
            <h3 class="font-bold text-gray-900">Full Checklist ({{ $total }} items)</h3>
        </div>

        {{-- Failed first, then passed --}}
        @php
            $sortedItems = $inspection->items->sortByDesc(fn($i) => $i->status === 'fail' ? 2 : ($i->status === 'pass' ? 1 : 0));
        @endphp

        <div class="divide-y divide-gray-50">
            @forelse($sortedItems as $item)
            <div class="px-6 py-4 flex items-start gap-4
                        {{ $item->status === 'fail' ? 'bg-red-50/60' : '' }}
                        hover:bg-gray-50/50 transition-colors">

                {{-- Status icon --}}
                <div class="mt-0.5 shrink-0">
                    @if($item->status === 'pass')
                    <div class="w-7 h-7 rounded-full bg-green-100 border border-green-200
                                flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    @elseif($item->status === 'fail')
                    <div class="w-7 h-7 rounded-full bg-red-100 border border-red-200
                                flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    @else
                    <div class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200
                                flex items-center justify-center">
                        <span class="text-gray-400 text-xs font-bold">—</span>
                    </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold
                               {{ $item->status === 'fail' ? 'text-red-900' : 'text-gray-900' }}">
                        {{ $item->item }}
                    </p>
                    @if($item->notes)
                    <div class="mt-1.5 flex items-start gap-1.5">
                        <i data-feather="alert-circle" class="w-3.5 h-3.5 text-red-500 mt-0.5 shrink-0"></i>
                        <p class="text-xs text-red-700 leading-relaxed">{{ $item->notes }}</p>
                    </div>
                    @endif
                </div>

                <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full
                    {{ $item->status === 'pass'
                        ? 'bg-green-100 text-green-700 border border-green-200'
                        : ($item->status === 'fail'
                            ? 'bg-red-100 text-red-700 border border-red-200'
                            : 'bg-gray-100 text-gray-500 border border-gray-200') }}">
                    {{ $item->status === 'not_applicable' ? 'N/A' : ucfirst($item->status) }}
                </span>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400">
                No checklist items recorded.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Action --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.my-inspections') }}"
           class="btn-secondary">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Back to Results
        </a>
        @if($failed > 0)
        <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl text-sm text-orange-700
                    flex items-center gap-2 max-w-sm">
            <i data-feather="alert-triangle" class="w-4 h-4 shrink-0"></i>
            <p><strong>{{ $failed }} item{{ $failed > 1 ? 's' : '' }}</strong> failed this inspection.
               Please address these issues before the next inspection.</p>
        </div>
        @endif
    </div>
</div>

@endsection
