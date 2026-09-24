<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Admin manages INSPECTORS only.
 *
 * Admins can:
 *   - Register a new inspector
 *   - Update inspector info (name, email, phone, password)
 *   - Lock / unlock (disable / enable) an inspector account
 *   - Delete an inspector account
 *
 * Vendors and other admins are NOT managed here.
 */
class UserManagementController extends Controller
{
    /*──────────────────────────────────────────────────────────*/

    public function index()
    {
        $inspectors = User::where('role', 'inspector')
            ->withCount('inspections')
            ->latest()
            ->paginate(20);

        $stats = [
            'total'  => User::where('role', 'inspector')->count(),
            'active' => User::where('role', 'inspector')->where('is_locked', false)->count(),
            'locked' => User::where('role', 'inspector')->where('is_locked', true)->count(),
        ];

        return view('admin.users.index', compact('inspectors', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'nullable|string|max:20',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $inspector = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'role'      => 'inspector',              // always inspector
            'password'  => Hash::make($validated['password']),
            'is_locked' => false,
        ]);

        return redirect()->route('admin.users')
            ->with('success', "Inspector \"{$inspector->name}\" registered successfully.");
    }

    public function edit(User $user)
    {
        $this->ensureIsInspector($user);
        $user->loadCount('inspections');
        $user->load('assignedStalls.vendor');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureIsInspector($user);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', "Inspector \"{$user->name}\" updated.");
    }

    /** Lock — prevent inspector from logging in */
    public function lock(User $user)
    {
        $this->ensureIsInspector($user);
        $user->update(['is_locked' => true]);
        return back()->with('success', "\"{$user->name}\" has been locked.");
    }

    /** Unlock — restore inspector login access */
    public function unlock(User $user)
    {
        $this->ensureIsInspector($user);
        $user->update(['is_locked' => false]);
        return back()->with('success', "\"{$user->name}\" has been unlocked.");
    }

    /** Show stall-assignment page */
    public function assignStalls(User $user)
    {
        $this->ensureIsInspector($user);

        $market = \App\Models\Market::where('is_active', true)->firstOrFail();
        $stalls = \App\Models\Stall::with('vendor')
            ->where('market_id', $market->id)
            ->orderBy('stall_number')
            ->get();

        $assignedIds = $user->assignedStalls->pluck('id')->toArray();

        return view('admin.users.assign-stalls', compact('user', 'stalls', 'assignedIds', 'market'));
    }

    /** Save stall assignments */
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

    public function destroy(User $user)
    {
        $this->ensureIsInspector($user);

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Inspector \"{$name}\" deleted.");
    }

    /*──────────────────────────────────────────────────────────*/

    private function ensureIsInspector(User $user): void
    {
        if ($user->role !== 'inspector') {
            abort(403, 'This section manages inspectors only.');
        }
    }
}
