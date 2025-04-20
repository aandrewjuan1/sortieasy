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
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        $quantity = $this->faker->numberBetween(1, 20);
        $unitPrice = $product ? $product->price : $this->faker->randomFloat(2, 5, 100);
        $totalPrice = $quantity * $unitPrice;
        $channel = $this->faker->randomElement(SaleChannel::getValues());
        $saleDate = now()->format('Y-m-d');

        return [
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'channel' => $channel,
            'sale_date' => $saleDate,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Sale $sale) {
            Transaction::factory()->create([
                'product_id' => $sale->product_id,
                'type' => TransactionType::Sale->value,
                'quantity' => $sale->quantity,
                'created_by' => $sale->user_id,
                'created_at' => $sale->sale_date,
                'updated_at' => now(),
                'notes' => "Sold {$sale->quantity} units of {$sale->product->name}.",
            ]);
        });
    }
}
