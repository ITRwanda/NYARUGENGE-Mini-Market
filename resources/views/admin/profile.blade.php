@extends('admin.layouts.app')
@section('title', 'My Profile')
@section('subtitle', 'Update your account details')

@section('content')

<div class="max-w-2xl mx-auto space-y-5">

    {{-- Profile card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-600 flex items-center justify-center text-white font-extrabold text-xl shrink-0">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $user->name }}</h3>
                <div class="flex items-center gap-2 mt-1">
                    @switch($user->role)
                        @case('admin') <span class="badge badge-red">Admin</span> @break
                        @case('market_admin') <span class="badge" style="background:#f3e8ff;color:#7e22ce;">Market Admin</span> @break
                        @case('inspector') <span class="badge badge-blue">Inspector</span> @break
                        @default <span class="badge badge-green">Vendor</span>
                    @endswitch
                    <span class="text-xs text-gray-400">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">Contact an admin to change your email address.</p>
            </div>

            @if($user->isVendor() && $user->vendor)
            <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                <p class="text-sm font-semibold text-green-800 mb-2">Vendor Profile</p>
                <div class="grid grid-cols-2 gap-3 text-sm text-green-700">
                    <div><span class="text-green-500">Business:</span> {{ $user->vendor->business_name ?? '—' }}</div>
                    <div><span class="text-green-500">Code:</span> <span class="font-mono">{{ $user->vendor->vendor_code }}</span></div>
                    <div><span class="text-green-500">Market:</span> {{ $user->vendor->market?->name ?? '—' }}</div>
                    <div><span class="text-green-500">Category:</span> {{ $user->vendor->food_category ?? '—' }}</div>
                </div>
            </div>
            @endif

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i data-feather="save" class="w-4 h-4"></i>
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    {{-- Change password --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Change Password</h3>
            <p class="text-xs text-gray-400 mt-0.5">Use a strong password of at least 8 characters</p>
        </div>

        <form method="POST" action="{{ route('admin.profile.password') }}" class="p-6 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 @error('current_password') border-red-400 @enderror">
                @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500">
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i data-feather="lock" class="w-4 h-4"></i>
                    Update Password
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
