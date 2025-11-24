<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Unit;
use App\Models\User;
use App\Models\Story;
use App\Models\Banner;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubUnit;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Story\StoryResource;
use App\Http\Resources\Banner\BannerResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Subject\SubjectResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Services\Base\ContextService;

/**
 * Class StudentHomeService.
 */
class StudentHomeService
{
    public function __construct(
        protected ContextService $contextService
    ) {}

    public function get($clevel_id = null)
    {
        if($clevel_id == null)
        {
            $profile = User::findByIdOrFail(auth()->id() , ['c_level' , 'e_level']);
            $clevel_id = $profile->c_level_id;
        }
        
        $stories = Story::active()
                ->cLevel()
                ->where('storiable_id', $clevel_id)
                ->get();

        $banners = Banner::active()
                ->cLevel()
                ->where('bannerable_id', $clevel_id)
                ->get();

        $courses = Course::published()
            ->where('c_level_id', $clevel_id)
            ->with('publishedSubjects')
            ->get();

        // $subjects = Subject::published()->where('c_level_id', $clevel_id)->get();

        $latestLessons = [];

        $teachers = User::where('role_id', 3)->whereHas('responsibilities', function($query) use ($clevel_id){
            $query->where('c_level_id', $clevel_id);
        })->get();

        $leaderBoard = [];

        $quizzes = [];
                
        $this->contextService->disableExpiredCopons();
        $this->contextService->lockTemporarlyContexts();

        return [
            'profile' => $clevel_id == null 
            ? UserResource::make($profile)
            : null,
            'stories' => StoryResource::collection($stories),
            'banners' => BannerResource::collection($banners),
            'courses' => CourseResource::collection($courses),
            // 'subjects' => SubjectResource::collection($subjects),
            'latestLessons' => $latestLessons,
            'teachers' => TeacherResource::collection($teachers),
            'leaderBoard' => $leaderBoard,
            'quizzes' => $quizzes,
        ];
    }

    public function search($data)
    {
        return getOrPaginate(
            Subject::published()->filter($data)->with('course'),
            $data
        );
    }
}
