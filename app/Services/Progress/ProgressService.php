<?php

namespace App\Services\Progress;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\QuizResult;
use App\Constants\MediaCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Media\MediaResource;

/**
 * Class ProgressService.
 */
class ProgressService
{
    public function updateStudyMinutes($minutes)
    {
        $user = User::findByIdOrFail(auth()->id());
        $user->study_minutes += $minutes;
        $user->save();
    }
    private function numberOfWatchedLessonsInSubject($student_id, $subject_id)
    {
        return Lesson::published()
            ->where('subject_id', $subject_id)
            ->whereHas('viewers', function ($query) use ($student_id) {
                $query->where('student_id', $student_id);
            })
            ->count();
    }
    private function numberOfAllLessonsInSubject($student_id, $subject_id)
    {
        $count =  Lesson::published()
            ->where('subject_id', $subject_id)
            ->count();

        return $count > 0 ? $count : 1;
    }
    private function numberOfAllWatchedLessons($student_id)
    {
        return Lesson::published()
            ->whereHas('unlockedContexts', function ($query) use ($student_id) {
                $query->where('user_id', $student_id);
            })
            ->whereHas('viewers', function ($query) use ($student_id) {
                $query->where('student_id', $student_id);
            })
            ->count();
    }
    private function numberOfAllLessons($student_id)
    {
        $count = Lesson::published()
            ->whereHas('unlockedContexts', function ($query) use ($student_id) {
                $query->where('user_id', $student_id);
            })
            ->count();

        return $count > 0 ? $count : 1;
    }

    public function adminProgress($studentId)
    {
        $profile = User::findByIdOrFail($studentId , ['c_level' , 'e_level']);

        $progress = $this->numberOfAllWatchedLessons($studentId) / $this->numberOfAllLessons($studentId) * 100;

        $studyHours = $profile->study_minutes / 60;

        $quizzesResult = $this->getQuizzesResult($studentId);

        $unlockedSubjects = Subject::whereHas('unlockedContexts', function ($query) use ($studentId) {
            $query->where('user_id', $studentId);
        })->get();

        $subjectProgress = [];

        foreach ($unlockedSubjects as $subject) {
            $subjectProgress[$subject->name] = $this->numberOfWatchedLessonsInSubject($studentId, $subject->id) / $this->numberOfAllLessonsInSubject($studentId, $subject->id) * 100;
        }

        return [
            'profile' => UserResource::make($profile),
            'progress' => $progress,
            'studyHours' => $studyHours,
            'quizzesResult' => $quizzesResult,
            'subjectProgress' => $subjectProgress,
        ];
    }
    public function progress($studentId)
    {
        $profile = User::findByIdOrFail($studentId , ['c_level' , 'e_level']);

        $progress = $this->getProgress($studentId);

        $studyHours = $this->getStudyHours($studentId);

        $quizzesResult = $this->getQuizzesResult($studentId);

        $unlockedSubjects = Subject::whereHas('unlockedContexts', function ($query) use ($studentId) {
            $query->where('user_id', $studentId);
        })->with('course')->get();

        $subjectProgress = [];

        foreach ($unlockedSubjects as $subject) {
            $media = $subject->getFirstMedia(MediaCollection::SUBJECT_COLLECTION);
            $media = $media ? MediaResource::make($media) : null;
            $subjectProgress[$subject->name] = [
                $this->numberOfWatchedLessonsInSubject($studentId, $subject->id)
                 / $this->numberOfAllLessonsInSubject($studentId, $subject->id) 
                 * 100,
                 $media,
                 $subject->course->name,
            ];
        }

        return [
            'profile' => UserResource::make($profile),
            'progress' => $progress,
            'studyHours' => $studyHours,
            'quizzesResult' => $quizzesResult,
            'subjectProgress' => $subjectProgress,
        ];
    }

    public function totalScore($studentId)
    {
        $quizzesResult = $this->getQuizzesResult($studentId);

        $progress = $this->getProgress($studentId);

        return 0.2 * $quizzesResult + 0.8 * $progress;
    }

    public function leaderboard()
    {
        $students = User::where('role_id' , 5)->get();

        $sortedStudents = [];
        $counter = 0;
        foreach ($students as $student) {
            $sortedStudents[] = [
                'student' => UserResource::make($student),
                'total_score' => $this->totalScore($student->id)
            ];

            $counter++;
            if($counter == 10) break;
        }

        // Sort by total_score in descending order
        usort($sortedStudents, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });

        return $sortedStudents;
    }

    private function getStudyHours($studentId)
    {
        $student = User::findByIdOrFail($studentId);

        return $student->study_minutes / 60;
    }
    private function getQuizzesResult($student_id)
    {
        $quizResults = QuizResult::where('student_id', $student_id)->get();

        $quizzes = Quiz::whereIn('id', $quizResults->pluck('quiz_id')->toArray())->get();

        $resultsSum = $quizResults->sum('degree');

        $quizzesSum = $quizzes->sum('degree');

        $quizzesSum = $quizzesSum > 0 ? $quizzesSum : 1;

        return $resultsSum / $quizzesSum * 100;
    }
    private function getProgress($studentId)
    {
        return $this->numberOfAllWatchedLessons($studentId) 
        / $this->numberOfAllLessons($studentId) 
        * 100;
    }
}
