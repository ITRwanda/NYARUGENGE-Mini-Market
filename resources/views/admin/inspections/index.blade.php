@extends('admin.layouts.app')
@section('title', 'Inspections')
@section('subtitle', 'Hygiene compliance inspection records')

@section('content')

@php
    $completedCount = \App\Models\Inspection::where('status','completed')->count();
    $pendingCount   = \App\Models\Inspection::where('status','pending')->count();
    $followUpCount  = \App\Models\Inspection::where('status','follow_up')->count();
@endphp

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 text-center">
        <p class="text-2xl font-bold text-green-700">{{ $completedCount }}</p>
        <p class="text-xs text-green-600 font-medium mt-0.5">Completed</p>
    </div>
    <div class="bg-yellow-50 rounded-2xl p-4 border border-yellow-100 text-center">
        <p class="text-2xl font-bold text-yellow-700">{{ $pendingCount }}</p>
        <p class="text-xs text-yellow-600 font-medium mt-0.5">Pending</p>
    </div>
    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-center">
        <p class="text-2xl font-bold text-orange-700">{{ $followUpCount }}</p>
        <p class="text-xs text-orange-600 font-medium mt-0.5">Follow-up</p>
    </div>
</div>

<div class="flex justify-end mb-4">
    <a href="{{ route('admin.inspections.create') }}" class="btn-primary">
        <i data-feather="plus" class="w-4 h-4"></i>New Inspection
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Market</th>
                    <th>Stall</th>
                    <th>Inspector</th>
                    <th>Items</th>
                    <th>Pass Rate</th>
                    <th>Status</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $inspection)
                @php
                    $total    = $inspection->items->count();
                    $passed   = $inspection->items->where('status','pass')->count();
                    $failed   = $inspection->items->where('status','fail')->count();
                    $passRate = $total > 0 ? round(($passed/$total)*100) : 0;
                @endphp
                <tr>
                    <td class="whitespace-nowrap">
                        <p class="font-semibold text-gray-900 text-sm">{{ $inspection->inspection_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $inspection->inspection_date->format('H:i') }}</p>
                    </td>
                    <td class="font-medium text-gray-700 text-sm">{{ $inspection->market?->name ?? '—' }}</td>
                    <td>
                        <span class="font-bold text-green-800 bg-green-50 px-2 py-0.5 rounded-xl text-xs">
                            {{ $inspection->stall?->stall_number ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700 shrink-0">
                                {{ strtoupper(substr($inspection->inspector?->name ?? 'I', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-700 truncate max-w-[100px]">{{ $inspection->inspector?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="text-sm text-gray-700">
                        <span class="text-green-600 font-semibold">{{ $passed }}✓</span>
                        @if($failed > 0) <span class="text-red-500 font-semibold ml-1">{{ $failed }}✗</span> @endif
                        <span class="text-gray-400"> / {{ $total }}</span>
                    </td>
                    <td>
                        @if($total > 0)
                        <div class="flex items-center gap-2">
                            <div class="w-16 h-1.5 bg-gray-100 rounded-full">
                                <div class="h-1.5 rounded-full {{ $passRate >= 80 ? 'bg-green-500' : ($passRate >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                     style="width:{{ $passRate }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $passRate >= 80 ? 'text-green-600' : ($passRate >= 60 ? 'text-yellow-600' : 'text-red-500') }}">
                                {{ $passRate }}%
                            </span>
                        </div>
                        @else
                            <span class="text-gray-300 text-xs">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($inspection->status === 'completed') <span class="badge badge-green">✅ Completed</span>
                        @elseif($inspection->status === 'pending') <span class="badge badge-yellow">⏳ Pending</span>
                        @else <span class="badge badge-orange">🔄 Follow-up</span>
                        @endif
                    </td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.inspections.show', $inspection) }}"
                               class="text-xs text-gray-600 hover:text-gray-900 font-medium px-2.5 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                View
                            </a>
                            <a href="{{ route('admin.inspections.edit', $inspection) }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i data-feather="clipboard" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No inspections found.</p>
                        <a href="{{ route('admin.inspections.create') }}" class="btn-primary inline-flex">
                            <i data-feather="plus" class="w-4 h-4"></i>Create First Inspection
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ $inspections->total() }} total inspections</p>
        {{ $inspections->links() }}
    </div>
</div>

@endsection
