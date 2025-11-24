<?php

namespace App\Services\Base;

use App\Models\Quiz;
use App\Models\Unit;
use App\Models\CLevel;
use App\Models\Contry;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\ELevel;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubUnit;
use App\Models\Download;
use App\Models\Transaction;
use App\Constants\ModelPaths;
use App\Enums\AccessTypeEnum;
use App\Models\UnlockedContext;
use App\Enums\PublishStatusEnum;
use App\Constants\ExceptionMessages;

/**
 * Class ContextService.
 */
class ContextService
{
    public function getCoutnryIdByElevelId($e_level_id)
    {
        $eLevel = ELevel::findByIdOrFail($e_level_id);
        return $eLevel->contry_id ?? null;
    }
    public function disableExpiredCopons()
    {
        Coupon::
        whereNotNull('expired_at')
            ->where('expired_at', '<', now())
                ->update(['is_expired' => 1]);
    }

    public function lockTemporarlyContexts()
    {
        $coponIds = Coupon::whereNotNull('context_expired_at')
            ->where('context_expired_at', '<', now())
            ->pluck('id')
            ->toArray();

            // dd($coponIds);
        $unlockedContextIds = Transaction::whereIn('coupon_id', $coponIds)
            ->pluck('unlocked_context_id')
            ->toArray();

        $usersIds = Transaction::whereIn('coupon_id', $coponIds)
            ->pluck('user_id')
            ->toArray();

        foreach($unlockedContextIds as $unlockedContextId)
        {
            $unlockedContext = UnlockedContext::find($unlockedContextId);

            if($unlockedContext->context_type == Course::class)
            {
                $course = Course::find($unlockedContext->context_id);
                $subjectIds = Subject::where('course_id', $course->id)->pluck('id')->toArray();
                $unitIds = Unit::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
                $subUnitIds = SubUnit::whereIn('unit_id', $unitIds)->pluck('id')->toArray();
                $lessonIds = Lesson::whereIn('sub_unit_id', $subUnitIds)->pluck('id')->toArray();

                foreach($usersIds as $userId)
                {
                    UnlockedContext::whereIn('context_id', $subjectIds)
                        ->where('context_type', Subject::class)
                        ->where('user_id', $userId)
                        ->delete();
                    UnlockedContext::whereIn('context_id', $unitIds)
                        ->where('context_type', Unit::class)
                        ->where('user_id', $userId)
                        ->delete();
                    UnlockedContext::whereIn('context_id', $subUnitIds)
                        ->where('context_type', SubUnit::class)
                        ->where('user_id', $userId)
                        ->delete();
                    UnlockedContext::whereIn('context_id', $lessonIds)
                        ->where('context_type', Lesson::class)
                        ->where('user_id', $userId)
                        ->delete();
                }
            }
            else if($unlockedContext->context_type == Subject::class)
            {
                $subject = Subject::find($unlockedContext->context_id);
                $unitIds = Unit::where('subject_id', $subject->id)->pluck('id')->toArray();
                $subUnitIds = SubUnit::whereIn('unit_id', $unitIds)->pluck('id')->toArray();
                $lessonIds = Lesson::whereIn('sub_unit_id', $subUnitIds)->pluck('id')->toArray();

                foreach($usersIds as $userId)
                {
                    UnlockedContext::whereIn('context_id', $unitIds)
                        ->where('context_type', Unit::class)
                        ->where('user_id', $userId)
                        ->delete();
                    UnlockedContext::whereIn('context_id', $subUnitIds)
                        ->where('context_type', SubUnit::class)
                        ->where('user_id', $userId)
                        ->delete();
                    UnlockedContext::whereIn('context_id', $lessonIds)
                        ->where('context_type', Lesson::class)
                        ->where('user_id', $userId)
                        ->delete();
                }
            }
        }

        UnlockedContext::whereIn('id', $unlockedContextIds)
            ->delete();
    }
    /**
     * Change publish status for any content model with publish_status field
     * 
     * @param mixed $model The model instance
     * @return mixed The updated model
     */
    public function changePublishStatus($context , $type , $status)
    { 
        $context->update(['publish_status' => $status]);

        $operation = $status === PublishStatusEnum::PUBLISHED->value ? '+' : '-';
        
        // if($type == 'content')
        //     $this->updateParentNumberOfContents($context, $operation, true);
        // elseif($type == 'file')
        //     $this->updateParentNumberOfFiles($context, $context->context_id , $operation , true);
        // elseif($type == 'quiz')
        //     $this->updateParentNumberOfQuizzes($context, $context->context_id , $operation , true);
    }

