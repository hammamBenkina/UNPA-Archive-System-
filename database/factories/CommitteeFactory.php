<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Committee>
 */
class CommitteeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no' => rand(1, 1000),
            'yearOfEstablishment' => $this->faker->date(),
            'isCurrent' => 0,
            'createdBy' => 106,
        ];
    }
}
