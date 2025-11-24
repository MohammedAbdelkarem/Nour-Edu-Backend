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
        ];
    }

    public function messages()
    {
        return [];
    }
}