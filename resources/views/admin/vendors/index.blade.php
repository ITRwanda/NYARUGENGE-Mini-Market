@extends('admin.layouts.app')
@section('title', 'Vendors')
@section('subtitle', 'All registered market vendors')

@section('content')

<div class="flex items-center justify-between mb-5">
    <span class="badge badge-blue">{{ $vendors->total() }} vendors</span>
    <a href="{{ route('admin.vendors.create') }}" class="btn-primary">
        <i data-feather="plus" class="w-4 h-4"></i>Add Vendor
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Owner</th>
                    <th>Code</th>
                    <th>Market</th>
                    <th>Category</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-700 font-bold text-xs shrink-0">
                                {{ strtoupper(substr($vendor->business_name ?? 'V', 0, 2)) }}
                            </div>
                            <span class="font-semibold text-gray-900">{{ $vendor->business_name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="text-gray-700 text-sm">{{ $vendor->user?->name ?? '—' }}</td>
                    <td>
                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-lg">
                            {{ $vendor->vendor_code }}
                        </span>
                    </td>
                    <td class="text-sm text-gray-700">{{ $vendor->market?->name ?? '—' }}</td>
                    <td>
                        @if($vendor->food_category)
                            <span class="badge badge-blue text-xs">{{ $vendor->food_category }}</span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="text-gray-500 text-sm">{{ $vendor->phone ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $vendor->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-xs text-gray-400 whitespace-nowrap">{{ $vendor->created_at->format('M d, Y') }}</td>
                    <td class="text-right pr-4">
                        <a href="{{ route('admin.vendors.edit', $vendor) }}"
                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                            <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-12 text-gray-400">
                        <i data-feather="users" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No vendors found.</p>
                        <a href="{{ route('admin.vendors.create') }}" class="btn-primary inline-flex">
                            <i data-feather="plus" class="w-4 h-4"></i>Add Vendor
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">Showing {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} of {{ $vendors->total() }}</p>
        {{ $vendors->links() }}
    </div>
</div>

@endsection
