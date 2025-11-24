<?php

namespace App\Services\System\Info;

use App\Enums\AppTypes;
use App\Models\System\Info\FaqCategory;
use Illuminate\Support\Facades\DB;

/**
 * Class FaqCategoryService.
 */
class FaqCategoryService
{
    public function apps()
    {
        return AppTypes::values();
    }

    public function index($per_page, $search, $app)
    {
        return FaqCategory::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%" . $search . "%");
            })
            ->when($app, function ($query) use ($app) {
                $query->where("app", $app);
            })
            ->with('updater')
            ->paginate($per_page);
    }

    public function store($validatedData)
    {
        $cat = FaqCategory::create([
            'name'      => $validatedData["name"],
            "app"       => $validatedData["app"],
            "update_by" => auth()->id(),
        ]);
        return [
            "id"    => $cat->id,
            "name"  => $cat->name,
        ];
    }

    public function show($id)
    {
        return FaqCategory::with([
            "updater" => fn($q) => $q->select('id', 'name'),
            "faqs" => fn($q) => $q->with([
                "category"  => fn($q) => $q->select('id', 'name'),
                "updater"   => fn($q) => $q->select('id', 'name')
            ])
        ])
            ->findOrFail($id);
    }

    public function update($id, $validatedData)
    {
        $category = FaqCategory::findOrFail($id);

        $category->update([
            'name'      => $validatedData["name"],
            "app"       => $validatedData["app"],
            "update_by" => auth()->id(),
        ]);
    }

    public function destroy($id)
    {
        FaqCategory::findOrFail($id)->delete();
    }
}
