<?php

namespace Database\Factories;

use App\Models\CompanyBranch;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'contactable_id' => CompanyBranch::all()->random()->id,
            'contactable_type' => CompanyBranch::class,
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'birthdate' => $this->faker->date(),
            'charge' => $this->faker->jobTitle(),
        ];
    }
}
