<?php

namespace Database\Seeders;

use App\Models\CommitteeMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommitteeMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        CommitteeMember::factory()->count(500)->create();
    }
}
