<?php

namespace App\Traits;

trait ImagesHelper
{
    protected function getProfileImage($user): string
    {
        $url = config('app.url');
        if (!$user)
            return  "";
        return strpos($user->avatar, 'http') === 0 ?
            $user->avatar
            : ($user->avatar === null ? $url . '/' . config('_custom.user_default_image') : "$url/storage/{$user->avatar}");
    }

    protected function getFullImageUrl($url)
    {
        if ($url)
            return config('app.url') . "/storage/{$url}";
        return "";
    }
}