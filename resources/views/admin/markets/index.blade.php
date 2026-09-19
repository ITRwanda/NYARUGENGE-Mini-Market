@extends('admin.layouts.app')
@section('title', 'Markets')
@section('subtitle', 'Manage all registered markets')

@section('content')

<div class="flex items-center justify-between mb-6">
    <span class="badge badge-green">{{ $markets->total() }} markets</span>
    <a href="{{ route('admin.markets.create') }}" class="btn-primary">
        <i data-feather="plus" class="w-4 h-4"></i>Add Market
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">
    @forelse($markets as $market)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
        <div class="h-1.5" style="background: linear-gradient(90deg,#16a34a,#22c55e);"></div>
        <div class="p-6">
            <div class="flex items-start justify-between mb-2">
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-900 text-base truncate">{{ $market->name }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                        <i data-feather="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                        {{ $market->district }}, {{ $market->city }}, {{ $market->country }}
                    </p>
                </div>
                <span class="badge {{ $market->is_active ? 'badge-green' : 'badge-gray' }} ml-3 shrink-0">
                    {{ $market->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($market->description)
            <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed">{{ $market->description }}</p>
            @endif

            <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('admin.vendors', ['market_id' => $market->id]) }}" class="hover:bg-gray-50 rounded-lg p-2 transition-colors">
                    <p class="text-xl font-bold text-gray-900">{{ $market->vendors_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Vendors</p>
                </a>
                <a href="{{ route('admin.stalls', ['market_id' => $market->id]) }}" class="border-x border-gray-100 hover:bg-gray-50 rounded-lg p-2 transition-colors">
                    <p class="text-xl font-bold text-gray-900">{{ $market->stalls_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Stalls</p>
                </a>
                <a href="{{ route('admin.devices', ['market_id' => $market->id]) }}" class="hover:bg-gray-50 rounded-lg p-2 transition-colors">
                    <p class="text-xl font-bold text-gray-900">{{ $market->devices_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Devices</p>
                </a>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Added {{ $market->created_at->format('M d, Y') }}</span>
                <a href="{{ route('admin.markets.edit', $market) }}"
                   class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-semibold px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                    <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Edit
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-16 text-gray-400">
        <i data-feather="map-pin" class="w-10 h-10 mx-auto mb-3"></i>
        <p class="text-sm mb-4">No markets found.</p>
        <a href="{{ route('admin.markets.create') }}" class="btn-primary inline-flex">
            <i data-feather="plus" class="w-4 h-4"></i>Add First Market
        </a>
    </div>
    @endforelse
</div>

<div class="flex justify-center">{{ $markets->links() }}</div>

@endsection
