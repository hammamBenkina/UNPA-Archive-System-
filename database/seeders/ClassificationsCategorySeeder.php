<?php

namespace Database\Seeders;

use App\Models\ClassificationsCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ClassificationsCategory::factory()->count(100)->create();
    }
}
