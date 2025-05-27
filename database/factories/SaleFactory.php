<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use App\Models\SalesArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'area_id' => SalesArea::factory(),
        ];
    }
}
