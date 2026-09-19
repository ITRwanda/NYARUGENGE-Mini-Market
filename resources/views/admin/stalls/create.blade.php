@extends('admin.layouts.app')
@section('title', 'Add Stall')
@section('subtitle', 'Register a new stall in a market')
@section('breadcrumb')
    <a href="{{ route('admin.stalls') }}" class="hover:text-green-600">Stalls</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Create</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                <i data-feather="box" class="w-5 h-5 text-purple-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">New Stall</h3>
                <p class="text-xs text-gray-400">Assign it to a market and optionally a vendor</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stalls.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                    <select name="market_id" required id="marketSelect"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white @error('market_id') border-red-400 @enderror">
                        <option value="">— Select market —</option>
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('market_id') == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('market_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stall Number *</label>
                    <input type="text" name="stall_number" value="{{ old('stall_number') }}" required
                           placeholder="e.g. S-001"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 @error('stall_number') border-red-400 @enderror">
                    @error('stall_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Section</label>
                    <input type="text" name="section" value="{{ old('section') }}"
                           placeholder="e.g. Section A"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign Vendor</label>
                    <select name="vendor_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— Unassigned —</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->business_name ?? $vendor->vendor_code }} ({{ $vendor->market?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="2" placeholder="Optional notes about this stall..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Stall is active</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.stalls') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>Create Stall
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
