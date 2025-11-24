<?php

namespace App\Services\Banner;

use App\Models\Banner;
use App\Constants\MediaCollection;
use App\Services\Media\MediaService;

/**
 * Class BannerService.
 */
class BannerService
{
    public function getAll($data)
    {
        return getOrPaginate(
            Banner::query()->orderBy('created_at' , 'desc')
                    ->with('bannerable'),
            $data
        );
    }
    public function show($id)
    {
        return Banner::findByIdOrFail($id , ['bannerable']);
    }
    public function store($data)
    {
        $data['bannerable_type'] = getModel($data['bannerable_type']);

        $banner = Banner::create($data);

        //TODO:  store the image or update or delete as you want
        if(isset($data['image']))
            uploadFileOnMedia($data['image'] , $banner , MediaCollection::BANNER_COLLECTION);
        
        $banner->save();
    }

    public function update($data , $id)
    {
        $banner =  Banner::findByIdOrFail($id);

        $data['bannerable_type'] = getModel($data['bannerable_type']);

        $banner->update($data);

        //TODO:  store the image or update or delete as you want
        // in medialibrary, we update the media by it's id, check MediaService.php


        // if(isset($data['image']))
        //     updateFileOnMedia($data['image'] , $banner , MediaCollection::BANNER_COLLECTION);

        // if(isset($data['video']))
        //     updateFileOnMediaWithoutClear($data['video'] , $banner , MediaCollection::BANNER_COLLECTION);

        $banner->save();
    }

    public function destroy($id)
    {
        $banner = Banner::findByIdOrFail($id);


        //TODO:  store the image or update or delete as you want

        deleteFilesFromMedia($banner , MediaCollection::BANNER_COLLECTION);

        $banner->delete();
    }

    public function changeStatus($id)
    {
        $banner = Banner::findByIdOrFail($id);

        (new MediaService())->changeMediaStatus($banner , MediaCollection::BANNER_COLLECTION);
    }
}
