<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerCardsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('customer_cards')->delete();

        \DB::table('customer_cards')->insert(array(
            0 =>
            array(
                'id' => 1,
                'user_id' => 3,
                'title' => 'Create a product',
                'description' => 'Can\'t create some field over the product budget.',
                'type' => 'inquiry',
                'date' => NULL,
                'admin_answer' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-01-16 03:46:53',
                'updated_at' => '2025-01-17 03:46:53',
            ),
            1 =>
            array(
                'id' => 2,
                'user_id' => 3,
                'title' => 'عدم القدرة على إتمام عملية الدفع',
                'description' => 'يوجد مشكلة تمنع إتمام عملية الدفع عن طريق سيرياتل كاش ضمن التطبيق',
                'type' => 'bug',
                'date' => '2025-06-06',
                'admin_answer' => 'تم إصلاح العطل',
                'deleted_at' => NULL,
                'created_at' => '2025-01-16 23:56:09',
                'updated_at' => '2025-01-16 23:56:09',
            ),
            2 =>
            array(
                'id' => 3,
                'user_id' => 2,
                'title' => 'عطل بإنشاء منتج',
                'description' => 'يوجد عطل بإنشاء المنتجات يمنع اختيار
فئة الترجمة ضمن عملية محددة',
                'type' => 'bug',
                'date' => '2025-06-06',
                'admin_answer' => 'تم إصلاح العطل',
                'deleted_at' => NULL,
                'created_at' => '2025-01-16 23:57:01',
                'updated_at' => '2025-01-16 23:57:01',
            ),
            3 =>
            array(
                'id' => 4,
                'user_id' => 2,
                'title' => 'استفسار حول عملية تعديل بانر',
                'description' => 'ما هي آلية تعديل البانر قبل الموافقة عليه؟',
                'type' => 'inquiry',
                'date' => NULL,
                'admin_answer' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-01-16 23:58:04',
                'updated_at' => '2025-01-16 23:58:04',
            ),
        ));
    }
}
