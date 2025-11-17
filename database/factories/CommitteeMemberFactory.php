<?php

namespace Database\Factories;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommitteeMember>
 */
class CommitteeMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->word() . rand(1, 1000),
            'adjective' => $this->faker->word(),
            'about' => $this->faker->text(),
            'committeeId' => Committee::inRandomOrder()->first()->id,
            'accountId' => NULL,
            'createdBy' => User::where('roleId', 2)->inRandomOrder()->first()?->id,
        ];
    }
}
