<?php

namespace App\Services\CLevel;

use App\Models\CLevel;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;

class CLevelService
{
    public function __construct(
        protected ContextService $contextService
    ) {}

    /**
     * Get all CLevels with optional ELevel filtering
     */
    public function getAll($data)
    {
        $query = CLevel::orderBy('created_at', 'desc')
                ->with(['eLevel', 'courses' , 'responsibilities.teacher']);

        // Filter by ELevel ID if provided
        if (isset($data['e_level_id'])) {
            $query->where('e_level_id', $data['e_level_id']);
        }

        return getOrPaginate($query, $data);
    }

    public function getList($e_level_id)
    {
        return CLevel::published()->where('e_level_id', $e_level_id)->get();
    }

    public function store($data)
    {
        $cLevel = CLevel::create($data);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $cLevel, MediaCollection::C_LEVEL_COLLECTION);
        }

        $cLevel->contry_id = $this->contextService->getCoutnryIdByElevelId($data['e_level_id']);

        $cLevel->save();

        // Update parent ELevel numbers
        // $this->contextService->updateParentNumberOfContents($cLevel, '+');
    }

    public function update($data, $id)
    {
        $cLevel = CLevel::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , CLevel::class);

        $cLevel->update($data);

        $cLevel->save();
    }

    public function destroy($id)
    {
        $cLevel = CLevel::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , CLevel::class);
        $this->contextService->checkIfHasRegisterdStudentsBeforeDeleting($id , CLevel::class);
        $this->contextService->checkIfHasContentBeforeDeleting($id , CLevel::class);

        // Update parent ELevel numbers before deletion
        // $this->contextService->updateParentNumberOfContents($cLevel, '-');
        
        $cLevel->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $cLevel = CLevel::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfParentPublishedBeforePublish($id , CLevel::class);
            $this->contextService->checkIfContextHasContentBeforePublish($id , CLevel::class);
            $this->contextService->checkIfContextHasResponsibilitiesBeforePublish($id , CLevel::class);
        }

        $this->contextService->changePublishStatus($cLevel , 'content' , $status);
    }

}
