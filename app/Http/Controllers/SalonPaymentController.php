<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class SalonPaymentController extends Controller
{
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