    /**
     * Change access type status for any content model with access_type field
     * 
     * @param mixed $context The model instance
     * @return mixed The updated model
     */
    public function changeContentAccessTypeStatus($context, $price)
    {   
        $newAccessType = $context->access_type === AccessTypeEnum::FREE->value 
            ? AccessTypeEnum::PAID->value 
            : AccessTypeEnum::FREE->value;
            
        $context->update(['access_type' => $newAccessType, 'price' => $price]);
    }

    /**
     * Update lesson duration and add it to all parent levels
     *
     * @param \App\Models\Lesson $lesson The lesson to update
     * @param int $duration The duration in minutes to add
     * @return void
     */
    public function updateLessonDurationAndParentLevels($lesson, $duration , $operation)
    {
        $parents = [
            'eLevel',
            'cLevel',
            'course',
            'subject',
            'unit',
            'subUnit',
        ];

        // Add duration to all parent levels
        foreach ($parents as $parent) {
            $this->updateDurationToParentLevel($lesson->$parent, $duration , $operation);
        }
    }

    /**
     * Add duration to a parent level if it exists
     *
     * @param mixed $parentLevel The parent level model (ELevel, CLevel, Course, Subject, Unit, SubUnit)
     * @param int $duration The duration to add
     * @return void
     */
    private function updateDurationToParentLevel($parentLevel, $duration , $operation)
    {
        $operation = ($operation == '+' ? 'increment' : 'decrement');

        if ($parentLevel) {
            $parentLevel->$operation('duration', $duration);
        }
    }

    /**
     * Update parent's number of contents for all hierarchy levels from ELevel down to SubUnit
     * 
     * @param mixed $context The model instance
     * @param string $operation '+' for increment, '-' for decrement
     */
    // public function updateParentNumberOfContents($context, $operation , $published = false)
    // {
    //     $class = get_class($context);
    //     $operation = ($operation == '+' ? 'increment' : 'decrement');

    //     $field = $published ? 'number_of_published_contents' : 'number_of_contents';

    //     switch($class)
    //     {
    //         case ModelPaths::ELevel:
    //             // ELevel is the top level, no parent to update
    //             break;
    //         case ModelPaths::CLevel:
    //             $context->load('eLevel');
    //             $context->eLevel->$operation($field);
    //             break;
    //         case ModelPaths::Course:
    //             $context->load('cLevel');
    //             $context->cLevel->$operation($field);
    //             break;
    //         case ModelPaths::Subject:
    //             $context->load('course');
    //             $context->course->$operation($field);
    //             break;
    //         case ModelPaths::Unit:
    //             $context->load('subject');
    //             $context->subject->$operation($field);
    //             break;
    //         case ModelPaths::SubUnit:
    //             $context->load('unit');
    //             $context->unit->$operation($field);
    //             break;
    //         case ModelPaths::Lesson:
    //             $context->load('subUnit');
    //             $context->subUnit->$operation($field);
    //             break;
    //     }
    // }

    public function changeContextsPriority($contextsData , $model)
    {
        foreach ($contextsData as $contextId => $priority) {
            $context = $model::find($contextId);

            if ($context) {
                $context->update(['priority' => $priority]);
            }

            $context->save();
        }
    }

    // public function updateParentNumberOfFiles($context , $id , $operation , $published = false)
    // {
    //     // Update context number of files
    //     $class = $context->context_type;

    //     $operation = ($operation == '+' ? 'increment' : 'decrement');

    //     $field = $published ? 'number_of_published_files' : 'number_of_files';

    //     switch($class)
    //     {
    //         case ModelPaths::Subject:
    //             $subject = Subject::find($id);
    //             $subject->$operation($field);
    //             break;
    //         case ModelPaths::Unit:
    //             $unit = Unit::find($id);
    //             $unit->$operation($field);
    //             break;
    //         case ModelPaths::SubUnit:
    //             $subUnit = SubUnit::find($id);
    //             $subUnit->$operation($field);
    //             break;
    //         case ModelPaths::Lesson:
    //             $lesson = Lesson::find($id);
    //             $lesson->$operation($field);
    //             break;
    //     }
    // }
    // public function updateParentNumberOfQuizzes($context , $id , $operation , $published = false)
    // {
    //     // Update context number of files
    //     $class = $context->context_type;

    //     $operation = ($operation == '+' ? 'increment' : 'decrement');

    //     $field = $published ? 'number_of_published_quizzes' : 'number_of_quizzes';

