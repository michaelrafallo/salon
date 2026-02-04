<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;

class SalonCouponController extends Controller
{
    public function index(): JsonResponse
    {
        $coupons = Coupon::query()
            ->orderBy('code')
            ->get()
            ->map(fn (Coupon $c) => [
                'id' => $c->id,
                'code' => $c->code,
                'description' => $c->description,
                'discount_type' => $c->discount_type,
                'discount_value' => (float) $c->discount_value,
                'min_order_amount' => $c->min_order_amount !== null ? (float) $c->min_order_amount : null,
                'active' => $c->active,
            ]);

        return response()->json([
            'success' => true,
            'data' => $coupons,
        ]);
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['active'] = $request->boolean('active', true);

        $coupon = Coupon::query()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'data' => $this->couponToShape($coupon),
        ], 201);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResponse
    {
        $validated = $request->validated();
        if (array_key_exists('active', $validated)) {
            $validated['active'] = $request->boolean('active');
        }
        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully.',
            'data' => $this->couponToShape($coupon->fresh()),
        ]);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function couponToShape(Coupon $coupon): array
    {
        return [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'description' => $coupon->description,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'min_order_amount' => $coupon->min_order_amount !== null ? (float) $coupon->min_order_amount : null,
            'active' => $coupon->active,
        ];
    }
}
