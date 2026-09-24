@extends('admin.layouts.app')
@section('title', 'Edit Inspector')
@section('subtitle', 'Update inspector details')
@section('breadcrumb')
    <a href="{{ route('admin.users') }}" class="hover:text-green-600">Inspectors</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    {{-- Inspector card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full {{ $user->is_locked ? 'bg-red-100' : 'bg-blue-600' }}
                    flex items-center justify-center font-extrabold text-lg shrink-0
                    {{ $user->is_locked ? 'text-red-600' : 'text-white' }}">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
        </div>
        <div class="text-right">
            <span class="badge {{ $user->is_locked ? 'badge-red' : 'badge-green' }} text-xs">
                {{ $user->is_locked ? '🔒 Locked' : '✅ Active' }}
            </span>
            <p class="text-xs text-gray-400 mt-1">{{ $user->inspections_count ?? 0 }} inspections</p>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Account Details</h3>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                  @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="+250 788 000 000"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                </div>
            </div>

            {{-- Password section --}}
            <div class="pt-4 border-t border-dashed border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i data-feather="lock" class="w-4 h-4 text-gray-400"></i>
                    Change Password
                    <span class="text-gray-400 font-normal text-xs">(leave blank to keep current)</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                        <input type="password" name="password" minlength="8"
                               placeholder="Min. 8 characters"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               placeholder="Repeat password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary"
                        style="background:#2563eb" onmouseover="this.style.background='#1d4ed8'"
                        onmouseout="this.style.background='#2563eb'">
                    <i data-feather="save" class="w-4 h-4"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Assigned stalls preview --}}
    @php $assigned = $user->assignedStalls; @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-feather="map-pin" class="w-4 h-4 text-green-600"></i>
                <h3 class="font-bold text-gray-900">Assigned Stalls</h3>
                <span class="badge badge-green text-xs">{{ $assigned->count() }} assigned</span>
            </div>
            <a href="{{ route('admin.users.assign-stalls', $user) }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5
                      bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors">
                <i data-feather="edit-2" class="w-3.5 h-3.5"></i>
                Manage Assignments
            </a>
        </div>
        <div class="p-5">
            @if($assigned->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($assigned as $stall)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50
                             border border-green-200 rounded-xl text-sm font-semibold text-green-800">
                    <i data-feather="box" class="w-3.5 h-3.5 text-green-600"></i>
                    {{ $stall->stall_number }}
                    @if($stall->vendor)
                    <span class="text-green-600 font-normal">— {{ $stall->vendor->business_name }}</span>
                    @endif
                </span>
                @endforeach
            </div>
            @else
            <div class="text-center py-6 text-gray-400">
                <i data-feather="map-pin" class="w-7 h-7 mx-auto mb-2 text-gray-300"></i>
                <p class="text-sm">No stalls assigned yet.</p>
                <a href="{{ route('admin.users.assign-stalls', $user) }}"
                   class="inline-flex items-center gap-1.5 text-xs text-green-600 hover:underline
                          font-semibold mt-2">
                    Assign stalls now →
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
