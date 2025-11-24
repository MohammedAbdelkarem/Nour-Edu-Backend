<?php

namespace Database\Seeders;

use App\Models\Contry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contries')->delete();

        DB::table('contries')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Syria',
                'country_code' => '+963',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'UAE',
                'country_code' => '+971',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Kuwait',
                'country_code' => '+965',
            ),
        ));
    }
}
