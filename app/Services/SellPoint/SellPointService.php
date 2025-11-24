<?php

namespace App\Services\SellPoint;

use App\Models\SellPoint;
use App\Constants\MediaCollection;

/**
 * Class SellPointService.
 */
class SellPointService
{
    public function getAll($data)
    {
        return getOrPaginate(
            SellPoint::orderBy('created_at', 'desc'),
            $data
        );
    }
    public function create($data)
    {
        $sellPoint = SellPoint::create($data);
        
        if(isset($data['image']))
            uploadFileOnMedia($data['image'] , $sellPoint , MediaCollection::SELL_POINT_COLLECTION);

        $sellPoint->save();
    }

    public function update($data, $id)
    {
        $sellPoint = SellPoint::findByIdOrFail($id);
        
        $sellPoint->update($data);
        
        $sellPoint->save();
    }

    public function destroy($id)
    {
        $sellPoint = SellPoint::findByIdOrFail($id);
        
        $sellPoint->delete();
    }
}
