<?php

namespace App\Services\Media;

use App\Models\Lesson;
use App\Enums\MediaTypeEnum;
use App\Enums\MediaStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Constants\MediaCollection;
use App\Constants\ExceptionMessages;
use App\Services\Lesson\LessonService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class MediaService.
 */
class MediaService
{
    public function __construct(
        protected LessonService $lessonService
    ) {}

    public function store($data)
    {
        $model = getModel($data['context_type']);

        $mediaCollection = mediaCollectionByContxt($data['context_type']);

        $context = $model::find($data['context_id']);

        
        if (isset($data['images']))
            uploadFilesOnMedia($data['images'], $context, $mediaCollection);
        if (isset($data['videos']))
            uploadFilesOnMedia($data['videos'], $context, $mediaCollection);
        if (isset($data['files']))
            uploadFilesOnMedia($data['files'], $context, $mediaCollection);
    }

    public function update($data, $id)
    {
        $media = Media::find($id);

        $model = getModelByPath($media->model_type);

        $context = $model::find($media->model_id);

        $mediaCollection = $media->collection_name;

        $media->delete();

        if (isset($data['images']))
            uploadFilesOnMedia($data['images'], $context, $mediaCollection);
        if (isset($data['videos']))
            uploadFilesOnMedia($data['videos'], $context, $mediaCollection);
        if (isset($data['files']))
            uploadFilesOnMedia($data['files'], $context, $mediaCollection);
    }

    public function delete($data)
    {
        $media = Media::whereIn('id', $data['ids'])->get();

        foreach($media as $item) {
            if($item->model_type == Lesson::class) {
                $this->lessonService->deleteDownloads($item->model_id);
            }
            
            $item->delete();
        }
    }

    public function changeMediaStatus($context , $mediaCollection)
    {
        if($context->status == MediaStatusEnum::INACTIVE)
            $this->checkIfHasMedia($context , $mediaCollection);
        
        $context->status =
            ($context->status == MediaStatusEnum::ACTIVE)
            ? MediaStatusEnum::INACTIVE
            : MediaStatusEnum::ACTIVE;

        $context->save();
    }

    private function checkIfHasMedia($context , $mediaCollection)
    {
        $hasImage = $context->getMedia($mediaCollection)
            ->contains(function ($media) {
                return strpos($media->mime_type, 'image') !== false;
            });
        if(!$hasImage)
            return forbiddenFailure(null , ExceptionMessages::MSG_CANNOT_SET_TO_ACTIVE_CUZ_HAS_NO_MEDIA);
    }
}
