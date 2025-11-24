<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sleep;
use App\Models\Visit;
use App\Models\Medicine;
use App\Models\MedicineDay;
use App\Models\Reservation;
use Faker\Provider\Medical;
use App\Models\MedicineTime;
use App\Enums\DaysToTakeEnum;
use App\Jobs\SendNotificationsJob;
use App\Traits\NotificationHelper;
use App\Enums\ReservationStatusEnum;
use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationTypes;
use App\Services\System\Notification\NotificationService;

/**
 * Class CronJobService.
 */
class CronJobService
{
    use NotificationHelper;

    public function __construct(
        protected NotificationService $notificationService,
        protected PatientNotificationService $patientNotificationService,
        protected HealthService $healthService,
    ) {}
    public function remindForReservationsDaily()
    {
        Reservation::where('status', ReservationStatusEnum::ACCEPTED->value)
            ->where('daily_reminded', 0)
            ->chunkById(100, function ($reservations) {
                foreach ($reservations as $reservation) {
                    $dateTime = $reservation->date . ' ' . $reservation->time_to_come;

                    if (Carbon::now()->diffInHours(Carbon::parse($dateTime)) <= 24) {
                        $reservation->daily_reminded = 1;
                        $reservation->save();

                        $this->sendDirectNotification(
                            user_id_of_patient($reservation->patient_id),
                            $this->notificationMessage(NotificationMessages::APPOINTMENT_REMINDER_24_TITLE),
                            $this->notificationMessage(
                                NotificationMessages::APPOINTMENT_REMINDER_24_BODY,
                                [
                                    'name' => $reservation->doctor->clinic_name,
                                ]
                            ),
                            NotificationTypes::RESERVATIONS->value,
                            'ar',
                            false,
                            "",
                            [],
                            true,
                            [],
                            true
                        );
                    }
                }
            });
    }

    public function remindForReservationsHourly()
    {
        Reservation::where('status', ReservationStatusEnum::ACCEPTED->value)
            ->where('hourly_reminded', 0)
            ->chunkById(100, function ($reservations) {
                foreach ($reservations as $reservation) {
                    $dateTime = $reservation->date . ' ' . $reservation->time_to_come;

                    if (Carbon::now()->diffInHours(Carbon::parse($dateTime)) <= 1) {
                        $reservation->hourly_reminded = 1;
                        $reservation->save();

                        $this->sendDirectNotification(
                            user_id_of_patient($reservation->patient_id),
                            $this->notificationMessage(NotificationMessages::APPOINTMENT_REMINDER_1_TITLE),
                            $this->notificationMessage(
                                NotificationMessages::APPOINTMENT_REMINDER_1_BODY,
                                [
                                    'name' => $reservation->doctor->clinic_name,
                                ]
                            ),
                            NotificationTypes::RESERVATIONS->value,
                            'ar',
                            false,
                            "",
                            [],
                            true,
                            [],
                            true
                        );
                    }
                }
            });
    }

    public function remindForVisitsRating()
    {
        Visit::where('rate_reminded',0)
            ->chunkById(100, function ($visits) {
                foreach ($visits as $visit) {

                    if (Carbon::now()->diffInHours(Carbon::parse($visit)) >= 24 ) {

                        $visit->rate_reminded = 1;
                        $visit->save();
                        
                        $this->sendDirectNotification(
                            user_id_of_patient($visit->patient_id),
                            $this->notificationMessage(NotificationMessages::APPOINTMENT_RATING_TITLE),
                            $this->notificationMessage(
                                NotificationMessages::APPOINTMENT_RATING_BODY,
                                [
                                    'name' => $visit->doctor->clinic_name,
                                ]
                            ),
                            NotificationTypes::RESERVATIONS->value,
                            'ar',
                            false,
                            "",
                            [],
                            true,
                            [],
                            true
                        );
                    }
                }
            });
    }

    public function remindForMedicinesTimes()
    {
        $medicines_ids = Medicine::where('days_to_take', '!=', DaysToTakeEnum::WHEN_NEEDED->value)
            ->pluck('id')
            ->toArray();

        $todayName = Carbon::today()->englishDayOfWeek;
        $nowTime = Carbon::now()->format('H:i:s');

        MedicineDay::whereIn('medicine_id', $medicines_ids)
            ->chunkById(100, function ($medicineDays) use ($todayName, $nowTime) {
                foreach ($medicineDays as $medicineDay) {
                    if ($medicineDay->day->name === $todayName) {
                        MedicineTime::where('medicine_day_id', $medicineDay->id)
                            ->where('daily_reminded', 0)
                            ->whereNotNull('time')
                            ->where('time', '<=', $nowTime)
                            ->chunkById(50, function ($times) {
                                foreach ($times as $medicineTime) {
                                    $medicineTime->update(['daily_reminded' => 1]);

                                    $this->sendDirectNotification(
                                        user_id_of_patient($medicineTime->medicine_day->medicine->patient_id),
                                        $this->notificationMessage(NotificationMessages::MEDICATION_REMINDER_TITLE),
                                        $this->notificationMessage(
                                            NotificationMessages::MEDICATION_REMINDER_BODY,
                                            [
                                                'medication' => $medicineTime->medicine_day->medicine->text,
                                            ]
                                        ),
                                        NotificationTypes::TREATMENT_REMINDER->value,
                                        'ar',
                                        false,
                                        "",
                                        [],
                                        true,
                                        [],
                                        true
                                    );
                                }
                            });
                    }
                }
            });
    }


