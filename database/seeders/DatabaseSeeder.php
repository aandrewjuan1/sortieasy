<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Logistic;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->admin()->create();
        User::factory(10)->employee()->inactive()->create();

        // Create a hardcoded employee user
        User::factory()->create([
            'name' => 'Employee Test',
            'email' => 'employee@example.com',
            'role' => 'employee', // Direct string value
            'password' => bcrypt('password'),
            'phone' => '+1234567890', // Added phone
            'is_active' => true, // Explicitly active
        ]);

        // Create a hardcoded admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin', // Direct string value
            'password' => bcrypt('adminpassword'),
            'phone' => '+1987654321', // Added phone
            'is_active' => true, // Explicitly active
        ]);

        Supplier::factory()->count(10)->create();
        Product::factory()->count(55)->create();

        Transaction::factory()->count(200)->create([
            'type' => TransactionType::Purchase->value,
        ]);
        Transaction::factory()->count(500)->create([
            'type' => TransactionType::Return->value,
        ]);
        Transaction::factory()->count(800)->create([
            'type' => TransactionType::Adjustment->value,
        ]);

        Logistic::factory()->count(40)->create();
        Logistic::factory()->count(30)->shipped()->create();
        Logistic::factory()->count(30)->delivered()->create();

        Sale::factory()->count(5000)->create();
    }
}
