<?php

namespace App\Services\System\Info;

use App\Constants\Resources;
use App\Models\System\Info\FAQ;
use App\Models\System\Info\FaqCategory;
use App\Services\MainService;

class FAQService extends MainService
{
    public function index($per_page, $category_id = null, $search = null)
    {
        return FaqCategory::query()
            ->when($category_id, function ($query) use ($category_id) {
                $query->where('id', $category_id);
            })
            ->withWhereHas('faqs', function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->whereAny(['question', 'answer'], 'like', "%" . $search . "%");
                });
                if (auth()->user()?->isAdmin()) {
                    $query->with([
                        "category"  => fn($q) => $q->select('id', 'name'),
                        "updater"   => fn($q) => $q->select('id', 'name')
                    ]);
                }
                if (!auth()->user()?->isAdmin()) {
                    $query->where('is_draft', 0);
                }
            })
            ->with('updater')
            ->paginate($per_page);
    }

    public function store($validatedData)
    {
        FAQ::create(
            [
                'question'  => $validatedData["question"],
                'answer'    => $validatedData["answer"],
                "faq_category_id" => $validatedData["category_id"],
                "is_draft"  => $validatedData["is_draft"],
                "update_by" => auth()->id(),
            ]
        );
    }

    public function show($id)
    {
        return findByIdOrFail(FAQ::class, $id, 'male' , Resources::ITEM);
    }

    public function update($validatedData, $id)
    {
        $faq = findByIdOrFail(FAQ::class, $id , 'male' , Resources::ITEM);

        $faq->update([
            'question'  => $validatedData["question"],
            'answer'    => $validatedData["answer"],
            "faq_category_id" => $validatedData["category_id"],
            "is_draft"  => $validatedData["is_draft"],
            "update_by" => auth()->id(),
        ]);
    }

    public function destroy($id)
    {
        findByIdOrFail(FAQ::class, $id , 'male' , Resources::ITEM)->delete();
    }
}
