<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'contact_email' => $this->faker->unique()->companyEmail,
            'contact_phone' => $this->faker->unique()->phoneNumber,
            'address' => $this->faker->address,
        ];
    }
}
