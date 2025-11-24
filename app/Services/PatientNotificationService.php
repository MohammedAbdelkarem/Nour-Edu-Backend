<?php

namespace App\Services;

use App\Models\User;
use App\Models\Article;
use App\Models\Favorite;
use App\Jobs\SendNotificationsJob;
use App\Traits\NotificationHelper;
use App\Models\NotificationManagement;
use App\Constants\NotificationMessages;
use App\Models\Users\Profile\UserDevice;
use App\Enums\Notifications\NotificationTypes;
use App\Services\System\Notification\NotificationService;

/**
 * Class PatientNotificationService.
 */
class PatientNotificationService
{
    use NotificationHelper;

    public function __construct(
        protected NotificationService $notificationService,
    ) {}
    public function getSettings()
    {
        return auth()->user()->notification_management;
    }

    public function update($id , $type)
    {
        $setting = NotificationManagement::find($id);

        $column = $type . '_notification';

        $setting->$column = ($setting->$column == 1) ? 0 : 1;

        $setting->save();
    }

    public function notifyForArticles($article)
    {
        
        $users_ids = $this->notificationService->getEligibleUserIds(true , 'articles');
        $users_tokens_list = $this->notificationService->getNotificationTokens($users_ids);

        $fav_users_ids = $this->getEligibleFavoriteUserIds($article);
        $fav_users_tokens_list = $this->notificationService->getNotificationTokens($users_ids);

        //Create Notification
        $userNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::NEW_ARTICLE_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::NEW_ARTICLE_BODY , ['title' => $article->title]),
            type: NotificationTypes::ARTICLES->value,
            is_public: false
        );

        $userNotification->receivers()->attach($users_ids);

        $favoriteUserNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::DOCTOR_ARTICLE_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::DOCTOR_ARTICLE_BODY , ['title' => $article->title , 'doctor' => $article->doctor->clinic_name]),
            type: NotificationTypes::ARTICLES->value,
            is_public: false
        );

        $favoriteUserNotification->receivers()->attach($fav_users_ids);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $users_tokens_list,
            notification: $userNotification,
            shouldTranslate: true,
        ));

        dispatch(new SendNotificationsJob(
            tokens: $fav_users_tokens_list,
            notification: $favoriteUserNotification,
            shouldTranslate: true,
        ));
    }

    public function getEligibleStepsUserIds()
    {
        return User::whereNull('deactive_at')
        ->where('active_notifications', true)
        ->where('role_id', 4)
        ->whereHas('notification_management', function ($q) {
            $q->where('steps_notification', 1); // adjust key if needed
        })
        ->where(function ($query) {
            $query
                // Case 1: User has today's steps but achieved less than 20% of goal
                ->whereHas('steps', function ($q) {
                    $q->whereDate('created_at', today())
                      ->whereRaw('(total_amount / goal) < 0.2');
                })
                // Case 2: OR User has NO steps record for today
                ->orWhereDoesntHave('steps', function ($q) {
                    $q->whereDate('created_at', today());
                });
        })
        ->pluck('id')
        ->toArray();
    }

    public function getEligibleWaterUserIds()
    {
        return User::whereNull('deactive_at')
        ->where('active_notifications', true)
        ->where('role_id', 4)
        ->whereHas('notification_management', function ($q) {
            $q->where('water_notification', 1); // adjust key if needed
        })
        ->where(function ($query) {
            $query
                ->whereHas('water', function ($q) {
                    $q->whereDate('created_at', today())
                      ->whereRaw('total_amount < goal');
                })
                ->orWhereDoesntHave('water', function ($q) {
                    $q->whereDate('created_at', today());
                });
        })
        ->pluck('id')
        ->toArray();
    }

    private function getEligibleFavoriteUserIds($article)
    {
        return User::whereIn('id', function ($query) use ($article) {
                $query->select('user_id')
                    ->from('favorites')
                    ->where('favoritable_type', Article::class)
                    ->where('favoritable_id', $article->id);
            })
            ->whereNull('deactive_at')
            ->where('active_notifications', true)
            ->where('role_id', 4)
            ->whereHas('notification_management', function ($q) {
                $q->where('articles_notification', 1);
            })
            ->pluck('id')
            ->toArray();
    }

}
