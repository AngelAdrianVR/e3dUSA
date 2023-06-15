<?php

namespace Database\Factories;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawMaterial>
 */
class RawMaterialFactory extends Factory
{
    protected $model = RawMaterial::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'part_number' => Str::random(10),
            'description' => $this->faker->sentences(),
            'meaure_unit' => $this->faker->randomElement(['cm', 'm', 'mm', 'pieza', 'lt', 'ml', 'rollo', 'par', 'gr', 'kg']),
            'min_quantity' => $this->faker->numberBetween(5, 1000),
            'max_quantity' => $this->faker->numberBetween(1001, 10000),
            'cost' => $this->faker->numberBetween(10.5, 100.65),
            'features' => null,
        ];
    }
}
