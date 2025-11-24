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
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:07:46',
                'updated_at' => '2024-02-26 06:07:46',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'ريف دمشق',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:07:46',
                'updated_at' => '2024-02-26 06:07:46',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'حلب',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'درعا',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'القنيطرة',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'السويداء',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:13:37',
                'updated_at' => '2024-02-26 06:13:37',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'حمص',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'حماة',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'اللاذقية',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:14:16',
                'updated_at' => '2024-02-26 06:14:16',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'طرطوس',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'دير الزور',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'إدلب',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:13:37',
                'updated_at' => '2024-02-26 06:13:37',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'الرقة',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:09:02',
                'updated_at' => '2024-02-26 06:09:02',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'الحسكة',
                'contry_id' => 1,
                'created_at' => '2024-02-26 06:14:16',
                'updated_at' => '2024-02-26 06:14:16',
            ),
            14 => array(
                'id' => 15,
                'name' => 'Abu Dhabi',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            15 => array(
                'id' => 16,
                'name' => 'Ajman',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            16 => array(
                'id' => 17,
                'name' => 'Dubai',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            17 => array(
                'id' => 18,
                'name' => 'Fujairah',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            18 => array(
                'id' => 19,
                'name' => 'Ras Al Khaimah',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            19 => array(
                'id' => 20,
                'name' => 'Sharjah',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            20 => array(
                'id' => 21,
                'name' => 'Umm Al Quwain',
                'contry_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            21 => array(
                'id' => 22,
                'name' => 'Al Jahra',
                'contry_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            22 => array(
                'id' => 23,
                'name' => 'Capital',
                'contry_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            23 => array(
                'id' => 24,
                'name' => 'Al Farwaniyah',
                'contry_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            24 => array(
                'id' => 25,
                'name' => 'Hawalli',
                'contry_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            25 => array(
                'id' => 26,
                'name' => 'Mubarak Al Kabeer',
                'contry_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            26 => array(
                'id' => 27,
                'name' => 'Al Ahmadi',
                'contry_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),

        ));
    }
}
