@extends('admin.layouts.app')
@section('title', 'Inspector Management')
@section('subtitle', 'Register, update, lock or delete inspectors')

@section('content')

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center">
            <i data-feather="users" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-blue-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-blue-600 font-medium mt-0.5">Total Inspectors</p>
        </div>
    </div>
    <div class="bg-green-50 rounded-2xl p-5 border border-green-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center">
            <i data-feather="user-check" class="w-5 h-5 text-green-600"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-green-800">{{ $stats['active'] }}</p>
            <p class="text-xs text-green-600 font-medium mt-0.5">Active</p>
        </div>
    </div>
    <div class="bg-red-50 rounded-2xl p-5 border border-red-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center">
            <i data-feather="lock" class="w-5 h-5 text-red-500"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-red-800">{{ $stats['locked'] }}</p>
            <p class="text-xs text-red-500 font-medium mt-0.5">Locked</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div>
            <h3 class="font-bold text-gray-900">Inspectors</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $inspectors->total() }} registered</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i data-feather="user-plus" class="w-4 h-4"></i>Register Inspector
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Inspector</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Inspections</th>
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
                            <div class="w-9 h-9 rounded-full flex items-center justify-center
                                        font-bold text-sm shrink-0
                                        {{ $inspector->is_locked ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper(substr($inspector->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $inspector->name }}</p>
                                @if($inspector->is_locked)
                                    <span class="text-xs text-red-500 font-medium flex items-center gap-1">
                                        <i data-feather="lock" class="w-3 h-3"></i>Account locked
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-600 text-sm">{{ $inspector->email }}</td>
                    <td class="text-gray-500 text-sm">{{ $inspector->phone ?? '—' }}</td>
                    <td class="text-center">
                        <span class="font-semibold text-gray-900">{{ $inspector->inspections_count }}</span>
                    </td>
                    <td>
                        @if($inspector->is_locked)
                            <span class="badge badge-red">
                                <i data-feather="lock" class="w-3 h-3"></i>Locked
                            </span>
                        @else
                            <span class="badge badge-green">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                                Active
                            </span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-400">{{ $inspector->created_at->format('M d, Y') }}</td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.users.assign-stalls', $inspector) }}"
                               class="text-xs text-green-700 hover:text-green-900 font-medium
                                      px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition-colors"
                               title="Assign stalls">
                                <i data-feather="map-pin" class="w-3.5 h-3.5 inline"></i>
                                Stalls
                                @php $cnt = $inspector->assignedStalls()->count(); @endphp
                                @if($cnt > 0)
                                    <span class="ml-0.5 text-xs bg-green-100 text-green-700
                                                 font-bold px-1.5 py-0.5 rounded-full">{{ $cnt }}</span>
                                @endif
                            </a>

                            <a href="{{ route('admin.users.edit', $inspector) }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium
                                      px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                <i data-feather="edit-2" class="w-3.5 h-3.5 inline"></i>Edit
                            </a>

                            {{-- Lock / Unlock --}}
                            @if($inspector->is_locked)
                            <form method="POST" action="{{ route('admin.users.unlock', $inspector) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs text-green-700 hover:text-green-900 font-medium
                                               px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition-colors">
                                    <i data-feather="unlock" class="w-3.5 h-3.5 inline"></i>Unlock
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.lock', $inspector) }}"
                                  onsubmit="return confirm('Lock {{ addslashes($inspector->name) }}? They will not be able to log in.')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs text-yellow-700 hover:text-yellow-900 font-medium
                                               px-2.5 py-1.5 rounded-lg hover:bg-yellow-50 transition-colors">
                                    <i data-feather="lock" class="w-3.5 h-3.5 inline"></i>Lock
                                </button>
                            </form>
                            @endif

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.users.destroy', $inspector) }}"
                                  onsubmit="return confirm('Permanently delete {{ addslashes($inspector->name) }} and all their inspections?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium
                                               px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i data-feather="trash-2" class="w-3.5 h-3.5 inline"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i data-feather="users" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="mb-3">No inspectors registered yet.</p>
                        <a href="{{ route('admin.users.create') }}" class="btn-primary inline-flex">
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
        {{ $inspectors->links() }}
    </div>
</div>

@endsection
