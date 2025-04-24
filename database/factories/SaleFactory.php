<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use App\Models\Transaction;
use App\Enums\SaleChannel;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        $quantity = $this->faker->numberBetween(1, 50);
        $unitPrice = $product ? $product->price : $this->faker->randomFloat(2, 5, 100);
        $totalPrice = $quantity * $unitPrice;
        $channel = $this->faker->randomElement(SaleChannel::getValues());

        return [
            'product_id' => $product?->id,
            'user_id' => $user?->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'channel' => $channel,
            'sale_date' => now(), // <- leave it NULL here, to allow state() to properly inject
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }


    public function configure()
    {
        return $this->afterCreating(function (Sale $sale) {
            if ($sale->product_id && $sale->user_id) {
                Transaction::factory()->create([
                    'product_id' => $sale->product_id,
                    'type' => TransactionType::Sale->value,
                    'quantity' => $sale->quantity,
                    'created_by' => $sale->user_id,
                    'created_at' => $sale->sale_date,
                    'updated_at' => now(),
                    'notes' => "Sold {$sale->quantity} units of {$sale->product->name}.",
                ]);
            }
        });
    }

    // In your SaleFactory, modify the states:

    public function normal()
    {
        return $this->state(function () {
            return [
                'sale_date' => Carbon::now()->subDays(rand(1, 20))->startOfDay(), // Ensure past dates
            ];
        });
    }

    public function slowMoving()
    {
        return $this->state(function () {
            return [
                'sale_date' => Carbon::now()->subDays(rand(30, 89))->startOfDay(),
            ];
        });
    }

    public function obsolete()
    {
        return $this->state(function () {
            return [
                'sale_date' => Carbon::now()->subDays(rand(150, 365))->startOfDay(),
            ];
        });
    }
}
