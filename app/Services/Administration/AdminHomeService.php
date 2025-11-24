<?php

namespace App\Services\Administration;

use App\Http\Resources\Administration\Profile\AdminListResource;
use App\Services\MainService;

/**
 * Class AdminHomeService.
 */
class AdminHomeService extends MainService
{
    public function home()
    {
        //TODO
    }

    public function overview()
    {
        $user = auth()->user();

        return [
            "user"              => new AdminListResource($user),
            "new_notifications" => $user->notifications()->wherePivot("is_read", 0)->count(),
            "abilities"         => $user->role->abilities()->pluck('ability_id')->toArray(),
        ];
    }
}