<?php

namespace App\Services\Purchase;

use App\Models\Unit;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubUnit;
use App\Constants\ExceptionMessages;

/**
 * Class PurchaseService.
 */
class PurchaseService
{
    public function unlockContexts($context_id , $model , $student_id)
    {
        $context = $model::findByIdOrFail($context_id);

        $this->checkIfPurchasedChildsExists($context_id , $model , $student_id);

        $unlockedContext = $context->unlockedContexts()->create([
            'user_id' => $student_id,
        ]);
        $this->incrementPurchasedStudents($context);

        if($model == Course::class)
        {
            $subjects = $context->publishedSubjects()->get();
            foreach($subjects as $subject)
            {
                $subject->unlockedContexts()->create([
                    'user_id' => $student_id,
                ]);

                $units = $subject->publishedUnits()->get();
                foreach($units as $unit)
                {
                    $unit->unlockedContexts()->create([
                        'user_id' => $student_id,
                    ]);

                    $subUnits = $unit->publishedSubUnits()->get();
                    foreach($subUnits as $subUnit)
                    {
                        $subUnit->unlockedContexts()->create([
                            'user_id' => $student_id,
                        ]);

                        $lessons = $subUnit->publishedLessons()->get();
                        foreach($lessons as $lesson)
                        {
                            $lesson->unlockedContexts()->create([
                                'user_id' => $student_id,
                            ]);
                        }
                    }
                }
            }
        }
        elseif($model == Subject::class)
        {
            $units = $context->publishedUnits()->get();
            foreach($units as $unit)
            {
                $unit->unlockedContexts()->create([
                    'user_id' => $student_id,
                ]);

                $subUnits = $unit->publishedSubUnits()->get();
                foreach($subUnits as $subUnit)
                {
                    $subUnit->unlockedContexts()->create([
                        'user_id' => $student_id,
                    ]);

                    $lessons = $subUnit->publishedLessons()->get();
                    foreach($lessons as $lesson)
                    {
                        $lesson->unlockedContexts()->create([
                            'user_id' => $student_id,
                        ]);
                    }
                }
            }
        }
        else 
        {
            $subUnits = $context->publishedSubUnits()->get();
            foreach($subUnits as $subUnit)
            {
                $subUnit->unlockedContexts()->create([
                    'user_id' => $student_id,
                ]);

                $lessons = $subUnit->publishedLessons()->get();
                foreach($lessons as $lesson)
                {
                    $lesson->unlockedContexts()->create([
                        'user_id' => $student_id,
                    ]);
                }
            }
        }

        return $unlockedContext;
    }
    public function unlockOthersWhenAddnig($model , $context_id)
    {
        $purchasedStudents = [];

        $context = $model::findByIdOrFail($context_id);

        if($model == Subject::class)
            $purchasedStudents = $context->course->unlockedContexts()->pluck('user_id')->toArray();
        elseif($model == Unit::class)
            $purchasedStudents = $context->subject->unlockedContexts()->pluck('user_id')->toArray();
        elseif($model == SubUnit::class)
            $purchasedStudents = $context->unit->unlockedContexts()->pluck('user_id')->toArray();
        elseif($model == Lesson::class)
            $purchasedStudents = $context->subUnit->unlockedContexts()->pluck('user_id')->toArray();

        foreach($purchasedStudents as $student_id)
        {
            $context->unlockedContexts()->create([
                'user_id' => $student_id,
                'context_id' => $context_id,
                'context_type' => $model,
            ]);
        }
        
    }

    public function deleteUnlockOthersWhenDeleting($model , $context_id)
    {
        $context = $model::findByIdOrFail($context_id);

        $context->unlockedContexts()->delete();
    }
    private function incrementPurchasedStudents($context)
    {
        $context->increment('number_of_purchased_students');
    }

    private function checkIfPurchasedChildsExists($context_id , $model , $student_id)
    {
        $context = $model::findByIdOrFail($context_id);

        if($model == Course::class)
        {
            $publishedSubjects = $context->publishedSubjects()->get();
            foreach($publishedSubjects as $subject)
            {
                if($subject->unlockedContexts()->where('user_id', $student_id)->exists())
                {
                    return forbiddenFailure([] , ExceptionMessages::MSG_ENTITY_HAS_SUB_ENTITIES_PURCHASED);
                }
            }

            $publishedUnits = $context->publishedUnits()->get();
            foreach($publishedUnits as $unit)
            {
                if($unit->unlockedContexts()->where('user_id', $student_id)->exists())
                {
                    return forbiddenFailure([] , ExceptionMessages::MSG_ENTITY_HAS_SUB_ENTITIES_PURCHASED);
                }
            }
        }
        else if($model == Subject::class)
        {
            $publishedUnits = $context->publishedUnits()->get();
            foreach($publishedUnits as $unit)
            {
                if($unit->unlockedContexts()->where('user_id', $student_id)->exists())
                {
                    return forbiddenFailure([] , ExceptionMessages::MSG_ENTITY_HAS_SUB_ENTITIES_PURCHASED);
                }
            }
        }
    }
}
