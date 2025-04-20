<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use App\Models\Transaction;
use App\Enums\SaleChannel;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        // Random product and user (user is optional)
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first(); // Optional, user can be null

        // Random quantity between 1 and 20 for the sale
        $quantity = $this->faker->numberBetween(1, 20);

        // Use the product's price or generate a random price if no product
        $unitPrice = $product ? $product->price : $this->faker->randomFloat(2, 5, 100);

        // Calculate total price (quantity * unit_price)
        $totalPrice = $quantity * $unitPrice;

        // Select a random sales channel from the SaleChannel enum
        $channel = $this->faker->randomElement(SaleChannel::getValues());

        // Sale date: use the current date
        $saleDate = now()->format('Y-m-d');

        // Sale data
        $saleData = [
            'product_id' => $product->id,
            'user_id' => $user?->id, // User is optional, can be null
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'channel' => $channel,
            'sale_date' => $saleDate, // Use the current date
        ];

        // Create the Sale record
        $sale = Sale::create($saleData);

        // Now create a corresponding Transaction record for the sale
        Transaction::factory()->create([
            'product_id' => $product->id,
            'type' => TransactionType::Sale->value, // Transaction type set to Sale
            'quantity' => $quantity,
            'created_by' => $user?->id, // Optional, user who created the transaction
            'created_at' => $saleDate, // Same date as the sale
            'updated_at' => now(), // Update timestamp
            'notes' => "Sold {$quantity} units of {$product->name}.", // Notes for the transaction
        ]);

        return $saleData;
    }
}
