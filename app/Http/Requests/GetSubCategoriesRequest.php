<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseApiRequest;

class GetSubCategoriesRequest extends BaseApiRequest
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
            'per_page'                  => ['integer'],
            'page'                      => ['integer'],
            'category_ids'             => ['nullable', 'array'],
            'category_ids.*'           => ['nullable', 'exists:categories,id'],
        ];
    }
}
