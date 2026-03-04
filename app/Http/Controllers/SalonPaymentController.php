<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\Salon\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalonPaymentController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $email = $request->session()->get('salon_user_email');
        $processedByUserId = $email
            ? User::query()->where('email', $email)->value('id')
            : null;

        if (! $processedByUserId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $settings = Setting::getAllAsKeyValue();
        $commissionRatePercent = (float) ($settings['commission_rate'] ?? 30);
        if ($commissionRatePercent < 0 || $commissionRatePercent > 100) {
            $commissionRatePercent = 30;
        }
        $commissionRate = $commissionRatePercent / 100;
        $currency = strtoupper(trim((string) ($settings['currency_code'] ?? 'USD')));
        if ($currency === '' || strlen($currency) !== 3) {
            $currency = 'USD';
        }

        try {
            $payment = DB::transaction(function () use ($validated, $processedByUserId, $commissionRate, $currency, $settings) {
                $appointment = Appointment::query()->with('customer')->lockForUpdate()->findOrFail($validated['appointment_id']);

                $paymentId = strtoupper(Str::random(20));
                $status = $validated['status'] ?? 'Paid';

                $payment = Payment::query()->create([
                    'id' => $paymentId,
                    'appointment_id' => $appointment->id,
                    'amount' => $validated['amount'],
                    'currency' => $currency,
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

                AppointmentService::query()
                    ->where('appointment_id', $appointment->id)
                    ->update(['currency' => $currency]);

                if (($validated['credits'] ?? 0) > 0) {
                    $customer = $appointment->customer;
                    if ($customer) {
                        $this->customerService->adjustCredits(
                            $customer,
                            (float) $validated['credits'],
                            'redeem',
                            (int) $processedByUserId
                        );
                    }
                }

                $customer = $appointment->customer;
                if ($customer) {
                    $paymentTotal = (float) ($validated['amount'] ?? 0);
                    $earnedCredits = 0.0;

                    if (($settings['points_rate_fixed'] ?? '0') === '1') {
                        $unitValue = (float) ($settings['points_unit_value'] ?? 0);
                        $perUnit = (float) ($settings['points_per_unit'] ?? 0);
                        if ($unitValue > 0 && $perUnit > 0 && $paymentTotal > 0) {
                            $earnedCredits = round(($paymentTotal / $unitValue) * $perUnit, 2);
                        }
                    } elseif (($settings['points_rate_percentage'] ?? '0') === '1') {
                        $rewardPct = (float) ($settings['reward_percentage'] ?? 0);
                        if ($rewardPct > 0 && $paymentTotal > 0) {
                            $earnedCredits = round($paymentTotal * ($rewardPct / 100), 2);
                        }
                    }

                    if ($earnedCredits > 0) {
                        $this->customerService->adjustCredits(
                            $customer,
                            $earnedCredits,
                            'earned',
                            (int) $processedByUserId
                        );
                    }
                }

                $serviceTotalsByTechnician = AppointmentService::query()
                    ->where('appointment_id', $appointment->id)
                    ->selectRaw('user_id, SUM(COALESCE(quantity, 1) * COALESCE(unit_price, 0)) AS total_service')
                    ->groupBy('user_id')
                    ->pluck('total_service', 'user_id')
                    ->all();

                $tipsByTechnician = collect($validated['tips_by_technician'] ?? [])
                    ->filter(fn ($row) => isset($row['technician_id']))
                    ->mapWithKeys(fn ($row) => [(int) $row['technician_id'] => round((float) ($row['tip'] ?? 0), 2)]);

                $existingTechnicianIds = $appointment->technicians()
                    ->pluck('users.id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $technicianIds = collect(array_merge(
                    array_map(fn ($id) => (int) $id, array_keys($serviceTotalsByTechnician)),
                    $tipsByTechnician->keys()->all(),
                    $existingTechnicianIds
                ))->unique()->values();

                $pivotUpdates = $technicianIds
                    ->mapWithKeys(function (int $technicianId) use ($serviceTotalsByTechnician, $tipsByTechnician, $commissionRate, $currency) {
                        $tip = (float) ($tipsByTechnician[$technicianId] ?? 0);
                        $totalService = round((float) ($serviceTotalsByTechnician[$technicianId] ?? 0), 2);
                        $commission = round($totalService * $commissionRate, 2);
                        $total = round($tip + $commission, 2);

                        return [
                            $technicianId => [
                                'total_service' => $totalService,
                                'tip' => round($tip, 2),
                                'commission' => $commission,
                                'total' => $total,
                                'currency' => $currency,
                            ],
                        ];
                    })
                    ->all();

                if (! empty($pivotUpdates)) {
                    $appointment->technicians()->syncWithoutDetaching($pivotUpdates);
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
        $validated = $request->validated();

        if (array_key_exists('status', $validated)) {
            $payment->status = $validated['status'];
        }

        if (($validated['status'] ?? null) === 'Refunded') {
            if (array_key_exists('refund_notes', $validated)) {
                $payment->refund_notes = $validated['refund_notes'];
            }
            $payment->refunded_at = $validated['refunded_at'] ?? now();
        }

        if ($payment->isDirty()) {
            $payment->save();
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
