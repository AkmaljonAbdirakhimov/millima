<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Day;
use App\Models\WorkingHours;

class WorkingHoursTableSeeder extends Seeder
{
    public function run()
    {
        $days = Day::all();

        foreach ($days as $day) {
            WorkingHours::create([
                'day_id' => $day->id,
                'is_working_day' => ($day->name !== 'saturday' && $day->name !== 'sunday'),
                'opening_time' => ($day->name !== 'saturday' && $day->name !== 'sunday') ? '09:00' : null,
                'closing_time' => ($day->name !== 'saturday' && $day->name !== 'sunday') ? '17:00' : null,
            ]);
        }
    }
}
