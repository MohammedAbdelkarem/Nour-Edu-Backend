<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Step;
use App\Models\Sleep;
use App\Models\Water;
use App\Models\Patient;
use App\Models\StepTime;
use App\Models\WaterTime;
use App\Models\StepProfit;
use App\Models\BmiClassification;
use App\Traits\NotificationHelper;
use App\Constants\ExceptionMessages;
use App\Services\Base\ContextService;
use App\Constants\NotificationMessages;
use App\Models\OwnerPatientWeightHistory;
use App\Models\Users\Profile\LoginHistory;
use App\Enums\Notifications\NotificationTypes;

/**
 * Class HealthService.
 */
class HealthService
{
    use NotificationHelper;
    public function __construct(
        protected ContextService $contextService,
    )
    {}
    // weight
    public function updateWeight($current_weight)
    {
        $patient = Patient::findByIdOrFail(owner_id());

        $this->contextService->createWeightHistory($patient->id , $patient->weight , $current_weight);

        $patient->weight = $current_weight;

        $patient->save();
    }

    public function getWeightHistory($data)
    {
        return getOrPaginate(
            OwnerPatientWeightHistory::where('patient_id' , owner_id()),
            $data
        );
    }

    // bmi
    public function BMI($user_id)
    {
        $patient = Patient::where('is_owner' , 1)
                    ->where('user_id' , $user_id)
                    ->first();

        $bmi = BMI($patient->weight , $patient->height);
        $classification = $this->getClassification($bmi);

        return [
            'BMI' => $bmi,
            'classification' => $classification
        ];
    }

    private function getClassification($bmi)
    {
        // dd($bmi > 18.5 );
        return BmiClassification::where('start' , '<=' , $bmi)
                            ->where('end' , '>=' , $bmi)
                            ->first();
    }

    // water
    public function getWaterGoal()
    {
        $patient = Patient::findByIdOrFail(owner_id());

        return water_goal($patient->weight , $patient->is_male , $patient->birth_date);
    }

    public function storeWater($data)
    {
        $patient = Patient::findByIdOrFail(owner_id());

        $water = Water::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'created_at' => date('Y-m-d')
            ],
            [
                'user_id' => auth()->id(),
                'goal' => water_goal($patient->weight , $patient->is_male , $patient->birth_date),
            ]
        );

        $water->total_amount += $data['amount'];

        $water->save();

        WaterTime::create([
            'water_id' => $water->id,
            'amount' => $data['amount'],
            'time' => $data['time'],
        ]);

        if(! $water->goal_notified && $water->total_amount >= $water->goal && active_water_notification(auth()->id()))
        {
            $water->goal_notified = 1;
            $water->save();

            $this->sendDirectNotification(
                auth()->id(),
                $this->notificationMessage(NotificationMessages::WATER_GOAL_ACHIEVED_TITLE),
                $this->notificationMessage(
                    NotificationMessages::WATER_GOAL_ACHIEVED_BODY,
                ),
                NotificationTypes::WATER->value,
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
    
    public function getWaterHistory($data)
    {
        return getOrPaginate(
            Water::where('user_id' , auth()->id())->orderBy('created_at' , 'desc')->with('water_times'),
            $data
        );
    }

    // sleep
    public function storeSleep($amount)
    {
        $existSleep = Sleep::where('user_id' , auth()->id())
        ->whereDate('created_at' , Carbon::today())
        ->exists();

        if($existSleep)
            return forbiddenFailure([] , ExceptionMessages::MSG_SLEEP_ALREADY_EXIST);

        $patient = Patient::findByIdOrFail(owner_id());

        $sleep = Sleep::create([
            'user_id' => auth()->id(),
            'goal' => sleep_goal($patient->birth_date),
            'total_amount' => round($amount, 1)
        ]);

        if(($sleep->total_amount / $sleep->goal) > 0.7 && active_sleep_notification(auth()->id()))
        {
            $this->sendDirectNotification(
                    $sleep->user_id,
                    $this->notificationMessage(NotificationMessages::SLEEP_REVIEW_TITLE),
                    $this->notificationMessage(
                        NotificationMessages::SLEEP_REVIEW_BODY,
                        [
                            'hours' => $sleep->total_amount,
                        ]
                    ),
                    NotificationTypes::SLEEP->value,
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

    public function getSleepHistory($data)
    {
        return getOrPaginate(
            Sleep::where('user_id' , auth()->id())->orderBy('created_at' , 'desc'),
            $data
        );
    }

    // steps
    public function setStepCalcAsActive($loginHistoryId)
    {
        LoginHistory::where('user_id', auth()->id())
            ->whereHas('token', function ($q) {
                $q->where('expire_at', '>', Carbon::now());
            })
            ->update([
                'steps_calculator' => 0
            ]);
        
        LoginHistory::where('id', $loginHistoryId)
            ->update([
                'steps_calculator' => 1
            ]);
    }

    public function storeStep($data)
    {
        $patient = Patient::findByIdOrFail(owner_id());

        $step = Step::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'created_at' => date('Y-m-d')
            ],
            [
                'user_id' => auth()->id(),
                'goal' => steps_daily_goal(),
                'goal_reward' => step_reward_value(),
            ]
        );

        $step->total_amount += $data['amount'];

        $step->distance = distance($step->total_amount);

        $step->calories = calories($step->total_amount);

        $step->save();


        if($step->total_amount >= $step->goal)
        {
            $profitExists = StepProfit::where('step_id' , $step->id)->exists();

            if(!$profitExists)
            {
                StepProfit::create([
                    'steps_id' => $step->id,
                    'balance' => $step->goal_reward,
                ]);
            }

            if(! $step->goal_notified && active_steps_notification(auth()->id()))
            {
                $step->goal_notified = 1;
                $step->save();

                $this->sendDirectNotification(
                    auth()->id(),
                    $this->notificationMessage(NotificationMessages::STEP_GOAL_ACHIEVED_TITLE),
                    $this->notificationMessage(
                        NotificationMessages::STEP_GOAL_ACHIEVED_BODY,
                        [
                            'goal' => $step->goal,
                            'points' => $step->goal_reward
                        ]
                    ),
                    NotificationTypes::STEPS->value,
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

        StepTime::create([
            'step_id' => $step->id,
            'amount' => $data['amount'],
            'time' => $data['time'],
        ]);

        if(active_steps_notification(auth()->id()))
        {
            $this->sendDirectNotification(
                auth()->id(),
                $this->notificationMessage(NotificationMessages::DAILY_PROGRESS_TITLE),
                $this->notificationMessage(
                    NotificationMessages::DAILY_PROGRESS_BODY,
                    [
                        'steps' => $step->total_amount
                    ]
                ),
                NotificationTypes::STEPS->value,
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

    public function getStepsHistory($data)
    {
        return getOrPaginate(
            Step::where('user_id' , auth()->id())->orderBy('created_at' , 'desc')->with('steps_times'),
            $data
        );
    }

    public function getStepsByDate($data)
    {
        return Step::whereDate('created_at' , $data['date'])
                ->where('user_id' , auth()->id())
                ->with('steps_times')
                ->first();
    }
    
}
