<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Enums\AlertType;  // Import the AlertType enum

class AlertFactory extends Factory
{
    public function definition(): array
    {
        // Randomly select an AlertType enum case
        $type = $this->faker->randomElement(AlertType::cases());

        return [
            'product_id' => Product::factory(),
            'type' => $type->value,  // Use the enum value for the type
            'message' => $this->generateMessage($type),  // Use a method to generate appropriate messages
            'resolved' => $this->faker->boolean(20), // 20% chance of being resolved
            'resolved_at' => function (array $attributes) {
                return $attributes['resolved'] ? $this->faker->dateTimeThisMonth() : null;
            },
        ];
    }

    // Generate specific messages for different types of alerts
    private function generateMessage(AlertType $type): string
    {
        return match ($type) {
            AlertType::LowStock => 'Product stock is running low.',
            AlertType::OverStock => 'Product stock is higher than expected.',
            AlertType::RestockSuggestion => 'Consider restocking this product soon.',
        };
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
            $type = AlertType::LowStock;
            return [
                'type' => $type->value,
                'message' => $this->generateMessage($type),
            ];
        });
    }

    public function overStock(): static
    {
        return $this->state(function (array $attributes) {
            $type = AlertType::OverStock;
            return [
                'type' => $type->value,
                'message' => $this->generateMessage($type),
            ];
        });
    }

    public function restockSuggestion(): static
    {
        return $this->state(function (array $attributes) {
            $type = AlertType::RestockSuggestion;
            return [
                'type' => $type->value,
                'message' => $this->generateMessage($type),
            ];
        });
    }
}
