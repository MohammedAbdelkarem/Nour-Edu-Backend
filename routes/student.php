<?php

use App\Constants\RouteNames;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\HomeController;
use App\Http\Controllers\Mobile\DownloadController;
use App\Http\Controllers\Mobile\File\FileController;
use App\Http\Controllers\Mobile\HirarichyController;
use App\Http\Controllers\Mobile\Quiz\QuizController;
use App\Http\Controllers\Mobile\AppVersionController;
use App\Http\Controllers\Mobile\HierarichyController;
use App\Http\Controllers\Mobile\Comment\CommentController;
use App\Http\Controllers\Mobile\Progress\ProgressController;
use App\Http\Controllers\Mobile\Saved\SavedContextController;
use App\Http\Controllers\Mobile\Transaction\TransactionController;
use App\Http\Controllers\Mobile\LessonQuestion\LessonQuestionController;

/*
|--------------------------------------------------------------------------
| Doctor API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Doctors API routes for doctors in the system
|
*/

// No Auth Needed
Route::middleware([])->withoutMiddleware('is_student')->group(function () {
    Route::prefix('e-levels')->controller(HierarichyController::class)->group(function () {
        Route::get('/', 'e_levels');
    });
    Route::prefix('c-levels')->controller(HierarichyController::class)->group(function () {
        Route::get('/{e_level_id}', 'c_levels');
    });
    Route::prefix('teachers')->controller(HomeController::class)->group(function () {
        Route::get('/', 'getOnboarding');
    });
});

//Auth Needed
Route::group(['middleware' => ['auth:api', "is_user", 'token.access_api', 'user.active', 'user.verified']], function () {
    Route::prefix('home')->controller(HomeController::class)->group(function () {
        Route::get('/', 'home')->name(RouteNames::STUDENT_HOME);
        Route::get('/others/{id}', 'othersHome')->name(RouteNames::STUDENT_HOME);
        Route::get('/search', 'search')->name(RouteNames::MOBILE_HOME_SEARCH);
    });
    Route::prefix('hierarichy')->controller(HierarichyController::class)->group(function () {
        Route::get('/subject/{subject_id}', 'getSubject')->name(RouteNames::MOBILE_HIERARICHY_SUBJECT);
        Route::get('/unit/{unit_id}', 'getUnit')->name(RouteNames::MOBILE_HIERARICHY_UNIT);
        Route::get('/sub-unit/{sub_unit_id}', 'getSubUnit')->name(RouteNames::MOBILE_HIERARICHY_SUB_UNIT);
        Route::get('/unit-details/{unit_id}', 'getUnitDetails')->name(RouteNames::MOBILE_HIERARICHY_UNIT_DETAILS);
        Route::get('/responsibilities-by-teacher/{teacher_id}/c-level/{c_level_id}', 'getResponsibilitiesByTeacherId')->name(RouteNames::MOBILE_HIERARICHY_RESPONSIBILITIES_BY_TEACHER_ID);
        Route::get('/teacher-details/{teacher_id}', 'getTeacherDetails')->name(RouteNames::MOBILE_HIERARICHY_TEACHER_DETAILS);
        Route::post('/lesson/{lesson_id}/watch', 'recordLessonView');
        Route::get('/lesson/{lesson_id}/rate', 'rateLesson');
        Route::get('/purchased-courses', 'getPurchasedCourses')->name(RouteNames::MOBILE_PURCHASED_COURSES);
        Route::get('/purchased-subjects', 'getPurchasedSubjects')->name(RouteNames::MOBILE_PURCHASED_SUBJECTS);
        Route::get('/purchased-units', 'getPurchasedUnits')->name(RouteNames::MOBILE_PURCHASED_UNITS);
        Route::get('/purchased-lists-by-type', 'getPurchasedListsByType');
        Route::get('/lesson/{lesson_id}', 'getLesson')->name(RouteNames::MOBILE_LESSON_DETAILS);
    });

    // Transactions
    Route::prefix('transactions')->controller(TransactionController::class)->group(function () {
        Route::post('/use-points-cupon', 'usePointsCopon');
        Route::post('/use-context-cupon', 'useContextCupon');
        Route::post('/direct-purchase', 'directPurchase');
        Route::get('/get', 'getStudentTransactions');
    });

    // Files
    Route::prefix('files')->controller(FileController::class)->group(function () {
        Route::get('/search', 'search');
        Route::get('/filter', 'filter');
        Route::get('/purchased', 'getPurchasedFiles');
    });

    // App Versions
    Route::prefix('app-versions')->controller(AppVersionController::class)->group(function () {
        Route::get('/', 'indexStudent');
    });

    // Quizzes
    Route::prefix('quizzes')->controller(QuizController::class)->group(function () {
        Route::get('/search', 'search');
        Route::get('/filter', 'filter');
        Route::get('details/{id}' , 'show')->name(RouteNames::MOBILE_QUIZ_DETAILS);
        Route::get('/purchased', 'getPurchasedQuizzes');
        Route::prefix('solution')->controller(QuizController::class)->group(function () {
            Route::get('prev/{id}' , 'getPrevSolution')->name(RouteNames::MOBILE_QUIZ_PREV_SOLUTION);
            Route::get('start/{id}' , 'startQuiz');
            Route::post('solve' , 'solveQuiz');
        });
    });

    // Saved Contexts
    Route::prefix('saved')->controller(SavedContextController::class)->group(function () {
        Route::get('/lessons', 'getSavedLessons')->name(RouteNames::MOBILE_SAVED_LESSONS);
        Route::get('/questions', 'getSavedQuestions')->name(RouteNames::MOBILE_SAVED_QUESTIONS);
        Route::post('/toggle', 'saveToggle');
        Route::get('/search-questions', 'searchForQuestions');
        Route::get('/search-lessons', 'searchSavedLessons');
    });

    // Comments
    Route::prefix('comments')->controller(CommentController::class)->group(function () {
        Route::get('/{lesson_id}', 'index')->name(RouteNames::MOBILE_COMMENTS_LIST);
        Route::post('/{lesson_id}', 'store');
        Route::delete('/{id}', 'deleteComment');
    });

    // Lesson Questions
    Route::prefix('lesson-questions')->controller(LessonQuestionController::class)->group(function () {
        Route::get('/{lesson_id}/student', 'getForStudent');
        Route::post('/{lesson_id}', 'ask');
        Route::get('/lessons', 'getQuestionableLessons');
    });

    // Progress
    Route::prefix('progress')->controller(ProgressController::class)->group(function () {
        Route::get('/update-study-minutes', 'updateStudyMinutes');
        Route::get('/get', 'getProgress')->name(RouteNames::MOBILE_PROGRESS_GET);
        Route::get('/leaderboard', 'getLeaderboard');
    });

    // Sell Points
    Route::prefix('sell-points')->controller(HierarichyController::class)->group(function () {
        Route::get('/', 'getSellPoints');
    });

    // Downloads
    Route::prefix('downloads')->controller(DownloadController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/download/{lesson_id}', 'download');
    });
});