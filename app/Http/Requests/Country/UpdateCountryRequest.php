<?php

namespace App\Http\Requests\Country;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class UpdateCountryRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:255' , Rule::unique('contries', 'name')->ignore($this->route('id'))],
            'country_code' => ['sometimes', 'string', 'min:2', 'max:255' , Rule::unique('contries', 'country_code')->ignore($this->route('id'))],
        ];
    }
}
