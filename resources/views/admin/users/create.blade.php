@extends('admin.layouts.app')
@section('title', 'Add New User')
@section('subtitle', 'Register a user and assign their role')
@section('breadcrumb')
    <a href="{{ route('admin.users') }}" class="hover:text-green-600">Users</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Create</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-feather="user-plus" class="w-5 h-5 text-green-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">New User Account</h3>
                <p class="text-xs text-gray-400">Fill in all required fields</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Jean-Pierre Habimana"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="user@market.rw"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           placeholder="+250 788 000 000"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role *</label>
                    <select name="role" id="roleSelect" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                        <option value="">— Select role —</option>
                        <option value="admin"        {{ old('role') === 'admin'        ? 'selected' : '' }}>Admin</option>
                        <option value="market_admin" {{ old('role') === 'market_admin' ? 'selected' : '' }}>Market Admin</option>
                        <option value="inspector"    {{ old('role') === 'inspector'    ? 'selected' : '' }}>Inspector</option>
                        <option value="vendor"       {{ old('role') === 'vendor'       ? 'selected' : '' }}>Vendor</option>
                    </select>
                    @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password *</label>
                    <input type="password" name="password" required minlength="8"
                           placeholder="Min. 8 characters"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Repeat password"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>

            </div>

            {{-- Vendor-specific fields --}}
            <div id="vendorFields" class="hidden space-y-5 pt-4 border-t border-dashed border-green-200">
                <p class="text-sm font-semibold text-green-700 flex items-center gap-2">
                    <i data-feather="shopping-bag" class="w-4 h-4"></i>
                    Vendor Profile (required for Vendor role)
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign Market *</label>
                        <select name="market_id"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white">
                            <option value="">— Select market —</option>
                            @foreach($markets as $market)
                                <option value="{{ $market->id }}" {{ old('market_id') == $market->id ? 'selected' : '' }}>
                                    {{ $market->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('market_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Business Name</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}"
                               placeholder="e.g. Nyirabeza Fresh Produce"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Food Category</label>
                        <input type="text" name="food_category" value="{{ old('food_category') }}"
                               placeholder="e.g. Vegetables & Fruits"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    </div>
                </div>
            </div>

            {{-- Role info box --}}
            <div id="roleInfo" class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700 hidden">
                <div id="roleInfoText"></div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const roleDescriptions = {
    admin:        'Full system access. Can manage users, markets, devices, alerts, and all settings.',
    market_admin: 'Can manage markets, vendors, stalls, devices and view all monitoring data.',
    inspector:    'Can conduct and record hygiene inspections, view sensor data and alerts.',
    vendor:       'Limited view — sees their own stall sensor data and alerts only.',
};

const select = document.getElementById('roleSelect');
const vendorFields = document.getElementById('vendorFields');
const roleInfo = document.getElementById('roleInfo');
const roleInfoText = document.getElementById('roleInfoText');

function updateRole() {
    const role = select.value;
    vendorFields.classList.toggle('hidden', role !== 'vendor');
    if (role && roleDescriptions[role]) {
        roleInfo.classList.remove('hidden');
        roleInfoText.textContent = '🔐 ' + roleDescriptions[role];
    } else {
        roleInfo.classList.add('hidden');
    }
}

select.addEventListener('change', updateRole);
updateRole(); // init
</script>
@endpush
