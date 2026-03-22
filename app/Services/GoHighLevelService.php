<?php

namespace App\Services;

use App\Http\Controllers\SalonSettingsController;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoHighLevelService
{
    /**
     * Sync a booking to GoHighLevel: resolve contact, create appointment.
     * Runs silently — failures are logged but never block the main flow.
     */
    public static function syncAppointment(Appointment $appointment, ?string $overrideCalendarId = null): void
    {
        try {
            Log::info('GHL syncAppointment started for appointment #'.$appointment->id);

            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                Log::warning('GHL syncAppointment aborted: no access token available');
                return;
            }

            $locationId = Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');
            $calendarId = $overrideCalendarId ?: Setting::query()->where('option_key', 'clickaio_calendar_id')->value('option_value');

            if (! $locationId || ! $calendarId) {
                Log::warning('GHL syncAppointment aborted: missing locationId='.$locationId.' calendarId='.$calendarId);
                return;
            }

            $oldGhlAppointmentId = $appointment->ghl_appointment_id;

            $appointment->loadMissing('customer');
            $customer = $appointment->customer;
            if (! $customer) {
                Log::warning('GHL syncAppointment aborted: no customer for appointment #'.$appointment->id);
                return;
            }

            // Resolve GHL contact ID: find or create
            $contactId = $customer->ghl_contact_id;
            if (! $contactId) {
                $contactId = static::findGhlContact($token, $locationId, $customer);
            }
            if (! $contactId) {
                $contactId = static::createGhlContact($token, $locationId, $customer);
            }
            if (! $contactId) {
                return;
            }

            // Create new GHL appointment first
            $ghlAppointmentId = static::createGhlAppointment($token, $locationId, $calendarId, $contactId, $appointment);
            if ($ghlAppointmentId) {
                // Only delete old appointment after new one is successfully created
                if ($oldGhlAppointmentId) {
                    static::deleteGhlAppointment($oldGhlAppointmentId);
                }
                $appointment->updateQuietly(['ghl_appointment_id' => $ghlAppointmentId, 'ghl_calendar_id' => $calendarId]);
            }
        } catch (\Exception $e) {
            Log::warning('GHL sync failed: '.$e->getMessage());
        }
    }

    /**
     * Search for a customer in GHL by phone (priority) or email, save ghl_contact_id if found.
     */
    public static function findGhlContact(string $token, string $locationId, Customer $customer, bool $searchByPhone = true, bool $searchByEmail = true): ?string
    {
        $query = null;
        if ($searchByPhone && $customer->phone) {
            $query = $customer->phone;
        } elseif ($searchByEmail && $customer->email) {
            $query = $customer->email;
        }
        if (! $query) {
            return null;
        }

        $endpoint = Setting::query()->where('option_key', 'clickaio_endpoint_find_contact')->value('option_value')
            ?: 'https://services.leadconnectorhq.com/contacts/search';

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->post($endpoint, [
                    'locationId' => $locationId,
                    'query' => $query,
                    'page' => 1,
                    'pageLimit' => 50,
                ]);

            if ($response->failed()) {
                Log::warning('GHL contact search failed: '.$response->body());

                return null;
            }

            $contacts = $response->json('contacts', []);
            if (empty($contacts)) {
                return null;
            }

            $contactId = $contacts[0]['id'] ?? null;
            if ($contactId) {
                $customer->updateQuietly(['ghl_contact_id' => $contactId]);
            }

            return $contactId;
        } catch (\Exception $e) {
            Log::warning('GHL contact search error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Create a contact in GHL, save ghl_contact_id on the customer.
     */
    public static function createGhlContact(string $token, string $locationId, Customer $customer): ?string
    {
        $endpoint = Setting::query()->where('option_key', 'clickaio_endpoint_create_contact')->value('option_value')
            ?: 'https://services.leadconnectorhq.com/contacts/';

        try {
            $payload = [
                'locationId' => $locationId,
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'source' => 'api',
                'tags' => ['api-created'],
            ];

            if ($customer->email) {
                $payload['email'] = $customer->email;
            }
            if ($customer->phone) {
                $payload['phone'] = $customer->phone;
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->post($endpoint, $payload);

            if ($response->failed()) {
                Log::warning('GHL create contact failed: '.$response->body());

                return null;
            }

            $contactId = $response->json('contact.id');
            if ($contactId) {
                $customer->updateQuietly(['ghl_contact_id' => $contactId]);
            }

            return $contactId;
        } catch (\Exception $e) {
            Log::warning('GHL create contact error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Find or create a GHL contact for a customer. Non-blocking.
     */
    public static function syncCustomerContact(Customer $customer): void
    {
        try {
            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                return;
            }

            $locationId = Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');
            if (! $locationId) {
                return;
            }

            // Try to find existing contact
            $contactId = static::findGhlContact($token, $locationId, $customer);

            // If not found, create one
            if (! $contactId) {
                static::createGhlContact($token, $locationId, $customer);
            }
        } catch (\Exception $e) {
            Log::warning('GHL customer sync failed: '.$e->getMessage());
        }
    }

    /**
     * Update a GHL contact with the customer's current details.
     */
    public static function updateGhlContact(Customer $customer): void
    {
        try {
            if (! $customer->ghl_contact_id) {
                return;
            }

            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                return;
            }

            $endpoint = Setting::query()->where('option_key', 'clickaio_endpoint_create_contact')->value('option_value')
                ?: 'https://services.leadconnectorhq.com/contacts/';

            $payload = [
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
            ];

            if ($customer->email) {
                $payload['email'] = $customer->email;
            }
            if ($customer->phone) {
                $payload['phone'] = $customer->phone;
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->put(rtrim($endpoint, '/').'/'.$customer->ghl_contact_id, $payload);

            if ($response->failed()) {
                Log::warning('GHL update contact failed: '.$response->body());
            } else {
                Log::info('GHL contact updated for customer #'.$customer->id);
            }
        } catch (\Exception $e) {
            Log::warning('GHL update contact error: '.$e->getMessage());
        }
    }

    /**
     * Create an appointment in GHL calendar.
     */
    public static function createGhlAppointment(
        string $token,
        string $locationId,
        string $calendarId,
        string $contactId,
        Appointment $appointment
    ): ?string {
        $timezone = Setting::query()->where('option_key', 'timezone')->value('option_value') ?: config('app.timezone');

        // Ensure datetime is in the salon's configured timezone before converting to UTC
        $dt = $appointment->appointment_datetime->copy()->setTimezone($timezone)->second(0);
        $remainder = $dt->minute % 15;
        if ($remainder > 0) {
            $dt->addMinutes(15 - $remainder);
        }
        $startTime = $dt->copy()->utc()->format('Y-m-d\TH:i:s\Z');
        $endTime = $dt->copy()->addMinutes(30)->utc()->format('Y-m-d\TH:i:s\Z');

        $endpoint = Setting::query()->where('option_key', 'clickaio_endpoint_book_appointment')->value('option_value')
            ?: 'https://services.leadconnectorhq.com/calendars/events/appointments';

        $appointment->loadMissing('customer');
        $customerName = trim(($appointment->customer->first_name ?? '').' '.($appointment->customer->last_name ?? ''));

        // Fetch the GHL calendar to get its assignedUserId (needed for round-robin/class calendars)
        $assignedUserId = null;
        try {
            $calResponse = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-04-15',
                ])
                ->get('https://services.leadconnectorhq.com/calendars/'.$calendarId);

            if ($calResponse->successful()) {
                $calData = $calResponse->json('calendar') ?? $calResponse->json();
                $teamMembers = $calData['teamMembers'] ?? [];
                if (! empty($teamMembers)) {
                    $assignedUserId = $teamMembers[0]['userId'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::warning('GHL fetch calendar details failed: '.$e->getMessage());
        }

        $payload = [
            'contactId' => $contactId,
            'calendarId' => $calendarId,
            'locationId' => $locationId,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'timezone' => $timezone,
            'status' => 'booked',
            'notes' => $customerName,
            'selectedSlot' => $startTime,
            'selectedTimezone' => $timezone,
            'ignoreDateRange' => true,
            'ignoreFreeSlotValidation' => true,
        ];

        if ($assignedUserId) {
            $payload['assignedUserId'] = $assignedUserId;
        }

        Log::info('GHL create appointment payload: '.json_encode($payload));

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->post($endpoint, $payload);

            if ($response->failed()) {
                Log::warning('GHL create appointment failed: '.$response->body());

                return null;
            }

            $data = $response->json();
            Log::info('GHL create appointment response: '.json_encode($data));

            // GHL returns the appointment ID at various paths depending on API version
            return $data['id'] ?? $data['event']['id'] ?? $data['calendarEvent']['id'] ?? $data['appointment']['id'] ?? null;
        } catch (\Exception $e) {
            Log::warning('GHL create appointment error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Find or create a GHL contact for a user. Non-blocking.
     */
    public static function syncUserContact(User $user): void
    {
        try {
            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                return;
            }

            $locationId = Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');
            if (! $locationId) {
                return;
            }

            if ($user->ghl_staff_id) {
                return;
            }

            $ghlUserId = static::createGhlUser($token, $locationId, $user);
            if ($ghlUserId) {
                $user->updateQuietly(['ghl_staff_id' => $ghlUserId]);
            }
        } catch (\Exception $e) {
            Log::warning('GHL user sync failed: '.$e->getMessage());
        }
    }

    /**
     * Update a GHL user with the user's current details. Non-blocking.
     */
    public static function updateGhlUserContact(User $user): void
    {
        try {
            if (! $user->ghl_staff_id) {
                return;
            }

            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                return;
            }

            // GHL Users API does not allow updating email or role
            $payload = [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
            ];

            if ($user->phone) {
                $payload['phone'] = $user->phone;
            }

            // Try GHL Users API first (PUT /users/{userId})
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->put('https://services.leadconnectorhq.com/users/'.$user->ghl_staff_id, $payload);

            if ($response->successful()) {
                Log::info('GHL user updated for user #'.$user->id);

                return;
            }

            // If Users API fails (e.g. it's a contact ID), try Contacts API
            $contactEndpoint = Setting::query()->where('option_key', 'clickaio_endpoint_create_contact')->value('option_value')
                ?: 'https://services.leadconnectorhq.com/contacts/';

            $response2 = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->put(rtrim($contactEndpoint, '/').'/'.$user->ghl_staff_id, $payload);

            if ($response2->failed()) {
                Log::warning('GHL update user failed: '.$response2->body());
            } else {
                Log::info('GHL contact updated for user #'.$user->id);
            }
        } catch (\Exception $e) {
            Log::warning('GHL update user error: '.$e->getMessage());
        }
    }

    /**
     * Create a staff user in GHL. Returns the GHL user ID or null.
     */
    public static function createGhlUser(string $token, string $locationId, User $user): ?string
    {
        try {
            $ghlRole = in_array($user->role, ['superadmin', 'admin'], true) ? 'admin' : 'user';

            $payload = [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'password' => 'ClickAIO'.rand(1000, 9999).'!',
                'locationIds' => [$locationId],
                'type' => 'account',
                'role' => $ghlRole,
                'permissions' => [
                    'campaignsEnabled' => false,
                    'contactsEnabled' => true,
                    'opportunitiesEnabled' => true,
                    'appointmentsEnabled' => true,
                    'conversationsEnabled' => true,
                ],
            ];

            if ($user->phone) {
                $payload['phone'] = $user->phone;
            }

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->post('https://services.leadconnectorhq.com/users/', $payload);

            Log::info('GHL create user for #'.$user->id.' ('.$user->email.') response: '.$response->status().' '.$response->body());

            if ($response->failed()) {
                return null;
            }

            $userId = $response->json('id') ?? $response->json('user.id');
            if ($userId) {
                Log::info('GHL staff user created for user #'.$user->id.' ghl_id='.$userId);
            }

            return $userId;
        } catch (\Exception $e) {
            Log::warning('GHL create user error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Fetch all users/staff from GHL for the configured location.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fetchGhlUsers(): array
    {
        try {
            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                Log::warning('GHL fetchGhlUsers aborted: no access token available');

                return [];
            }

            $locationId = Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');
            if (! $locationId) {
                Log::warning('GHL fetchGhlUsers aborted: no location ID configured');

                return [];
            }

            // GHL Users Search endpoint (requires users.readonly scope)
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->get('https://services.leadconnectorhq.com/users/search', [
                    'locationId' => $locationId,
                ]);

            if ($response->successful()) {
                $users = $response->json('users') ?? [];
                Log::info('GHL fetchGhlUsers: found '.count($users).' users via /users/search');

                return $users;
            }

            Log::warning('GHL fetchGhlUsers /users/search failed ('.$response->status().'): '.$response->body());

            // Fallback: try /users/ endpoint
            $response2 = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->get('https://services.leadconnectorhq.com/users/', [
                    'locationId' => $locationId,
                ]);

            if ($response2->successful()) {
                $users = $response2->json('users') ?? [];
                Log::info('GHL fetchGhlUsers: found '.count($users).' users via /users/');

                return $users;
            }

            Log::warning('GHL fetchGhlUsers /users/ failed ('.$response2->status().'): '.$response2->body());
        } catch (\Exception $e) {
            Log::warning('GHL fetchGhlUsers error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Delete an appointment from GHL calendar.
     */
    public static function deleteGhlAppointment(string $ghlAppointmentId): void
    {
        try {
            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                return;
            }

            $endpoint = Setting::query()->where('option_key', 'clickaio_endpoint_delete_appointment')->value('option_value')
                ?: 'https://services.leadconnectorhq.com/calendars/events/';

            Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28',
                ])
                ->delete(rtrim($endpoint, '/').'/'.$ghlAppointmentId);
        } catch (\Exception $e) {
            Log::warning('GHL delete appointment error: '.$e->getMessage());
        }
    }
}
