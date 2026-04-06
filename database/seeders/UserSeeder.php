<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'nailadmin'],
            [
                'name' => 'Nail Salon',
                'first_name' => 'Nail',
                'last_name' => 'Salon',
                'email' => 'admin@nailsalon.com',
                'password' => 'nailsalon',
                'role' => 'superadmin',
                'status' => 'active',
            ]
        );
    }
}
