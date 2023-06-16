<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyBranch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyBranch>
 */
class CompanyBranchFactory extends Factory
{
    protected $model = CompanyBranch::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'address' => $this->faker->address(),
            'post_code' => '45135',
            'sat_method' => $this->faker->paragraph(2),
            'sat_type' => $this->faker->paragraph(2),
            'sat_way' => $this->faker->paragraph(2),
            'company_id' => Company::all()->random()->id,
        ];
    }
}
