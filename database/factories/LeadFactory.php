<?php

namespace Database\Factories;

use App\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name'         => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'phone'             => $this->faker->phoneNumber(),
            'status'            => $this->faker->randomElement(array_column(StatusEnum::cases(), 'value')),
            'registration_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
