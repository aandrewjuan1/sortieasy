<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class AlertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => $this->faker->randomElement(['low_stock', 'over_stock', 'restock_suggestion']),
            'message' => $this->faker->sentence(),
            'resolved' => $this->faker->boolean(20), // 20% chance of being resolved
            'resolved_at' => function (array $attributes) {
                return $attributes['resolved'] ? $this->faker->dateTimeThisMonth() : null;
            },
        ];
    }

    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'resolved' => true,
                'resolved_at' => now(),
            ];
        });
    }

    public function unresolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'resolved' => false,
                'resolved_at' => null,
            ];
        });
    }

    public function lowStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'low_stock',
                'message' => 'Product stock is running low.',
            ];
        });
    }

    public function overStock(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'over_stock',
                'message' => 'Product stock is higher than expected.',
            ];
        });
    }

    public function restockSuggestion(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'restock_suggestion',
                'message' => 'Consider restocking this product soon.',
            ];
        });
    }
}
