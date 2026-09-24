@extends('admin.layouts.app')
@section('title', 'Edit Stall')
@section('subtitle', 'Update stall details')
@section('breadcrumb')
    <a href="{{ route('admin.stalls') }}" class="hover:text-green-600">Stalls</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $stall->stall_number }}</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center
                        text-white font-extrabold text-sm">
                {{ $stall->stall_number }}
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Stall {{ $stall->stall_number }}</h3>
                <p class="text-xs text-gray-400">{{ $stall->section ?? 'No section' }}
                    &bull; {{ $market->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stalls.update', $stall) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stall Number *</label>
                    <input type="text" name="stall_number" value="{{ old('stall_number', $stall->stall_number) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Section</label>
                    <input type="text" name="section" value="{{ old('section', $stall->section) }}"
                           list="sectionList"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    <datalist id="sectionList">
                        @foreach(['Section A','Section B','Section C','Section D','Section E'] as $s)
                            <option value="{{ $s }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Assigned Vendor
                        <span class="text-gray-400 font-normal text-xs">(one vendor can have multiple stalls)</span>
                    </label>
                    <select name="vendor_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— Unassigned —</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}"
                                    {{ old('vendor_id', $stall->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->business_name ?? $vendor->vendor_code }}
                                ({{ $vendor->food_category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500
                                     resize-none">{{ old('description', $stall->description) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $stall->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                    Stall is active
                </label>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.stalls.destroy', $stall) }}"
                      onsubmit="return confirm('Delete stall {{ $stall->stall_number }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800
                                   font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i data-feather="trash-2" class="w-4 h-4"></i>Delete
                    </button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('admin.stalls') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i data-feather="save" class="w-4 h-4"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
