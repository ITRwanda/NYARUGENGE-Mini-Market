@extends('admin.layouts.app')
@section('title', 'Edit Vendor')
@section('subtitle', 'Update vendor profile')
@section('breadcrumb')
    <a href="{{ route('admin.vendors') }}" class="hover:text-green-600">Vendors</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $vendor->business_name ?? $vendor->vendor_code }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                {{ strtoupper(substr($vendor->business_name ?? 'V', 0, 2)) }}
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ $vendor->business_name ?? $vendor->vendor_code }}</h3>
                <p class="text-xs text-gray-400 font-mono">{{ $vendor->vendor_code }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.vendors.update', $vendor) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">User Account</label>
                    <select name="user_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— No linked account —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $vendor->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                    <select name="market_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" {{ old('market_id', $vendor->market_id) == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $vendor->business_name) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Food Category</label>
                    <input type="text" name="food_category" value="{{ old('food_category', $vendor->food_category) }}"
                           list="categoryList"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    <datalist id="categoryList">
                        @foreach(['Vegetables & Fruits','Meat & Poultry','Dairy Products','Fish & Seafood','Grains & Cereals','Spices & Condiments','Bakery & Pastry','Beverages','General Goods'] as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">Vendor is active</label>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}"
                      onsubmit="return confirm('Delete this vendor?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i data-feather="trash-2" class="w-4 h-4"></i>Delete
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('admin.vendors') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i data-feather="save" class="w-4 h-4"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
