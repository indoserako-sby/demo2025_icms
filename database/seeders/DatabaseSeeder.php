<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // AreaSeeder::class,
            // GroupSeeder::class,
            UserSeeder::class,
            AssetSeeder::class,
            LogDataSeeder::class,
            DataAlarmsSeeder::class,
        ]);
    }
}
