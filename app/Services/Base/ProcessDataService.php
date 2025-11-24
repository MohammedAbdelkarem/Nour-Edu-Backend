<?php

namespace App\Services\Base;

use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Instruction;
use App\Models\Subscription;
use App\Enums\TreatmentStatusEnum;
use App\Services\Plan\PlanService;
use App\Traits\NotificationHelper;
use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationTypes;

/**
 * Class ProcessDataService.
 */
class ProcessDataService
{
    use NotificationHelper;
    public function __construct(
        protected PlanService $planService
    ){}
    public function processSubscriptionsData()
    {
        $current_plan = $this->planService->getDoctorActiveSubscription(doctor_id());

        if($current_plan->end_at < now())
        {
            $current_plan->is_active = 0;
            $current_plan->save();

            $possible_next_plan = Subscription::where('doctor_id' , doctor_id())
                ->whereDate('start_at' , '<=' , now())
                ->whereDate('end_at' , '>=' , now())
                ->first();
            
            if($possible_next_plan)
            {
                $possible_next_plan->is_active = 1;
                $possible_next_plan->save();
            }
            else
            {
                $this->sendDirectNotification(
                    auth()->id(),
                    $this->notificationMessage(NotificationMessages::NO_SUBSCRIPTION_TITLE),
                    $this->notificationMessage(
                        NotificationMessages::NO_SUBSCRIPTION_BODY
                    ),
                    NotificationTypes::SUBSCRIPTION->value,
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
    }

    public function processTreatments()
    {
        $patients = Patient::where('user_id' , auth()->id())->get();

        foreach($patients as $patient)
        {
            $this->processMedicines($patient);
            $this->processInstructions($patient);
        }
    }

    private function processMedicines($patient)
    {
        $medicines = Medicine::where('patient_id' , $patient->id)
                ->where('status' ,'!=' , TreatmentStatusEnum::EXPIRED->value)
                ->where('is_latest' , 1)
                ->whereDate('end_date' ,'<=' , now())
                ->get();

        foreach($medicines as $medicine)
        {
            $medicine->status = TreatmentStatusEnum::EXPIRED->value;
            $medicine->save();
        }
    }

    private function processInstructions($patient)
    {
        $instructions = Instruction::where('patient_id' , $patient->id)
                ->where('status' ,'!=' , TreatmentStatusEnum::EXPIRED->value)
                ->where('is_latest' , 1)
                ->whereDate('end_date' ,'<=' , now())
                ->get();

        foreach($instructions as $instruction)
        {
            $instruction->status = TreatmentStatusEnum::EXPIRED->value;
            $instruction->save();
        }
    }
}
