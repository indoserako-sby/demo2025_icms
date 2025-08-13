<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'name' => 'Mill A',
                'location' => 'Mill AB'
            ],
            [
                'name' => 'MILL AB SHARING',
                'location' => 'Mill AB'
            ],
            [
                'name' => 'Mill B',
                'location' => 'Mill AB'
            ],
            [
                'name' => 'Mill G',
                'location' => 'Mill GH'
            ],
            [
                'name' => 'Mill H',
                'location' => 'Mill GH'
            ],
            [
                'name' => 'Mill GH SHARING',
                'location' => 'Mill GH'
            ],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}
