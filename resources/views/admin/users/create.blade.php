@extends('admin.layouts.app')
@section('title', 'Register Inspector')
@section('subtitle', 'Add a new hygiene inspector')
@section('breadcrumb')
    <a href="{{ route('admin.users') }}" class="hover:text-green-600">Inspectors</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">Register New</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-feather="user-plus" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">New Inspector Account</h3>
                <p class="text-xs text-gray-400">
                    Role is fixed to <strong>Inspector</strong> — for vendor registration use the Vendors section.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g. Patrick Nzeyimana"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      @error('name') border-red-400 @enderror">
                    </div>
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="mail" class="w-4 h-4"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="inspector@nyarugenge.rw"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      @error('email') border-red-400 @enderror">
                    </div>
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="phone" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="+250 788 000 000"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password *</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password" id="password" required minlength="8"
                               placeholder="Min. 8 characters"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password *</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password_confirmation" required
                               placeholder="Repeat password"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- Role badge (display only) --}}
            <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <i data-feather="shield" class="w-4 h-4 text-blue-500 shrink-0"></i>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Role: Inspector</p>
                    <p class="text-xs text-blue-600 mt-0.5">
                        This account will be able to conduct hygiene inspections and view sensor data.
                        After registration, assign specific stalls for this inspector to monitor.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary"
                        style="background: #2563eb;" onmouseover="this.style.background='#1d4ed8'"
                        onmouseout="this.style.background='#2563eb'">
                    <i data-feather="user-plus" class="w-4 h-4"></i>
                    Register Inspector
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
