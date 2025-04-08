<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Enums\SaleChannel;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Get a random product and user
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        // Calculate total price
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->randomFloat(2, 5, 100); // Random price between 5 and 100

        return [
            'product_id' => $product->id,  // Link to a random product
            'user_id' => $user ? $user->id : null,  // Link to a random user, or null
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,  // Calculate total price
            'channel' => $this->faker->randomElement(SaleChannel::getValues()),  // Random sale channel
            'sale_date' => $this->faker->date,  // Random sale date
        ];
    }
}
