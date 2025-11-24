<?php

namespace App\Services\System\Info;

use App\Services\MainService;
use App\Models\System\Info\AboutUs;
use Illuminate\Support\Facades\DB;

class AboutUsService extends MainService
{
    public function index()
    {
        return AboutUs::with('updated_by')->get();
    }

    public function store($validatedData)
    {
        AboutUs::updateOrCreate(
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
        if (!$about = AboutUs::with('updated_by')->where("lang", strtolower($lang))->first())
            $about = AboutUs::with('updated_by')->where("lang", strtolower('en'))->firstOrFail();
        return $about;
    }
}