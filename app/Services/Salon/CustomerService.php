<?php

namespace App\Services\Salon;

use App\Models\Customer;
use App\Models\CustomerCreditLedger;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * @param  array{first_name: string, last_name: string, email?: string|null, phone?: string|null}  $data
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            return Customer::query()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);
        });
    }

    /**
     * @param  array{first_name: string, last_name: string, email?: string|null, phone?: string|null}  $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            $updates = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ];

            if (array_key_exists('ghl_contact_id', $data)) {
                $updates['ghl_contact_id'] = $data['ghl_contact_id'] ?: null;
            }

            $customer->update($updates);

            return $customer->fresh();
        });
    }

    public function delete(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer) {
            return (bool) $customer->delete();
        });
    }

    public function adjustCredits(Customer $customer, float $amount, string $operation, int $processedByUserId): Customer
    {
        return DB::transaction(function () use ($customer, $amount, $operation, $processedByUserId) {
            $freshCustomer = Customer::query()
                ->whereKey($customer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $current = (float) $freshCustomer->credit_balance;
            $newBalance = match ($operation) {
                'add', 'earned' => $current + $amount,
                'subtract' => $current - $amount,
                'redeem' => $current - $amount,
                'set' => $amount,
                default => $current,
            };

            if ($newBalance < 0) {
                if ($operation === 'redeem') {
                    throw new \RuntimeException('Customer credits are insufficient.');
                }

                throw new \RuntimeException('Credit balance cannot be negative.');
            }

            CustomerCreditLedger::query()->create([
                'user_id' => $processedByUserId,
                'customer_id' => $freshCustomer->id,
                'operation' => $operation,
                'amount' => round($amount, 2),
                'remaining_balance' => round($current, 2),
                'new_balance' => round($newBalance, 2),
            ]);

            $freshCustomer->update([
                'credit_balance' => round($newBalance, 2),
            ]);

            return $freshCustomer->fresh();
        });
    }
}
