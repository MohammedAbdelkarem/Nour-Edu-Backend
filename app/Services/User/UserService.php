<?php

namespace App\Services\User;

use App\Models\Patient;
use App\Models\User;
use App\Traits\StorageHelper;

/**
 * Class UserService.
 */
class UserService
{
    use StorageHelper;
    public function getPatients($data)
    {
        return getOrPaginate(
            User::where('role_id' , 4)->with(['city', 'profile', 'archivedAccount'])->filter($data),
            $data
        );
    }
}
