<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class LogisticFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'delivery_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function shipped(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'shipped',
            ];
        });
    }

    public function delivered(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
                'delivery_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }

    public function withProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'product_id' => $product->id,
            ];
        });
    }
}
