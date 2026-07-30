<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupPricingHistory;
use App\Models\PickupPricingSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PickupPricingController extends Controller
{
    /**
     * Get the current active pricing configuration plus paginated history.
     */
    public function index(Request $request): JsonResponse
    {
        $setting = PickupPricingSetting::where('is_active', true)->latest()->first();

        $historyQuery = PickupPricingHistory::orderByDesc('created_at');

        if ($q = $request->input('q')) {
            $historyQuery->where(function ($query) use ($q) {
                $query->where('created_by', 'like', "%{$q}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $history = $historyQuery->paginate($perPage);

        return response()->json([
            'setting' => $setting,
            'history' => $history,
        ]);
    }

    /**
     * Update the active pricing configuration.
     *
     * Creates a new active record in pickup_pricing_settings and an
     * immutable record in pickup_pricing_histories inside a DB transaction.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'initial_fee'        => 'required|numeric|min:0',
            'distance_rate'      => 'required|numeric|min:0.01',
            'time_rate'          => 'required|numeric|min:0.01',
            'demand_multiplier'  => 'required|numeric|min:0.10|max:5.00',
            'weather_multiplier' => 'required|numeric|min:0.10|max:5.00',
            'traffic_multiplier' => 'required|numeric|min:0.10|max:5.00',
            'event_multiplier'   => 'required|numeric|min:0.10|max:5.00',
        ]);

        $totalMultiplier = round(
            $validated['demand_multiplier']  *
            $validated['weather_multiplier'] *
            $validated['traffic_multiplier'] *
            $validated['event_multiplier'],
            4
        );

        $updatedBy = Auth::user()?->name ?? 'Admin';

        $result = DB::transaction(function () use ($validated, $totalMultiplier, $updatedBy) {
            // Deactivate all previous configurations
            PickupPricingSetting::where('is_active', true)->update(['is_active' => false]);

            // Create new active configuration
            $setting = PickupPricingSetting::create([
                'initial_fee'        => $validated['initial_fee'],
                'distance_rate'      => $validated['distance_rate'],
                'time_rate'          => $validated['time_rate'],
                'demand_multiplier'  => $validated['demand_multiplier'],
                'weather_multiplier' => $validated['weather_multiplier'],
                'traffic_multiplier' => $validated['traffic_multiplier'],
                'event_multiplier'   => $validated['event_multiplier'],
                'total_multiplier'   => $totalMultiplier,
                'is_active'          => true,
                'updated_by'         => $updatedBy,
            ]);

            // Write immutable history record
            PickupPricingHistory::create([
                'initial_fee'        => $validated['initial_fee'],
                'distance_rate'      => $validated['distance_rate'],
                'time_rate'          => $validated['time_rate'],
                'demand_multiplier'  => $validated['demand_multiplier'],
                'weather_multiplier' => $validated['weather_multiplier'],
                'traffic_multiplier' => $validated['traffic_multiplier'],
                'event_multiplier'   => $validated['event_multiplier'],
                'total_multiplier'   => $totalMultiplier,
                'created_by'         => $updatedBy,
            ]);

            return $setting;
        });

        return response()->json([
            'message' => 'Pengaturan biaya penjemputan berhasil disimpan.',
            'setting' => $result,
        ]);
    }

    /**
     * Get paginated history only.
     */
    public function history(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $query = PickupPricingHistory::orderByDesc('created_at');

        if ($q = $request->input('q')) {
            $query->where(function ($query) use ($q) {
                $query->where('created_by', 'like', "%{$q}%");
            });
        }

        return response()->json($query->paginate($perPage));
    }
}
