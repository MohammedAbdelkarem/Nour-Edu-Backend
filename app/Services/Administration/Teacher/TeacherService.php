<?php

namespace App\Services\Administration\Teacher;

use App\Http\Resources\Responsibility\ResponsibilityResource;
use App\Models\User;
use App\Enums\LevelEnum;
use App\Services\MainService;
use App\Constants\MediaCollection;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Responsibility;
use App\Services\Administration\ResponsibilityService;

class TeacherService extends MainService
{
    public function __construct(
        protected ResponsibilityService $responsibilityService
    ) {}

    public function getAll($data)
    {
        $foreignId = [
            LevelEnum::E_LEVEL => 'e_level_id',
            LevelEnum::C_LEVEL => 'c_level_id',
            LevelEnum::COURSE => 'course_id',
            LevelEnum::SUBJECT => 'subject_id',
            LevelEnum::UNIT => 'unit_id',
            LevelEnum::SUB_UNIT => 'sub_unit_id',
            LevelEnum::LESSON => 'lesson_id',
        ];
        

        return getOrPaginate(
            User::where('role_id', 3) // Teacher role
                ->when(isset($data['context_type']), function($query) use ($data , $foreignId) {
                    $query->whereHas('responsibilities', function($query) use ($data, $foreignId) {
                        $query->where($foreignId[$data['context_type']], $data['context_id']);
                    });
                })
                ->with(['profile', 'city'])
                ->orderBy('created_at', 'desc'),
            $data
        );
    }

    public function getRandom()
    {
        return User::where('role_id' , 3)->inRandomOrder()->get();
    }
    public function show($id)
    {
        return User::where('role_id', 3)
            ->with(['profile', 'city', 'responsibilities.context'])
            ->findOrFail($id);
    }

    public function store($data)
    {
        // Create teacher user
        $teacher = User::create([
            'role_id' => 3, // Teacher role
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'bio' => $data['bio'],
            'email' => $data['email'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'is_male' => $data['is_male'] ?? null,
            'city_id' => $data['city_id'] ?? null,
        ]);

        if(isset($data['image']))
            uploadFileOnMedia($data['image'] , $teacher , MediaCollection::USER_COLLECTION);
    }

    public function getTeacherDetails($teacher_id)  
    {
        return User::findByIdOrFail($teacher_id , ['e_level' , 'c_level']);
    }

    public function getTeacherDetailsForTeacherApp($teacher_id)
    {
        $profile = TeacherResource::make(User::findByIdOrFail($teacher_id));

        $subjects = ResponsibilityResource::collection(Responsibility::where('teacher_id', $teacher_id)
            ->whereNull('unit_id')
            ->with('publishedELevel' , 'publishedCLevel' , 'publishedSubject')
            ->get());

        return [
            'profile' => $profile,
            'subjects' => $subjects,
        ];
    }

    public function update($data , $id)
    {
        $teacher = User::findByIdOrFail($id);

        $teacher->update($data);

        if(isset($data['image']))
            uploadFileOnMedia($data['image'] , $teacher , MediaCollection::USER_COLLECTION);

        $teacher->save();
    }
}
