<?php

namespace App\Services\System\Info;

use App\Services\MainService;
use App\Models\System\Info\PrivacyPolicy;
use Illuminate\Support\Facades\DB;

class PrivacyPolicyService extends MainService
{
    public function index()
    {
        return PrivacyPolicy::with('updated_by')->get();
    }

    public function store($validatedData)
    {
        PrivacyPolicy::updateOrCreate(
            ["lang" => strtolower($validatedData["lang"])],
            [
                "text" => $validatedData["text"],
                "update_by" => auth()->id(),
            ]
        );
    }

    public function show($lang)
    {
        if (!$lang)
            $lang = strtolower(app()->getLocale());
        if (!$policy = PrivacyPolicy::Where("lang", strtolower($lang))->first())
            $policy = PrivacyPolicy::Where("lang", strtolower('en'))->firstOrFail();
        return $policy;
    }
}