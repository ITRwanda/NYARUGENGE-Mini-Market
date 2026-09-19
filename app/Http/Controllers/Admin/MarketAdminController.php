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
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $vendors = Vendor::with('market')->where('is_active', true)->orderBy('business_name')->get();

        return view('admin.stalls.create', compact('markets', 'vendors'));
    }

    public function storeStall(Request $request)
    {
        $data = $request->validate([
            'market_id'    => 'required|exists:markets,id',
            'vendor_id'    => 'nullable|exists:vendors,id',
            'stall_number' => 'required|string|max:50',
            'section'      => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Stall::create($data);

        return redirect()->route('admin.stalls')
            ->with('success', 'Stall created successfully.');
    }

    public function editStall(Stall $stall)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $vendors = Vendor::with('market')->where('is_active', true)->orderBy('business_name')->get();

        return view('admin.stalls.edit', compact('stall', 'markets', 'vendors'));
    }

    public function updateStall(Request $request, Stall $stall)
    {
        $data = $request->validate([
            'market_id'    => 'required|exists:markets,id',
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
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $stalls  = Stall::with('market')->where('is_active', true)->orderBy('stall_number')->get();

        return view('admin.devices.create', compact('markets', 'stalls'));
    }

    public function storeDevice(Request $request)
    {
        $data = $request->validate([
            'market_id'            => 'required|exists:markets,id',
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

        $data['is_active'] = $request->boolean('is_active', true);
        Device::create($data);

        return redirect()->route('admin.devices')
            ->with('success', 'Device registered successfully.');
    }

    public function editDevice(Device $device)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $stalls  = Stall::with('market')->where('is_active', true)->orderBy('stall_number')->get();

        return view('admin.devices.edit', compact('device', 'markets', 'stalls'));
    }

    public function updateDevice(Request $request, Device $device)
    {
        $data = $request->validate([
            'market_id'            => 'required|exists:markets,id',
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
        $devices = Device::with('market')->where('is_active', true)->orderBy('device_name')->get();

        return view('admin.thresholds.create', compact('markets', 'devices'));
    }

    public function storeThreshold(Request $request)
    {
        $data = $request->validate([
            'market_id'     => 'required|exists:markets,id',
            'device_id'     => 'nullable|exists:devices,id',
            'parameter'     => 'required|in:temperature,humidity,gas_level',
            'minimum_value' => 'nullable|numeric',
            'maximum_value' => 'nullable|numeric',
            'unit'          => 'nullable|string|max:20',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Threshold::create($data);

        return redirect()->route('admin.thresholds')
            ->with('success', 'Threshold created.');
    }

    public function editThreshold(Threshold $threshold)
    {
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $devices = Device::with('market')->where('is_active', true)->orderBy('device_name')->get();

        return view('admin.thresholds.edit', compact('threshold', 'markets', 'devices'));
    }

    public function updateThreshold(Request $request, Threshold $threshold)
    {
        $data = $request->validate([
            'market_id'     => 'required|exists:markets,id',
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
        $query = Alert::with(['device', 'stall']);

        if (auth()->user()->isInspector()) {
            // Inspectors only see alerts for stalls they've inspected
            $stallIds = Inspection::where('inspector_id', auth()->id())
                ->pluck('stall_id')->unique();
            $query->whereIn('stall_id', $stallIds);
        }

        $alerts = $query->latest()->paginate(20);

        return view('admin.alerts.index', compact('alerts'));
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
     | INSPECTIONS
     *=========================================================================*/

    public function inspections()
    {
        $query = Inspection::with(['market', 'stall', 'inspector', 'items']);

        if (auth()->user()->isInspector()) {
            $query->where('inspector_id', auth()->id());
        }

        $inspections = $query->latest('inspection_date')->paginate(15);

        return view('admin.inspections.index', compact('inspections'));
    }

    public function createInspection()
    {
        $markets    = Market::where('is_active', true)->orderBy('name')->get();
        $stalls     = Stall::with('market')->where('is_active', true)->orderBy('stall_number')->get();
        $inspectors = User::whereIn('role', ['inspector', 'market_admin', 'admin'])->orderBy('name')->get();

        return view('admin.inspections.create', compact('markets', 'stalls', 'inspectors'));
    }

    public function storeInspection(Request $request)
    {
        $validated = $request->validate([
            'market_id'       => 'required|exists:markets,id',
            'stall_id'        => 'required|exists:stalls,id',
            'inspector_id'    => 'required|exists:users,id',
            'inspection_date' => 'required|date',
            'status'          => 'required|in:pending,completed,follow_up',
            'general_notes'   => 'nullable|string',
            'items'           => 'nullable|array',
            'items.*.item'    => 'required|string|max:255',
            'items.*.status'  => 'required|in:pass,fail,not_applicable',
            'items.*.notes'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $items = $validated['items'] ?? [];
            unset($validated['items']);
            $inspection = Inspection::create($validated);
            foreach ($items as $item) {
                $inspection->items()->create($item);
            }
        });

        return redirect()->route('admin.inspections')
            ->with('success', 'Inspection recorded successfully.');
    }

    public function showInspection(Inspection $inspection)
    {
        $inspection->load(['market', 'stall', 'inspector', 'items']);

        return view('admin.inspections.show', compact('inspection'));
    }

    public function editInspection(Inspection $inspection)
    {
        $markets    = Market::where('is_active', true)->orderBy('name')->get();
        $stalls     = Stall::with('market')->where('is_active', true)->orderBy('stall_number')->get();
        $inspectors = User::whereIn('role', ['inspector', 'market_admin', 'admin'])->orderBy('name')->get();
        $inspection->load('items');

        return view('admin.inspections.edit', compact('inspection', 'markets', 'stalls', 'inspectors'));
    }

    public function updateInspection(Request $request, Inspection $inspection)
    {
        $validated = $request->validate([
            'market_id'       => 'required|exists:markets,id',
            'stall_id'        => 'required|exists:stalls,id',
            'inspector_id'    => 'required|exists:users,id',
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

        return view('admin.sensor-readings.index', compact('readings', 'devices'));
    }

    /*==========================================================================
     | VENDOR PORTAL
     *=========================================================================*/

    public function myStall()
    {
        $vendor = auth()->user()->vendor?->load([
            'market',
            'stalls.devices.sensorReadings' => function ($q) {
                $q->latest('recorded_at')->limit(1);
            },
        ]);

        return view('admin.vendor.my-stall', compact('vendor'));
    }

    public function myAlerts()
    {
        $vendor    = auth()->user()->vendor;
        $stallIds  = $vendor?->stalls->pluck('id') ?? collect();
        $alerts    = Alert::with(['device', 'stall'])
            ->whereIn('stall_id', $stallIds)
            ->latest()
            ->paginate(20);

        return view('admin.vendor.my-alerts', compact('alerts', 'vendor'));
    }

    public function myReadings()
    {
        $vendor   = auth()->user()->vendor;
        $stallIds = $vendor?->stalls->pluck('id') ?? collect();
        $readings = SensorReading::with(['device', 'stall'])
            ->whereIn('stall_id', $stallIds)
            ->latest('recorded_at')
            ->paginate(25);

        return view('admin.vendor.my-readings', compact('readings', 'vendor'));
    }
}
