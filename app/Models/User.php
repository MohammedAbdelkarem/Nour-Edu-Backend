<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use App\Models\CLevel;
use App\Models\ELevel;
use App\Models\Replay;
use App\Models\Comment;
use App\Enums\GenderEnum;
use App\Models\QuizResult;
use App\Constants\Resources;
use App\Models\SavedContext;
use App\Models\LessonQuestion;
use App\Models\Responsibility;
use App\Models\System\Info\FAQ;
use App\Models\System\Info\Tos;
use App\Models\System\Info\City;
use App\Models\System\Role\Role;
use Spatie\MediaLibrary\HasMedia;
use App\Constants\MediaCollection;
use App\Models\System\Info\AboutUs;
use App\Models\System\SystemSetting;
use Illuminate\Support\Facades\Auth;
use App\Models\System\Info\ContactUs;
use App\Models\System\Info\FaqCategory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Users\Profile\UserDevice;
use Illuminate\Notifications\Notifiable;
use App\Models\Administration\Log\BanLog;
use App\Models\System\Info\PrivacyPolicy;
use App\Models\Users\Profile\UserProfile;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Users\Profile\ArchivedUser;
use App\Models\Users\Profile\LoginHistory;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Scopes\LoadStudentLevelsScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\System\Notification\Notification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Administration\Profile\AdminProfile;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\System\CustomerService\CustomerServiceCard;

class User extends Authenticatable implements JWTSubject , HasMedia
{
    use HasFactory, Notifiable, SoftDeletes , InteractsWithMedia;

    protected $with = ["role"];

