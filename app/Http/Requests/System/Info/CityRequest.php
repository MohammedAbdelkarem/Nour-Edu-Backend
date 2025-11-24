<?php

namespace App\Http\Requests\System\Info;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class CityRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            "store" => $this->storeRules(),
            "update" => $this->updateRules(),
        };
    }

    public function storeRules()
    {
        return [
            "name" => [
                "required",
                "string",
                'between:2,255',
                'unique:cities,name'
            ],
            "country_id" => [
                "required",
                "exists:contries,id"
            ],
        ];
    }

    public function updateRules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                'between:2,255',
                Rule::unique('cities', 'name')->ignore(request()->id)
            ],
            "country_id" => [
                "required",
                "exists:contries,id"
            ],
        ];
    }

    public function messages()
    {
        return [];
    }
}