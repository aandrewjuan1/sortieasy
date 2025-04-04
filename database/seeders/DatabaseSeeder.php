<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create realistic suppliers
        Supplier::factory()->count(5)->create();

        // Create realistic products, each associated with a supplier
        Product::factory()->count(5)->create();

        // Create realistic transactions (both purchases and sales)
        Transaction::factory()->count(10)->create();
    }
}
