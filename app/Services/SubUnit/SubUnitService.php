<?php

namespace App\Services\SubUnit;

use App\Models\SubUnit;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;
use App\Services\Purchase\PurchaseService;

class SubUnitService
{
    public function __construct(
        protected ContextService $contextService,
        protected PurchaseService $purchaseService
    ) {}

    /**
     * Get all SubUnits with optional filtering
     */
    public function getAll($data)
    {
        $query = SubUnit::orderBy('created_at', 'desc')
                ->with([ 'unit', 'lessons' , 'responsibilities']);

        // Filter by Unit ID if provided
        if (isset($data['unit_id'])) {
            $query->where('unit_id', $data['unit_id']);
        }

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
        $subUnit = SubUnit::create($data);

        $this->purchaseService->unlockOthersWhenAddnig(SubUnit::class, $subUnit->id);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $subUnit, MediaCollection::SUB_UNIT_COLLECTION);
        }

        $subUnit->save();

        // Update parent Unit numbers
        // $this->contextService->updateParentNumberOfContents($subUnit, '+');
    }

    public function update($data, $id)
    {
        $subUnit = SubUnit::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , SubUnit::class);

        $subUnit->update($data);

        $subUnit->save();
    }

    public function destroy($id)
    {
        $subUnit = SubUnit::findByIdOrFail($id);
        
        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , SubUnit::class);
        // $this->contextService->checkIfHasPurchasedStudentsBeforeDeleting($id , SubUnit::class);
        // $this->contextService->checkIfHasContentBeforeDeleting($id , SubUnit::class);
        
        // Update parent Unit numbers before deletion
        // $this->contextService->updateParentNumberOfContents($subUnit, '-');
        
        $this->purchaseService->deleteUnlockOthersWhenDeleting(SubUnit::class , $id);

        $subUnit->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $subUnit = SubUnit::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfParentPublishedBeforePublish($id , SubUnit::class);
            $this->contextService->checkIfContextHasContentBeforePublish($id , SubUnit::class);
        }

        $this->contextService->changeWithChildsPublishStatus($id , SubUnit::class , $status);
    }

    public function changePriority($contextsData)
    {
        $this->contextService->changeContextsPriority($contextsData , SubUnit::class);
    }
}
