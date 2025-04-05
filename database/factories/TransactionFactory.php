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
        // Generate random product and user IDs
        $product = Product::inRandomOrder()->first(); // Get a random product
        $user = User::inRandomOrder()->first(); // Get a random user

        // Randomly select a transaction type from the TransactionType enum
        $type = $this->faker->randomElement(TransactionType::cases());

        // Randomly decide on the quantity for the transaction
        $quantity = $this->faker->numberBetween(1, 20);

        // Add specific transaction logic for different types
        $notes = $this->generateTransactionNotes($type, $product, $quantity);

        // Return the transaction data
        return [
            'product_id' => $product->id,  // Use the product ID
            'type' => $type->value,  // Transaction type (purchase, sale, etc.)
            'quantity' => $quantity,
            'notes' => $notes,
            'created_by' => $user->id,  // User who created the transaction
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),  // Random date within the past year
            'updated_at' => now(), // Updated timestamp
        ];
    }

    /**
     * Generate specific notes for each type of transaction.
     *
     * @param \App\Enums\TransactionType $type
     * @param \App\Models\Product $product
     * @param int $quantity
     * @return string
     */
    private function generateTransactionNotes(TransactionType $type, $product, $quantity)
    {
        switch ($type) {
            case TransactionType::Purchase:
                return "Purchased {$quantity} units of {$product->name}.";
            case TransactionType::Sale:
                return "Sold {$quantity} units of {$product->name}.";
            case TransactionType::Return:
                return "Returned {$quantity} units of {$product->name}.";
            case TransactionType::Adjustment:
                return "Adjusted stock for {$product->name}: {$quantity} units.";
            default:
                return "Transaction for {$product->name}.";
        }
    }
}
