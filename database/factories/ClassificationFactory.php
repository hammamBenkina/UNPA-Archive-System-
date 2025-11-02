<?php

namespace Database\Factories;

use App\Models\ClassificationsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClassificationFactory extends Factory
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
            'categoryId' => ClassificationsCategory::inRandomOrder()->first()->id,
            'color' => $this->faker->word(),
            'desc' => $this->faker->word(),
        ];
    }
}
