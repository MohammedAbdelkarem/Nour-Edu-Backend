<?php

namespace App\Http\Requests\System\Info;

use App\Enums\ContactTypes;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class ContactUsRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'store'     =>  $this->storeRules(),
            'update'    =>  $this->updateRules(),
        };
    }

    public function storeRules(): array
    {
        return [
            "link"      => ["required", "string", "unique:contact_us,link", 'between:2,600'],
            "type"      => ["required", "string", Rule::in(ContactTypes::values())],
        ];
    }

    public function updateRules(): array
    {
        return [
            "link"      => ["required", "string", 'between:2,600', Rule::unique('contact_us', 'link')->ignore($this->id)],
            "type"      => ["required", "string", Rule::in(ContactTypes::values())],
        ];
    }

    public function messages()
    {
        return [];
    }
}
