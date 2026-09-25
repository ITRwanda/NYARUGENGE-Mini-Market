@extends('admin.layouts.app')
@section('title', 'Register User')
@section('subtitle', 'Create an inspector or vendor account with login credentials')
@section('breadcrumb')
    <a href="{{ route('admin.users') }}" class="hover:text-green-600">User Management</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Register</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center" id="headerIcon">
                <i data-feather="user-plus" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900" id="headerTitle">Register New User</h3>
                <p class="text-xs text-gray-400" id="headerSub">Choose a role to configure the account</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- ── ROLE TOGGLE ─────────────────────────────────────── --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Account Role *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label id="btn-inspector"
                           class="role-btn flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('role', request('role', 'inspector')) === 'inspector' ? 'border-blue-500 bg-blue-50' : 'border-gray-100 hover:border-gray-300' }}">
                        <input type="radio" name="role" value="inspector"
                               {{ old('role', request('role', 'inspector')) === 'inspector' ? 'checked' : '' }}
                               class="sr-only" onchange="switchRole('inspector')">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                            <i data-feather="shield" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Inspector</p>
                            <p class="text-xs text-gray-400">Conducts hygiene inspections</p>
                        </div>
                    </label>

                    <label id="btn-vendor"
                           class="role-btn flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ old('role', request('role', 'inspector')) === 'vendor' ? 'border-purple-500 bg-purple-50' : 'border-gray-100 hover:border-gray-300' }}">
                        <input type="radio" name="role" value="vendor"
                               {{ old('role', request('role', 'inspector')) === 'vendor' ? 'checked' : '' }}
                               class="sr-only" onchange="switchRole('vendor')">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                            <i data-feather="shopping-bag" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Vendor</p>
                            <p class="text-xs text-gray-400">Owns stalls, views sensor data</p>
                        </div>
                    </label>
                </div>
                @error('role')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ── CREDENTIALS (both roles) ─────────────────────────── --}}
            <div class="pt-2 border-t border-gray-100">
                <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <i data-feather="user" class="w-4 h-4 text-gray-400"></i>Login Credentials
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g. Patrick Nzeyimana"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="user@nyarugenge.rw"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="+250 788 000 000"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password *</label>
                        <input type="password" name="password" required minlength="8"
                               placeholder="Min. 8 characters"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      @error('password') border-red-400 @enderror">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               placeholder="Repeat password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- ── VENDOR PROFILE FIELDS (hidden for inspector) ────── --}}
            <div id="vendorFields"
                 class="{{ old('role', request('role', 'inspector')) === 'vendor' ? '' : 'hidden' }}
                        pt-2 border-t border-gray-100 space-y-5">

                <p class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <i data-feather="shopping-bag" class="w-4 h-4 text-purple-400"></i>
                    Vendor Profile
                    <span class="text-xs font-normal text-gray-400">— auto-created with account</span>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign to Market *</label>
                        <select name="market_id"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                       focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 bg-white
                                       @error('market_id') border-red-400 @enderror">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Vendor Code *</label>
                        <input type="text" name="vendor_code"
                               value="{{ old('vendor_code', 'VND-' . str_pad(\App\Models\Vendor::count() + 1, 4, '0', STR_PAD_LEFT)) }}"
                               placeholder="VND-0001"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500
                                      @error('vendor_code') border-red-400 @enderror">
                        @error('vendor_code')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Business Name</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}"
                               placeholder="e.g. Nyirabeza Fresh Produce"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Food Category</label>
                        <input type="text" name="food_category" value="{{ old('food_category') }}"
                               placeholder="e.g. Vegetables & Fruits"
                               list="categoryList"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500">
                        <datalist id="categoryList">
                            @foreach(['Vegetables & Fruits','Meat & Poultry','Dairy Products','Fish & Seafood','Grains & Cereals','Spices & Condiments','Bakery & Pastry','Beverages','General Goods'] as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Assign Stall
                            <span class="text-xs font-normal text-gray-400">(optional — can assign later)</span>
                        </label>
                        <select name="stall_id"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                       focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 bg-white">
                            <option value="">— No stall yet —</option>
                            @foreach($stalls->whereNull('vendor_id') as $stall)
                                <option value="{{ $stall->id }}" {{ old('stall_id') == $stall->id ? 'selected' : '' }}>
                                    {{ $stall->stall_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Credentials summary box --}}
                <div class="p-4 bg-purple-50 border border-purple-100 rounded-xl text-xs text-purple-700 space-y-1">
                    <p class="font-semibold flex items-center gap-1.5">
                        <i data-feather="info" class="w-3.5 h-3.5"></i>
                        What gets created
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-purple-600 ml-1">
                        <li>A login account with the email &amp; password above</li>
                        <li>A linked vendor profile with business details</li>
                        <li>The vendor can immediately log in and view their sensor data</li>
                    </ul>
                </div>
            </div>

            {{-- ── INSPECTOR INFO BOX ───────────────────────────────── --}}
            <div id="inspectorInfo"
                 class="{{ old('role', request('role', 'inspector')) === 'vendor' ? 'hidden' : '' }}
                        p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <p class="text-sm font-semibold text-blue-800 flex items-center gap-1.5">
                    <i data-feather="shield" class="w-4 h-4"></i>Inspector Account
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    After registration, go to <strong>Edit &rarr; Assign Stalls</strong> to configure which stalls this inspector monitors.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Cancel</a>
                <button type="submit" id="submitBtn" class="btn-primary">
                    <i data-feather="user-plus" class="w-4 h-4"></i>
                    <span id="submitLabel">Register Inspector</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchRole(role) {
    const isVendor = role === 'vendor';

    // Toggle vendor fields
    document.getElementById('vendorFields').classList.toggle('hidden', !isVendor);
    document.getElementById('inspectorInfo').classList.toggle('hidden', isVendor);

    // Toggle button styles
    const btnI = document.getElementById('btn-inspector');
    const btnV = document.getElementById('btn-vendor');
    btnI.className = btnI.className.replace(/border-\w+-500 bg-\w+-50/g, '').replace('border-gray-100', '');
    btnV.className = btnV.className.replace(/border-\w+-500 bg-\w+-50/g, '').replace('border-gray-100', '');

    if (isVendor) {
        btnV.classList.add('border-purple-500', 'bg-purple-50');
        btnI.classList.add('border-gray-100');
    } else {
        btnI.classList.add('border-blue-500', 'bg-blue-50');
        btnV.classList.add('border-gray-100');
    }

    // Update header + submit button
    document.getElementById('headerTitle').textContent = isVendor ? 'Register New Vendor' : 'Register New Inspector';
    document.getElementById('headerSub').textContent   = isVendor
        ? 'Creates login account + vendor profile in one step'
        : 'Creates a hygiene inspector login account';
    document.getElementById('submitLabel').textContent  = isVendor ? 'Register Vendor' : 'Register Inspector';

    const btn = document.getElementById('submitBtn');
    btn.style.background = isVendor ? '#7c3aed' : '';
}

// Init on page load (handles old() re-population after validation error)
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="role"]:checked');
    if (checked) switchRole(checked.value);
    feather.replace();
});
</script>
@endpush
