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
}
