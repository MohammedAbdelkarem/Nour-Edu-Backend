<?php

namespace App\Services\Lesson;

use App\Models\Lesson;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use App\Models\Download;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;
use App\Services\Purchase\PurchaseService;

class LessonService
{
    public function __construct(
        protected ContextService $contextService,
        protected PurchaseService $purchaseService
    ) {}

    /**
     * Get all Lessons with optional filtering
     */
    public function getAll($data)
    {
        $query = Lesson::orderBy('priority', 'asc')
                ->with([ 'subUnit', 'quizzes', 'files' , 'responsibilities']);

        // Filter by SubUnit ID if provided
        if (isset($data['sub_unit_id'])) {
            $query->where('sub_unit_id', $data['sub_unit_id']);
        }

        // Filter by Unit ID if provided
        if (isset($data['unit_id'])) {
            $query->where('unit_id', $data['unit_id']);
        }

        // Filter by Subject ID if provided
        if (isset($data['subject_id'])) {
            $query->where('subject_id', $data['subject_id']);
        }

        // Filter by Course ID if provided
        if (isset($data['course_id'])) {
            $query->where('course_id', $data['course_id']);
        }

        // Filter by CLevel ID if provided
        if (isset($data['c_level_id'])) {
            $query->where('c_level_id', $data['c_level_id']);
        }

        // Filter by ELevel ID if provided
        if (isset($data['e_level_id'])) {
            $query->where('e_level_id', $data['e_level_id']);
        }

        return getOrPaginate($query, $data);
    }

    public function store($data)
    {
        $lesson = Lesson::create($data);

        $this->purchaseService->unlockOthersWhenAddnig(Lesson::class, $lesson->id);

        if(isset($data['images']))
            uploadFilesOnMedia($data['images'] , $lesson , MediaCollection::LESSON_COLLECTION);


        if (isset($data['videos'])) {
            // Upload video first
            uploadFilesOnMedia($data['videos'], $lesson, MediaCollection::LESSON_VIDEO_COLLECTION);
        }

        $lesson->save();

        // Update parent SubUnit numbers
        // $this->contextService->updateParentNumberOfContents($lesson, '+');
    }

    public function showForStudent($lesson_id)
    {
        return Lesson::findByIdOrFail($lesson_id , ['publishedFiles' , 'publishedQuizzes']);
    }

    public function showForAdmin($lesson_id)
    {
        return Lesson::findByIdOrFail($lesson_id , [
            'files',
            'quizzes',
            'comments.replay',
        ]);
    }

    public function uploadVideos($data, $id)
    {
        $lesson = Lesson::findByIdOrFail($id);

        $lesson->duration = $data['duration'];

        $file['image'] = $data['video'];
        $file['quality'] = $data['quality'];

        uploadFileOnMedia($file, $lesson, MediaCollection::LESSON_VIDEO_COLLECTION);

        $lesson->save();
    }

    public function update($data, $id)
    {
        $lesson = Lesson::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Lesson::class);

        $lesson->update($data);

        $lesson->save();
    }

    public function destroy($id)
    {
        $lesson = Lesson::findByIdOrFail($id);
        
        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Lesson::class);
        // $this->contextService->checkIfHasPurchasedStudentsBeforeDeleting($id , Lesson::class);

        // Update parent SubUnit numbers before deletion
        // $this->contextService->updateParentNumberOfContents($lesson, '-');

        $this->purchaseService->deleteUnlockOthersWhenDeleting(Lesson::class , $id);

        $lesson->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $lesson = Lesson::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfParentPublishedBeforePublish($id , Lesson::class);
            // $this->contextService->updateLessonDurationAndParentLevels($lesson, $lesson->duration , '+');
        }
        elseif($status == PublishStatusEnum::DRAFT->value) {
            // $this->contextService->updateLessonDurationAndParentLevels($lesson, $lesson->duration , '-');
        }

        $this->contextService->changeWithChildsPublishStatus($id , Lesson::class , $status);
    }

    public function changePriority($contextsData)
    {
        $this->contextService->changeContextsPriority($contextsData , Lesson::class);
    }

    public function recordLessonView($lesson_id, $student_id)
    {
        $lesson = Lesson::findByIdOrFail($lesson_id);
        
        // Check if the student has already watched this lesson
        $existingRecord = $lesson->viewers()->where('student_id', $student_id)->first();
        
        if (!$existingRecord) {
            // Record the lesson view with current timestamp
            $lesson->viewers()->attach($student_id, [
                'watched_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function rateLesson($lesson_id, $rate)
    {
        $lesson = Lesson::findByIdOrFail($lesson_id);

        if(! is_rated($lesson_id)) {
            $lesson->lessonRates()->create([
                    'rate' => $rate,
                    'student_id' => auth()->id()
                ]);

            $lesson->total_rate = $lesson->lessonRates()->avg('rate');

            $lesson->save();
        }
    }

    public function getTeacherLessons($teacher_id, $data)
    {
        return getOrPaginate(
            Lesson::published()
            ->whereHas('unit.responsibilities', function($query) use ($teacher_id) {
                $query->where('teacher_id', $teacher_id);
            })
            ->with(['subUnit', 'publishedFiles', 'publishedQuizzes' , 'existComments.existReplay' , 'lessonRates']),
            $data
        );
    }

    public function downloadLesson($lesson_id , $user_id)
    {
        $lesson = Lesson::findByIdOrFail($lesson_id);

        $lesson->downloads()->create([
            'user_id' => $user_id
        ]);
    }

    public function deleteDownloads($lesson_id)
    {
        Download::where('lesson_id', $lesson_id)
            ->delete();
    }

    public function getDownloads($user_id)
    {
        return Lesson::published()->whereHas('downloads', function($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
        ->get();
    }
}
