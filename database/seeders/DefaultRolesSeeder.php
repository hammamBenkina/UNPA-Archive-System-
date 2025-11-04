<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('role')->insertOrIgnore([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'data_entry'],
            ['id' => 3, 'name' => 'viewer'],
        ]);
    }
}
