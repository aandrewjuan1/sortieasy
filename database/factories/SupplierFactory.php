<?php

// database/factories/SupplierFactory.php
namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition()
    {
        // Define hardcoded suppliers for different categories of products
        $suppliers = [
            'Furniture' => [
                ['name' => 'IKEA', 'contact_email' => 'info@ikea.com', 'contact_phone' => '1-800-423-4532', 'address' => '1 IKEA Way, Conshohocken, PA 19428'],
                ['name' => 'Wayfair', 'contact_email' => 'support@wayfair.com', 'contact_phone' => '1-844-403-2921', 'address' => '4 Copley Place, 7th Floor, Boston, MA 02116'],
            ],
            'Stationery' => [
                ['name' => 'Staples', 'contact_email' => 'support@staples.com', 'contact_phone' => '1-800-333-3330', 'address' => '500 Staples Drive, Framingham, MA 01702'],
                ['name' => 'Office Depot', 'contact_email' => 'contact@officedepot.com', 'contact_phone' => '1-800-463-3768', 'address' => '6600 North Military Trail, Boca Raton, FL 33496'],
            ],
            'Technology & Electronics' => [
                ['name' => 'Best Buy', 'contact_email' => 'customer.service@bestbuy.com', 'contact_phone' => '1-888-237-8289', 'address' => '7601 Penn Avenue South, Richfield, MN 55423'],
                ['name' => 'Newegg', 'contact_email' => 'support@newegg.com', 'contact_phone' => '1-800-390-1119', 'address' => '17560 Rowland Street, City of Industry, CA 91748'],
            ]
        ];

        // Randomly select a product category to get the relevant suppliers
        $category = $this->faker->randomElement(['Furniture', 'Stationery', 'Technology & Electronics']);

        // Randomly select a supplier within the chosen category
        $supplier = $this->faker->randomElement($suppliers[$category]);

        return [
            'name' => $supplier['name'],
            'contact_email' => $supplier['contact_email'],
            'contact_phone' => $supplier['contact_phone'],
            'address' => $supplier['address'],
        ];
    }
}
