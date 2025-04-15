<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Enums\LogisticStatus;  // Import the LogisticStatus enum

class LogisticFactory extends Factory
{
    public function definition(): array
    {
        // Randomly select a status from the LogisticStatus enum
        $status = $this->faker->randomElement(LogisticStatus::cases());

        return [
            'product_id' => Product::inRandomOrder()->value('id') ?? Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'delivery_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $status->value,  // Use the enum value
        ];
    }

    // Pending logistic status
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            $status = LogisticStatus::Pending;  // Use the enum value
            return [
                'status' => $status->value,
            ];
        });
    }

    // Shipped logistic status
    public function shipped(): static
    {
        return $this->state(function (array $attributes) {
            $status = LogisticStatus::Shipped;  // Use the enum value
            return [
                'status' => $status->value,
            ];
        });
    }

    // Delivered logistic status
    public function delivered(): static
    {
        return $this->state(function (array $attributes) {
            $status = LogisticStatus::Delivered;  // Use the enum value
            return [
                'status' => $status->value,
                'delivery_date' => $this->faker->dateTimeBetween('-1 week', 'now'),  // Adjust delivery date for 'delivered' status
            ];
        });
    }

    // Assign a specific product to the logistic record
    public function withProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'product_id' => $product->id,
            ];
        });
    }
}
