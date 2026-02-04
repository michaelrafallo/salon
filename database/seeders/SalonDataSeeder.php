<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SalonDataSeeder extends Seeder
{
    protected string $jsonPath;

    public function __construct()
    {
        $this->jsonPath = public_path('json');
    }

    public function run(): void
    {
        $this->seedServiceCategories();
        $this->seedCustomers();
        $this->seedUsers();
        $this->seedServices();
        $this->seedAppointments();
        $this->seedPayments();
    }

    protected function seedServiceCategories(): void
    {
        $path = $this->jsonPath.'/service-categories.json';
        if (! is_readable($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        $categories = $data['categories'] ?? [];
        foreach ($categories as $slug => $name) {
            ServiceCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
    }

    protected function seedCustomers(): void
    {
        $path = $this->jsonPath.'/customers.json';
        if (! is_readable($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        $rows = $data['customers'] ?? [];
        foreach ($rows as $row) {
            DB::table('customers')->insertOrIgnore([
                'id' => $row['id'],
                'first_name' => $row['firstName'] ?? '',
                'last_name' => $row['lastName'] ?? '',
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function seedUsers(): void
    {
        $path = $this->jsonPath.'/users.json';
        if (! is_readable($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        $rows = $data['users'] ?? [];
        foreach ($rows as $row) {
            $name = trim(($row['firstName'] ?? '').' '.($row['lastName'] ?? ''));
            DB::table('users')->insertOrIgnore([
                'id' => $row['id'],
                'name' => $name ?: $row['username'] ?? 'User',
                'username' => $row['username'] ?? null,
                'email' => $row['email'] ?? ('user'.$row['id'].'@salon.com'),
                'email_verified_at' => null,
                'password' => Hash::make($row['password'] ?? 'password'),
                'first_name' => $row['firstName'] ?? null,
                'last_name' => $row['lastName'] ?? null,
                'phone' => $row['phone'] ?? null,
                'role' => $row['role'] ?? null,
                'status' => $row['status'] ?? 'active',
                'initials' => $row['initials'] ?? null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function seedServices(): void
    {
        $path = $this->jsonPath.'/services.json';
        if (! is_readable($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        $rows = $data['services'] ?? [];
        foreach ($rows as $row) {
            DB::table('services')->insertOrIgnore([
                'id' => $row['id'],
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? null,
                'price' => $row['price'] ?? 0,
                'active' => (bool) ($row['active'] ?? true),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categorySlugs = $row['categories'] ?? [];
            $categoryIds = ServiceCategory::query()
                ->whereIn('slug', $categorySlugs)
                ->pluck('id');
            $service = Service::find($row['id']);
            if ($service) {
                $service->categories()->sync($categoryIds);
            }
        }
    }

    protected function seedAppointments(): void
    {
        $path = $this->jsonPath.'/appointments.json';
        if (! is_readable($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        $rows = $data['appointments'] ?? [];
        $categoryBySlug = ServiceCategory::query()->pluck('id', 'slug')->all();
        foreach ($rows as $row) {
            $id = $row['id'];
            $createdAt = isset($row['created_at']) ? \Carbon\Carbon::parse($row['created_at']) : now();
            $appointmentDatetime = isset($row['appointment_datetime'])
                ? \Carbon\Carbon::parse($row['appointment_datetime'])
                : $createdAt;
            DB::table('appointments')->insertOrIgnore([
                'id' => $id,
                'customer_id' => $row['customer_id'],
                'type' => $row['appointment'] ?? 'walk-in',
                'status' => $row['status'] ?? 'unpaid',
                'appointment_datetime' => $appointmentDatetime,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $technicianIds = $row['assigned_technician'] ?? [];
            foreach ($technicianIds as $userId) {
                DB::table('appointment_technician')->insertOrIgnore([
                    'appointment_id' => $id,
                    'user_id' => (int) $userId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
            $services = $row['services'] ?? [];
            foreach ($services as $svc) {
                $slug = $svc['service'] ?? null;
                $technicianId = (int) ($svc['technician_id'] ?? 0);
                $categoryId = $slug ? ($categoryBySlug[$slug] ?? null) : null;
                if ($categoryId && $technicianId) {
                    DB::table('appointment_services')->insert([
                        'appointment_id' => $id,
                        'service_category_id' => $categoryId,
                        'user_id' => $technicianId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            }
        }
    }

    protected function seedPayments(): void
    {
        $path = $this->jsonPath.'/payments.json';
        if (! is_readable($path)) {
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        $rows = $data['payments'] ?? [];
        foreach ($rows as $row) {
            $bookingId = $row['bookingId'] ?? '';
            if (preg_match('/^ORDER(\d+)$/i', $bookingId, $m)) {
                $appointmentId = (int) $m[1];
                if (Appointment::where('id', $appointmentId)->exists()) {
                    Payment::firstOrCreate(
                        ['id' => $row['id']],
                        [
                            'appointment_id' => $appointmentId,
                            'amount' => $row['amount'] ?? 0,
                            'method' => $row['method'] ?? null,
                            'status' => $row['status'] ?? 'Completed',
                            'paid_at' => isset($row['date']) ? \Carbon\Carbon::parse($row['date']) : null,
                        ]
                    );
                }
            }
        }
    }
}
