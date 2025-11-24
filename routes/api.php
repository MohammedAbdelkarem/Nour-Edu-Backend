<?php

use Illuminate\Http\Request;
use App\Constants\RouteNames;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Users\UserHomeController;
use App\Http\Controllers\System\Info\FAQController;
use App\Http\Controllers\System\Info\TosController;
use App\Http\Controllers\Users\Auth\AuthController;
use App\Http\Controllers\System\Info\CityController;
use App\Http\Controllers\System\Info\AboutUsController;
use App\Http\Controllers\System\Info\ContactUsController;
use App\Http\Controllers\Users\Profile\ProfileController;
use App\Http\Controllers\System\Info\PrivacyPolicyController;
use App\Http\Controllers\Users\Profile\NumberUpdateController;
use App\Http\Controllers\System\Notification\NotificationController;
use App\Http\Controllers\System\CustomerServiceCard\CustomerServiceCardController;
use App\Http\Controllers\Administration\CLevel\CLevelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Auth Needed
Route::group(['middleware' => ['is_user', 'auth:api', 'token.access_api', 'user.active', 'user.complete', 'user.verified']], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get("/active-session", "activeSessions");
        Route::post("/logout-session", "logoutSessions");
        Route::post("/logout", "logout");
        Route::get("/logout-all", "logoutAll");
        Route::get("/refresh", "refresh")->withoutMiddleware('token.access_api')->withoutMiddleware('token.access_refresh');
    });

    //OTP
    Route::prefix('otp')->withoutMiddleware(['token.access_api', 'user.active', 'user.complete', 'user.verified'])
        ->middleware(['token.access_otp'])->controller(OTPController::class)
        ->group(function () {
            Route::post("/verify", "userVerify")->middleware('bots');
            Route::get("/send", "sendOTP");
        });

    //Home
    Route::controller(UserHomeController::class)->group(function () {
        Route::get("/overview", "overview");
    });

    //Profile
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::controller(ProfileController::class)->group(function () {
            Route::post("/complete", "completeProfile")->withoutMiddleware(['user.complete']);
            Route::put("/update-image", "updateProfileImage");
            Route::put("/update", "update");
            // Route::get("/profile", "show"); TODO:TEMPLATE If app allow only to see my profile
            //Profile Settings
            Route::get("/login-history", "loginHistory")->name(RouteNames::LOGIN_HISTORY_List);

            Route::post("/lang", "changeLang");
            Route::get("/notifications-status", "changeNotificationState");
            //Profile Deactive and Delete
            Route::get("/deactivate", "deactivateAccount");
            Route::post("/delete", "deleteProfile");
        });
        Route::controller(NumberUpdateController::class)->group(function () {
            Route::post("/update-number", "updateNumber");
            Route::post("/verify-number", "verifyNumber");
        });
    });

    //Customer Card
    Route::prefix("customer-cards")->controller(CustomerServiceCardController::class)->group(function () {
        Route::get("/", "indexUser")->name(RouteNames::CUSTOMER_CARD_LIST);
        Route::get("/types-status", "getTypesStatus");
        Route::get("/{id}", "showUser");
        Route::post("/", "store");
        // Route::put("/{id}", "update");
        Route::delete("/{id}", "destroy");
    });


});

//No Auth Needed
Route::group([], function () {
    //Auth
    Route::controller(AuthController::class)->group(function () {
        Route::post("/register/student", "registerStudent")->middleware('bots')->name('registerStudent');
        Route::post("/login/student", "loginStudent")->middleware('bots')->name('loginStudent');
        Route::post("/login/parent", "loginParent")->middleware('bots')->name('loginParent');
        Route::post("/login/teacher", "loginTeacher")->middleware('bots')->name('loginTeacher');
    });

    //Profile
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get("/{id}", "show");
    });


    //Notifications
    Route::prefix("notifications")->controller(NotificationController::class)->group(function () {
        Route::get("/", "getMyNotifications")->name(RouteNames::MY_NOTIFICATIONS_LIST);
        Route::get("/{id}", "show");
    });

    //System Info
    Route::prefix("about-us")->controller(AboutUsController::class)->group(function () {
        Route::get("/", "show");
    });
    Route::prefix("faq")->controller(FAQController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::FAQ_LIST);
        Route::get("/{id}", "show");
    });
    Route::prefix("tos")->controller(TosController::class)->group(function () {
        Route::get("/", "show");
    });
    Route::prefix("privacy-policy")->controller(PrivacyPolicyController::class)->group(function () {
        Route::get("/", "show");
    });
    Route::prefix("contact-us")->controller(ContactUsController::class)->group(function () {
        Route::get("/", "index");
        Route::get("/{id}", "show");
    });
    Route::prefix("cities")->controller(CityController::class)->group(function () {
        Route::get("/", "index")->name(RouteNames::CITIES_SELECTABLE_LIST);
        Route::get("/{id}", "show");
    });

    //Home
    Route::controller(UserHomeController::class)->group(function () {
        Route::get("/home", "home");
    });

    //CLevels - Public API
    Route::prefix("c-levels")->controller(\App\Http\Controllers\Administration\CLevel\CLevelController::class)->group(function () {
        Route::get("/{eLevelId?}", "index");
    });
});
