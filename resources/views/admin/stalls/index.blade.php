@extends('admin.layouts.app')
@section('title', 'Stalls')
@section('subtitle', 'Market stall inventory and vendor assignments')

@section('content')

<div class="flex items-center justify-between mb-5">
    <span class="badge" style="background:#f3e8ff;color:#7e22ce;">{{ $stalls->total() }} stalls</span>
    <a href="{{ route('admin.stalls.create') }}" class="btn-primary">
        <i data-feather="plus" class="w-4 h-4"></i>Add Stall
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Stall #</th>
                    <th>Section</th>
                    <th>Market</th>
                    <th>Vendor</th>
                    <th>Devices</th>
                    <th>Status</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stalls as $stall)
                <tr>
                    <td>
                        <span class="font-bold text-green-800 bg-green-50 px-3 py-1 rounded-xl text-sm">
                            {{ $stall->stall_number }}
                        </span>
                    </td>
                    <td class="text-gray-600 text-sm">{{ $stall->section ?? '—' }}</td>
                    <td class="font-medium text-gray-800 text-sm">{{ $stall->market?->name ?? '—' }}</td>
                    <td>
                        @if($stall->vendor)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($stall->vendor->business_name ?? 'V', 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-700 truncate max-w-[120px]">
                                    {{ $stall->vendor->business_name ?? $stall->vendor->vendor_code }}
                                </span>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm italic">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @if($stall->devices->count() > 0)
                            <span class="badge badge-green text-xs font-mono">
                                {{ $stall->devices->count() }} device{{ $stall->devices->count() !== 1 ? 's' : '' }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">None</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $stall->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $stall->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-right pr-4">
                        <a href="{{ route('admin.stalls.edit', $stall) }}"
                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                            <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i data-feather="box" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No stalls found.</p>
                        <a href="{{ route('admin.stalls.create') }}" class="btn-primary inline-flex">
                            <i data-feather="plus" class="w-4 h-4"></i>Add Stall
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">Showing {{ $stalls->firstItem() }}–{{ $stalls->lastItem() }} of {{ $stalls->total() }}</p>
        {{ $stalls->links() }}
    </div>
</div>

@endsection
