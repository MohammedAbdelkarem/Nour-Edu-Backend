<?php

namespace App\Services;

use App\Enums\AppTypeEnum;
use App\Models\AppVersion;
use App\Constants\MediaCollection;

/**
 * Class AppVersionService.
 */
class AppVersionService
{
    public function store($data)
    {
        $appVersion = AppVersion::create($data);

        if($data['is_force_update'])
        {
            AppVersion::where('app_type', $data['app_type'])->update(['is_force_update' => false]);
            $appVersion->update(['is_force_update' => true]);
        }

        if(isset($data['file']))
            uploadFileOnMedia($data['file'] , $appVersion , MediaCollection::APP_VERSION_COLLECTION);

        $appVersion->save();
    }

    public function index($data)
    {
        $versions = AppVersion::query()->orderBy('version' , 'desc');
        
        if(isset($data['app_type']))
            $versions->where('app_type', $data['app_type']);

        return getOrPaginate(
            $versions,
            $data
        );
    }

    public function studentVersions($data)
    {
        return getOrPaginate(
            AppVersion::query()->where('app_type', AppTypeEnum::STUDENT->value)->orderBy('version' , 'desc'),
            $data
        );
    }

    public function teacherVersions($data)
    {
        return getOrPaginate(
            AppVersion::query()->where('app_type', AppTypeEnum::TEACHER->value)->orderBy('version' , 'desc'),
            $data
        );
    }

    public function delete($id)
    {
        $appVersion = AppVersion::findByIdOrFail($id);
        $appVersion->delete();
    }
}
