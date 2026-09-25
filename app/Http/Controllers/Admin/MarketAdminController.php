<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Device;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Models\Market;
use App\Models\SensorReading;
use App\Models\Stall;
use App\Models\Threshold;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketAdminController extends Controller
{
    /*==========================================================================
     | MARKETS
     *=========================================================================*/

    /** Resolve the single market dynamically — no hardcoded ID */
    private function getMarket(): \App\Models\Market
    {
        return \App\Models\Market::where('is_active', true)->firstOrFail();
    }

    public function markets()
    {
        $markets = Market::withCount(['vendors', 'stalls', 'devices'])
            ->latest()->paginate(15);

        return view('admin.markets.index', compact('markets'));
    }

    public function createMarket()
    {
        return view('admin.markets.create');
    }

    public function storeMarket(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'district'    => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:255',
            'country'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Market::create($data);

        return redirect()->route('admin.markets')
            ->with('success', 'Market created successfully.');
    }

    public function editMarket(Market $market)
    {
        return view('admin.markets.edit', compact('market'));
    }

    public function updateMarket(Request $request, Market $market)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'district'    => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:255',
            'country'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $market->update($data);

        return redirect()->route('admin.markets')
            ->with('success', 'Market updated successfully.');
    }

    public function destroyMarket(Market $market)
    {
        $market->delete();

        return redirect()->route('admin.markets')
            ->with('success', 'Market deleted.');
    }

    /*==========================================================================
     | VENDORS
     *=========================================================================*/

    public function vendors(Request $request)
    {
        $query = Vendor::with(['user', 'market']);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        $vendors = $query->latest()->paginate(15)->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function createVendor()
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $users   = User::where('role', 'vendor')->orderBy('name')->get();

        return view('admin.vendors.create', compact('markets', 'users'));
    }

    public function storeVendor(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'nullable|exists:users,id',
            'market_id'     => 'required|exists:markets,id',
            'vendor_code'   => 'required|string|unique:vendors,vendor_code',
            'business_name' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'food_category' => 'nullable|string|max:255',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Vendor::create($data);

        return redirect()->route('admin.vendors')
            ->with('success', 'Vendor created successfully.');
    }

    public function editVendor(Vendor $vendor)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $users   = User::where('role', 'vendor')->orderBy('name')->get();

        return view('admin.vendors.edit', compact('vendor', 'markets', 'users'));
    }

    public function updateVendor(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'user_id'       => 'nullable|exists:users,id',
            'market_id'     => 'required|exists:markets,id',
            'business_name' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'food_category' => 'nullable|string|max:255',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $vendor->update($data);

        return redirect()->route('admin.vendors')
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroyVendor(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.vendors')
            ->with('success', 'Vendor deleted.');
    }

    /*==========================================================================
     | STALLS
     *=========================================================================*/

    public function stalls(Request $request)
    {
        $query = Stall::with(['market', 'vendor', 'devices']);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        $stalls = $query->latest()->paginate(15)->withQueryString();

        return view('admin.stalls.index', compact('stalls'));
    }

    public function createStall()
    {
        $market  = $this->getMarket();
        $vendors = Vendor::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view('admin.stalls.create', compact('market', 'vendors'));
    }

    public function storeStall(Request $request)
    {
        $market = $this->getMarket();

        $data = $request->validate([
            'vendor_id'    => 'nullable|exists:vendors,id',
            'stall_number' => 'required|string|max:50',
            'section'      => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $data['market_id'] = $market->id;
        $data['is_active'] = $request->boolean('is_active', true);
        Stall::create($data);

        return redirect()->route('admin.stalls')
            ->with('success', 'Stall created successfully.');
    }

    public function editStall(Stall $stall)
    {
        $market  = $this->getMarket();
        $vendors = Vendor::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view('admin.stalls.edit', compact('stall', 'market', 'vendors'));
    }

    public function updateStall(Request $request, Stall $stall)
    {
        $data = $request->validate([
            'vendor_id'    => 'nullable|exists:vendors,id',
            'stall_number' => 'required|string|max:50',
            'section'      => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $stall->update($data);

        return redirect()->route('admin.stalls')
            ->with('success', 'Stall updated successfully.');
    }

    public function destroyStall(Stall $stall)
    {
        $stall->delete();

        return redirect()->route('admin.stalls')
            ->with('success', 'Stall deleted.');
    }

    /*==========================================================================
     | DEVICES
     *=========================================================================*/

    public function devices(Request $request)
    {
        $query = Device::with(['market', 'stall']);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->latest()->paginate(15)->withQueryString();

        return view('admin.devices.index', compact('devices'));
    }

    public function createDevice()
    {
        $market = $this->getMarket();
        $stalls = Stall::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('stall_number')
            ->get();

        return view('admin.devices.create', compact('market', 'stalls'));
    }

    public function storeDevice(Request $request)
    {
        $market = $this->getMarket();

        $data = $request->validate([
            'stall_id'             => 'nullable|exists:stalls,id',
            'device_uid'           => 'required|string|unique:devices,device_uid',
            'device_name'          => 'nullable|string|max:255',
            'microcontroller'      => 'nullable|string|max:100',
            'communication_module' => 'nullable|string|max:100',
            'temperature_sensor'   => 'nullable|string|max:100',
            'humidity_sensor'      => 'nullable|string|max:100',
            'gas_sensor'           => 'nullable|string|max:100',
            'firmware_version'     => 'nullable|string|max:50',
            'status'               => 'required|in:online,offline,maintenance,inactive',
            'is_active'            => 'boolean',
        ]);

        $data['market_id'] = $market->id;
        $data['is_active'] = $request->boolean('is_active', true);
        Device::create($data);

        return redirect()->route('admin.devices')
            ->with('success', 'Device registered successfully.');
    }

    public function editDevice(Device $device)
    {
        $market = $this->getMarket();
        $stalls = Stall::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('stall_number')
            ->get();

        return view('admin.devices.edit', compact('device', 'market', 'stalls'));
    }

    public function updateDevice(Request $request, Device $device)
    {
        $data = $request->validate([
            'stall_id'             => 'nullable|exists:stalls,id',
            'device_name'          => 'nullable|string|max:255',
            'microcontroller'      => 'nullable|string|max:100',
            'communication_module' => 'nullable|string|max:100',
            'temperature_sensor'   => 'nullable|string|max:100',
            'humidity_sensor'      => 'nullable|string|max:100',
            'gas_sensor'           => 'nullable|string|max:100',
            'firmware_version'     => 'nullable|string|max:50',
            'status'               => 'required|in:online,offline,maintenance,inactive',
            'is_active'            => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $device->update($data);

        return redirect()->route('admin.devices')
            ->with('success', 'Device updated successfully.');
    }

    public function destroyDevice(Device $device)
    {
        $device->delete();

        return redirect()->route('admin.devices')
            ->with('success', 'Device deleted.');
    }

    /*==========================================================================
     | THRESHOLDS
     *=========================================================================*/

    public function thresholds()
    {
        $thresholds = Threshold::with(['market', 'device'])
            ->latest()->paginate(15);

        return view('admin.thresholds.index', compact('thresholds'));
    }

    public function createThreshold()
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $market  = $markets->first() ?? $this->getMarket();
        $devices = Device::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('device_name')
            ->get();

        return view('admin.thresholds.create', compact('markets', 'market', 'devices'));
    }

    public function storeThreshold(Request $request)
    {
        $market = $this->getMarket();

        $data = $request->validate([
            'device_id'     => 'nullable|exists:devices,id',
            'parameter'     => 'required|in:temperature,humidity,gas_level',
            'minimum_value' => 'nullable|numeric',
            'maximum_value' => 'nullable|numeric',
            'unit'          => 'nullable|string|max:20',
            'is_active'     => 'boolean',
        ]);

        $data['market_id'] = $market->id;
        $data['is_active'] = $request->boolean('is_active', true);
        Threshold::create($data);

        return redirect()->route('admin.thresholds')
            ->with('success', 'Threshold created.');
    }

    public function editThreshold(Threshold $threshold)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $devices = Device::where('market_id', $threshold->market_id)
            ->where('is_active', true)
            ->orderBy('device_name')
            ->get();

        return view('admin.thresholds.edit', compact('threshold', 'markets', 'devices'));
    }

    public function updateThreshold(Request $request, Threshold $threshold)
    {
        $data = $request->validate([
            'device_id'     => 'nullable|exists:devices,id',
            'parameter'     => 'required|in:temperature,humidity,gas_level',
            'minimum_value' => 'nullable|numeric',
            'maximum_value' => 'nullable|numeric',
            'unit'          => 'nullable|string|max:20',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $threshold->update($data);

        return redirect()->route('admin.thresholds')
            ->with('success', 'Threshold updated.');
    }

    public function destroyThreshold(Threshold $threshold)
    {
        $threshold->delete();

        return redirect()->route('admin.thresholds')
            ->with('success', 'Threshold deleted.');
    }

    /*==========================================================================
     | ALERTS
     *=========================================================================*/

    public function alerts()
    {
        $query = Alert::with(['device', 'stall.vendor']);

        if (auth()->user()->isInspector()) {
            // Inspectors only see alerts for stalls they've inspected
            $stallIds = Inspection::where('inspector_id', auth()->id())
                ->pluck('stall_id')->unique();
            $query->whereIn('stall_id', $stallIds);
        }
        // Admin sees all alerts (no additional filter)

        $alerts = $query->latest()->paginate(20);

        // Scoped stats — admin sees all, inspector sees their scope
        $statsQuery = Alert::query();
        if (auth()->user()->isInspector()) {
            $stallIds = Inspection::where('inspector_id', auth()->id())
                ->pluck('stall_id')->unique();
            $statsQuery->whereIn('stall_id', $stallIds);
        }
        $openCount     = (clone $statsQuery)->where('status', 'open')->count();
        $ackCount      = (clone $statsQuery)->where('status', 'acknowledged')->count();
        $resolvedCount = (clone $statsQuery)->where('status', 'resolved')->count();
        $criticalCount = (clone $statsQuery)->where('severity', 'critical')->where('status', 'open')->count();

        return view('admin.alerts.index', compact('alerts', 'openCount', 'ackCount', 'resolvedCount', 'criticalCount'));
    }

    public function acknowledgeAlert(Alert $alert)
    {
        $alert->update([
            'status'           => 'acknowledged',
            'acknowledged_by'  => auth()->id(),
            'acknowledged_at'  => now(),
        ]);

        return back()->with('success', 'Alert acknowledged.');
    }

    public function resolveAlert(Alert $alert)
    {
        $alert->update([
            'status'       => 'resolved',
            'resolved_by'  => auth()->id(),
            'resolved_at'  => now(),
        ]);

        return back()->with('success', 'Alert resolved.');
    }

    /*==========================================================================
     | INSPECTIONS — Inspector role ONLY
     | Admin can VIEW the list (read-only). Only inspectors create/edit/delete.
     *=========================================================================*/

    public function inspections()
    {
        $query = Inspection::with(['stall.vendor', 'inspector', 'items']);

        // Inspector only sees their own inspections
        if (auth()->user()->isInspector()) {
            $query->where('inspector_id', auth()->id());
        }
        // Admin sees all — read only (no create/edit/delete in their view)

        $inspections = $query->latest('inspection_date')->paginate(15);

        return view('admin.inspections.index', compact('inspections'));
    }

    /** Inspector creates inspection — always assigned to themselves */
    public function createInspection()
    {
        // Only inspectors may create
        if (! auth()->user()->isInspector()) {
            abort(403, 'Only inspectors can create inspections.');
        }

        $market = $this->getMarket();
        $stalls = Stall::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('stall_number')
            ->get();

        return view('admin.inspections.create', compact('stalls', 'market'));
    }

    public function storeInspection(Request $request)
    {
        if (! auth()->user()->isInspector()) {
            abort(403, 'Only inspectors can create inspections.');
        }

        $market = $this->getMarket();

        $validated = $request->validate([
            'stall_id'        => 'required|exists:stalls,id',
            'inspection_date' => 'required|date',
            'status'          => 'required|in:pending,completed,follow_up',
            'general_notes'   => 'nullable|string',
            'items'           => 'nullable|array',
            'items.*.item'    => 'required|string|max:255',
            'items.*.status'  => 'required|in:pass,fail,not_applicable',
            'items.*.notes'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $market) {
            $items = $validated['items'] ?? [];
            unset($validated['items']);

            $inspection = Inspection::create(array_merge($validated, [
                'market_id'    => $market->id,
                'inspector_id' => auth()->id(), // always the logged-in inspector
            ]));

            foreach ($items as $item) {
                $inspection->items()->create($item);
            }
        });

        return redirect()->route('admin.inspections')
            ->with('success', 'Inspection recorded successfully.');
    }

    public function showInspection(Inspection $inspection)
    {
        // Inspector can only view their own
        if (auth()->user()->isInspector() && $inspection->inspector_id !== auth()->id()) {
            abort(403);
        }

        $inspection->load(['stall.vendor', 'inspector', 'items']);

        return view('admin.inspections.show', compact('inspection'));
    }

    public function editInspection(Inspection $inspection)
    {
        if (! auth()->user()->isInspector()) {
            abort(403, 'Only inspectors can edit inspections.');
        }

        if ($inspection->inspector_id !== auth()->id()) {
            abort(403, 'You can only edit your own inspections.');
        }

        $market = $this->getMarket();
        $stalls = Stall::where('market_id', $market->id)
            ->where('is_active', true)
            ->orderBy('stall_number')
            ->get();
        $inspection->load('items');

        return view('admin.inspections.edit', compact('inspection', 'stalls', 'market'));
    }

    public function updateInspection(Request $request, Inspection $inspection)
    {
        if (! auth()->user()->isInspector()) {
            abort(403, 'Only inspectors can update inspections.');
        }

        if ($inspection->inspector_id !== auth()->id()) {
            abort(403, 'You can only edit your own inspections.');
        }

        $validated = $request->validate([
            'stall_id'        => 'required|exists:stalls,id',
            'inspection_date' => 'required|date',
            'status'          => 'required|in:pending,completed,follow_up',
            'general_notes'   => 'nullable|string',
            'items'           => 'nullable|array',
            'items.*.item'    => 'required|string|max:255',
            'items.*.status'  => 'required|in:pass,fail,not_applicable',
            'items.*.notes'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $inspection) {
            $items = $validated['items'] ?? null;
            unset($validated['items']);
            $inspection->update($validated);

            if ($items !== null) {
                $inspection->items()->delete();
                foreach ($items as $item) {
                    $inspection->items()->create($item);
                }
            }
        });

        return redirect()->route('admin.inspections')
            ->with('success', 'Inspection updated.');
    }

    public function destroyInspection(Inspection $inspection)
    {
        if (! auth()->user()->isInspector()) {
            abort(403, 'Only inspectors can delete inspections.');
        }

        if ($inspection->inspector_id !== auth()->id()) {
            abort(403, 'You can only delete your own inspections.');
        }

        $inspection->delete();

        return redirect()->route('admin.inspections')
            ->with('success', 'Inspection deleted.');
    }

    /*==========================================================================
     | SENSOR READINGS
     *=========================================================================*/

    public function sensorReadings()
    {
        $devices  = Device::with('stall')->orderBy('device_name')->get();
        $readings = SensorReading::with(['device', 'stall'])
            ->latest('recorded_at')
            ->paginate(25);

        // Load active thresholds so the view can do dynamic breach detection
        $thresholds = Threshold::where('is_active', true)->get()
            ->groupBy('parameter')
            ->map(fn ($group) => $group->first()); // one threshold per parameter (market-wide)

        return view('admin.sensor-readings.index', compact('readings', 'devices', 'thresholds'));
    }

    /*==========================================================================
     | VENDOR PORTAL — one vendor, potentially many stalls
     *=========================================================================*/

    public function myStall()
    {
        $vendor = auth()->user()->vendor?->load([
            'market',
            'stalls.devices',
        ]);

        if (! $vendor) {
            return view('admin.vendor.no-profile');
        }

        // Load latest 1 reading per device for all the vendor's stalls
        $vendor->stalls->each(function ($stall) {
            $stall->devices->each(function ($device) {
                $device->setRelation(
                    'latestReading',
                    $device->sensorReadings()->latest('recorded_at')->first()
                );
            });
        });

        $thresholds = Threshold::where('is_active', true)->get()
            ->groupBy('parameter')
            ->map(fn ($g) => $g->first());

        return view('admin.vendor.my-stall', compact('vendor', 'thresholds'));
    }

    public function myAlerts()
    {
        $vendor   = auth()->user()->vendor?->load('stalls');
        $stallIds = $vendor?->stalls->pluck('id') ?? collect();

        $alerts = Alert::with(['device', 'stall'])
            ->whereIn('stall_id', $stallIds)
            ->latest()
            ->paginate(20);

        // Total open count across ALL pages, not just current page
        $openCount     = Alert::whereIn('stall_id', $stallIds)->where('status', 'open')->count();
        $criticalCount = Alert::whereIn('stall_id', $stallIds)->where('status', 'open')->where('severity', 'critical')->count();

        return view('admin.vendor.my-alerts', compact('alerts', 'vendor', 'openCount', 'criticalCount'));
    }

    public function myReadings()
    {
        $vendor   = auth()->user()->vendor;
        $stallIds = $vendor?->stalls->pluck('id') ?? collect();

        $readings = SensorReading::with(['device', 'stall'])
            ->whereIn('stall_id', $stallIds)
            ->latest('recorded_at')
            ->paginate(25);

        // Dynamic thresholds — same as admin view
        $thresholds = Threshold::where('is_active', true)->get()
            ->groupBy('parameter')
            ->map(fn ($group) => $group->first());

        return view('admin.vendor.my-readings', compact('readings', 'vendor', 'thresholds'));
    }

    /** Vendor sees inspection results for their own stalls */
    public function myInspections()
    {
        $vendor   = auth()->user()->vendor?->load('stalls');
        $stallIds = $vendor?->stalls->pluck('id') ?? collect();

        $inspections = Inspection::with(['stall', 'inspector', 'items'])
            ->whereIn('stall_id', $stallIds)
            ->latest('inspection_date')
            ->paginate(15);

        return view('admin.vendor.my-inspections', compact('inspections', 'vendor'));
    }

    /** Single inspection detail for vendor */
    public function myInspectionShow(Inspection $inspection)
    {
        $vendor   = auth()->user()->vendor?->load('stalls');
        $stallIds = $vendor?->stalls->pluck('id') ?? collect();

        // Vendor can only view inspections on their stalls
        if (! $stallIds->contains($inspection->stall_id)) {
            abort(403);
        }

        $inspection->load(['stall.vendor', 'inspector', 'items']);

        return view('admin.vendor.my-inspection-show', compact('inspection', 'vendor'));
    }
}
