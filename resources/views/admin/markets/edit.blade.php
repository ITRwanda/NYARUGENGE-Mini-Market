@extends('admin.layouts.app')
@section('title', 'Edit Market')
@section('subtitle', 'Update market information')
@section('breadcrumb')
    <a href="{{ route('admin.markets') }}" class="hover:text-green-600">Markets</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $market->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-feather="map-pin" class="w-5 h-5 text-green-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ $market->name }}</h3>
                <p class="text-xs text-gray-400">{{ $market->district }}, {{ $market->city }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.markets.update', $market) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market Name *</label>
                <input type="text" name="name" value="{{ old('name', $market->name) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">District</label>
                    <input type="text" name="district" value="{{ old('district', $market->district) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city', $market->city) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Country</label>
                    <input type="text" name="country" value="{{ old('country', $market->country) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 resize-none">{{ old('description', $market->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $market->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Market is active</label>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <button type="submit" form="delete-form"
                        onclick="return confirm('Delete this market? All related data will be removed.')"
                        class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                    <i data-feather="trash-2" class="w-4 h-4"></i>Delete Market
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('admin.markets') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i data-feather="save" class="w-4 h-4"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>

        {{-- Delete form lives OUTSIDE the edit form to avoid nested-form HTML issue --}}
        <form id="delete-form" method="POST" action="{{ route('admin.markets.destroy', $market) }}" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>
</div>
@endsection