    //     switch($class)
    //     {
    //         case ModelPaths::Subject:
    //             $subject = Subject::find($id);
    //             $subject->$operation($field);
    //             break;
    //         case ModelPaths::Unit:
    //             $unit = Unit::find($id);
    //             $unit->$operation($field);
    //             break;
    //         case ModelPaths::SubUnit:
    //             $subUnit = SubUnit::find($id);
    //             $subUnit->$operation($field);
    //             break;
    //         case ModelPaths::Lesson:
    //             $lesson = Lesson::find($id);
    //             $lesson->$operation($field);
    //             break;
    //     }
    // }
    public function getPurchasedCourses($student_id)
    {
        $courses = Course::whereHas('unlockedContexts', function($query) use ($student_id) {
            $query->where('user_id', $student_id);
        })->get();

        return $courses;
    }
    public function getPurchasedSubjects($student_id)
    {
        $subjects = Subject::whereHas('unlockedContexts', function($query) use ($student_id) {
            $query->where('user_id', $student_id);
        })->get();

        return $subjects;
    }
    public function getPurchasedUnits($student_id)
    {
        $units = Unit::whereHas('unlockedContexts', function($query) use ($student_id) {
            $query->where('user_id', $student_id);
        })->get();

        return $units;
    }

    //use it for: clevel , course , subject , unit , subunit , lesson
    public function checkIfParentPublishedBeforePublish($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);
        $published = true;

        if($model == CLevel::class)
            $published = $context->eLevel->publish_status == PublishStatusEnum::PUBLISHED->value;
        else if($model == Course::class)
            $published = $context->cLevel->publish_status == PublishStatusEnum::PUBLISHED->value;
        else if($model == Subject::class)
            $published = $context->course->publish_status == PublishStatusEnum::PUBLISHED->value;
        else if($model == Unit::class)
            $published = $context->subject->publish_status == PublishStatusEnum::PUBLISHED->value;
        else if($model == SubUnit::class)
            $published = $context->unit->publish_status == PublishStatusEnum::PUBLISHED->value;
        else if($model == Lesson::class)
            $published = $context->subUnit->publish_status == PublishStatusEnum::PUBLISHED->value;

