<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;  // Import the UserRole enum
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random users using the factory
        User::factory(10)->create();

        // Create a hardcoded employee user
        User::factory()->create([
            'name' => 'Employee Test', // Hardcoded name
            'email' => 'employee@example.com', // Hardcoded email
            'role' => UserRole::Employee->value, // Set role to 'employee'
            'password' => bcrypt('password'), // Hardcoded password
        ]);

        // Create a hardcoded admin user
        User::factory()->create([
            'name' => 'Admin User', // Hardcoded name
            'email' => 'admin@example.com', // Hardcoded email
            'role' => UserRole::Admin->value, // Set role to 'admin'
            'password' => bcrypt('adminpassword'), // Hardcoded password
        ]);

        // Create realistic suppliers
        Supplier::factory()->count(5)->create();

        // Create realistic products, each associated with a supplier
        Product::factory()->count(5)->create();

        // Create realistic transactions (both purchases and sales)
        Transaction::factory()->count(10)->create();
    }
}
