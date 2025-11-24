<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TosTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */

    public function run()
    {


        \DB::table('tos')->delete();

        \DB::table('tos')->insert(array(
            0 =>
            array(
                'id' => 2,
                'lang' => 'ar',
                'text' => '<h1>شروط الخدمة</h1>
<h4>Name</h4>
<p>شروط الخدمة في التطبيق   </p>',
                'update_by' => 1,
                'created_at' => '2024-02-02 02:28:15',
                'updated_at' => '2024-02-02 02:28:15',
            ),
        ));
    }
}