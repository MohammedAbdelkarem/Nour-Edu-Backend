<?php

use App\Constants\RouteNames;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Media\MediaController;
use App\Http\Controllers\Media\StoryController;
use App\Http\Controllers\Media\BannerController;
use App\Http\Controllers\System\Info\FAQController;
use App\Http\Controllers\System\Info\TosController;
use App\Http\Controllers\System\Info\CityController;
use App\Http\Controllers\System\Info\AboutUsController;
use App\Http\Controllers\System\SystemSettingController;
use App\Http\Controllers\System\Info\ContactUsController;
use App\Http\Controllers\System\Info\FaqCategoryController;
use App\Http\Controllers\Administration\AdminHomeController;
use App\Http\Controllers\Administration\Auth\AuthController;
use App\Http\Controllers\Administration\File\FileController;
use App\Http\Controllers\Administration\Quiz\QuizController;
use App\Http\Controllers\Administration\Unit\UnitController;
use App\Http\Controllers\Administration\AppVersionController;
use App\Http\Controllers\Administration\Log\BanLogController;
use App\Http\Controllers\System\Info\PrivacyPolicyController;
use App\Http\Controllers\Administration\CLevel\CLevelController;
use App\Http\Controllers\Administration\Course\CourseController;
use App\Http\Controllers\Administration\ELevel\ELevelController;
use App\Http\Controllers\Administration\Lesson\LessonController;
use App\Http\Controllers\Administration\Subject\SubjectController;
use App\Http\Controllers\Administration\SubUnit\SubUnitController;
use App\Http\Controllers\Administration\Teacher\TeacherController;
use App\Http\Controllers\Administration\Question\QuestionController;
use App\Http\Controllers\System\Notification\NotificationController;
use App\Http\Controllers\Administration\Profile\UserProfileController;
use App\Http\Controllers\Administration\SellPoint\SellPointController;
use App\Http\Controllers\Administration\Profile\AdminProfileController;
use App\Http\Controllers\Administration\Transaction\TransactionController;
use App\Http\Controllers\Administration\Responsibility\ResponsibilityController;
use App\Http\Controllers\System\CustomerServiceCard\CustomerServiceCardController;



/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admins API routes for admins in the system
|
*/

// No Auth Needed
Route::middleware([])->group(function () {
    Route::controller(AuthController::class)->middleware('bots')->group(function () {
        Route::post("/login", "login")->name('login');
    });
});

