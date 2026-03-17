<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerCreditRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Models\Setting;
use App\Services\GoHighLevelService;
use App\Services\Salon\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalonCustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());

        // Sync to GoHighLevel: find or create contact (non-blocking)
        GoHighLevelService::syncCustomerContact($customer);
        $customer->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data' => [
                'id' => $customer->id,
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'ghlContactId' => $customer->ghl_contact_id,
                'createdAt' => $customer->created_at?->format('Y-m-d'),
            ],
        ], 201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());

        // Sync updated details to GHL contact (non-blocking)
        GoHighLevelService::updateGhlContact($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data' => [
                'id' => $customer->id,
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'ghlContactId' => $customer->ghl_contact_id,
            ],
        ]);
    }

    public function updateCredits(UpdateCustomerCreditRequest $request, Customer $customer): JsonResponse
    {
        $validated = $request->validated();

        $email = $request->session()->get('salon_user_email');
        $processedByUserId = $email
            ? User::query()->where('email', $email)->value('id')
            : null;

        if (! $processedByUserId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $customer = $this->customerService->adjustCredits(
                $customer,
                (float) $validated['amount'],
                (string) $validated['operation'],
                (int) $processedByUserId
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Credits updated successfully.',
            'data' => [
                'id' => $customer->id,
                'creditBalance' => (float) $customer->credit_balance,
            ],
        ]);
    }

    public function ghlLookup(Request $request, Customer $customer): JsonResponse
    {
        $token = \App\Http\Controllers\SalonSettingsController::getClickaioToken();
        if (! $token) {
            return response()->json(['success' => false, 'message' => 'GHL not connected. Please authorize in Settings.'], 422);
        }

        $locationId = Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');
        if (! $locationId) {
            return response()->json(['success' => false, 'message' => 'Location ID not configured in Settings.'], 422);
        }

        $searchByPhone = $request->boolean('search_by_phone', true);
        $searchByEmail = $request->boolean('search_by_email', true);
        $contactId = GoHighLevelService::findGhlContact($token, $locationId, $customer, $searchByPhone, $searchByEmail);

        if (! $contactId) {
            return response()->json(['success' => false, 'message' => 'Contact not found in GoHighLevel.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact found and linked.',
            'data' => ['ghl_contact_id' => $contactId],
        ]);
    }

    public function syncFromGhl(Request $request, string $ghlContactId): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|string|email|max:255',
            'updated_at' => 'nullable|date',
        ]);

        $customer = Customer::updateOrCreate(
            ['ghl_contact_id' => $ghlContactId],
            [
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'phone'      => $validated['phone'] ?? null,
                'email'      => $validated['email'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer synced successfully.',
            'data' => [
                'id'           => $customer->id,
                'firstName'    => $customer->first_name,
                'lastName'     => $customer->last_name,
                'email'        => $customer->email,
                'phone'        => $customer->phone,
                'ghlContactId' => $customer->ghl_contact_id,
            ],
        ]);
    }

    public function destroy(Request $request, Customer $customer): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $this->customerService->delete($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ]);
    }
}