    protected $guarded = [
        'id',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new LoadStudentLevelsScope);
    }

    //JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function jwtTokens(): HasMany
    {
        return $this->hasMany(JWTPersonalTokens::class, "user_id");
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::USER_COLLECTION)->singleFile();
    }

    public function isRegularUser(): bool
    {
        return $this->role_id === 3 || $this->role_id === 4 || $this->role_id === 5;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1 || $this->role_id === 2;
    }

    public function isTeacher(): bool
    {
        return $this->role_id === 3;
    }

    public function isParent(): bool
    {
        return $this->role_id === 4;
    }

    public function isStudent(): bool
    {
        return $this->role_id === 5;
    }

    public function hasELevel(): bool
    {
        return $this->isStudent() && !is_null($this->e_level_id);
    }

    public function hasCLevel(): bool
    {
        return $this->isStudent() && !is_null($this->c_level_id);
    }

    public function hasLevels(): bool
    {
        return $this->isStudent() && $this->hasELevel() && $this->hasCLevel();
    }

    public function hasParent(): bool
    {
        return $this->isStudent() && !is_null($this->parent_id);
    }

    public function hasStudents(): bool
    {
        return $this->isParent() && $this->students()->exists();
    }

    public function getStudentsCount(): int
    {
        return $this->isParent() ? $this->students()->count() : 0;
    }

    public function getParentName(): ?string
    {
        return $this->isStudent() && $this->parent ? $this->parent->name : null;
    }

    public function getELevelName(): ?string
    {
        return $this->isStudent() && $this->eLevel ? $this->eLevel->name : null;
    }

    public function getCLevelName(): ?string
    {
        return $this->isStudent() && $this->cLevel ? $this->cLevel->name : null;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isRegularAdmin(): bool
    {
        return $this->role_id === 2;
    }

    //Relations
    public function pointsCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'user_id');
    }

    //Account Relations

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class, "user_id");
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, "role_id");
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, "user_id");
    }

    public function adminProfile(): HasOne
    {
        return $this->hasOne(AdminProfile::class, "user_id");
    }

    public function adminsCreated(): HasMany
    {
        return $this->hasMany(AdminProfile::class, "created_by");
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, "city_id");
    }

    public function contry(): BelongsTo
    {
        return $this->belongsTo(Contry::class, 'contry_id');
    }

    public function e_level(): BelongsTo
    {
        return $this->belongsTo(ELevel::class, "e_level_id");
    }

    public function c_level(): BelongsTo
    {
        return $this->belongsTo(CLevel::class, "c_level_id");
    }

    public function userDevices(): HasMany
    {
        return $this->hasMany(UserDevice::class, "user_id");
    }

    public function archivedAccount(): HasOne
    {
        return $this->hasOne(ArchivedUser::class, "user_id");
    }

    public function otp(): HasOne
    {
        return $this->hasOne(OTP::class, 'user_id');
    }

    //Notifications
    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'user_notification')
            ->withTimestamps()
            ->withPivot('is_read');
    }

    public function createdNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'created_by');
    }

    //System Info Relations
    public function appContacts(): HasMany
    {
        return $this->hasMany(ContactUs::class, 'created_by');
    }

    public function appAbouts(): HasMany
    {
        return $this->hasMany(AboutUs::class, 'update_by');
    }

    public function appFaqCategories(): HasMany
    {
        return $this->hasMany(FaqCategory::class, 'update_by');
    }

    public function appFAQs(): HasMany
    {
        return $this->hasMany(FAQ::class, 'update_by');
    }

    public function appTos(): HasMany
    {
        return $this->hasMany(Tos::class, 'update_by');
    }

    public function appPrivacies(): HasMany
    {
        return $this->hasMany(PrivacyPolicy::class, 'update_by');
    }

    public function systemSettingsUpdated(): HasMany
    {
        return $this->hasMany(SystemSetting::class, 'update_by');
    }

    //Customer Service Cards
    public function CustomerServiceCard(): HasMany
    {
        return $this->hasMany(CustomerServiceCard::class, "user_id");
    }

    //E-Learning Responsibilities
    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'teacher_id');
    }

    //E-Learning Watched Lessons
    public function watchedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_student', 'student_id', 'lesson_id')
                    ->withPivot('watched_at')
                    ->withTimestamps();
    }

    //E-Learning Unlocked Contexts
    public function unlockedContexts(): HasMany
    {
        return $this->hasMany(UnlockedContext::class, 'user_id');
    }

    //E-Learning Transactions
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    //E-Learning Lesson Questions
    public function lessonQuestionsAsTeacher(): HasMany
    {
        return $this->hasMany(LessonQuestion::class, 'teacher_id');
    }

    public function lessonQuestionsAsStudent(): HasMany
    {
        return $this->hasMany(LessonQuestion::class, 'student_id');
    }

    //E-Learning Comments and Replays
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function replays(): HasMany
    {
        return $this->hasMany(Replay::class, 'user_id');
    }

    //E-Learning Quiz Results
    public function quizResults(): HasMany
    {
        return $this->hasMany(QuizResult::class, 'student_id');
    }

    //E-Learning Saved Contexts
    public function savedContexts(): HasMany
    {
        return $this->hasMany(SavedContext::class, 'student_id');
    }

    public function savedLessons(): HasMany
    {
        return $this->savedContexts()->lessons();
    }

    public function savedQuestions(): HasMany
    {
        return $this->savedContexts()->questions();
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class, 'user_id');
    }

    //Parent-Student Relationships
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id')->where('role_id', 5);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id')->where('role_id', 4);
    }

    public function lessonRates(): HasMany
    {
        return $this->hasMany(LessonRate::class, 'student_id');
    }

    //Ban System
    public function bans(): HasMany
    {
        return $this->hasMany(BanLog::class, "banned_id");
    }

    public function bannedByMe(): HasMany
    {
        return $this->hasMany(BanLog::class, "banned_by_id");
    }

    public function unbannedByMe(): HasMany
    {
        return $this->hasMany(BanLog::class, "unbanned_by_id");
    }

    //Scopes

    /**
     * Search users by given search criteria
     * This scope will search 'name' and email columns
     * with LIKE operator and case insensitive
     *
     * @param Builder $query
     * @param string|null $search
     */
    public function scopeSearchName(Builder $query, string|null $search)
    {
        $query->when($search, function (Builder $q) use ($search) {
            $q->where("name", 'like', '%' . strtolower($search) . '%')
                ->orWhere("phone_number", "like", "%$search%");
            if (Auth::user() && Auth::user()->role_id != 3)
                $q->orWhere("email", "like", "%$search%");
        });
    }

    /**
     * Search users by given role(s) and filter by criteria below
     * 1. User account should be completed ('name' is not null)
     * 2. User have an active account (deactive_at is null)
     * 3. User account should be verified (account_verified_at is not null)
     *
     * @param Builder $query
     * @param array $role_id
     *
     */
    public function scopeUsersSearchCriteria(Builder $query, $checkBan = true)
    {
        $query->whereIn("role_id", [5,4])
            ->whereNotNull(['name', 'account_verified_at'])            //User account is completed and active
            ->whereNull("deactive_at")              //User have an active account
            ->when($checkBan, function (Builder $q) {
                $q->whereHas("profile", function ($query) {
                    $query->where("banned_until", "<", Carbon::now())
                        ->orWhereNull("banned_until");
                });
            });
    }

    public function scopeFilter($query, $data)
    {
        return $query

        ->when(isset($data['search']) , function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone_number', 'like', '%' . $search . '%');
        })
        ->when(isset($data['e_level_id']), function ($query, $e_level_id) {
            $query->where('e_level_id', $e_level_id);
        })
        ->when(isset($data['c_level_id']), function ($query, $c_level_id) {
            $query->where('c_level_id', $c_level_id);
        });
    }
    public function scopeStudentsByLevels($query, $e_level_id = null, $c_level_id = null)
    {
        return $query->where('role_id', 5)
            ->when($e_level_id, function ($q) use ($e_level_id) {
                $q->where('e_level_id', $e_level_id);
            })
            ->when($c_level_id, function ($q) use ($c_level_id) {
                $q->where('c_level_id', $c_level_id);
            });
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE,
            Resources::RES_USER,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
}
