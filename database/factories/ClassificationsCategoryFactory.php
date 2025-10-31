<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassificationsCategory>
 */
class ClassificationsCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'arName' => $this->faker->word() . rand(1000, 9999),
            'enName' => $this->faker->word() . rand(1000, 9999),
            'arSymbol' => $this->faker->word() . rand(1000, 9999),
            'enSymbol' => $this->faker->word() . rand(1000, 9999),
            'color' => $this->faker->word() ,
            'desc' => $this->faker->word() ,
        ];
    }
}
