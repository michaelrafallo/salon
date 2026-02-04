<?php

namespace App\Services\Salon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @param  array{username: string, first_name: string, last_name: string, email: string, password: string, phone?: string|null, role: string, status?: string|null}  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $name = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')) ?: $data['username'];

            return User::query()->create([
                'name' => $name,
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'],
                'status' => $data['status'] ?? 'active',
            ]);
        });
    }

    /**
     * @param  array{username?: string, first_name?: string, last_name?: string, email?: string, password?: string|null, phone?: string|null, role?: string, status?: string|null}  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $attrs = [];
            if (array_key_exists('username', $data)) {
                $attrs['username'] = $data['username'];
            }
            if (array_key_exists('first_name', $data)) {
                $attrs['first_name'] = $data['first_name'];
            }
            if (array_key_exists('last_name', $data)) {
                $attrs['last_name'] = $data['last_name'];
            }
            if (array_key_exists('email', $data)) {
                $attrs['email'] = $data['email'];
            }
            if (! empty($data['password'])) {
                $attrs['password'] = Hash::make($data['password']);
            }
            if (array_key_exists('phone', $data)) {
                $attrs['phone'] = $data['phone'];
            }
            if (array_key_exists('role', $data)) {
                $attrs['role'] = $data['role'];
            }
            if (array_key_exists('status', $data)) {
                $attrs['status'] = $data['status'] ?? 'active';
            }
            $attrs['name'] = trim(($attrs['first_name'] ?? $user->first_name).' '.($attrs['last_name'] ?? $user->last_name)) ?: ($attrs['username'] ?? $user->username);
            $user->update($attrs);

            return $user->fresh();
        });
    }

    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return (bool) $user->delete();
        });
    }
}
