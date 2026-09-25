@extends('admin.layouts.app')
@section('title', 'User Management')
@section('subtitle', 'Manage inspector and vendor accounts')

@section('content')

{{-- Stats row --}}
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100 text-center md:col-span-2">
        <p class="text-2xl font-extrabold text-blue-800">{{ $stats['inspectors_total'] }}</p>
        <p class="text-xs text-blue-600 font-medium mt-0.5">Total Inspectors</p>
    </div>
    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 text-center">
        <p class="text-2xl font-extrabold text-green-800">{{ $stats['inspectors_active'] }}</p>
        <p class="text-xs text-green-600 font-medium mt-0.5">Active</p>
    </div>
    <div class="bg-red-50 rounded-2xl p-4 border border-red-100 text-center">
        <p class="text-2xl font-extrabold text-red-800">{{ $stats['inspectors_locked'] }}</p>
        <p class="text-xs text-red-500 font-medium mt-0.5">Locked</p>
    </div>
    <div class="bg-purple-50 rounded-2xl p-4 border border-purple-100 text-center">
        <p class="text-2xl font-extrabold text-purple-800">{{ $stats['vendors_total'] }}</p>
        <p class="text-xs text-purple-600 font-medium mt-0.5">Total Vendors</p>
    </div>
    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100 text-center">
        <p class="text-2xl font-extrabold text-orange-800">{{ $stats['vendors_locked'] }}</p>
        <p class="text-xs text-orange-500 font-medium mt-0.5">Locked</p>
    </div>
</div>

