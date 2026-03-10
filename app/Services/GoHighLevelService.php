<?php

namespace App\Services;

use App\Http\Controllers\SalonSettingsController;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoHighLevelService
{
    /**
     * Sync a booking to GoHighLevel: resolve contact, create appointment.
     * Runs silently — failures are logged but never block the main flow.
     */
    public static function syncAppointment(Appointment $appointment): void
    {
        try {
            Log::info('GHL syncAppointment started for appointment #'.$appointment->id);

            $token = SalonSettingsController::getClickaioToken();
            if (! $token) {
                Log::warning('GHL syncAppointment aborted: no access token available');
                return;
            }

            $locationId = Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');
            $calendarId = Setting::query()->where('option_key', 'clickaio_calendar_id')->value('option_value');

            if (! $locationId || ! $calendarId) {
                Log::warning('GHL syncAppointment aborted: missing locationId='.$locationId.' calendarId='.$calendarId);
                return;
            }

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

            // Create GHL appointment
            $ghlAppointmentId = static::createGhlAppointment($token, $locationId, $calendarId, $contactId, $appointment);
            if ($ghlAppointmentId) {
                $appointment->updateQuietly(['ghl_appointment_id' => $ghlAppointmentId]);
            }
        } catch (\Exception $e) {
            Log::warning('GHL sync failed: '.$e->getMessage());
        }
    }

    /**
     * Search for a customer in GHL by phone (priority) or email, save ghl_contact_id if found.
     */
    public static function findGhlContact(string $token, string $locationId, Customer $customer): ?string
    {
        $query = $customer->phone ?: $customer->email;
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
     * Create an appointment in GHL calendar.
     */
    public static function createGhlAppointment(
        string $token,
        string $locationId,
        string $calendarId,
        string $contactId,
        Appointment $appointment
    ): ?string {
        $timezone = Setting::query()->where('option_key', 'timezone')->value('option_value') ?: 'America/New_York';

        // Round up to nearest 15-minute interval, then convert to UTC ISO 8601
        $dt = $appointment->appointment_datetime->copy()->second(0);
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

        $payload = [
            'contactId' => $contactId,
            'calendarId' => $calendarId,
            'locationId' => $locationId,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'timezone' => $timezone,
            'status' => 'booked',
            'notes' => $customerName,
        ];

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
