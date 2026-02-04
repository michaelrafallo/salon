<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SalonSettingsController extends Controller
{
    /**
     * Return all settings as key => value (for current salon/context).
     */
    public function index(): JsonResponse
    {
        $settings = Setting::getAllAsKeyValue();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Bulk update settings. Only allowed keys are persisted.
     */
    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $allowed = array_fill_keys(Setting::allowedKeys(), true);
        $input = $request->validated('settings', []);
        $settings = is_array($input) ? $input : [];

        foreach ($settings as $key => $value) {
            if (! isset($allowed[$key])) {
                continue;
            }
            Setting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated.',
            'data' => Setting::getAllAsKeyValue(),
        ]);
    }
}
