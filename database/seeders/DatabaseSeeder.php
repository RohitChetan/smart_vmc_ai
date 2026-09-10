<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WardSeeder::class,
            DepartmentSeeder::class,
            CategorySeeder::class,
        ]);
    }
}