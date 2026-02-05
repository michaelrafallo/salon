<?php

namespace App\Services\Salon;

use App\Models\Customer;
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
            $customer->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            return $customer->fresh();
        });
    }

    public function delete(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer) {
            return (bool) $customer->delete();
        });
    }

    public function adjustCredits(Customer $customer, float $amount, string $operation): Customer
    {
        return DB::transaction(function () use ($customer, $amount, $operation) {
            $freshCustomer = Customer::query()
                ->whereKey($customer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $current = (float) $freshCustomer->credit_balance;
            $newBalance = match ($operation) {
                'add' => $current + $amount,
                'subtract' => $current - $amount,
                'set' => $amount,
                default => $current,
            };

            if ($newBalance < 0) {
                throw new \RuntimeException('Credit balance cannot be negative.');
            }

            $freshCustomer->update([
                'credit_balance' => round($newBalance, 2),
            ]);

            return $freshCustomer->fresh();
        });
    }
}
