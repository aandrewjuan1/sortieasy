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
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->admin()->create();
        User::factory(10)->employee()->inactive()->create();

        // Hardcoded users
        User::factory()->create([
            'name' => 'Employee Test',
            'email' => 'employee@example.com',
            'role' => 'employee',
            'password' => bcrypt('password'),
            'phone' => '+1234567890',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('adminpassword'),
            'phone' => '+1987654321',
            'is_active' => true,
        ]);

        Supplier::factory()->count(10)->create();

        // Use withoutEvents to prevent observer triggers during seeding
        Product::withoutEvents(function () {
            // Create 55 products with different categories
            $normalProducts = Product::factory()->count(40)->create(['quantity_in_stock' => rand(100, 300)]);
            $slowProducts = Product::factory()->count(10)->create(['quantity_in_stock' => rand(50, 100)]);
            $obsoleteProducts = Product::factory()->count(5)->create(['quantity_in_stock' => rand(30, 60)]);

            // Create sales for normal products
            foreach ($normalProducts as $product) {
                Sale::factory()->count(100)->normal()->create([
                    'product_id' => $product->id,
                ]);
            }

            // Create sales for slow-moving products
            foreach ($slowProducts as $product) {
                Sale::factory()->count(90)->slowMoving()->create([
                    'product_id' => $product->id,
                ]);
            }

            // Create sales for obsolete products
            foreach ($obsoleteProducts as $product) {
                Sale::factory()->count(20)->obsolete()->create([
                    'product_id' => $product->id,
                ]);
            }
        });

        // Other random data
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
    }
}
