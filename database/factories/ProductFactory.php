<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $productionPrice = $this->faker->numberBetween(50000, 500000);
        return [
            'name' => $this->faker->words(3, true),
            'production_price' => $productionPrice,
            'selling_price' => $productionPrice * 1.3, // 30% markup
        ];
    }
}
