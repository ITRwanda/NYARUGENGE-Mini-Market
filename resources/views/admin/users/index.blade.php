@extends('admin.layouts.app')
@section('title', 'User Management')
@section('subtitle', 'Register users and assign roles')

@section('content')

{{-- Role summary --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        'admin'        => ['label'=>'Admins',        'color'=>'bg-red-50 border-red-100 text-red-700',     'icon'=>'shield'],
        'market_admin' => ['label'=>'Market Admins',  'color'=>'bg-purple-50 border-purple-100 text-purple-700', 'icon'=>'briefcase'],
        'inspector'    => ['label'=>'Inspectors',     'color'=>'bg-blue-50 border-blue-100 text-blue-700', 'icon'=>'search'],
        'vendor'       => ['label'=>'Vendors',        'color'=>'bg-green-50 border-green-100 text-green-700','icon'=>'shopping-bag'],
    ] as $role => $meta)
    <div class="rounded-2xl p-5 border {{ $meta['color'] }} flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-white/60 flex items-center justify-center">
            <i data-feather="{{ $meta['icon'] }}" class="w-5 h-5"></i>
        </div>
        <div>
            <p class="text-2xl font-extrabold">{{ $roleCounts[$role] ?? 0 }}</p>
            <p class="text-xs font-medium mt-0.5">{{ $meta['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div>
            <h3 class="font-bold text-gray-900">All Users</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $users->total() }} registered</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i data-feather="user-plus" class="w-4 h-4"></i>
            Add User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Inspections</th>
                    <th>Joined</th>
                    <th class="text-right pr-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                @switch($user->role)
                                    @case('admin') bg-red-100 text-red-700 @break
                                    @case('market_admin') bg-purple-100 text-purple-700 @break
                                    @case('inspector') bg-blue-100 text-blue-700 @break
                                    @default bg-green-100 text-green-700
                                @endswitch">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-green-600 font-medium">(You)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-600 text-sm">{{ $user->email }}</td>
                    <td class="text-gray-500 text-sm">{{ $user->phone ?? '—' }}</td>
                    <td>
                        @switch($user->role)
                            @case('admin') <span class="badge badge-red">Admin</span> @break
                            @case('market_admin') <span class="badge" style="background:#f3e8ff;color:#7e22ce;">Market Admin</span> @break
                            @case('inspector') <span class="badge badge-blue">Inspector</span> @break
                            @default <span class="badge badge-green">Vendor</span>
                        @endswitch
                    </td>
                    <td class="text-center font-semibold text-gray-700">{{ $user->inspections_count ?? 0 }}</td>
                    <td class="text-xs text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="text-right pr-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i data-feather="trash-2" class="w-3.5 h-3.5"></i>Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i data-feather="users" class="w-8 h-8 mx-auto mb-2"></i>
                        <p>No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-3">
        <p class="text-sm text-gray-500">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </p>
        {{ $users->links() }}
    </div>
</div>

@endsection
