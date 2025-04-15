<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\User;
use App\Models\Alert;
use App\Models\Product;
use App\Models\Logistic;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;  // Import the UserRole enum

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random users using the factory
        User::factory(10)->admin()->create(); // Active admin
        User::factory(10)->employee()->inactive()->create(); // Inactive employee

        // Create a hardcoded employee user
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

        // Create realistic suppliers
        Supplier::factory()->count(10)->create();

        // Create realistic products, each associated with a supplier
        Product::factory()->count(55)->create();

        // Generate 25 'purchase' transactions
        Transaction::factory()->count(25)->create([
            'type' => TransactionType::Purchase->value,
        ]);
        // Generate 25 'sale' transactions
        Transaction::factory()->count(25)->create([
            'type' => TransactionType::Sale->value,
        ]);
        // Generate 25 'return' transactions
        Transaction::factory()->count(50)->create([
            'type' => TransactionType::Return->value,
        ]);
        // Generate 25 'adjustment' transactions
        Transaction::factory()->count(50)->create([
            'type' => TransactionType::Adjustment->value,
        ]);

        // Create alerts for products
        Alert::factory()->count(40)->create();

        // Create some resolved alerts
        Alert::factory()->count(20)->resolved()->create();

        // Create logistics records
        Logistic::factory()->count(40)->create();

        // Create some shipped logistics
        Logistic::factory()->count(30)->shipped()->create();

        // Create some delivered logistics
        Logistic::factory()->count(30)->delivered()->create();

        Sale::factory()->count(100)->create();
    }
}