        if(!$published)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_PUBLISH_CUZ_PARENT_IS_NOT_PUBLISHED);
    }

    //use it for: elevel , clevel , course , subject , unit , subunit
    public function checkIfContextHasContentBeforePublish($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);
        $hasContent = true;

        if($model == ELevel::class)
            $hasContent = $context->cLevels()->count() > 0;
        else if($model == CLevel::class)
            $hasContent = $context->courses()->count() > 0;
        else if($model == Course::class)
            $hasContent = $context->subjects()->count() > 0;
        else if($model == Subject::class)
            $hasContent = $context->units()->count() > 0;
        else if($model == Unit::class)
            $hasContent = $context->subUnits()->count() > 0;
        else if($model == SubUnit::class)
            $hasContent = $context->lessons()->count() > 0;

        if(!$hasContent)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_PUBLISH_CUZ_HAS_NO_CONTENT);
    }

    //use it for:elevel , clevel , course , subject , unit
    public function checkIfContextHasResponsibilitiesBeforePublish($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);
        
        $hasTeachers = $context->responsibilities()->count() > 0;

        if(!$hasTeachers)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_PUBLISH_CUZ_HAS_NO_TEACHERS);
    }

    //use it for:course , subject , unit , subunit , lesson
    public function checkIfHasPurchasedStudentsBeforeDeleting($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);
        $hasPurchasedStudents = false;

        if($context->unlockedContexts()->count() > 0)
            $hasPurchasedStudents = true;

        if($hasPurchasedStudents)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_DELETE_CUZ_HAS_PURCHASED_STUDENTS);
    }

    //use it for:elevel , clevel
    public function checkIfHasRegisterdStudentsBeforeDeleting($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);
        $hasStudents = $context->students()->count() > 0;

        if($hasStudents)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_DELETE_CUZ_HAS_REGISTERED_STUDENTS);
    }

    //use it for deleting or updating: elevel, clevel , course , subject , unit , subunit , lesson , file , quiz
    public function checkIfDraftBeforeDeletingOrUpdating($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);

        if($context->publish_status == PublishStatusEnum::PUBLISHED->value)
            return forbiddenFailure([] , ExceptionMessages::MSG_HAS_TO_BE_DRAFT_BEFORE_DELETING_OR_UPDATING);
    }

    //use it for elevel, clevel , course , subject , unit , subunit
    public function checkIfHasContentBeforeDeleting($context_id , $model)
    {
        $context = $model::findByIdOrFail($context_id);
        $hasContent = true;

        if($model == Contry::class)
            $hasContent = $context->eLevels()->count() > 0;
        if($model == ELevel::class)
            $hasContent = $context->cLevels()->count() > 0;
        else if($model == CLevel::class)
            $hasContent = $context->courses()->count() > 0;
        else if($model == Course::class)
            $hasContent = $context->subjects()->count() > 0;
        else if($model == Subject::class)
            $hasContent = $context->units()->count() > 0;
        else if($model == Unit::class)
            $hasContent = $context->subUnits()->count() > 0;
        else if($model == SubUnit::class)
            $hasContent = $context->lessons()->count() > 0;

        if($hasContent)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_DELETE_CUZ_HAS_CONTENT);
    }
    //use it for the question update or delete
    public function checkIfQuestionBelongsToQuizBeforeDeletingOrUpdating($question)
    {
        if($question->quizzes()->count() > 0)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_DELETE_OR_UPDATE_CUZ_HAS_QUIZ);
    }

    public function checkIfQuizHasResults($quizId)
    {
        $quiz = Quiz::findByIdOrFail($quizId);
        
        if($quiz->quizResults()->count() > 0)
            return forbiddenFailure([] , ExceptionMessages::MSG_CAN_NOT_DELETE_OR_UPDATE_CUZ_HAS_QUIZ_RESULTS);
    }
    
    public function changeWithChildsPublishStatus($context_id , $model , $status)
    {
        $context = $model::findByIdOrFail($context_id);
        
        $this->changePublishStatus($context, 'content' , $status);

        if($model != Course::class)
        {
            $files = $context->files()->get();
            $quizzes = $context->quizzes()->get();

            $this->changeFilesPublishStatus($files , $status);
            $this->changeQuizzesPublishStatus($quizzes , $status);
        }
        
        if($model == Course::class)
        {
            $subjects = $context->subjects()->get();
            $this->changeSubjectsPublishStatus($subjects , $status);
        }
        else if($model == Subject::class)
        {
            $units = $context->units()->get();
            $this->changeUnitsPublishStatus($units , $status);
        }
        else if($model == Unit::class)
        {
            $subUnits = $context->subUnits()->get();
            $this->changeSubUnitsPublishStatus($subUnits , $status);
        }
        else if($model == SubUnit::class)
        {
            $lessons = $context->lessons()->get();
            $this->changeLessonsPublishStatus($lessons , $status);
        }
    }

    public function clearDownloads($student_id)
    {
        Download::where('user_id', $student_id)
            ->delete();
    }

    private function changeLessonsPublishStatus($lessons , $status)
    {
        foreach($lessons as $lesson)
        {
            $this->changePublishStatus($lesson, 'content' , $status);

            $files = $lesson->files()->get();
            $quizzes = $lesson->quizzes()->get();

            $this->changeFilesPublishStatus($files , $status);
            $this->changeQuizzesPublishStatus($quizzes , $status);
        }
    }

    private function changeSubUnitsPublishStatus($subUnits , $status)
    {
        foreach($subUnits as $subUnit)
        {
            $this->changePublishStatus($subUnit, 'content' , $status);

            $files = $subUnit->files()->get();
            $quizzes = $subUnit->quizzes()->get();

            $this->changeFilesPublishStatus($files , $status);
            $this->changeQuizzesPublishStatus($quizzes , $status);

            $lessons = $subUnit->lessons()->get();

            $this->changeLessonsPublishStatus($lessons , $status);
        }
    }

    private function changeUnitsPublishStatus($units , $status)
    {
        foreach($units as $unit)
        {
            $this->changePublishStatus($unit, 'content' , $status);

            $files = $unit->files()->get();
            $quizzes = $unit->quizzes()->get();

            $this->changeFilesPublishStatus($files , $status);
            $this->changeQuizzesPublishStatus($quizzes , $status);

            $subUnits = $unit->subUnits()->get();

            $this->changeSubUnitsPublishStatus($subUnits , $status);
        }
    }

    private function changeSubjectsPublishStatus($subjects , $status)
    {
        foreach($subjects as $subject)
        {
            $this->changePublishStatus($subject, 'content' , $status);

            $files = $subject->files()->get();
            $quizzes = $subject->quizzes()->get();

            $this->changeFilesPublishStatus($files , $status);
            $this->changeQuizzesPublishStatus($quizzes , $status);

            $units = $subject->units()->get();

            $this->changeUnitsPublishStatus($units , $status);
        }
    }

    private function changeQuizzesPublishStatus($quizzes , $status)
    {
        foreach($quizzes as $quiz)
            $this->changePublishStatus($quiz, 'quiz' , $status);
    }

    private function changeFilesPublishStatus($files , $status)
    {
        foreach($files as $file)
            $this->changePublishStatus($file, 'file' , $status);
    }
}
