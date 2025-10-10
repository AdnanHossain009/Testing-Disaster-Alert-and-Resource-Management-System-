<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users first (optional, for development)
        User::whereIn('email', [
            'admin@disaster.gov.bd',
            'citizen@example.com', 
            'relief@disaster.gov.bd'
        ])->delete();

        // Create the same users that were hardcoded
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@disaster.gov.bd',
            'password' => Hash::make('admin123'),
            'phone' => '+880-1712345001',
            'role' => 'admin',
            'address' => 'Disaster Management Office, Dhaka',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'John Citizen',
            'email' => 'citizen@example.com',
            'password' => Hash::make('citizen123'),
            'phone' => '+880-1712345002',
            'role' => 'citizen',
            'address' => 'Dhanmondi, Dhaka',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Relief Worker',
            'email' => 'relief@disaster.gov.bd',
            'password' => Hash::make('relief123'),
            'phone' => '+880-1712345003',
            'role' => 'relief_worker',
            'address' => 'Relief Operations Center, Dhaka',
            'email_verified_at' => now()
        ]);

        echo "✅ Created authentication users:\n";
        echo "   Admin: admin@disaster.gov.bd / admin123\n";
        echo "   Citizen: citizen@example.com / citizen123\n";
        echo "   Relief Worker: relief@disaster.gov.bd / relief123\n";
    }
}