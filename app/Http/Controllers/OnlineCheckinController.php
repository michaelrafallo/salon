<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\OnlineCheckin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineCheckinController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $phone = $validated['phone'];
        $customer = Customer::query()->where('phone', $phone)->first();

        $appointment = null;
        if ($customer) {
            $appointment = Appointment::query()
                ->where('customer_id', $customer->id)
                ->whereNotIn('status', ['Paid', 'Refunded', 'Completed', 'confirmed'])
                ->latest('appointment_datetime')
                ->first();

            if ($appointment) {
                $appointment->update(['status' => 'confirmed']);
            }
        }

        $checkin = OnlineCheckin::query()->create([
            'customer_id' => $customer?->id,
            'appointment_id' => $appointment?->id,
            'phone' => $phone,
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in recorded successfully.',
            'data' => [
                'id' => $checkin->id,
                'customer_found' => $customer !== null,
            ],
        ], 201);
    }
}
