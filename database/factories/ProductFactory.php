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
        // Define the hardcoded product data
        $products = [
            // Furniture products
            'Furniture' => [
                ['name' => 'Office Desk', 'sku' => 'FURN-0001', 'price' => 199.99, 'cost' => 120.00],
                ['name' => 'Ergonomic Chair', 'sku' => 'FURN-0002', 'price' => 129.99, 'cost' => 80.00],
                ['name' => 'Filing Cabinet', 'sku' => 'FURN-0003', 'price' => 89.99, 'cost' => 50.00],
                ['name' => 'Wooden Bookcase', 'sku' => 'FURN-0004', 'price' => 149.99, 'cost' => 90.00],
            ],

            // Stationery products
            'Stationery' => [
                ['name' => 'Ballpoint Pen', 'sku' => 'STAT-0001', 'price' => 1.99, 'cost' => 1.00],
                ['name' => 'A4 Printer Paper', 'sku' => 'STAT-0002', 'price' => 9.99, 'cost' => 5.00],
                ['name' => 'Ink Cartridge (Black)', 'sku' => 'STAT-0003', 'price' => 29.99, 'cost' => 15.00],
                ['name' => 'Stapler', 'sku' => 'STAT-0004', 'price' => 7.99, 'cost' => 3.50],
                ['name' => 'File Folder', 'sku' => 'STAT-0005', 'price' => 4.99, 'cost' => 2.50],
            ],

            // Technology & Electronics products
            'Technology & Electronics' => [
                ['name' => 'Laser Printer', 'sku' => 'TECH-0001', 'price' => 250.00, 'cost' => 150.00],
                ['name' => '27-inch Monitor', 'sku' => 'TECH-0002', 'price' => 299.99, 'cost' => 180.00],
                ['name' => 'Mechanical Keyboard', 'sku' => 'TECH-0003', 'price' => 99.99, 'cost' => 60.00],
                ['name' => 'Wireless Mouse', 'sku' => 'TECH-0004', 'price' => 29.99, 'cost' => 15.00],
                ['name' => 'USB Hub', 'sku' => 'TECH-0005', 'price' => 19.99, 'cost' => 10.00],
            ]
        ];

        // Randomly select a product category
        $category = $this->faker->randomElement(['Furniture', 'Stationery', 'Technology & Electronics']);

        // Randomly select a product from the chosen category
        $product = $this->faker->randomElement($products[$category]);

        // Ensure the SKU is unique by appending a random number to it
        $sku = $product['sku'] . '-' . $this->faker->unique()->randomNumber(3);  // Unique suffix

        // Hardcoded values for stock and restocking details
        $quantityInStock = $this->faker->numberBetween(0, 50); // Stock between 0 and 50
        $reorderThreshold = $this->faker->numberBetween(5, 15);  // Reorder threshold between 5 and 15
        $safetyStock = $this->faker->numberBetween(3, 5); // Safety stock between 3 and 5

        // Generate stock quantity based on reorder threshold
        $quantityInStock = $this->generateStockQuantity($quantityInStock, $reorderThreshold);

        return [
            'name' => $product['name'],
            'description' => $this->faker->paragraph(),
            'category' => $category,
            'sku' => $sku,  // Use the unique SKU here
            'price' => $product['price'],
            'cost' => $product['cost'],
            'quantity_in_stock' => $quantityInStock,
            'reorder_threshold' => $reorderThreshold,
            'safety_stock' => $safetyStock,
            'last_restocked' => $this->faker->date(),
            'supplier_id' => Supplier::factory(),  // Assuming you have a Supplier model
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
