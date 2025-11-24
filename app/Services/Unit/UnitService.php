<?php

namespace App\Services\Unit;

use App\Models\Unit;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;
use App\Services\Purchase\PurchaseService;

class UnitService
{
    public function __construct(
        protected ContextService $contextService,
        protected PurchaseService $purchaseService
    ) {}

    /**
     * Get all Units with optional filtering
     */
    public function getAll($data)
    {
        $query = Unit::orderBy('created_at', 'desc')
                ->with(['subject','subUnits' , 'responsibilities.teacher']);

        // Filter by Subject ID if provided
        if (isset($data['subject_id'])) {
            $query->where('subject_id', $data['subject_id']);
        }

        // Filter by Course ID if provided
        if (isset($data['course_id'])) {
            $query->where('course_id', $data['course_id']);
        }

        // Filter by CLevel ID if provided
        if (isset($data['c_level_id'])) {
            $query->where('c_level_id', $data['c_level_id']);
        }

        // Filter by ELevel ID if provided
        if (isset($data['e_level_id'])) {
            $query->where('e_level_id', $data['e_level_id']);
        }

        return getOrPaginate($query, $data);
    }

    public function store($data)
    {
        $unit = Unit::create($data);

        $this->purchaseService->unlockOthersWhenAddnig(Unit::class, $unit->id);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $unit, MediaCollection::UNIT_COLLECTION);
        }

        $unit->contry_id = $this->contextService->getCoutnryIdByElevelId($data['e_level_id']);

        $unit->save();

        // Update parent Subject numbers
        // $this->contextService->updateParentNumberOfContents($unit, '+');
    }

    public function update($data, $id)
    {
        $unit = Unit::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Unit::class);

        $unit->update($data);

        $unit->save();
    }

    public function destroy($id)
    {
        $unit = Unit::findByIdOrFail($id);
        
        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Unit::class);
        // $this->contextService->checkIfHasPurchasedStudentsBeforeDeleting($id , Unit::class);
        // $this->contextService->checkIfHasContentBeforeDeleting($id , Unit::class);

        // Update parent Subject numbers before deletion
        // $this->contextService->updateParentNumberOfContents($unit, '-');
        
        $this->purchaseService->deleteUnlockOthersWhenDeleting(Unit::class , $id);

        $unit->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $unit = Unit::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfParentPublishedBeforePublish($id , Unit::class);
            $this->contextService->checkIfContextHasContentBeforePublish($id , Unit::class);
            $this->contextService->checkIfContextHasResponsibilitiesBeforePublish($id , Unit::class);
        }

        $this->contextService->changeWithChildsPublishStatus($id , Unit::class , $status);
    }

    public function changePriority($contextsData)
    {
        $this->contextService->changeContextsPriority($contextsData , Unit::class);
    }

    public function changeAccessTypeStatus($id, $price)
    {
        $unit = Unit::findByIdOrFail($id);
        
        return $this->contextService->changeContentAccessTypeStatus($unit, $price);
    }
}
