<?php

namespace App\Http\Requests\System\Info;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class AboutUsRequest extends BaseApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return match ($this->route()->getActionMethod()) {
            'store' =>  $this->storeRules(),
        };
    }

    public function storeRules(): array
    {
        return [
            "text" => ["required", "string", 'between:2,60000'],
            "lang" => ["required", "string", Rule::in(config("_custom.accepted_languages"))],
        ];
    }

    public function messages()
    {
        return [];
    }
}