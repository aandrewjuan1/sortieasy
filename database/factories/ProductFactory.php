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
        $products = [
            ['name' => 'Pilot G2 0.7mm Gel Pen', 'category' => 'Stationery', 'price' => 2.99],
            ['name' => 'Sharpie Fine Point Marker', 'category' => 'Stationery', 'price' => 1.89],
            ['name' => 'BIC Round Stic Pen', 'category' => 'Stationery', 'price' => 0.99],
            ['name' => 'Paper Mate Flair Felt Tip Pen', 'category' => 'Stationery', 'price' => 1.99],
            ['name' => 'Post-it Super Sticky Notes', 'category' => 'Stationery', 'price' => 6.49],
            ['name' => 'Moleskine Classic Notebook', 'category' => 'Stationery', 'price' => 19.99],
            ['name' => 'Mead Five Star Notebook', 'category' => 'Stationery', 'price' => 5.49],
            ['name' => 'Staples Copy Paper 8.5x11', 'category' => 'Office Supplies', 'price' => 8.99],
            ['name' => 'Canon Ink Cartridge 245XL', 'category' => 'Office Supplies', 'price' => 32.50],
            ['name' => 'Brother TN760 High Yield Toner', 'category' => 'Office Supplies', 'price' => 84.99],
            ['name' => 'HP 902XL Black Ink Cartridge', 'category' => 'Office Supplies', 'price' => 39.99],
            ['name' => 'Scotch Heavy Duty Shipping Tape', 'category' => 'Stationery', 'price' => 5.99],
            ['name' => 'Duck Brand Bubble Wrap', 'category' => 'Office Supplies', 'price' => 14.99],
            ['name' => 'Avery Printable Labels 8160', 'category' => 'Office Supplies', 'price' => 12.49],
            ['name' => 'Fiskars Scissors 8 Inch', 'category' => 'Office Supplies', 'price' => 12.75],
            ['name' => 'Duracell AA Batteries 20-pack', 'category' => 'Office Supplies', 'price' => 15.99],
            ['name' => 'Energizer AAA Batteries 24-pack', 'category' => 'Office Supplies', 'price' => 18.99],
            ['name' => 'Logitech MX Master 3 Mouse', 'category' => 'Office Supplies', 'price' => 99.99],
            ['name' => 'Microsoft Sculpt Ergonomic Keyboard', 'category' => 'Office Supplies', 'price' => 74.99],
            ['name' => '3M Privacy Filter 15.6" Laptop', 'category' => 'Office Supplies', 'price' => 49.99],
            ['name' => 'Swingline Classic 747 Stapler', 'category' => 'Office Supplies', 'price' => 22.99],
            ['name' => 'Bostitch AntiJam Heavy Duty Stapler', 'category' => 'Office Supplies', 'price' => 39.99],
            ['name' => 'Fellowes Powershred 12C Shredder', 'category' => 'Office Supplies', 'price' => 129.99],
            ['name' => 'Westcott Stainless Steel Ruler', 'category' => 'Stationery', 'price' => 3.49],
            ['name' => 'Elmer’s Disappearing Purple Glue Stick', 'category' => 'Stationery', 'price' => 0.79],
            ['name' => 'Staedtler Mars Plastic Eraser', 'category' => 'Stationery', 'price' => 1.49],
            ['name' => 'Prismacolor Premier Colored Pencils', 'category' => 'Stationery', 'price' => 29.99],
            ['name' => 'Crayola 64 Crayon Box', 'category' => 'Stationery', 'price' => 6.99],
            ['name' => 'Expo Low Odor Dry Erase Markers 12-pack', 'category' => 'Stationery', 'price' => 18.99],
            ['name' => 'Quartet Magnetic Dry Erase Board 36x24', 'category' => 'Office Supplies', 'price' => 59.99],
            ['name' => 'Smead Hanging File Folders', 'category' => 'Office Supplies', 'price' => 14.99],
            ['name' => 'Bankers Box SmoothMove Boxes', 'category' => 'Office Supplies', 'price' => 24.99],
            ['name' => 'Oxford Twin Pocket Folders', 'category' => 'Stationery', 'price' => 11.49],
            ['name' => 'JAM Paper Color Envelopes', 'category' => 'Stationery', 'price' => 13.99],
            ['name' => 'Swingline GBC CombBind Machine', 'category' => 'Office Supplies', 'price' => 169.99],
            ['name' => 'Avery Heavy Duty View Binder 2"', 'category' => 'Stationery', 'price' => 8.99],
            ['name' => 'Pilot FriXion Clicker Erasable Pen', 'category' => 'Stationery', 'price' => 3.49],
            ['name' => 'Zebra Mildliner Highlighters 5-pack', 'category' => 'Stationery', 'price' => 7.99],
            ['name' => 'Paper Mate InkJoy Gel Pens 6-pack', 'category' => 'Stationery', 'price' => 9.99],
            ['name' => 'Kensington Pro Fit Wireless Mouse', 'category' => 'Office Supplies', 'price' => 29.99],
            ['name' => 'Fellowes Adjustable Monitor Stand', 'category' => 'Office Supplies', 'price' => 34.99],
            ['name' => 'Office Depot Brand Binder Clips', 'category' => 'Office Supplies', 'price' => 3.49],
            ['name' => 'Staedtler Lumocolor Whiteboard Markers', 'category' => 'Stationery', 'price' => 11.49],
            ['name' => 'Canson XL Mixed Media Pad', 'category' => 'Stationery', 'price' => 14.99],
            ['name' => 'Pentel EnerGel RTX Retractable Pen', 'category' => 'Stationery', 'price' => 2.49],
            ['name' => 'Prismacolor Premier Graphite Pencils', 'category' => 'Stationery', 'price' => 24.99],
            ['name' => 'HP Everyday Copy & Print Paper', 'category' => 'Office Supplies', 'price' => 7.49],
            ['name' => 'Staedtler Triplus Fineliner Pens 10-pack', 'category' => 'Stationery', 'price' => 13.99],
            ['name' => 'PaperPro One Finger Stapler', 'category' => 'Office Supplies', 'price' => 19.99],
            ['name' => 'Post-it Durable Tabs 2"', 'category' => 'Stationery', 'price' => 5.49],
            ['name' => 'Ampad Gold Fibre Writing Pads', 'category' => 'Stationery', 'price' => 12.99],
            ['name' => 'Avery UltraTabs Repositionable Tabs', 'category' => 'Stationery', 'price' => 8.49],
            ['name' => 'Logitech K380 Multi-Device Bluetooth Keyboard', 'category' => 'Office Supplies', 'price' => 39.99],
        ];

        $product = $this->faker->randomElement($products);

        $skuPrefix = strtoupper(substr($product['category'], 0, 4));
        $sku = $skuPrefix . '-' . $this->faker->unique()->numberBetween(1000, 9999);

        return [
            'name' => $product['name'],
            'description' => $this->faker->sentence(),
            'category' => $product['category'],
            'sku' => $sku,
            'price' => $product['price'],
            'cost' => round($product['price'] * 0.6, 2),
            'quantity_in_stock' => $this->faker->numberBetween(0, 300),
            'reorder_threshold' => $this->faker->numberBetween(5, 50),
            'safety_stock' => $this->faker->numberBetween(2, 10),
            'last_restocked' => $this->faker->date(),
            'supplier_id' => Supplier::inRandomOrder()->first()?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
