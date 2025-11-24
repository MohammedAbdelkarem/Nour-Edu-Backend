<?php

namespace App\Http\Requests\System\Info;

use App\Enums\AppTypes;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class FaqCategoryRequest extends BaseApiRequest
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
            "name"   => ["required", "string", "max:255", "unique:faq_categories,name"],
            "app"    => ["required", Rule::in(AppTypes::values())],
        ];
    }

    public function updateRules()
    {
        return [
            "name"      => ["required", "string", "max:255", Rule::unique("faq_categories", "name")->ignore($this->id)],
            "app"       => ["required", Rule::in(AppTypes::values())],
        ];
    }

    public function messages()
    {
        return [];
    }
}