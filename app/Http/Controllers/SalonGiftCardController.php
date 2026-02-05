<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGiftCardRequest;
use App\Http\Requests\UpdateGiftCardRequest;
use App\Models\GiftCard;
use Illuminate\Http\JsonResponse;

class SalonGiftCardController extends Controller
{
    public function index(): JsonResponse
    {
        $cards = GiftCard::query()
            ->orderBy('code')
            ->get()
            ->map(fn (GiftCard $card) => $this->giftCardToShape($card));

        return response()->json([
            'success' => true,
            'data' => $cards,
        ]);
    }

    public function store(StoreGiftCardRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['active'] = $request->boolean('active', true);
        if (! array_key_exists('balance', $validated)) {
            $validated['balance'] = $validated['initial_value'] ?? 0;
        }

        $card = GiftCard::query()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gift card created successfully.',
            'data' => $this->giftCardToShape($card),
        ], 201);
    }

    public function update(UpdateGiftCardRequest $request, GiftCard $gift_card): JsonResponse
    {
        $validated = $request->validated();
        if (array_key_exists('active', $validated)) {
            $validated['active'] = $request->boolean('active');
        }
        $gift_card->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gift card updated successfully.',
            'data' => $this->giftCardToShape($gift_card->fresh()),
        ]);
    }

    public function destroy(GiftCard $gift_card): JsonResponse
    {
        $gift_card->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gift card removed.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function giftCardToShape(GiftCard $card): array
    {
        return [
            'id' => $card->id,
            'code' => $card->code,
            'description' => $card->description,
            'pin' => $card->pin,
            'initial_value' => (float) $card->initial_value,
            'balance' => (float) $card->balance,
            'active' => $card->active,
        ];
    }
}
