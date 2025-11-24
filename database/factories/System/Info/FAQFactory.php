<?php

namespace Database\Factories\System\Info;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System\Info\FAQ>
 */
class FAQFactory extends Factory
{
    public function definition(): array
    {
        $arFaker = \Faker\Factory::create('ar_SA');
        return [
            'question'  => $arFaker->unique()->paragraph(1),
            'answer'    => $arFaker->unique()->paragraph(2),
            "is_draft"  => $this->faker->randomElement([1, 0, 0, 0]),
            "update_by" => 1,
        ];
    }
}