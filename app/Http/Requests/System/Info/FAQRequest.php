<?php

namespace App\Http\Requests\System\Info;

use App\Http\Requests\BaseApiRequest;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Validation\Rule;

class FAQRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "store"  => $this->storeRules(),
            "update" => $this->updateRules(),
        };
    }

    public function storeRules()
    {
        return [
            "question"      => ["required", "string", "max:255", "unique:faq,question"],
            "answer"        => ["required", "string", "max:2000"],
            "category_id"   => ["required", "exists:faq_categories,id"],
            "is_draft"      => ['required', 'boolean'],
        ];
    }

    public function updateRules()
    {
        return [
            "question"      => ["required", "string", "max:255", Rule::unique("faq", "question")->ignore($this->id)],
            "answer"        => ["required", "string", "max:2000"],
            "category_id"   => ["required", "exists:faq_categories,id"],
            "is_draft"      => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [];
    }
}
