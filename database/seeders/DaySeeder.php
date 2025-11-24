<?php

namespace Database\Seeders;

use App\Enums\DayEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prepare an array of days from the DayEnum
        $days = DayEnum::values();

        // Insert each day into the 'days' table
        foreach ($days as $day) {
            DB::table('days')->insert([
                'name' => $day,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
