<?php

namespace App\Services\Subject;

use App\Models\Subject;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;
use App\Services\Purchase\PurchaseService;

class SubjectService
{
    public function __construct(
        protected ContextService $contextService,
        protected PurchaseService $purchaseService
    ) {}

    /**
     * Get all Subjects with optional filtering
     */
    public function getAll($data)
    {
        $query = Subject::orderBy('created_at', 'desc')
                ->with([ 'course', 'units' , 'responsibilities.teacher']);

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
        $subject = Subject::create($data);

        $this->purchaseService->unlockOthersWhenAddnig(Subject::class, $subject->id);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $subject, MediaCollection::SUBJECT_COLLECTION);
        }

        if (isset($data['icon'])) {
            uploadFileOnMedia($data['icon'], $subject, MediaCollection::SUBJECT_ICON_COLLECTION);
        }

        if (isset($data['video'])) {
            uploadFileOnMedia($data['video'], $subject, MediaCollection::SUBJECT_VIDEO_COLLECTION);
        }

        $subject->save();

        // Update parent Course numbers
        // $this->contextService->updateParentNumberOfContents($subject, '+');
    }

    public function update($data, $id)
    {
        $subject = Subject::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Subject::class);

        $subject->update($data);

        $subject->save();
    }

    public function destroy($id)
    {
        $subject = Subject::findByIdOrFail($id);
        
        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Subject::class);
        $this->contextService->checkIfHasPurchasedStudentsBeforeDeleting($id , Subject::class);
        $this->contextService->checkIfHasContentBeforeDeleting($id , Subject::class);

        // Update parent Course numbers before deletion
        // $this->contextService->updateParentNumberOfContents($subject, '-');
        
        $subject->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $subject = Subject::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfParentPublishedBeforePublish($id , Subject::class);
            $this->contextService->checkIfContextHasContentBeforePublish($id , Subject::class);
            $this->contextService->checkIfContextHasResponsibilitiesBeforePublish($id , Subject::class);
        }

        $this->contextService->changeWithChildsPublishStatus($id , Subject::class , $status);
    }

    public function changeAccessTypeStatus($id, $price)
    {
        $subject = Subject::findByIdOrFail($id);
        
        $this->contextService->changeContentAccessTypeStatus($subject, $price);
    }
}
