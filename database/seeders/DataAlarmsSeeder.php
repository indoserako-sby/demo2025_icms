<?php

namespace Database\Seeders;

use App\Models\DataAlarm;
use App\Models\ListData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DataAlarmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some list data IDs to associate with alarms
        $listDataIds = ListData::pluck('id')->toArray();

        // Get some user IDs for acknowledgment
        $userIds = User::pluck('id')->toArray();

        if (empty($listDataIds)) {
            $this->command->error('No list data found. Please run ListDataSeeder first.');
            return;
        }

        if (empty($userIds)) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Clear existing records
        DataAlarm::truncate();

        // Generate alarms for the last 30 days
        $now = Carbon::now();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Create sample alarms
        for ($i = 0; $i < 50; $i++) {
            $startTime = Carbon::createFromTimestamp(
                rand($thirtyDaysAgo->timestamp, $now->timestamp)
            );

            // Random duration between 1 minute and 24 hours
            $endTime = $startTime->copy()->addMinutes(rand(1, 1440));

            // Get the ListData record to access its warning and danger limits
            $listData = ListData::find($listDataIds[array_rand($listDataIds)]);

            if (!$listData) {
                continue;
            }

            $warningThreshold = $listData->warning_limit;
            $dangerThreshold = $listData->danger_limit;

            // Generate a value that will trigger either warning or danger
            $baseValue = rand(0, 100);
            if ($baseValue < 40) { // 40% chance for normal value
                $value = $warningThreshold * 0.8; // Value below warning
            } elseif ($baseValue < 70) { // 30% chance for warning
                $value = rand(
                    (int)($warningThreshold * 1.01),
                    (int)($dangerThreshold * 0.99)
                );
            } else { // 30% chance for danger
                $value = rand(
                    (int)($dangerThreshold * 1.01),
                    (int)($dangerThreshold * 1.5)
                );
            }

            // Determine alert type based on actual thresholds
            $alertType = $value >= $dangerThreshold ? 'danger' : ($value >= $warningThreshold ? 'warning' : 'warning');

            // Random acknowledgment status
            $isAcknowledged = (bool)rand(0, 1);

            $alarm = DataAlarm::create([
                'list_data_id' => $listData->id,
                'alert_type' => $alertType,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'resolved' => true,
                'acknowledged' => $isAcknowledged,
                'value' => $value,
                'warning' => $warningThreshold,
                'danger' => $dangerThreshold,
            ]);

            // If alarm is acknowledged, add acknowledgment details
            if ($isAcknowledged) {
                $acknowledgedAt = $endTime->copy()->addMinutes(rand(1, 60));
                $alarm->update([
                    'acknowledged_by' => $userIds[array_rand($userIds)],
                    'acknowledged_at' => $acknowledgedAt,
                    'alarm_cause' => $this->getRandomAlarmCause(),
                    'notes' => $this->getRandomNote(),
                ]);
            }
        }

        $this->command->info('Sample DataAlarms have been created successfully!');
    }

    /**
     * Get a random alarm cause
     */
    private function getRandomAlarmCause(): string
    {
        $causes = ['fake_alarm', 'mall_function', 'test_alarm', 'error_alarm'];
        return $causes[array_rand($causes)];
    }

    /**
     * Get a random note
     */
    private function getRandomNote(): string
    {
        $notes = [
            'Sensor malfunction detected',
            'Regular maintenance check',
            'False alarm - system calibration required',
            'Equipment testing in progress',
            'Environmental factors triggered alarm',
        ];
        return $notes[array_rand($notes)];
    }
}