{{-- Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Tab header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 gap-4 flex-wrap">
        <div class="flex rounded-xl bg-gray-100 p-1 gap-1">
            <a href="{{ route('admin.users', ['tab' => 'inspectors']) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
                      {{ $tab === 'inspectors' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i data-feather="shield" class="w-3.5 h-3.5 inline mr-1"></i>
                Inspectors
                <span class="ml-1.5 text-xs {{ $tab === 'inspectors' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-500' }} font-bold px-1.5 py-0.5 rounded-full">
                    {{ $stats['inspectors_total'] }}
                </span>
            </a>
            <a href="{{ route('admin.users', ['tab' => 'vendors']) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
                      {{ $tab === 'vendors' ? 'bg-white text-purple-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <i data-feather="shopping-bag" class="w-3.5 h-3.5 inline mr-1"></i>
                Vendors
                <span class="ml-1.5 text-xs {{ $tab === 'vendors' ? 'bg-purple-100 text-purple-700' : 'bg-gray-200 text-gray-500' }} font-bold px-1.5 py-0.5 rounded-full">
                    {{ $stats['vendors_total'] }}
                </span>
            </a>
        </div>
        <a href="{{ route('admin.users.create', ['role' => $tab === 'vendors' ? 'vendor' : 'inspector']) }}"
           class="btn-primary {{ $tab === 'vendors' ? 'bg-purple-600 hover:bg-purple-700' : '' }}">
            <i data-feather="user-plus" class="w-4 h-4"></i>
            Register {{ $tab === 'vendors' ? 'Vendor' : 'Inspector' }}
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         INSPECTORS TAB
    ════════════════════════════════════════════════════════ --}}
    @if($tab === 'inspectors')
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Inspector</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Inspections</th>
                    <th>Assigned Stalls</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspectors as $inspector)
                <tr class="{{ $inspector->is_locked ? 'bg-red-50/30' : '' }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                        {{ $inspector->is_locked ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper(substr($inspector->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $inspector->name }}</p>
                                @if($inspector->is_locked)
                                    <span class="text-xs text-red-500 font-medium flex items-center gap-1">
                                        <i data-feather="lock" class="w-3 h-3"></i>Locked
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-600 text-sm">{{ $inspector->email }}</td>
                    <td class="text-gray-500 text-sm">{{ $inspector->phone ?? '—' }}</td>
                    <td class="text-center font-semibold text-gray-900">{{ $inspector->inspections_count }}</td>
                    <td>
                        @php $cnt = $inspector->assignedStalls()->count(); @endphp
                        @if($cnt > 0)
                            <span class="text-xs bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded-full">{{ $cnt }} stalls</span>
                        @else
                            <span class="text-xs text-gray-400">None</span>
                        @endif
                    </td>
                    <td>
                        @if($inspector->is_locked)
                            <span class="badge badge-red"><i data-feather="lock" class="w-3 h-3"></i>Locked</span>
                        @else
                            <span class="badge badge-green"><span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>Active</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-400">{{ $inspector->created_at->format('M d, Y') }}</td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.users.assign-stalls', $inspector) }}"
                               class="text-xs text-green-700 hover:text-green-900 font-medium px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                <i data-feather="map-pin" class="w-3.5 h-3.5 inline"></i> Stalls
                            </a>
                            <a href="{{ route('admin.users.edit', $inspector) }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                <i data-feather="edit-2" class="w-3.5 h-3.5 inline"></i> Edit
                            </a>
                            @if($inspector->is_locked)
                            <form method="POST" action="{{ route('admin.users.unlock', $inspector) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-green-700 hover:text-green-900 font-medium px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                    <i data-feather="unlock" class="w-3.5 h-3.5 inline"></i> Unlock
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.lock', $inspector) }}">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('Lock {{ addslashes($inspector->name) }}?')"
                                        class="text-xs text-yellow-700 hover:text-yellow-900 font-medium px-2.5 py-1.5 rounded-lg hover:bg-yellow-50 transition-colors">
                                    <i data-feather="lock" class="w-3.5 h-3.5 inline"></i> Lock
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.users.destroy', $inspector) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete {{ addslashes($inspector->name) }} permanently?')"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i data-feather="trash-2" class="w-3.5 h-3.5 inline"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i data-feather="users" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No inspectors registered yet.</p>
                        <a href="{{ route('admin.users.create', ['role' => 'inspector']) }}" class="btn-primary inline-flex">
                            <i data-feather="user-plus" class="w-4 h-4"></i>Register First Inspector
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ $inspectors->total() }} inspectors</p>
        {{ $inspectors->appends(['tab' => 'inspectors'])->links() }}
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         VENDORS TAB
    ════════════════════════════════════════════════════════ --}}
    @if($tab === 'vendors')
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Business</th>
                    <th>Email / Login</th>
                    <th>Phone</th>
                    <th>Category</th>
                    <th>Stalls</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendorUser)
                <tr class="{{ $vendorUser->is_locked ? 'bg-red-50/30' : '' }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                        {{ $vendorUser->is_locked ? 'bg-red-100 text-red-600' : 'bg-purple-100 text-purple-700' }}">
                                {{ strtoupper(substr($vendorUser->vendor?->business_name ?? $vendorUser->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $vendorUser->name }}</p>
                                @if($vendorUser->vendor)
                                    <p class="text-xs font-mono text-gray-400">{{ $vendorUser->vendor->vendor_code }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-sm text-gray-700 font-medium">
                        {{ $vendorUser->vendor?->business_name ?? '—' }}
                    </td>
                    <td>
                        <p class="text-sm text-gray-600">{{ $vendorUser->email }}</p>
                        <p class="text-xs text-gray-400">Login credential</p>
                    </td>
                    <td class="text-gray-500 text-sm">{{ $vendorUser->phone ?? '—' }}</td>
                    <td>
                        @if($vendorUser->vendor?->food_category)
                            <span class="text-xs bg-orange-50 text-orange-700 font-medium px-2 py-0.5 rounded-lg">
                                {{ $vendorUser->vendor->food_category }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td>
                        @php $stallCount = $vendorUser->vendor?->stalls->count() ?? 0; @endphp
                        @if($stallCount > 0)
                            <span class="text-xs bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded-full">
                                {{ $stallCount }} {{ $stallCount === 1 ? 'stall' : 'stalls' }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">None</span>
                        @endif
                    </td>
                    <td>
                        @if($vendorUser->is_locked)
                            <span class="badge badge-red"><i data-feather="lock" class="w-3 h-3"></i>Locked</span>
                        @else
                            <span class="badge badge-green"><span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>Active</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-400">{{ $vendorUser->created_at->format('M d, Y') }}</td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.users.edit', $vendorUser) }}"
                               class="text-xs text-purple-600 hover:text-purple-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-purple-50 transition-colors">
                                <i data-feather="edit-2" class="w-3.5 h-3.5 inline"></i> Edit
                            </a>
                            @if($vendorUser->is_locked)
                            <form method="POST" action="{{ route('admin.users.unlock', $vendorUser) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-green-700 hover:text-green-900 font-medium px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                    <i data-feather="unlock" class="w-3.5 h-3.5 inline"></i> Unlock
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.lock', $vendorUser) }}">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('Lock {{ addslashes($vendorUser->name) }}? They cannot log in.')"
                                        class="text-xs text-yellow-700 hover:text-yellow-900 font-medium px-2.5 py-1.5 rounded-lg hover:bg-yellow-50 transition-colors">
                                    <i data-feather="lock" class="w-3.5 h-3.5 inline"></i> Lock
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.users.destroy', $vendorUser) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete {{ addslashes($vendorUser->name) }} and their vendor profile?')"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i data-feather="trash-2" class="w-3.5 h-3.5 inline"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-12 text-gray-400">
                        <i data-feather="shopping-bag" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No vendor accounts yet.</p>
                        <a href="{{ route('admin.users.create', ['role' => 'vendor']) }}" class="btn-primary inline-flex" style="background:#7c3aed">
                            <i data-feather="user-plus" class="w-4 h-4"></i>Register First Vendor
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">{{ $vendors->total() }} vendor accounts</p>
        {{ $vendors->appends(['tab' => 'vendors'])->links() }}
    </div>
    @endif

</div>
@endsection
