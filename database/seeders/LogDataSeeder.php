<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Group;
use App\Models\ListData;
use App\Models\LogData;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all list_data records
        $listDataItems = ListData::with(['asset.group.area'])->get();

        if ($listDataItems->isEmpty()) {
            $this->command->info('No list data records found. Please create some list data records first.');
            return;
        }

        // Clear existing log data
        LogData::truncate();

        // Generate data for last 30 days
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);

        $this->command->info('Generating sample log data for the last 30 days...');

        // Generate readings for each parameter
        foreach ($listDataItems as $listData) {
            $this->command->info("Generating data for parameter ID: {$listData->id}");

            // Get the associated asset, group and area
            $asset = $listData->asset;

            if (!$asset) {
                $this->command->warn("Missing asset for list data ID: {$listData->id}. Skipping...");
                continue;
            }

            $group = $asset->group;
            $area = $group->area ?? null;

            if (!$group || !$area) {
                $this->command->warn("Missing group or area for list data ID: {$listData->id}. Skipping...");
                continue;
            }

            // Warning and danger limits for generating values
            $warningLimit = $listData->warning_limit ?? 70;
            $dangerLimit = $listData->danger_limit ?? 90;

            // Define base value and variance
            $baseValue = rand(20, 60);
            $variance = rand(5, 15);

            // Calculate a fixed minute for this list_data_id (0-59)
            $fixedMinute = $listData->id % 60;

            // For each day in the range
            for ($day = 0; $day < 30; $day++) {
                $date = $startDate->copy()->addDays($day);

                // Working hours: 7 AM to 5 PM (07:00 to 17:00)
                for ($hour = 7; $hour <= 17; $hour++) {
                    // Generate random value with some trend
                    $value = $baseValue + rand(-$variance, $variance);

                    // Occasionally generate values in warning or danger ranges
                    $randState = rand(1, 100);
                    if ($randState > 90) {
                        // Danger value
                        $value = $dangerLimit + rand(1, 10);
                        $condition = 'danger';
                    } elseif ($randState > 80) {
                        // Warning value
                        $value = $warningLimit + rand(1, ($dangerLimit - $warningLimit));
                        $condition = 'warning';
                    } else {
                        // Normal value
                        $condition = 'good';
                    }

                    // Create timestamp with the fixed minute for this list_data_id
                    $datetime = $date->copy()->setHour($hour)->setMinute($fixedMinute);

                    // Create log data record
                    LogData::create([
                        'area_id' => $area->id,
                        'group_id' => $group->id,
                        'asset_id' => $asset->id,
                        'list_data_id' => $listData->id,
                        'value' => $value,
                        'date' => $date->format('Y-m-d'),
                        'state' => null,
                        'condition' => $condition,
                        'warning' => 30,
                        'danger' => 60,
                        'created_at' => $datetime,
                        'updated_at' => $datetime
                    ]);
                }
            }
        }

        $this->command->info('Sample log data generated successfully.');
    }
}
