<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncTurnTrackerRequest;
use App\Models\Setting;
use App\Models\TurnTracker;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalonTurnTrackerController extends Controller
{
    /**
     * List turn tracker entries (technicians with services count and order).
     * Only returns technicians that already have a turn tracker row; does not insert on load.
     */
    public function index(Request $request): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $technicianIds = User::query()
            ->technicians()
            ->pluck('id')
            ->all();

        $entries = TurnTracker::query()
            ->with('user:id,first_name,last_name,initials')
            ->whereIn('user_id', $technicianIds)
            ->whereNull('clock_out')
            ->orderBy('id')
            ->get();

        $data = $entries->map(function (TurnTracker $t) {
            $u = $t->user;

            return [
                'id' => $t->id,
                'user_id' => $t->user_id,
                'services' => $t->services,
                'clock_in' => $t->clock_in?->toIso8601String(),
                'firstName' => $u?->first_name,
                'lastName' => $u?->last_name,
                'fullName' => trim(($u?->first_name ?? '').' '.($u?->last_name ?? '')),
                'initials' => $u?->initials ?? strtoupper(substr($u?->first_name ?? '', 0, 1).substr($u?->last_name ?? '', 0, 1)),
                'photo' => null,
            ];
        });

        $settings = Setting::getAllAsKeyValue();
        $turnTrackerOrder = $settings['turn_tracker_order'] ?? 'lowest';
        if (! in_array($turnTrackerOrder, ['lowest', 'highest'], true)) {
            $turnTrackerOrder = 'lowest';
        }

        return response()->json([
            'success' => true,
            'entries' => $data,
            'turn_tracker_order' => $turnTrackerOrder,
        ]);
    }

    /**
     * Sync turn tracker: update order and service counts from the UI.
     */
    public function sync(SyncTurnTrackerRequest $request): JsonResponse
    {
        $entries = $request->validated('entries');

        DB::transaction(function () use ($entries) {
            foreach ($entries as $item) {
                TurnTracker::query()->updateOrCreate(
                    ['user_id' => $item['user_id']],
                    [
                        'services' => (int) $item['services'],
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Turn tracker saved.',
        ]);
    }

    /**
     * Clock in a technician: add to turn tracker table with clock_in date/time, clock_out empty by default.
     */
    public function clockIn(Request $request, User $user): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if (! User::query()->technicians()->where('id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'User is not a technician.'], 422);
        }

        $existing = TurnTracker::query()->where('user_id', $user->id)->first();
        $services = $existing ? $existing->services : 0;

        $tracker = TurnTracker::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'services' => $services,
                'clock_in' => now(),
                'clock_out' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Clocked in successfully.',
            'data' => [
                'clock_in' => $tracker->clock_in?->toIso8601String(),
                'clock_out' => null,
            ],
        ]);
    }

    /**
     * Clock out a technician: set clock_out = now() on their turn tracker row.
     */
    public function clockOut(Request $request, User $user): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $tracker = TurnTracker::query()->where('user_id', $user->id)->first();
        if ($tracker) {
            $tracker->clock_out = now();
            $tracker->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully.',
            'data' => [
                'clock_out' => $tracker?->clock_out?->toIso8601String(),
            ],
        ]);
    }
}
