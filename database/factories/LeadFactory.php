<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name'         => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'phone'             => $this->faker->phoneNumber(),
            'status'            => $this->faker->randomElement(['novo', 'em andamento', 'concluído']),
            'registration_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
