<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use App\Models\CLevel;
use App\Models\Course;
use App\Models\ELevel;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubUnit;
use Illuminate\Database\Seeder;
use App\Enums\PublishStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $eLevels = ELevel::create([
        //     'name' => 'elevel 1',
        //     'bio' => 'elevel 1',
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $cLevels = CLevel::create([
        //     'name' => 'clevel 1',
        //     'bio' => 'clevel 1',
        //     'e_level_id' => $eLevels->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $course = Course::create([
        //     'name' => 'course 1',
        //     'bio' => 'course 1',
        //     'c_level_id' => $cLevels->id,
        //     'e_level_id' => $eLevels->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $subject = Subject::create([
        //     'name' => 'subject 1',
        //     'bio' => 'subject 1',
        //     'course_id' => $course->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels->id,
        //     'c_level_id' => $cLevels->id,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);
        
        // $unit = Unit::create([
        //     'name' => 'unit 1',
        //     'bio' => 'unit 1',
        //     'subject_id' => $subject->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels->id,
        //     'c_level_id' => $cLevels->id,
        //     'course_id' => $course->id,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $subunit = SubUnit::create([
        //     'name' => 'subunit 1',
        //     'bio' => 'subunit 1',
        //     'unit_id' => $unit->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels->id,
        //     'c_level_id' => $cLevels->id,
        //     'course_id' => $course->id,
        //     'subject_id' => $subject->id,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);
        
        // $lesson = Lesson::create([
        //     'name' => 'lesson 1',
        //     'bio' => 'lesson 1',
        //     'sub_unit_id' => $subunit->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels->id,
        //     'c_level_id' => $cLevels->id,
        //     'course_id' => $course->id,
        //     'subject_id' => $subject->id,
        //     'unit_id' => $unit->id,
        // ]);

        // $lessonn = Lesson::create([
        //     'name' => 'lesson 2',
        //     'bio' => 'lesson 2',
        //     'sub_unit_id' => $subunit->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels->id,
        //     'c_level_id' => $cLevels->id,
        //     'course_id' => $course->id,
        //     'subject_id' => $subject->id,
        //     'unit_id' => $unit->id,
        // ]);
        // $eLevels2 = ELevel::create([
        //     'name' => 'elevel 2',
        //     'bio' => 'elevel 2',
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $cLevels2 = CLevel::create([
        //     'name' => 'clevel 2',
        //     'bio' => 'clevel 2',
        //     'e_level_id' => $eLevels2->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $course2 = Course::create([
        //     'name' => 'course 2',
        //     'bio' => 'course 2',
        //     'c_level_id' => $cLevels2->id,
        //     'e_level_id' => $eLevels2->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $subject2 = Subject::create([
        //     'name' => 'subject 2',
        //     'bio' => 'subject 2',
        //     'course_id' => $course2->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels2->id,
        //     'c_level_id' => $cLevels2->id,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);
        
        // $unit2 = Unit::create([
        //     'name' => 'unit 2',
        //     'bio' => 'unit 2',
        //     'subject_id' => $subject2->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels2->id,
        //     'c_level_id' => $cLevels2->id,
        //     'course_id' => $course2->id,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);

        // $subunit2 = SubUnit::create([
        //     'name' => 'subunit 2',
        //     'bio' => 'subunit 2',
        //     'unit_id' => $unit2->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels2->id,
        //     'c_level_id' => $cLevels2->id,
        //     'course_id' => $course2->id,
        //     'subject_id' => $subject2->id,
        //     'number_of_contents' => 1,
        //     'number_of_published_contents' => 1,
        // ]);
        
        // $lesson2 = Lesson::create([
        //     'name' => 'lesson 2',
        //     'bio' => 'lesson 2',
        //     'sub_unit_id' => $subunit2->id,
        //     'publish_status' => PublishStatusEnum::PUBLISHED->value,
        //     'e_level_id' => $eLevels2->id,
        //     'c_level_id' => $cLevels2->id,
        //     'course_id' => $course2->id,
        //     'subject_id' => $subject2->id,
        //     'unit_id' => $unit2->id,
        // ]);

        // $teacher1 = User::create([
        //     'role_id' => 3, // Teacher role
        //     'name' => 'teacher 1',
        //     'phone_number' => '+963900000000',
        //     'email' => 'teacher1@example.com',
        //     'birth_date' => '2000-01-01',
        //     'is_male' => true,
        //     'city_id' => 1,
        // ]);

        // $teacher2 = User::create([
        //     'role_id' => 3, // Teacher role
        //     'name' => 'teacher 2',
        //     'phone_number' => '+963900000001',
        //     'email' => 'teacher2@example.com',
        //     'birth_date' => '2000-01-01',
        //     'is_male' => true,
        //     'city_id' => 1,
        // ]);

        $first = ELevel::create([
            'name' => 'ابتدائي',
            'bio' => 'ابتدائي',
            'publish_status' => PublishStatusEnum::PUBLISHED->value,
            'number_of_contents' => 6,
            'number_of_published_contents' => 6,
            'number_of_students' => 0,
            'number_of_teachers' => 0,
        ]);

        $second = ELevel::create([
            'name' => 'اعدادي',
            'bio' => 'اعدادي',
            'publish_status' => PublishStatusEnum::PUBLISHED->value,
            'number_of_contents' => 3,
            'number_of_published_contents' => 3,
            'number_of_students' => 0,
            'number_of_teachers' => 0,
        ]);

        $third = ELevel::create([
            'name' => 'ثانوي',
            'bio' => 'ثانوي',
            'publish_status' => PublishStatusEnum::PUBLISHED->value,
            'number_of_contents' => 4,
            'number_of_published_contents' => 4,
            'number_of_students' => 0,
            'number_of_teachers' => 0,
        ]);

        $firstArray = [
            'الأول',
            'الثاني',
            'الثالث',
            'الرابع',
            'الخامس',
            'السادس',
        ];

        $secondArray = [
            'السابع',
            'الثامن',
            'التاسع',
        ];

        $thirdArray = [
            'العاشر',
            'الحادي عشر',
            'بكلوريا علمي',
            'أدبي',
        ];

        for($i = 1; $i <= 6; $i++) {
            $first->cLevels()->create([
                'name' => $firstArray[$i - 1],
                'bio' => $firstArray[$i - 1],
                'publish_status' => PublishStatusEnum::PUBLISHED->value,
            ]);
        }

        for($i = 1; $i <= 3; $i++) {
            $second->cLevels()->create([
                'name' => $secondArray[$i - 1],
                'bio' => $secondArray[$i - 1],
                'publish_status' => PublishStatusEnum::PUBLISHED->value,
            ]);
        }

        for($i = 1; $i <= 4; $i++) {
            $third->cLevels()->create([
                'name' => $thirdArray[$i - 1],
                'bio' => $thirdArray[$i - 1],
                'publish_status' => PublishStatusEnum::PUBLISHED->value,
            ]);
        }
        
        
    }
}
