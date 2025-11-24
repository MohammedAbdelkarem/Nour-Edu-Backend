<?php

namespace Database\Seeders;

use App\Models\System\Info\FAQ;
use App\Models\System\Info\FaqCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FAQTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FaqCategory::factory()
            ->count(30)
            ->has(FAQ::factory()->count(4), 'faqs')
            ->create();
    }
}
