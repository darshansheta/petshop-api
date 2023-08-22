<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = config('app.admin.email');
        $adminPassword = config('app.admin.password');

        if (User::where('email', $adminEmail)->exists()) {
            return;
        }

        User::factory()->create([
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'is_admin' => 1,
        ]);
    }
}
