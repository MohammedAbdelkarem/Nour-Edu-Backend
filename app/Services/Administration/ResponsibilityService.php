<?php

namespace App\Services\Administration;

use App\Models\Unit;
use App\Models\User;
use App\Models\CLevel;
use App\Models\Course;
use App\Models\ELevel;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\SubUnit;
use App\Enums\LevelEnum;
use App\Services\MainService;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;

/**
 * Class ResponsibilityService.
 */
class ResponsibilityService extends MainService
{
    public function linking($data, $operation)
    {
        $context_id = $data['context_id'];
        $context_type = $data['context_type'];
        $teacher_ids = $data['teacher_ids'];

        foreach ($teacher_ids as $teacher_id) 
        {
            $teacher = User::findByIdOrFail($teacher_id);
            $model = getModel($context_type);
            $context = $model::findByIdOrFail($context_id);

            if ($operation == 'create')
                $this->buildResponsibilityData($teacher, $context, $context_type , $operation);
            else
                $this->buildResponsibilityData($teacher, $context, $context_type , $operation);

            $this->updateTeacherMainLevels($teacher);
        }
    }

    private function buildResponsibilityData($teacher, $context, $context_type, $operation)
    {
        if ($operation == 'create') {
            $this->createHierarchicalResponsibilities($teacher, $context, $context_type);
        } else {
            $this->deleteHierarchicalResponsibilities($teacher, $context, $context_type);
        }
    }

    private function createHierarchicalResponsibilities($teacher, $context, $context_type)
    {
        $levels = $this->getLevelsForContext($context_type);
        $accumulatedData = [];
        
        foreach ($levels as $level) {
            $field = $level['field'];
            $value = $context->{$field};
            
            if ($value) {
                // Add current field to accumulated data
                $accumulatedData[$field] = $value;
                
                // Check if this combination already exists
                if (!$this->responsibilityExists($teacher, $accumulatedData)) {
                    $teacher->responsibilities()->create($accumulatedData);
                }
            }
        }
        
        // Ensure the final context record is created
        $finalField = $this->getContextField($context_type);
        if ($finalField && $context->id) {
            $finalData = $accumulatedData;
            $finalData[$finalField] = $context->id;
            
            if (!$this->responsibilityExists($teacher, $finalData)) {
                $teacher->responsibilities()->create($finalData);
            }
        }
    }
    private function deleteHierarchicalResponsibilities($teacher, $context, $context_type)
    {
        $keyByContext = [
            LevelEnum::E_LEVEL => 'e_level_id',
            LevelEnum::C_LEVEL => 'c_level_id',
            LevelEnum::COURSE => 'course_id',
            LevelEnum::SUBJECT => 'subject_id',
            LevelEnum::UNIT => 'unit_id',
            LevelEnum::SUB_UNIT => 'sub_unit_id',
            LevelEnum::LESSON => 'lesson_id',
        ];
        $teacher->responsibilities()->where(
            $keyByContext[$context_type] , $context->id
        )->delete();
    }

    private function getLevelsForContext($context_type)
    {
        switch ($context_type) {
            case LevelEnum::E_LEVEL:
                return [
                    ['field' => 'e_level_id']
                ];
                
            case LevelEnum::C_LEVEL:
                return [
                    ['field' => 'e_level_id'],
                    ['field' => 'c_level_id']
                ];
                
            case LevelEnum::COURSE:
                return [
                    ['field' => 'e_level_id'],
                    ['field' => 'c_level_id'],
                    ['field' => 'course_id']
                ];
                
            case LevelEnum::SUBJECT:
                return [
                    ['field' => 'e_level_id'],
                    ['field' => 'c_level_id'],
                    ['field' => 'course_id'],
                    ['field' => 'subject_id']
                ];
                
            case LevelEnum::UNIT:
                return [
                    ['field' => 'e_level_id'],
                    ['field' => 'c_level_id'],
                    ['field' => 'course_id'],
                    ['field' => 'subject_id'],
                    ['field' => 'unit_id']
                ];
                
            case LevelEnum::SUB_UNIT:
                return [
                    ['field' => 'e_level_id'],
                    ['field' => 'c_level_id'],
                    ['field' => 'course_id'],
                    ['field' => 'subject_id'],
                    ['field' => 'unit_id'],
                    ['field' => 'sub_unit_id']
                ];
                
            case LevelEnum::LESSON:
                return [
                    ['field' => 'e_level_id'],
                    ['field' => 'c_level_id'],
                    ['field' => 'course_id'],
                    ['field' => 'subject_id'],
                    ['field' => 'unit_id'],
                    ['field' => 'sub_unit_id'],
                    ['field' => 'lesson_id']
                ];
                
            default:
                return [];
        }
    }

