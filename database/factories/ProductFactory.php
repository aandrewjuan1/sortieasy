<?php
// database/factories/ProductFactory.php
namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $products = [
            ['name' => 'Dell XPS 13', 'category' => 'Laptops', 'price' => 999.99, 'cost' => 750.00, 'quantity_in_stock' => 100, 'reorder_threshold' => 10, 'safety_stock' => 5],
            ['name' => 'Apple iPhone 13', 'category' => 'Smartphones', 'price' => 799.00, 'cost' => 600.00, 'quantity_in_stock' => 200, 'reorder_threshold' => 20, 'safety_stock' => 10],
            ['name' => 'HP OfficeJet Pro 9015', 'category' => 'Printers', 'price' => 179.99, 'cost' => 150.00, 'quantity_in_stock' => 50, 'reorder_threshold' => 5, 'safety_stock' => 3],
            ['name' => 'Logitech MX Master 3', 'category' => 'Peripherals', 'price' => 99.99, 'cost' => 60.00, 'quantity_in_stock' => 150, 'reorder_threshold' => 10, 'safety_stock' => 5],
            ['name' => 'Sony WH-1000XM4', 'category' => 'Headphones', 'price' => 348.00, 'cost' => 250.00, 'quantity_in_stock' => 75, 'reorder_threshold' => 10, 'safety_stock' => 5],
        ];

        // Select a random product from the array
        $product = $products[array_rand($products)];

        // Create a new supplier for the product
        $supplier = Supplier::factory()->create();

        // Generate a unique SKU by appending a random string
        $sku = strtoupper(str_replace(' ', '-', $product['name'])) . '-' . strtoupper(uniqid());

        return array_merge($product, [
            'sku' => $sku,  // Assign the unique SKU
            'supplier_id' => $supplier->id, // Assign the supplier
        ]);
    }
}
