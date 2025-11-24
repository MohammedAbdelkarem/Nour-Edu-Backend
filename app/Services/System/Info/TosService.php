<?php

namespace App\Services\System\Info;

use App\Models\System\Info\Tos;
use App\Services\MainService;
use Illuminate\Support\Facades\DB;

class TosService extends MainService
{
    public function index()
    {
        return Tos::with('updated_by')->get();
    }

    public function store($validatedData)
    {
        Tos::updateOrCreate(
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
            $lang = strtolower((app()->getLocale()));
        if (!$tos = Tos::where("lang", strtolower($lang))->first())
            $tos = Tos::where("lang", strtolower('en'))->firstOrFail();
        return $tos;
    }
}