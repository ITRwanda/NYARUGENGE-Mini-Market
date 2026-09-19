<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::withCount('inspections')
            ->latest()
            ->paginate(20);

        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    public function create()
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.create', compact('markets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string|max:20',
            'role'        => 'required|in:admin,market_admin,inspector,vendor',
            'password'    => 'required|string|min:8|confirmed',
            // Vendor-specific
            'market_id'   => 'required_if:role,vendor|nullable|exists:markets,id',
            'business_name' => 'nullable|string|max:255',
            'food_category' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Auto-create vendor profile for vendor role
        if ($validated['role'] === 'vendor' && !empty($validated['market_id'])) {
            $lastVendor = Vendor::orderByDesc('id')->first();
            $nextNum    = $lastVendor ? ($lastVendor->id + 1) : 1;

            Vendor::create([
                'user_id'       => $user->id,
                'market_id'     => $validated['market_id'],
                'vendor_code'   => 'VND-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT),
                'business_name' => $validated['business_name'] ?? $user->name,
                'phone'         => $validated['phone'],
                'food_category' => $validated['food_category'] ?? null,
                'is_active'     => true,
            ]);
        }

        return redirect()->route('admin.users')
            ->with('success', "User \"{$user->name}\" created successfully.");
    }

    public function edit(User $user)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'markets'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,market_admin,inspector,vendor',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role'  => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "User \"{$name}\" deleted.");
    }
}
