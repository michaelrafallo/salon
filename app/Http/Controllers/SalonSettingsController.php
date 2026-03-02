<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    public function sendWebhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'webhook_key' => ['required', 'string', 'in:ghl_webhook_no_show_sms,ghl_webhook_book_appointment,ghl_webhook_update_appointment'],
            'payload' => ['required', 'array'],
        ]);

        $webhookUrl = Setting::query()
            ->where('option_key', $validated['webhook_key'])
            ->value('option_value');

        if (! $webhookUrl || ! filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook URL is not configured.',
            ], 422);
        }

        try {
            $response = Http::timeout(10)->post($webhookUrl, $validated['payload']);

            return response()->json([
                'success' => true,
                'message' => 'Webhook sent successfully.',
                'status_code' => $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send webhook: '.$e->getMessage(),
            ], 500);
        }
    }
}
