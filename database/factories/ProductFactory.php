<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    // Static variable to track used products
    protected static Collection $productPool;
    protected static array $usedProducts = [];

    public function definition()
    {
        // Office Supplies & Equipment SME
        if (!isset(static::$productPool)) {
            static::$productPool = collect([
                // Furniture (15 items)
                ['name' => 'Office Desk', 'sku' => 'FURN-0001', 'price' => 199.99, 'cost' => 120.00, 'category' => 'Furniture'],
                ['name' => 'Ergonomic Chair', 'sku' => 'FURN-0002', 'price' => 129.99, 'cost' => 80.00, 'category' => 'Furniture'],
                ['name' => 'Filing Cabinet', 'sku' => 'FURN-0003', 'price' => 89.99, 'cost' => 50.00, 'category' => 'Furniture'],
                ['name' => 'Wooden Bookcase', 'sku' => 'FURN-0004', 'price' => 149.99, 'cost' => 90.00, 'category' => 'Furniture'],
                ['name' => 'Conference Table', 'sku' => 'FURN-0005', 'price' => 499.99, 'cost' => 300.00, 'category' => 'Furniture'],
                ['name' => 'Visitor Chair', 'sku' => 'FURN-0006', 'price' => 79.99, 'cost' => 45.00, 'category' => 'Furniture'],
                ['name' => 'Executive Chair', 'sku' => 'FURN-0007', 'price' => 229.99, 'cost' => 140.00, 'category' => 'Furniture'],
                ['name' => 'Office Partition', 'sku' => 'FURN-0008', 'price' => 129.99, 'cost' => 75.00, 'category' => 'Furniture'],
                ['name' => 'Reception Desk', 'sku' => 'FURN-0009', 'price' => 399.99, 'cost' => 240.00, 'category' => 'Furniture'],
                ['name' => 'Storage Cabinet', 'sku' => 'FURN-0010', 'price' => 159.99, 'cost' => 95.00, 'category' => 'Furniture'],
                ['name' => 'Desk Organizer', 'sku' => 'FURN-0011', 'price' => 24.99, 'cost' => 12.00, 'category' => 'Furniture'],
                ['name' => 'Whiteboard Stand', 'sku' => 'FURN-0012', 'price' => 89.99, 'cost' => 50.00, 'category' => 'Furniture'],
                ['name' => 'Bookshelf', 'sku' => 'FURN-0013', 'price' => 119.99, 'cost' => 70.00, 'category' => 'Furniture'],
                ['name' => 'Coffee Table', 'sku' => 'FURN-0014', 'price' => 139.99, 'cost' => 80.00, 'category' => 'Furniture'],
                ['name' => 'Folding Table', 'sku' => 'FURN-0015', 'price' => 79.99, 'cost' => 45.00, 'category' => 'Furniture'],

                // Stationery (20 items)
                ['name' => 'Ballpoint Pen', 'sku' => 'STAT-0001', 'price' => 1.99, 'cost' => 1.00, 'category' => 'Stationery'],
                ['name' => 'A4 Printer Paper', 'sku' => 'STAT-0002', 'price' => 9.99, 'cost' => 5.00, 'category' => 'Stationery'],
                ['name' => 'Ink Cartridge (Black)', 'sku' => 'STAT-0003', 'price' => 29.99, 'cost' => 15.00, 'category' => 'Stationery'],
                ['name' => 'Stapler', 'sku' => 'STAT-0004', 'price' => 7.99, 'cost' => 3.50, 'category' => 'Stationery'],
                ['name' => 'File Folder', 'sku' => 'STAT-0005', 'price' => 4.99, 'cost' => 2.50, 'category' => 'Stationery'],
                ['name' => 'Sticky Notes', 'sku' => 'STAT-0006', 'price' => 3.99, 'cost' => 1.50, 'category' => 'Stationery'],
                ['name' => 'Highlighters (Pack of 5)', 'sku' => 'STAT-0007', 'price' => 5.99, 'cost' => 2.50, 'category' => 'Stationery'],
                ['name' => 'Binder Clips (Box)', 'sku' => 'STAT-0008', 'price' => 6.99, 'cost' => 3.00, 'category' => 'Stationery'],
                ['name' => 'Paper Clips (Box)', 'sku' => 'STAT-0009', 'price' => 2.99, 'cost' => 1.00, 'category' => 'Stationery'],
                ['name' => 'Scissors', 'sku' => 'STAT-0010', 'price' => 8.99, 'cost' => 4.00, 'category' => 'Stationery'],
                ['name' => 'Tape Dispenser', 'sku' => 'STAT-0011', 'price' => 5.99, 'cost' => 2.50, 'category' => 'Stationery'],
                ['name' => 'Permanent Marker', 'sku' => 'STAT-0012', 'price' => 2.49, 'cost' => 1.00, 'category' => 'Stationery'],
                ['name' => 'Notebook', 'sku' => 'STAT-0013', 'price' => 4.99, 'cost' => 2.00, 'category' => 'Stationery'],
                ['name' => 'Whiteboard Marker', 'sku' => 'STAT-0014', 'price' => 3.49, 'cost' => 1.50, 'category' => 'Stationery'],
                ['name' => 'Envelopes (Pack of 50)', 'sku' => 'STAT-0015', 'price' => 7.99, 'cost' => 3.50, 'category' => 'Stationery'],
                ['name' => 'Rubber Bands (Box)', 'sku' => 'STAT-0016', 'price' => 3.99, 'cost' => 1.50, 'category' => 'Stationery'],
                ['name' => 'Calculator', 'sku' => 'STAT-0017', 'price' => 12.99, 'cost' => 6.00, 'category' => 'Stationery'],
                ['name' => 'Index Cards', 'sku' => 'STAT-0018', 'price' => 4.49, 'cost' => 2.00, 'category' => 'Stationery'],
                ['name' => 'Pencil Sharpener', 'sku' => 'STAT-0019', 'price' => 1.99, 'cost' => 0.75, 'category' => 'Stationery'],
                ['name' => 'Ruler', 'sku' => 'STAT-0020', 'price' => 2.49, 'cost' => 1.00, 'category' => 'Stationery'],

                // Technology & Electronics (20 items)
                ['name' => 'Laser Printer', 'sku' => 'TECH-0001', 'price' => 250.00, 'cost' => 150.00, 'category' => 'Technology & Electronics'],
                ['name' => '27-inch Monitor', 'sku' => 'TECH-0002', 'price' => 299.99, 'cost' => 180.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Mechanical Keyboard', 'sku' => 'TECH-0003', 'price' => 99.99, 'cost' => 60.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Wireless Mouse', 'sku' => 'TECH-0004', 'price' => 29.99, 'cost' => 15.00, 'category' => 'Technology & Electronics'],
                ['name' => 'USB Hub', 'sku' => 'TECH-0005', 'price' => 19.99, 'cost' => 10.00, 'category' => 'Technology & Electronics'],
                ['name' => 'External Hard Drive 1TB', 'sku' => 'TECH-0006', 'price' => 59.99, 'cost' => 35.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Webcam', 'sku' => 'TECH-0007', 'price' => 49.99, 'cost' => 25.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Bluetooth Speaker', 'sku' => 'TECH-0008', 'price' => 79.99, 'cost' => 40.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Wireless Headphones', 'sku' => 'TECH-0009', 'price' => 89.99, 'cost' => 45.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Document Scanner', 'sku' => 'TECH-0010', 'price' => 129.99, 'cost' => 70.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Laptop Stand', 'sku' => 'TECH-0011', 'price' => 39.99, 'cost' => 20.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Surge Protector', 'sku' => 'TECH-0012', 'price' => 24.99, 'cost' => 12.00, 'category' => 'Technology & Electronics'],
                ['name' => 'USB Flash Drive 64GB', 'sku' => 'TECH-0013', 'price' => 19.99, 'cost' => 10.00, 'category' => 'Technology & Electronics'],
                ['name' => 'HDMI Cable', 'sku' => 'TECH-0014', 'price' => 14.99, 'cost' => 7.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Wireless Presenter', 'sku' => 'TECH-0015', 'price' => 29.99, 'cost' => 15.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Noise-Canceling Mic', 'sku' => 'TECH-0016', 'price' => 49.99, 'cost' => 25.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Docking Station', 'sku' => 'TECH-0017', 'price' => 89.99, 'cost' => 45.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Portable SSD 500GB', 'sku' => 'TECH-0018', 'price' => 79.99, 'cost' => 40.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Monitor Arm', 'sku' => 'TECH-0019', 'price' => 69.99, 'cost' => 35.00, 'category' => 'Technology & Electronics'],
                ['name' => 'Ethernet Cable', 'sku' => 'TECH-0020', 'price' => 9.99, 'cost' => 4.50, 'category' => 'Technology & Electronics'],
            ]);
        }

        // Filter out used SKUs
        $availableProducts = static::$productPool->reject(function ($product) {
            return in_array($product['sku'], static::$usedProducts);
        });

        if ($availableProducts->isEmpty()) {
            throw new \Exception("No more unique products left in the factory pool.");
        }

        // Pick a random product from the available pool
        $product = $availableProducts->random();
        static::$usedProducts[] = $product['sku'];

        // Generate stock and reorder threshold
        $reorderThreshold = $this->faker->numberBetween(5, 15);
        $quantityInStock = $this->generateStockQuantity($reorderThreshold);
        $safetyStock = $this->faker->numberBetween(3, 5);

        return [
            'name' => $product['name'],
            'description' => $this->faker->paragraph(),
            'category' => $product['category'],
            'sku' => $product['sku'], // Already unique
            'price' => $product['price'],
            'cost' => $product['cost'],
            'quantity_in_stock' => $quantityInStock,
            'reorder_threshold' => $reorderThreshold,
            'safety_stock' => $safetyStock,
            'last_restocked' => $this->faker->date(),
            'supplier_id' => Supplier::inRandomOrder()->first()->id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateStockQuantity($reorderThreshold)
    {
        $stockStatus = $this->faker->randomElement(['low', 'normal', 'overstocked']);

        return match ($stockStatus) {
            'low' => $this->faker->numberBetween(0, $reorderThreshold - 1),
            'normal' => $this->faker->numberBetween($reorderThreshold, $reorderThreshold + 10),
            'overstocked' => $this->faker->numberBetween($reorderThreshold * 2, $reorderThreshold * 3),
        };
    }
}
