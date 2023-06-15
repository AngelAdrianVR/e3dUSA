<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'business_name' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'rfc' => 'xxxxxxxxxxxxxxxx',
            'post_code' => '45150',
            'fiscal_address' => $this->faker->address(),
        ];
    }
}
