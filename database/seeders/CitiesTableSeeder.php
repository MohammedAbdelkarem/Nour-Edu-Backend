<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cities')->delete();

        \DB::table('cities')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'دمشق',
                'created_at' => '2024-02-26 06:07:46',
                'updated_at' => '2024-02-26 06:07:46',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'ريف دمشق',
                'created_at' => '2024-02-26 06:07:46',
                'updated_at' => '2024-02-26 06:07:46',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'حلب',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'درعا',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'القنيطرة',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'السويداء',
                'created_at' => '2024-02-26 06:13:37',
                'updated_at' => '2024-02-26 06:13:37',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'حمص',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'حماة',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'اللاذقية',
                'created_at' => '2024-02-26 06:14:16',
                'updated_at' => '2024-02-26 06:14:16',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'طرطوس',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'دير الزور',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'إدلب',
                'created_at' => '2024-02-26 06:13:37',
                'updated_at' => '2024-02-26 06:13:37',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'الرقة',
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'الحسكة',
                'created_at' => '2024-02-26 06:14:16',
                'updated_at' => '2024-02-26 06:14:16',
            ),

        ));
    }
}
