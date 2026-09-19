@extends('admin.layouts.app')
@section('title', 'Inspection Details')
@section('subtitle', 'Full inspection checklist record')
@section('breadcrumb')
    <a href="{{ route('admin.inspections') }}" class="hover:text-green-600">Inspections</a>
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
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">Inspection Report</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $inspection->inspection_date->format('l, F d, Y — H:i') }}
                </p>
            </div>
            @if($inspection->status === 'completed') <span class="badge badge-green text-sm px-3 py-1">✅ Completed</span>
            @elseif($inspection->status === 'pending') <span class="badge badge-yellow text-sm px-3 py-1">⏳ Pending</span>
            @else <span class="badge badge-orange text-sm px-3 py-1">🔄 Follow-up</span> @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 mb-1">Market</p>
                <p class="font-semibold text-gray-900 text-sm">{{ $inspection->market?->name ?? '—' }}</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 mb-1">Stall</p>
                <p class="font-bold text-green-700 bg-green-50 inline-block px-2 py-0.5 rounded-lg text-sm">{{ $inspection->stall?->stall_number ?? '—' }}</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 mb-1">Inspector</p>
                <p class="font-semibold text-gray-900 text-sm">{{ $inspection->inspector?->name ?? '—' }}</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 mb-1">Pass Rate</p>
                <p class="font-bold text-2xl {{ $passRate >= 80 ? 'text-green-600' : ($passRate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $passRate }}%</p>
            </div>
        </div>

        {{-- Progress bar --}}
        @if($total > 0)
        <div class="mt-5">
            <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                <span>Pass rate</span>
                <span>{{ $passed }} / {{ $total }} items</span>
            </div>
            <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $passRate >= 80 ? 'bg-green-500' : ($passRate >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                     style="width: {{ $passRate }}%"></div>
            </div>
            <div class="flex gap-4 mt-2 text-xs">
                <span class="text-green-600 font-semibold">{{ $passed }} Pass</span>
                <span class="text-red-500 font-semibold">{{ $failed }} Fail</span>
                <span class="text-gray-400">{{ $na }} N/A</span>
            </div>
        </div>
        @endif

        @if($inspection->general_notes)
        <div class="mt-5 p-4 bg-blue-50 rounded-xl border border-blue-100">
            <p class="text-xs font-semibold text-blue-700 mb-1">General Notes</p>
            <p class="text-sm text-blue-800">{{ $inspection->general_notes }}</p>
        </div>
        @endif
    </div>

    {{-- Checklist items --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Checklist Items</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($inspection->items as $item)
            <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50/50 transition-colors">
                <div class="mt-0.5 shrink-0">
                    @if($item->status === 'pass')
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                            <i data-feather="check" class="w-3.5 h-3.5 text-green-600"></i>
                        </div>
                    @elseif($item->status === 'fail')
                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                            <i data-feather="x" class="w-3.5 h-3.5 text-red-500"></i>
                        </div>
                    @else
                        <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-gray-400 text-xs font-bold">—</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $item->item }}</p>
                    @if($item->notes)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item->notes }}</p>
                    @endif
                </div>
                <span class="shrink-0 text-xs font-semibold px-2.5 py-0.5 rounded-full
                    {{ $item->status === 'pass' ? 'bg-green-100 text-green-700' :
                       ($item->status === 'fail' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                    {{ ucfirst(str_replace('_',' ',$item->status)) }}
                </span>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400">
                <i data-feather="list" class="w-8 h-8 mx-auto mb-2"></i>
                <p>No checklist items recorded</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="flex gap-3 justify-end">
        <a href="{{ route('admin.inspections') }}" class="btn-secondary">Back to list</a>
        @if(auth()->user()->isAdmin() || auth()->user()->isMarketAdmin() || auth()->user()->isInspector())
        <a href="{{ route('admin.inspections.edit', $inspection) }}" class="btn-primary">
            <i data-feather="edit-2" class="w-4 h-4"></i>
            Edit Inspection
        </a>
        @endif
    </div>
</div>

@endsection
