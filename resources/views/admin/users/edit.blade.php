@extends('admin.layouts.app')
@section('title', $user->role === 'vendor' ? 'Edit Vendor Account' : 'Edit Inspector')
@section('subtitle', 'Update login credentials' . ($user->role === 'vendor' ? ' and vendor profile' : ''))
@section('breadcrumb')
    <a href="{{ route('admin.users', ['tab' => $user->role === 'vendor' ? 'vendors' : 'inspectors']) }}"
       class="hover:text-green-600">User Management</a>
    <i data-feather="chevron-right" class="w-3 h-3"></i>
    <span class="text-gray-700">{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Identity card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center font-extrabold text-lg shrink-0
                    {{ $user->is_locked ? 'bg-red-100 text-red-600' : ($user->role === 'vendor' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
            {{ strtoupper(substr($user->vendor?->business_name ?? $user->name, 0, 2)) }}
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
            @if($user->role === 'vendor' && $user->vendor)
                <p class="text-xs font-mono text-purple-500 mt-0.5">{{ $user->vendor->vendor_code }} &bull; {{ $user->vendor->business_name }}</p>
            @endif
        </div>
        <div class="text-right space-y-1">
            <span class="badge {{ $user->role === 'vendor' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} text-xs capitalize">
                {{ $user->role }}
            </span>
            <br>
            <span class="badge {{ $user->is_locked ? 'badge-red' : 'badge-green' }} text-xs">
                {{ $user->is_locked ? '🔒 Locked' : '✅ Active' }}
            </span>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Login Credentials</h3>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" id="edit-form" class="p-6 space-y-5">
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
                        <input type="password" name="password" minlength="8" placeholder="Min. 8 characters"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500
                                      @error('password') border-red-400 @enderror">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Repeat password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- Vendor profile section --}}
            @if($user->role === 'vendor')
            <div class="pt-4 border-t border-dashed border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i data-feather="shopping-bag" class="w-4 h-4 text-purple-400"></i>Vendor Profile
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Market *</label>
                        <select name="market_id" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                       focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 bg-white
                                       @error('market_id') border-red-400 @enderror">
                            @foreach($markets as $market)
                                <option value="{{ $market->id }}"
                                        {{ old('market_id', $user->vendor?->market_id) == $market->id ? 'selected' : '' }}>
                                    {{ $market->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('market_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Vendor Code</label>
                        <input type="text" value="{{ $user->vendor?->vendor_code }}" disabled
                               class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm font-mono
                                      bg-gray-50 text-gray-500 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Vendor code cannot be changed</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Business Name</label>
                        <input type="text" name="business_name"
                               value="{{ old('business_name', $user->vendor?->business_name) }}"
                               placeholder="e.g. Nyirabeza Fresh Produce"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Food Category</label>
                        <input type="text" name="food_category"
                               value="{{ old('food_category', $user->vendor?->food_category) }}"
                               list="categoryList"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500">
                        <datalist id="categoryList">
                            @foreach(['Vegetables & Fruits','Meat & Poultry','Dairy Products','Fish & Seafood','Grains & Cereals','Spices & Condiments','Bakery & Pastry','Beverages','General Goods'] as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                {{-- Stalls summary (read-only) --}}
                @if($user->vendor?->stalls->count() > 0)
                <div class="mt-4 p-4 bg-purple-50 rounded-xl border border-purple-100">
                    <p class="text-xs font-semibold text-purple-700 mb-2">Assigned Stalls</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->vendor->stalls as $stall)
                            <span class="text-xs bg-white border border-purple-200 text-purple-800 font-bold px-2.5 py-1 rounded-lg">
                                {{ $stall->stall_number }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-purple-500 mt-2">
                        Manage stalls in the <a href="{{ route('admin.vendors') }}" class="underline font-semibold">Vendors section</a>.
                    </p>
                </div>
                @endif
            </div>
            @endif

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <a href="{{ route('admin.users', ['tab' => $user->role === 'vendor' ? 'vendors' : 'inspectors']) }}"
                   class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary"
                        style="{{ $user->role === 'vendor' ? 'background:#7c3aed' : 'background:#2563eb' }}">
                    <i data-feather="save" class="w-4 h-4"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Inspector: assigned stalls panel --}}
    @if($user->role === 'inspector')
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
                <i data-feather="edit-2" class="w-3.5 h-3.5"></i>Manage
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
                   class="inline-flex items-center gap-1.5 text-xs text-green-600 hover:underline font-semibold mt-2">
                    Assign stalls now &rarr;
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