    private function responsibilityExists($teacher, $data)
    {
        return $teacher->responsibilities()
            ->where($data)
            ->exists();
    }

    private function getContextField($context_type)
    {
        $fieldMap = [
            LevelEnum::E_LEVEL => 'e_level_id',
            LevelEnum::C_LEVEL => 'c_level_id',
            LevelEnum::COURSE => 'course_id',
            LevelEnum::SUBJECT => 'subject_id',
            LevelEnum::UNIT => 'unit_id',
            LevelEnum::SUB_UNIT => 'sub_unit_id',
            LevelEnum::LESSON => 'lesson_id',
        ];

        return $fieldMap[$context_type] ?? null;
    }

    private function relationByContext($context_type)
    {
        $relationByContext = [
            LevelEnum::E_LEVEL => 'eLevel',
            LevelEnum::C_LEVEL => 'cLevel',
            LevelEnum::COURSE => 'course',
            LevelEnum::SUBJECT => 'subject',
            LevelEnum::UNIT => 'unit',
            LevelEnum::SUB_UNIT => 'subUnit',
            LevelEnum::LESSON => 'lesson',
        ];

        return $relationByContext[$context_type] ?? null;
    }

    private function relationPublishedByContext($context_type)
    {
        $relationPublishedByContext = [
            LevelEnum::E_LEVEL => 'publishedELevel',
            LevelEnum::C_LEVEL => 'publishedCLevel',
            LevelEnum::COURSE => 'publishedCourse',
            LevelEnum::SUBJECT => 'publishedSubject',
            LevelEnum::UNIT => 'publishedUnit',
            LevelEnum::SUB_UNIT => 'publishedSubUnit',
            LevelEnum::LESSON => 'publishedLesson',
        ];

        return $relationPublishedByContext[$context_type] ?? null;
    }

    public function getResponsibilitiesByTeacherId($data, $teacher_id , $published = false , $clevel_id = null)
    {
        // dd($clevel_id);
        $column = $this->getContextField($data['context_type']);

        $relationByContext = $published 
                ? $this->relationPublishedByContext($data['context_type']) 
                : $this->relationByContext($data['context_type']);

        
        // First get all responsibilities for the teacher
        $query = Responsibility::where('teacher_id', $teacher_id)
            ->when($clevel_id != null, function($query) use ($clevel_id) {
                $query->where('c_level_id', $clevel_id);
            })
            ->whereNotNull($column)
            ->with($relationByContext);
        
        // Get the results and then filter for uniqueness at collection level
        $results = $query->get();
        
        // Filter to keep only unique records based on the context column
        $uniqueResults = $results->unique($column);
        
        // Convert back to query builder for pagination
        $uniqueIds = $uniqueResults->pluck('id')->toArray();
        
        return getOrPaginate(
            Responsibility::whereIn('id', $uniqueIds)
            ->with($relationByContext),
            $data
        );
    }

    private function updateTeacherMainLevels($teacher)
    {
        $res1 = $teacher->responsibilities()->whereNotNull('e_level_id')->first();
        $res2 = $teacher->responsibilities()->whereNotNull('c_level_id')->first();

        if($res1)
            $teacher->update([
                'e_level_id' => $res1->e_level_id,
        ]);
        if($res2)
            $teacher->update([
                'c_level_id' => $res2->c_level_id,
        ]);
    }

}
