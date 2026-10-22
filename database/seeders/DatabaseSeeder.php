<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClassificationSeeder::class,
            PermitSeeder::class,
            HsePlanSeeder::class,
            ContractorSeeder::class,
        ]);
    }
}
