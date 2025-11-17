<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Committee;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Record>
 */
class RecordFactory extends Factory
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
            'no' => $this->faker->numberBetween(1, 100000),
            'referenceNumber' => $this->faker->word(),
            'year' => $this->faker->numberBetween(1995, 2025),
            'branchId' => Branch::inRandomOrder()->first()->id,
            'committeeId' => Committee::inRandomOrder()->first()->id,
            'docId' => File::inRandomOrder()->first()->id,
            'createdBy' => User::where('roleId', 2)->inRandomOrder()->first()->id,
            'desc' => $this->faker->text(),
            'conveningDate' => $this->faker->date(),
        ];
    }
}
