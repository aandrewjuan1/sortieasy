<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;
use App\Enums\Severity; // Import the Severity enum

class AlertFactory extends Factory
{
    public function definition(): array
    {
        // Randomly select a Severity enum case
        $severity = $this->faker->randomElement(Severity::cases());

        return [
            'product_id' => Product::factory(), // Create a related product
            'type' => $this->faker->randomElement(['low_stock', 'over_stock', 'restock_suggestion']), // Use random alert type
            'message' => $this->generateMessage($severity),  // Use the severity-based message
            'severity' => $severity->value,  // Use the enum value for severity
            'resolved' => $this->faker->boolean(20), // 20% chance of being resolved
            'resolved_at' => function (array $attributes) {
                return $attributes['resolved'] ? $this->faker->dateTimeThisMonth() : null;
            },
            'user_id' => function (array $attributes) {
                // If resolved, assign a user to the alert, else leave it null
                return $attributes['resolved'] ? User::factory()->create()->id : null;
            },
        ];
    }

    // Generate specific messages for different severity levels
    private function generateMessage(Severity $severity): string
    {
        return match ($severity) {
            Severity::Critical => 'Immediate attention required: critical issue.',
            Severity::High => 'High priority: please address this issue soon.',
            Severity::Medium => 'Medium priority: monitor this issue.',
            Severity::Low => 'Low priority: issue can be addressed later.',
        };
    }

    // State for resolved alerts
    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'resolved' => true,
                'resolved_at' => now(),
            ];
        });
    }

    // State for unresolved alerts
    public function unresolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'resolved' => false,
                'resolved_at' => null,
            ];
        });
    }

    // State for critical severity alerts
    public function critical(): static
    {
        return $this->state(function (array $attributes) {
            $severity = Severity::Critical;
            return [
                'severity' => $severity->value,
                'message' => $this->generateMessage($severity),
            ];
        });
    }

    // State for high severity alerts
    public function high(): static
    {
        return $this->state(function (array $attributes) {
            $severity = Severity::High;
            return [
                'severity' => $severity->value,
                'message' => $this->generateMessage($severity),
            ];
        });
    }

    // State for medium severity alerts
    public function medium(): static
    {
        return $this->state(function (array $attributes) {
            $severity = Severity::Medium;
            return [
                'severity' => $severity->value,
                'message' => $this->generateMessage($severity),
            ];
        });
    }

    // State for low severity alerts
    public function low(): static
    {
        return $this->state(function (array $attributes) {
            $severity = Severity::Low;
            return [
                'severity' => $severity->value,
                'message' => $this->generateMessage($severity),
            ];
        });
    }
}
