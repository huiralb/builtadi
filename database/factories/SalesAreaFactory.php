<?php

namespace Database\Factories;

use App\Models\SalesArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesAreaFactory extends Factory
{
    protected $model = SalesArea::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
        ];
    }
}
