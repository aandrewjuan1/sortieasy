<?php
namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Enums\TransactionType;  // Import the enum class
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'type' => $this->faker->randomElement(TransactionType::cases())->value, // Accessing enum value
            'quantity' => $this->faker->numberBetween(1, 20),
            'notes' => $this->faker->sentence(),
            'created_by' => User::factory(),
            // Set 'created_at' to a random date within the past week, month, or year
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
