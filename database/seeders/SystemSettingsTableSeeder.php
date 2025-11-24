<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('system_settings')->delete();

        DB::table('system_settings')->insert(array(
            0 =>
            array(
                'id' => 1,
                'key' => 'SP To USD',
                'value' => '10000',
                'update_by' => 1,
                'created_at' => '2025-02-06 20:54:44',
                'updated_at' => '2025-02-07 02:28:20',
            ),
            1 =>
            array(
                'id' => 2,
                'key' => 'Banner live time',
                'value' => '24',
                'update_by' => NULL,
                'created_at' => '2025-02-06 20:56:00',
                'updated_at' => '2025-02-06 20:56:00',
            ),
            2 =>
            array(
                'id' => 3,
                'key' => 'Reel live time',
                'value' => '24',
                'update_by' => NULL,
                'created_at' => '2025-02-06 20:56:00',
                'updated_at' => '2025-02-06 20:56:00',
            ),
            3 =>
            array(
                'id' => 4,
                'key' => 'Steps Reward Value',
                'value' => '1',
                'update_by' => NULL,
                'created_at' => '2025-02-06 20:56:00',
                'updated_at' => '2025-02-06 20:56:00',
            ),
            4 =>
            array(
                'id' => 5,
                'key' => 'Steps Daily Goal',
                'value' => '10000',
                'update_by' => NULL,
                'created_at' => '2025-02-06 20:56:00',
                'updated_at' => '2025-02-06 20:56:00',
            ),
            5 =>
            array(
                'id' => 6,
                'key' => 'Steps Minimum Balance to Get',
                'value' => '10',
                'update_by' => NULL,
                'created_at' => '2025-02-06 20:56:00',
                'updated_at' => '2025-02-06 20:56:00',
            ),
        ));
    }
}