<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;
use App\Services\Purchase\PurchaseService;

class CourseService
{
    public function __construct(
        protected ContextService $contextService,
        protected PurchaseService $purchaseService
    ) {}

    /**
     * Get all Courses with optional CLevel filtering
     */
    public function getAll($data)
    {
        $query = Course::orderBy('created_at', 'desc')
                ->with(['cLevel', 'subjects' , 'responsibilities.teacher']);

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
        $course = Course::create($data);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $course, MediaCollection::COURSE_COLLECTION);
        }

        if (isset($data['icon'])) {
            uploadFileOnMedia($data['icon'], $course, MediaCollection::COURSE_ICON_COLLECTION);
        }

        $course->contry_id = $this->contextService->getCoutnryIdByElevelId($data['e_level_id']);

        $course->save();

        // Update parent CLevel numbers
        // $this->contextService->updateParentNumberOfContents($course, '+');
    }

    public function update($data, $id)
    {
        $course = Course::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Course::class);

        $course->update($data);

        $course->save();
    }

    public function destroy($id)
    {
        $course = Course::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , Course::class);
        $this->contextService->checkIfHasPurchasedStudentsBeforeDeleting($id , Course::class);
        $this->contextService->checkIfHasContentBeforeDeleting($id , Course::class);
        
        // Update parent CLevel numbers before deletion
        // $this->contextService->updateParentNumberOfContents($course, '-');
        
        $this->purchaseService->deleteUnlockOthersWhenDeleting(Course::class , $id);

        $course->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $course = Course::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfParentPublishedBeforePublish($id , Course::class);
            $this->contextService->checkIfContextHasContentBeforePublish($id , Course::class);
            $this->contextService->checkIfContextHasResponsibilitiesBeforePublish($id , Course::class);
        }

        $this->contextService->changeWithChildsPublishStatus($id , Course::class , $status);
    }

    public function changeAccessTypeStatus($id, $price)
    {
        $course = Course::findByIdOrFail($id);
        
        $this->contextService->changeContentAccessTypeStatus($course, $price);
    }
}
