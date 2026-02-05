<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalonPaymentController extends Controller
{
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $payment = DB::transaction(function () use ($validated) {
                $appointment = Appointment::query()->with('customer')->lockForUpdate()->findOrFail($validated['appointment_id']);

                $paymentId = strtoupper(Str::random(20));
                $status = $validated['status'] ?? 'Paid';

                $payment = Payment::query()->create([
                    'id' => $paymentId,
                    'appointment_id' => $appointment->id,
                    'amount' => $validated['amount'],
                    'sub_total' => $validated['sub_total'],
                    'discount' => $validated['discount'],
                    'credits' => $validated['credits'],
                    'gift_card' => $validated['gift_card'],
                    'tax' => $validated['tax'],
                    'tip' => $validated['tip'],
                    'method' => $validated['method'] ?? null,
                    'status' => $status,
                    'paid_at' => now()->toDateString(),
                ]);

                if (($validated['credits'] ?? 0) > 0) {
                    $customer = $appointment->customer;
                    if ($customer) {
                        $newBalance = (float) $customer->credit_balance - (float) $validated['credits'];
                        if ($newBalance < 0) {
                            throw new \RuntimeException('Customer credits are insufficient.');
                        }
                        $customer->update(['credit_balance' => round($newBalance, 2)]);
                    }
                }

                if (! empty($validated['tips_by_technician'])) {
                    $tips = collect($validated['tips_by_technician'])
                        ->filter(fn ($row) => isset($row['technician_id']))
                        ->mapWithKeys(fn ($row) => [(int) $row['technician_id'] => ['tip' => (float) $row['tip']]]);
                    if ($tips->isNotEmpty()) {
                        $appointment->technicians()->syncWithoutDetaching($tips->all());
                    }
                }

                $appointment->update(['status' => $status]);

                return $payment;
            });
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => [
                'id' => $payment->id,
                'status' => $payment->status,
            ],
        ], 201);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    {
        if ($request->filled('status')) {
            $payment->update(['status' => $request->input('status')]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully.',
            'data' => [
                'id' => $payment->id,
                'status' => $payment->status,
            ],
        ]);
    }
}
