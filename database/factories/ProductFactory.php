<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        // Randomly decide the quantity_in_stock and reorder_threshold for each product
        $quantityInStock = $this->faker->numberBetween(0, 20); // Random stock between 0 and 20
        $reorderThreshold = $this->faker->numberBetween(5, 10);  // Reorder threshold between 5 and 10
        $safetyStock = $this->faker->numberBetween(3, 5); // Safety stock between 3 and 5

        // Create random stock status: low stock, normal stock, or overstocked
        $quantityInStock = $this->generateStockQuantity($quantityInStock, $reorderThreshold);

        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->word(),
            'sku' => strtoupper($this->faker->unique()->lexify('???-#####')),
            'price' => $this->faker->randomFloat(2, 10, 500),  // Price between 10 and 500
            'cost' => $this->faker->randomFloat(2, 5, 300),  // Cost between 5 and 300
            'quantity_in_stock' => $quantityInStock,
            'reorder_threshold' => $reorderThreshold,
            'safety_stock' => $safetyStock,
            'last_restocked' => $this->faker->date(),
            'supplier_id' => Supplier::factory(),  // Assuming a Supplier model exists, adjust if not
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Randomly assign stock quantity based on the reorder threshold to simulate different stock levels.
     *
     * @param int $quantityInStock
     * @param int $reorderThreshold
     * @return int
     */
    private function generateStockQuantity($quantityInStock, $reorderThreshold)
    {
        // Randomly decide whether to generate low, normal, or overstocked products
        $stockStatus = $this->faker->randomElement(['low', 'normal', 'overstocked']);

        if ($stockStatus == 'low') {
            // Low stock (below reorder threshold)
            return $this->faker->numberBetween(0, $reorderThreshold - 1);
        } elseif ($stockStatus == 'normal') {
            // Normal stock (within a reasonable range, above reorder threshold)
            return $this->faker->numberBetween($reorderThreshold, $reorderThreshold + 10);
        } else {
            // Overstocked (more than double the reorder threshold)
            return $this->faker->numberBetween($reorderThreshold * 2, $reorderThreshold * 3);
        }
    }
}
