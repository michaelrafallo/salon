<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerCreditRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
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

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data' => [
                'id' => $customer->id,
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'createdAt' => $customer->created_at?->format('Y-m-d'),
            ],
        ], 201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data' => [
                'id' => $customer->id,
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
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
