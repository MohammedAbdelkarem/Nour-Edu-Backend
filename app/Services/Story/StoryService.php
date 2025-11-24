<?php


namespace App\Services\Story;

use App\Constants\MediaCollection;
use App\Constants\Resources;
use App\Enums\GenderEnum;
use App\Models\Story;
use App\Services\Base\CRUDService;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\DB;

class StoryService
{
    public function getAll($data)
    {
        return getOrPaginate(
            Story::orderBy('created_at', 'desc')
                    ->with('storiable'),
            $data
        );
    }
    public function show($id)
    {
        return Story::findByIdOrFail($id , ['storiable']);
    }
    public function store($data)
    {
        $data['storiable_type'] = getModel($data['storiable_type']);

        $story = Story::create($data);


        //TODO:  store the image or update or delete as you want
        if(isset($data['image']))
            uploadFileOnMedia($data['image'] , $story , MediaCollection::STORY_COLLECTION);

        if(isset($data['video']))
            uploadFileOnMedia($data['video'] , $story , MediaCollection::STORY_COLLECTION);

        $story->save();
    }

    public function update($data , $id)
    {
        $story =  Story::findByIdOrFail($id);

        
        $data['storiable_type'] = getModel($data['storiable_type']);

        $story->update($data);

        //TODO:  store the image or update or delete as you want
        // in medialibrary, we update the media by it's id, check MediaService.php


        // if(isset($data['image']))
        //     updateFileOnMedia($data['image'] , $story , MediaCollection::STORY_COLLECTION);

        // if(isset($data['video']))
        //     updateFileOnMediaWithoutClear($data['video'] , $story , MediaCollection::STORY_COLLECTION);

        $story->save();
    }

    public function destroy($id)
    {
        $story = Story::findByIdOrFail($id);


        //TODO:  store the image or update or delete as you want

        deleteFilesFromMedia($story , MediaCollection::STORY_COLLECTION);

        $story->delete();
    }

    public function changeStatus($id)
    {
        $story = Story::findByIdOrFail($id);

        (new MediaService())->changeMediaStatus($story , MediaCollection::STORY_COLLECTION);
    }
}