//Auth Needed
Route::group(['middleware' => ['auth:api', "is_admin", 'token.access_api', 'user.active', 'user.verified']], function () {

    // Auth
    Route::controller(AuthController::class)->group(function () {
        Route::get("/active-session", "activeSessions");
        Route::post("/logout-session", "logoutSessions");
        Route::post("/logout", "logout");
        Route::get("/logout-all", "logoutAll");
        Route::get("/refresh", "refresh")->withoutMiddleware('token.access_api')->withoutMiddleware('token.access_refresh');
    });

    // //Home
    Route::controller(AdminHomeController::class)->group(function () {
        Route::get("/home", "home");
        Route::get("/overview", "overview");
    });

    //Profiles
    //Admins
    Route::prefix('profile')->controller(AdminProfileController::class)->group(function () {
        //Profile Settings
        Route::get("/login-history/{id?}", "loginHistory")->name(RouteNames::LOGIN_HISTORY_List);
        Route::post("/lang", "changeLang");
        Route::get("/notifications-status", "changeNotificationState");
        Route::get("/sugs", "adminSugs");
        Route::get("/list", "index")->name(RouteNames::ADMINS_LIST);
        Route::get("/{id}", "show");
        //Only super admin can access this routes
        Route::middleware(['is_super_admin'])->group(function () { //TODO:NEED CHECK FOR DYNAMIC AND POLICIES
            Route::post("/", "store");
            Route::put("/{id}", "update");
            Route::put("/update-image/{id}", "updateProfileImage");
            Route::get("/deactivate/{id}", "deactivateAccount");
        });
    });

    //Users
    Route::prefix("users")->group(function () {
        Route::controller(UserProfileController::class)->group(function () {
            Route::get("/sugs", "userSugs");
            Route::get("/list", "index")->name(RouteNames::USERS_LIST);
            Route::get("/profile/{id}", "show");
            Route::post("/restore", "restore");
            Route::get("/student-profile/{id}", "getStudentProfile")->name(RouteNames::ADMIN_STUDENT_PROFILE);
            Route::get("/student-progress/{id}", "getStudentProgress");
        });

        Route::prefix("ban")->controller(BanLogController::class)->group(function () {
            Route::post("/", "ban");
            Route::post("/remove", "unBan");
        });
    });

    //System Info
    Route::prefix("system")->group(function () {
        Route::prefix("about-us")->controller(AboutUsController::class)->group(function () {
            Route::get("/", "index");
            Route::post("/", "store")->withoutMiddleware("xss");
            Route::get("/{lang}", "show");
        });
        Route::prefix("tos")->controller(TosController::class)->group(function () {
            Route::get("/", "index");
            Route::post("/", "store")->withoutMiddleware("xss");
            Route::get("/{lang}", "show");
        });
        Route::prefix("privacy-policy")->controller(PrivacyPolicyController::class)->group(function () {
            Route::get("/", "index");
            Route::post("/", "store")->withoutMiddleware("xss");
            Route::get("/{lang}", "show");
        });
        Route::prefix("faq-category")->controller(FaqCategoryController::class)->group(function () {
            Route::get("/", "index")->name(RouteNames::ADMIN_FAQ_CATEGORY_LIST);
            Route::get("/apps", "apps");
            Route::post("/", "store");
            Route::get("/{id}", "show");
            Route::put("/{id}", "update");
            Route::delete("/{id}", "destroy");
        });
        Route::prefix("faq")->controller(FAQController::class)->group(function () {
            Route::get("/", "indexAdmin")->name(RouteNames::ADMIN_FAQ_LIST);
            Route::post("/", "store");
            Route::get("/{id}", "show");
            Route::put("/{id}", "update");
            Route::delete("/{id}", "destroy");
        });
        Route::prefix("contact-us")->controller(ContactUsController::class)->group(function () {
            Route::get("/", "index");
            Route::get("/types", "types");
            Route::post("/", "store");
            Route::put("/{id}", "update");
            Route::get("/{id}", "show");
            Route::delete("/{id}", "destroy");
        });
        Route::prefix("cities")->controller(CityController::class)->group(function () {
            Route::get("/", "index")->name(RouteNames::ADMIN_CITIES_SELECTABLE_LIST);
            Route::get("/{id}", "show");
        });
        Route::apiResource('/settings', SystemSettingController::class);
    });

    //e-learning
    Route::prefix("e-levels")->controller(ELevelController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_E_LEVEL_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
    });

    Route::prefix("c-levels")->controller(CLevelController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_C_LEVEL_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
    });

    Route::prefix("courses")->controller(CourseController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_COURSE_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::patch("/{id}/change-access-type-status", "changeAccessTypeStatus");
    });

    Route::prefix("subjects")->controller(SubjectController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_SUBJECT_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::patch("/{id}/change-access-type-status", "changeAccessTypeStatus");
    });

    Route::prefix("units")->controller(UnitController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_UNIT_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::patch("/{id}/change-access-type-status", "changeAccessTypeStatus");
        Route::post("/change-priority", "changePriority");
    });

    Route::prefix("sub-units")->controller(SubUnitController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_SUB_UNIT_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::post("/change-priority", "changePriority");
    });

    Route::prefix("lessons")->controller(LessonController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_LESSON_LIST);
        Route::post("/", "store");
        Route::get("/{id}", "show")->name(RouteNames::ADMIN_LESSON_SHOW);
        Route::post("/{id}/upload-videos", "uploadVideos");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::post("/change-priority", "changePriority");
        Route::delete("/comments/{id}", "deleteComment");
    });

    Route::prefix("files")->controller(FileController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_FILE_LIST);
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::post("/change-priority", "changePriority");
    });

    // Questions
    Route::prefix("questions")->controller(QuestionController::class)->group(function () {
        Route::get("/", "index");
        Route::post("/", "store");
        Route::get("/{id}", "show");
        Route::post("/{id}/update", "update");
        Route::delete("/{id}", "destroy");
    });

    // Quizzes
    Route::prefix("quizzes")->controller(QuizController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_QUIZ_LIST);
        Route::post("/", "store");
        Route::get("/{id}", "show")->name(RouteNames::ADMIN_QUIZ_SHOW);
        Route::post("/{id}/update", "update");
        Route::delete("/{id}", "destroy");
        Route::patch("/{id}/change-publish-status", "changePublishStatus");
        Route::post("/change-priority", "changePriority");
        Route::get("/detach-questions/{id}", "detachQuestionsFromQuiz");
        Route::get("/attach-questions/{id}", "attachQuestionsToQuiz");
    });

    // Sell Points
    Route::prefix("sell-points")->controller(SellPointController::class)->group(function () {
        Route::get("/", "index");
        Route::post("/", "store");
        Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
    });

    Route::prefix("responsibilities")->controller(ResponsibilityController::class)->group(function () {
        Route::post("/attach", "attach");
        Route::post("/detach", "detach");
        Route::get('/teacher/{id}' , 'getResponsibilitiesByTeacherId');
    });

    // Transactions
    Route::prefix("transactions")->controller(TransactionController::class)->group(function () {
        Route::post("/create-one-points-copon", "createOnePointsCopon");
        Route::post("/create-many-points-copon", "createManyPointsCopon");
        Route::post("/create-one-context-copon", "createOneContextCopon");
        Route::post("/create-many-context-copon", "createManyContextCopon");
        Route::get('/', 'get');
        Route::get("/set-copons-as-expired", "setCoponsAsExpired");
        Route::get("/copons", "getCopons");
    });

    // Teachers
    Route::prefix("teachers")->controller(TeacherController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::ADMIN_TEACHER_LIST);
        Route::post("/", "store");
        Route::get("/{id}", "show");
        Route::put("/{id}", "update");
    });

    // App Versions
    Route::prefix("app-versions")->controller(AppVersionController::class)->group(function () {
        Route::get("/", "index");
        Route::post("/", "store");
        Route::delete("/{id}", "destroy");
    });

    //Logs
    Route::prefix("logs")->group(function () {
        Route::prefix("bans-log")->controller(BanLogController::class)->group(function () {
            Route::get("/", "index")->name(RouteNames::BANLOG_LIST);
            Route::get("/{id}", "show");
        });
    });

    //Notifications
    Route::prefix("notifications")->controller(NotificationController::class)->group(function () {
        Route::get("/list", "index")->name(RouteNames::NOTIFICATIONS_LIST);
        Route::get("/", "getMyNotifications")->name(RouteNames::MY_NOTIFICATIONS_LIST);
        Route::get("/pre-store", "preStore");
        Route::post("/", "storePublic");
        Route::post("/private", "storePrivate");
        Route::get("/{id}", "showAdmin");
        Route::delete("/{id}", "destroy");
    });

    //customer service 
    Route::prefix("customer-cards")->controller(CustomerServiceCardController::class)->group(function () {
        Route::get("/", "indexAdmin")->name(RouteNames::ADMIN_CUSTOMER_CARD_LIST);
        Route::get("/types-status", "getTypesStatus");
        Route::put("/{id}", "update");
        Route::get("/{id}", "showAdmin");
        Route::post("/close", "close");
        Route::delete("/{id}", "destroyByAdmin");
    });

    //Banner & Reels
    // Route::prefix("banners")->controller(BannerController::class)->group(function () {
    //     Route::get("/", "index");
    //     Route::post("/", "store");
    //     Route::put("/{id}", "update");
    //     Route::delete("/{id}", "destroy");
    // });
    // Route::prefix("reels")->controller(ReelController::class)->group(function () {
    //     Route::get("/", "index");
    //     Route::get("/{id}", "show");
    //     Route::post("/", "store");
    //     Route::put("/{id}", "update");
    //     Route::delete("/{id}", "destroy");
    // });
    Route::prefix('story')->controller(StoryController::class)->group(function(){
        Route::get('changeStatus/{id}' , 'changeStatus');
    });
    Route::prefix('banner')->controller(BannerController::class)->group(function(){
        Route::get('changeStatus/{id}' , 'changeStatus');
    });
    Route::prefix('media')->controller(MediaController::class)->group(function(){
        Route::delete('delete' , 'delete');
    });
    
    Route::apiResource('/story', StoryController::class)
        ->name('show' , RouteNames::ADMIN_STORY_GET)
        ->name('index' , RouteNames::ADMIN_STORY_GET);
    Route::apiResource('/banner', BannerController::class)->name('show' , RouteNames::ADMIN_BANNER_GET);
    Route::apiResource('/media', MediaController::class);
    
});