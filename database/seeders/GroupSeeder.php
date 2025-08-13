<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            // Groups for Production Line 1
            [
                'area_id' => 1,
                'name' => 'FAN',
            ],
            [
                'area_id' => 1,
                'name' => 'FILTER',
            ],
            // Groups for Production Line 2
            [
                'area_id' => 1,
                'name' => 'BLOWER TRANSPORT',
            ],
            [
                'area_id' => 1,
                'name' => 'ROLL MILL',
            ],
            // Groups for Packaging Area
            [
                'area_id' => 1,
                'name' => 'AIRLOCK',
            ],
            [
                'area_id' => 1,
                'name' => 'BUCKET ELEVATOR',
            ],

            // MILL B
            [
                'area_id' => 2,
                'name' => 'FAN',
            ],
            [
                'area_id' => 2,
                'name' => 'FILTER',
            ],
            // Groups for Production Line 2
            [
                'area_id' => 2,
                'name' => 'BLOWER TRANSPORT',
            ],
            [
                'area_id' => 2,
                'name' => 'ROLL MILL',
            ],
            // Groups for Packaging Area
            [
                'area_id' => 2,
                'name' => 'AIRLOCK',
            ],
            [
                'area_id' => 2,
                'name' => 'BUCKET ELEVATOR',
            ],
            [
                'area_id' => 3,
                'name' => 'BLOWER TRANSPORT',
            ],
            [
                'area_id' => 3,
                'name' => 'FILTER',
            ],
            // Groups for Packaging Area
            [
                'area_id' => 3,
                'name' => 'AIRLOCK',
            ],
            [
                'area_id' => 3,
                'name' => 'HAMMER MILL',
            ],

            [
                'area_id' => 4,
                'name' => 'FAN',
            ],
            [
                'area_id' => 4,
                'name' => 'FILTER',
            ],
            // Groups for Production Line 2
            [
                'area_id' => 4,
                'name' => 'BLOWER TRANSPORT',
            ],
            [
                'area_id' => 4,
                'name' => 'ROLL MILL',
            ],
            // Groups for Packaging Area
            [
                'area_id' => 4,
                'name' => 'AIRLOCK',
            ],
            [
                'area_id' => 4,
                'name' => 'BUCKET ELEVATOR',
            ],
            [
                'area_id' => 5,
                'name' => 'FAN',
            ],
            [
                'area_id' => 5,
                'name' => 'FILTER',
            ],
            // Groups for Production Line 2
            [
                'area_id' => 5,
                'name' => 'BLOWER TRANSPORT',
            ],
            [
                'area_id' => 5,
                'name' => 'ROLL MILL',
            ],
            // Groups for Packaging Area
            [
                'area_id' => 5,
                'name' => 'AIRLOCK',
            ],
            [
                'area_id' => 5,
                'name' => 'BUCKET ELEVATOR',
            ],
            [
                'area_id' => 6,
                'name' => 'FILTER',
            ],
            [
                'area_id' => 6,
                'name' => 'HAMMER MILL',
            ],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
