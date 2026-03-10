<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    /**
     * Redirect to GoHighLevel OAuth authorization page.
     */
    public function clickaioAuthorizeRedirect()
    {
        $clientId = Setting::query()->where('option_key', 'clickaio_client_id')->value('option_value');

        if (! $clientId) {
            return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                ->with('error', 'Client ID must be saved first.');
        }

        $scopes = Setting::query()->where('option_key', 'clickaio_scopes')->value('option_value');
        if (! $scopes) {
            $scopes = 'contacts.readonly contacts.write';
        }

        $redirectUri = route('salon.settings.clickaio.callback');

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
        ]);

        return redirect("https://marketplace.gohighlevel.com/oauth/chooselocation?{$query}");
    }

    /**
     * Handle GoHighLevel OAuth callback - exchange code for access token.
     */
    public function clickaioCallback(Request $request)
    {
        $code = $request->query('code');

        if (! $code) {
            return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                ->with('error', 'Authorization was cancelled or failed.');
        }

        $clientId = Setting::query()->where('option_key', 'clickaio_client_id')->value('option_value');
        $clientSecret = Setting::query()->where('option_key', 'clickaio_client_secret')->value('option_value');

        if (! $clientId || ! $clientSecret) {
            return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                ->with('error', 'Client ID and Client Secret must be saved first.');
        }

        try {
            $response = Http::timeout(15)
                ->asForm()
                ->post('https://services.leadconnectorhq.com/oauth/token', [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => route('salon.settings.clickaio.callback'),
                    'user_type' => 'Location',
                ]);

            if ($response->failed()) {
                $errorMsg = $response->json('message') ?? $response->json('error_description') ?? 'Token exchange failed.';

                return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                    ->with('error', 'Authorization failed: '.$errorMsg);
            }

            $data = $response->json();

            if (! ($data['access_token'] ?? null)) {
                return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                    ->with('error', 'No access token received from GoHighLevel.');
            }

            static::storeClickaioTokens($data);

            return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                ->with('success', 'GoHighLevel API authorized successfully!');
        } catch (\Exception $e) {
            return redirect()->route('salon.settings.index', ['tab' => 'clickaio'])
                ->with('error', 'Failed to connect to GoHighLevel: '.$e->getMessage());
        }
    }

    /**
     * Refresh the GoHighLevel access token using the stored refresh token.
     */
    public function clickaioRefreshToken(): JsonResponse
    {
        try {
            $token = static::performClickaioRefresh();

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully.',
                'data' => ['access_token' => $token],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Store tokens from a GoHighLevel token response.
     */
    public static function storeClickaioTokens(array $data): void
    {
        Setting::set('clickaio_access_token', $data['access_token']);

        if (! empty($data['refresh_token'])) {
            Setting::set('clickaio_refresh_token', $data['refresh_token']);
        }

        if (! empty($data['locationId'])) {
            Setting::set('clickaio_location_id', $data['locationId']);
        }

        // Store expiry: GHL tokens expire in ~86399 seconds (~24h)
        $expiresIn = $data['expires_in'] ?? 86399;
        $expiresAt = now()->addSeconds((int) $expiresIn)->toIso8601String();
        Setting::set('clickaio_token_expires_at', $expiresAt);
    }

    /**
     * Perform the actual token refresh against GoHighLevel API.
     * Returns the new access token or throws on failure.
     */
    public static function performClickaioRefresh(): string
    {
        $clientId = Setting::query()->where('option_key', 'clickaio_client_id')->value('option_value');
        $clientSecret = Setting::query()->where('option_key', 'clickaio_client_secret')->value('option_value');
        $refreshToken = Setting::query()->where('option_key', 'clickaio_refresh_token')->value('option_value');

        if (! $clientId || ! $clientSecret || ! $refreshToken) {
            throw new \RuntimeException('Missing credentials or refresh token. Please re-authorize.');
        }

        $response = Http::timeout(15)
            ->asForm()
            ->post('https://services.leadconnectorhq.com/oauth/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'user_type' => 'Location',
                'redirect_uri' => route('salon.settings.clickaio.callback'),
            ]);

        if ($response->failed()) {
            $errorMsg = $response->json('message') ?? $response->json('error_description') ?? 'Token refresh failed.';
            throw new \RuntimeException($errorMsg);
        }

        $data = $response->json();

        if (! ($data['access_token'] ?? null)) {
            throw new \RuntimeException('No access token received.');
        }

        static::storeClickaioTokens($data);

        return $data['access_token'];
    }

    /**
     * Get a valid Clickaio access token, auto-refreshing if expired.
     * Use this from anywhere in the app to get a working token.
     */
    public static function getClickaioToken(): ?string
    {
        $token = Setting::query()->where('option_key', 'clickaio_access_token')->value('option_value');
        $expiresAt = Setting::query()->where('option_key', 'clickaio_token_expires_at')->value('option_value');

        if (! $token) {
            return null;
        }

        // If token is still valid (with 5 min buffer), return it
        if ($expiresAt && now()->lt(\Carbon\Carbon::parse($expiresAt)->subMinutes(5))) {
            return $token;
        }

        // Token expired or about to expire — auto-refresh
        try {
            return static::performClickaioRefresh();
        } catch (\Exception $e) {
            Log::warning('Clickaio auto-refresh failed: '.$e->getMessage());

            // Return the existing token as fallback (may still work)
            return $token;
        }
    }

    /**
     * Proxy an API request to GoHighLevel for testing (mini Postman).
     */
    public function clickaioTestApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'string', 'in:GET,POST,PUT,PATCH,DELETE'],
            'url' => ['required', 'url'],
            'headers' => ['nullable', 'array'],
            'body' => ['nullable', 'array'],
        ]);

        $token = static::getClickaioToken();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'No access token available. Please authorize first.',
            ], 422);
        }

        $headers = array_merge([
            'Authorization' => 'Bearer '.$token,
            'Version' => '2021-07-28',
        ], $validated['headers'] ?? []);

        $method = strtolower($validated['method']);
        $url = $validated['url'];
        $body = $validated['body'] ?? null;

        try {
            $startTime = microtime(true);

            $httpRequest = Http::timeout(30)->withHeaders($headers);

            if (in_array($method, ['post', 'put', 'patch'])) {
                $response = $httpRequest->$method($url, $body ?? []);
            } else {
                $response = $httpRequest->$method($url);
            }

            $elapsed = round((microtime(true) - $startTime) * 1000);

            return response()->json([
                'success' => true,
                'status' => $response->status(),
                'time_ms' => $elapsed,
                'response_headers' => $response->headers(),
                'body' => $response->json() ?? $response->body(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk action on customers for GHL sync: sync_from, sync_to, delete.
     */
    public function clickaioBulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:sync_from,sync_to,delete'],
            'customer_ids' => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['integer', 'exists:customers,id'],
        ]);

        $action = $validated['action'];
        $customers = \App\Models\Customer::whereIn('id', $validated['customer_ids'])->get();

        $token = static::getClickaioToken();
        $locationId = \App\Models\Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');

        if ($action !== 'delete' && (! $token || ! $locationId)) {
            return response()->json([
                'success' => false,
                'message' => 'GHL not connected. Please authorize and set Location ID first.',
            ], 422);
        }

        $results = ['synced' => 0, 'skipped' => 0, 'failed' => 0, 'deleted' => 0];

        foreach ($customers as $customer) {
            try {
                if ($action === 'sync_from') {
                    if ($customer->ghl_contact_id) {
                        $results['skipped']++;

                        continue;
                    }
                    $contactId = \App\Services\GoHighLevelService::findGhlContact($token, $locationId, $customer);
                    if ($contactId) {
                        $results['synced']++;
                    } else {
                        $results['failed']++;
                    }
                } elseif ($action === 'sync_to') {
                    $contactId = $customer->ghl_contact_id;
                    if (! $contactId) {
                        $contactId = \App\Services\GoHighLevelService::findGhlContact($token, $locationId, $customer);
                    }
                    if (! $contactId) {
                        $contactId = \App\Services\GoHighLevelService::createGhlContact($token, $locationId, $customer);
                    }
                    if ($contactId) {
                        $results['synced']++;
                    } else {
                        $results['failed']++;
                    }
                } elseif ($action === 'delete') {
                    $customer->updateQuietly(['ghl_contact_id' => null]);
                    $results['deleted']++;
                }
            } catch (\Exception $e) {
                Log::warning("GHL bulk {$action} failed for customer #{$customer->id}: ".$e->getMessage());
                $results['failed']++;
            }
        }

        $messages = [];
        if ($results['synced'] > 0) {
            $messages[] = $results['synced'].' synced';
        }
        if ($results['skipped'] > 0) {
            $messages[] = $results['skipped'].' skipped';
        }
        if ($results['failed'] > 0) {
            $messages[] = $results['failed'].' failed';
        }
        if ($results['deleted'] > 0) {
            $messages[] = $results['deleted'].' cleared';
        }

        // Refresh customers to return updated ghl_contact_id values
        $updatedCustomers = [];
        foreach ($customers->fresh() as $c) {
            $updatedCustomers[$c->id] = $c->ghl_contact_id;
        }

        return response()->json([
            'success' => true,
            'message' => implode(', ', $messages).'.',
            'data' => $results,
            'customers' => $updatedCustomers,
        ]);
    }

    public function sendWebhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'webhook_key' => ['required', 'string', 'in:ghl_webhook_no_show_sms,ghl_webhook_update_appointment'],
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
