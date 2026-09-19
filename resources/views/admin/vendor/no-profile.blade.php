@extends('admin.layouts.app')
@section('title', 'Profile Setup Needed')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-5">
            <i data-feather="alert-circle" class="w-10 h-10 text-yellow-500"></i>
        </div>
        <h2 class="text-xl font-extrabold text-gray-900 mb-2">No Vendor Profile Linked</h2>
        <p class="text-gray-500 text-sm leading-relaxed mb-6">
            Your account doesn't have a vendor profile associated with it yet.
            Please contact the system administrator to set up your vendor profile and assign you to a market.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('admin.profile') }}" class="btn-primary">
                <i data-feather="user" class="w-4 h-4"></i>
                View My Profile
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn-secondary w-full sm:w-auto">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
