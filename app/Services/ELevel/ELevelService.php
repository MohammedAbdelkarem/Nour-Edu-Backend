<?php

namespace App\Services\ELevel;

use App\Models\ELevel;
use App\Enums\PublishStatusEnum;
use App\Constants\MediaCollection;
use Illuminate\Support\Facades\DB;
use App\Services\Base\ContextService;

class ELevelService
{
    public function __construct(
        protected ContextService $contextService
    ) {}

    public function getAll($data)
    {
        $query = ELevel::orderBy('created_at', 'desc' , 'responsibilities')
        ->with(['cLevels' , 'responsibilities.teacher']);

        if (isset($data['contry_id'])) {
            $query->where('contry_id', $data['contry_id']);
        }
        else if(isset($data['shared_content'])) {
            $query->sharedElevels();
        }

        return getOrPaginate($query, $data);
    }

    public function getList($contry_id = null)
    {
        $query = ELevel::query();

        $query->sharedElevels()->orWhere('contry_id', $contry_id);

        $query->published();
        
        return $query->get();
    }

    public function getListShared()
    {
        return ELevel::published()->sharedElevels()->get();
    }
    public function show($id)
    {
        return ELevel::findByIdOrFail($id, ['cLevels' , 'responsibilities']);
    }

    public function store($data)
    {
        $eLevel = ELevel::create($data);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $eLevel, MediaCollection::E_LEVEL_COLLECTION);
        }

        $eLevel->save();
    }

    public function update($data, $id)
    {
        $eLevel = ELevel::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , ELevel::class);

        $eLevel->update($data);

        $eLevel->save();
    }

    public function destroy($id)
    {
        $eLevel = ELevel::findByIdOrFail($id);

        $this->contextService->checkIfDraftBeforeDeletingOrUpdating($id , ELevel::class);
        $this->contextService->checkIfHasRegisterdStudentsBeforeDeleting($id , ELevel::class);
        $this->contextService->checkIfHasContentBeforeDeleting($id , ELevel::class);
        
        $eLevel->delete();
    }

    public function changePublishStatus($id, $status)
    {
        $eLevel = ELevel::findByIdOrFail($id);

        if($status == PublishStatusEnum::PUBLISHED->value) {
            $this->contextService->checkIfContextHasContentBeforePublish($id , ELevel::class);
            $this->contextService->checkIfContextHasResponsibilitiesBeforePublish($id , ELevel::class);
        }

        $this->contextService->changePublishStatus($eLevel , 'content' , $status);
    }
}