    //cronjob function to put the medicines reminded_daily as 0 again , at 00:00
    public function resetDailyReminders()
    {
        MedicineTime::where('daily_reminded', 1)
            ->update(['daily_reminded' => 0]);
    }

    public function remindForStepsDaily()
    {
        $usersIds = $this->patientNotificationService->getEligibleStepsUserIds();

        $notificationTokens = $this->notificationService->getNotificationTokens($usersIds);

        $userNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::INACTIVITY_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::INACTIVITY_BODY),
            type: NotificationTypes::STEPS->value,
            is_public: false
        );

        $userNotification->receivers()->attach($usersIds);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $notificationTokens,
            notification: $userNotification,
            shouldTranslate: true,
        ));
    }

    public function remindForWaterHourly()
    {
        $usersIds = $this->patientNotificationService->getEligibleWaterUserIds();

        $notificationTokens = $this->notificationService->getNotificationTokens($usersIds);

        $userNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::WATER_REMINDER_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::WATER_REMINDER_BODY),
            type: NotificationTypes::WATER->value,
            is_public: false
        );

        $userNotification->receivers()->attach($usersIds);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $notificationTokens,
            notification: $userNotification,
            shouldTranslate: true,
        ));
    }

    public function remindForSleepDaily()
    {
        $usersIds = $this->notificationService->getEligibleUserIds(true , 'sleep');

        $notificationTokens = $this->notificationService->getNotificationTokens($usersIds);

        $userNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::SLEEP_REMINDER_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::SLEEP_REMINDER_BODY),
            type: NotificationTypes::SLEEP->value,
            is_public: false
        );

        $userNotification->receivers()->attach($usersIds);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $notificationTokens,
            notification: $userNotification,
            shouldTranslate: true,
        ));
    }
    public function remindForWeightMonthly()
    {
        $usersIds = $this->notificationService->getEligibleUserIds(true , 'weight');

        $notificationTokens = $this->notificationService->getNotificationTokens($usersIds);

        $userNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::WEIGHT_UPDATE_REQUEST_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::WEIGHT_UPDATE_REQUEST_BODY),
            type: NotificationTypes::WEIGHT->value,
            is_public: false
        );

        $userNotification->receivers()->attach($usersIds);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $notificationTokens,
            notification: $userNotification,
            shouldTranslate: true,
        ));
    }

    public function remindForBMIMonthly()
    {
        $userNotification = $this->createNotification(
            title: $this->notificationMessage(NotificationMessages::PERFORMANCE_IMPROVED_TITLE) ,
            body: $this->notificationMessage(NotificationMessages::PERFORMANCE_IMPROVED_BODY),
            type: NotificationTypes::GENERAL->value,
            is_public: false
        );

        $userIds = [];

        User::query()
            ->where('role_id', 4)
            ->whereNull('deactive_at')
            ->where('active_notifications', true)
            ->whereHas('notification_management', function ($q2) {
                $q2->where('general_notification', 1);
            })
            ->whereHas('patients', function ($query) {
                $query->where('is_owner', 1);
            })
            ->chunkById(100, function ($users) use ($userNotification) {
                foreach ($users as $user) {
                    $bmi = $this->healthService->BMI($user->id)['BMI'];
                    if($bmi >= 18.5 && $bmi < 25)
                    {
                        $userIds[] = $user->id;
                        $userNotification->receivers()->attach($user->id);
                    }
                }
            });

        $notificationTokens = $this->notificationService->getNotificationTokens($userIds);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $notificationTokens,
            notification: $userNotification,
            shouldTranslate: true,
        ));
    }

    
    public function remindForGoodMorning()
    {
        $this->remindForGeneral(NotificationMessages::GOOD_MORNING_TITLE , NotificationMessages::GOOD_MORNING_BODY);
    }

    public function remindForGoodEvening()
    {
        $this->remindForGeneral(NotificationMessages::GOOD_EVENING_TITLE , NotificationMessages::GOOD_EVENING_BODY);
    }

    public function remindForHealthcare()
    {
        $this->remindForGeneral(NotificationMessages::HEALTHCARE_REMINDER_TITLE , NotificationMessages::HEALTHCARE_REMINDER_BODY);
    }

    public function healthTip()
    {
        $this->remindForGeneral(NotificationMessages::HEALTH_TIP_TITLE , NotificationMessages::HEALTH_TIP_BODY);
    }

    private function remindForGeneral($notfication_title , $notification_body)
    {
        $userIds = $this->notificationService->getEligibleUserIds(true , 'general');

        $notificationTokens = $this->notificationService->getNotificationTokens($userIds);

        $userNotification = $this->createNotification(
            title: $this->notificationMessage($notfication_title) ,
            body: $this->notificationMessage($notification_body),
            type: NotificationTypes::GENERAL->value,
            is_public: false
        );

        $userNotification->receivers()->attach($userIds);

        //Dispatch Job To Send Notification
        dispatch(new SendNotificationsJob(
            tokens: $notificationTokens,
            notification: $userNotification,
            shouldTranslate: true,
        ));
    }
}
