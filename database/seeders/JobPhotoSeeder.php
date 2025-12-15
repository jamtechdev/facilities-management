<?php

namespace Database\Seeders;

use App\Models\JobPhoto;
use App\Models\Timesheet;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JobPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timesheets = Timesheet::whereNotNull('clock_out_time')
            ->with(['client', 'staff'])
            ->get();

        if ($timesheets->isEmpty()) {
            $this->command->warn('No completed timesheets found. Please run TimesheetSeeder first.');
            return;
        }

        // Select random timesheets to add photos
        $timesheetsWithPhotos = $timesheets->random(min(20, $timesheets->count()));

        foreach ($timesheetsWithPhotos as $timesheet) {
            // Create before photos (1-3 photos)
            $beforePhotoCount = rand(1, 3);
            for ($i = 0; $i < $beforePhotoCount; $i++) {
                JobPhoto::create([
                    'timesheet_id' => $timesheet->id,
                    'client_id' => $timesheet->client_id,
                    'staff_id' => $timesheet->staff_id,
                    'photo_type' => 'before',
                    'file_path' => 'storage/job_photos/before_' . $timesheet->id . '_' . ($i + 1) . '.jpg',
                    'file_name' => 'before_photo_' . ($i + 1) . '.jpg',
                    'file_size' => rand(500000, 2000000), // 500KB to 2MB
                    'status' => rand(1, 3) > 1 ? 'approved' : 'pending', // 66% approved
                    'admin_notes' => rand(1, 4) > 3 ? 'Good quality photo. Shows condition before cleaning.' : null,
                    'approved_by' => rand(1, 3) > 1 ? $timesheet->approved_by : null,
                    'approved_at' => rand(1, 3) > 1 ? $timesheet->approved_at : null,
                    'created_at' => $timesheet->clock_in_time,
                ]);
            }

            // Create after photos (1-3 photos)
            $afterPhotoCount = rand(1, 3);
            for ($i = 0; $i < $afterPhotoCount; $i++) {
                JobPhoto::create([
                    'timesheet_id' => $timesheet->id,
                    'client_id' => $timesheet->client_id,
                    'staff_id' => $timesheet->staff_id,
                    'photo_type' => 'after',
                    'file_path' => 'storage/job_photos/after_' . $timesheet->id . '_' . ($i + 1) . '.jpg',
                    'file_name' => 'after_photo_' . ($i + 1) . '.jpg',
                    'file_size' => rand(500000, 2000000), // 500KB to 2MB
                    'status' => rand(1, 3) > 1 ? 'approved' : 'pending', // 66% approved
                    'admin_notes' => rand(1, 4) > 3 ? 'Excellent work. Area cleaned thoroughly.' : null,
                    'approved_by' => rand(1, 3) > 1 ? $timesheet->approved_by : null,
                    'approved_at' => rand(1, 3) > 1 ? $timesheet->approved_at : null,
                    'created_at' => $timesheet->clock_out_time,
                ]);
            }
        }

        $this->command->info('Job photos seeded successfully!');
    }
}

