<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Stall;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Admin manages both INSPECTORS and VENDORS.
 *
 * Inspector actions: create, edit, lock/unlock, assign stalls, delete
 * Vendor actions:    create (with auto Vendor profile), edit credentials,
 *                    lock/unlock, delete
 */
class UserManagementController extends Controller
{
    /*──────────────────────────────────────────────────────────
     | INDEX — tabs: Inspectors / Vendors
     *──────────────────────────────────────────────────────────*/

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'inspectors'); // 'inspectors' | 'vendors'

        $inspectors = User::where('role', 'inspector')
            ->withCount('inspections')
            ->latest()
            ->paginate(20, ['*'], 'inspectors_page');

        $vendors = User::where('role', 'vendor')
            ->with('vendor.stalls')
            ->latest()
            ->paginate(20, ['*'], 'vendors_page');

        $stats = [
            'inspectors_total'  => User::where('role', 'inspector')->count(),
            'inspectors_active' => User::where('role', 'inspector')->where('is_locked', false)->count(),
            'inspectors_locked' => User::where('role', 'inspector')->where('is_locked', true)->count(),
            'vendors_total'     => User::where('role', 'vendor')->count(),
            'vendors_active'    => User::where('role', 'vendor')->where('is_locked', false)->count(),
            'vendors_locked'    => User::where('role', 'vendor')->where('is_locked', true)->count(),
        ];

        return view('admin.users.index', compact('inspectors', 'vendors', 'stats', 'tab'));
    }

    /*──────────────────────────────────────────────────────────
     | CREATE / STORE — both roles on one form
     *──────────────────────────────────────────────────────────*/

    public function create()
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $stalls  = Stall::with('vendor')->orderBy('stall_number')->get();

        return view('admin.users.create', compact('markets', 'stalls'));
    }

    public function store(Request $request)
    {
        $role = $request->input('role', 'inspector');

        // Common validation
        $rules = [
            'role'     => 'required|in:inspector,vendor',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Extra vendor-profile rules
        if ($role === 'vendor') {
            $rules['market_id']     = 'required|exists:markets,id';
            $rules['vendor_code']   = 'required|string|max:50|unique:vendors,vendor_code';
            $rules['business_name'] = 'nullable|string|max:255';
            $rules['food_category'] = 'nullable|string|max:255';
            $rules['phone_vendor']  = 'nullable|string|max:20';
            $rules['stall_id']      = 'nullable|exists:stalls,id';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $role, $request) {

            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'] ?? null,
                'role'      => $role,
                'password'  => Hash::make($validated['password']),
                'is_locked' => false,
            ]);

            if ($role === 'vendor') {
                $vendor = Vendor::create([
                    'user_id'       => $user->id,
                    'market_id'     => $validated['market_id'],
                    'vendor_code'   => $validated['vendor_code'],
                    'business_name' => $validated['business_name'] ?? null,
                    'phone'         => $validated['phone_vendor'] ?? $validated['phone'] ?? null,
                    'food_category' => $validated['food_category'] ?? null,
                    'is_active'     => true,
                ]);

                // Optionally assign a stall immediately
                if (! empty($validated['stall_id'])) {
                    Stall::where('id', $validated['stall_id'])
                        ->update(['vendor_id' => $vendor->id]);
                }
            }
        });

        $label = $role === 'vendor' ? 'Vendor' : 'Inspector';
        return redirect()->route('admin.users', ['tab' => $role . 's'])
            ->with('success', "{$label} \"{$validated['name']}\" registered successfully.");
    }

    /*──────────────────────────────────────────────────────────
     | EDIT / UPDATE — credentials for both roles
     *──────────────────────────────────────────────────────────*/

    public function edit(User $user)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();

        if ($user->role === 'inspector') {
            $user->loadCount('inspections');
            $user->load('assignedStalls.vendor');
        }

        if ($user->role === 'vendor') {
            $user->load('vendor.stalls');
        }

        return view('admin.users.edit', compact('user', 'markets'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Vendor profile fields
        if ($user->role === 'vendor') {
            $rules['market_id']     = 'required|exists:markets,id';
            $rules['business_name'] = 'nullable|string|max:255';
            $rules['food_category'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $user, $request) {

            $data = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];
            if (! empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }
            $user->update($data);

            if ($user->role === 'vendor' && $user->vendor) {
                $user->vendor->update([
                    'market_id'     => $validated['market_id'],
                    'business_name' => $validated['business_name'] ?? $user->vendor->business_name,
                    'food_category' => $validated['food_category'] ?? $user->vendor->food_category,
                    'phone'         => $validated['phone'] ?? $user->vendor->phone,
                ]);
            }
        });

        $tab = $user->role === 'vendor' ? 'vendors' : 'inspectors';
        return redirect()->route('admin.users', ['tab' => $tab])
            ->with('success', "\"{$user->name}\" updated successfully.");
    }

    /*──────────────────────────────────────────────────────────
     | LOCK / UNLOCK — both roles
     *──────────────────────────────────────────────────────────*/

    public function lock(User $user)
    {
        $this->ensureNotAdmin($user);
        $user->update(['is_locked' => true]);
        $tab = $user->role === 'vendor' ? 'vendors' : 'inspectors';
        return back()->with('success', "\"{$user->name}\" has been locked.");
    }

    public function unlock(User $user)
    {
        $this->ensureNotAdmin($user);
        $user->update(['is_locked' => false]);
        return back()->with('success', "\"{$user->name}\" has been unlocked.");
    }

    /*──────────────────────────────────────────────────────────
     | DELETE — both roles
     *──────────────────────────────────────────────────────────*/

    public function destroy(User $user)
    {
        $this->ensureNotAdmin($user);
        $name = $user->name;
        $tab  = $user->role === 'vendor' ? 'vendors' : 'inspectors';
        $user->delete(); // cascade handled by DB / model
        return redirect()->route('admin.users', ['tab' => $tab])
            ->with('success', "\"{$name}\" deleted.");
    }

    /*──────────────────────────────────────────────────────────
     | INSPECTOR-ONLY — stall assignment
     *──────────────────────────────────────────────────────────*/

    public function assignStalls(User $user)
    {
        $this->ensureIsInspector($user);

        $market = Market::where('is_active', true)->firstOrFail();
        $stalls = Stall::with('vendor')
            ->where('market_id', $market->id)
            ->orderBy('stall_number')
            ->get();

        $assignedIds = $user->assignedStalls->pluck('id')->toArray();

        return view('admin.users.assign-stalls', compact('user', 'stalls', 'assignedIds', 'market'));
    }

    public function saveStalls(Request $request, User $user)
    {
        $this->ensureIsInspector($user);

        $request->validate([
            'stall_ids'   => 'nullable|array',
            'stall_ids.*' => 'exists:stalls,id',
        ]);

        $user->assignedStalls()->sync($request->input('stall_ids', []));

        return redirect()->route('admin.users')
            ->with('success', "Stall assignments updated for \"{$user->name}\".");
    }

    /*──────────────────────────────────────────────────────────
     | HELPERS
     *──────────────────────────────────────────────────────────*/

    private function ensureIsInspector(User $user): void
    {
        if ($user->role !== 'inspector') {
            abort(403, 'This action is for inspectors only.');
        }
    }

    private function ensureNotAdmin(User $user): void
    {
        if ($user->role === 'admin') {
            abort(403, 'Admin accounts cannot be modified here.');
        }
    }
}
