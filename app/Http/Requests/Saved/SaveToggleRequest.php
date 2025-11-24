<?php

namespace App\Http\Requests\Saved;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class SaveToggleRequest extends BaseApiRequest
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
            'context_id' => ['required', 'integer', 'min:1'],
            'context_type' => ['required', 'string', Rule::in('Lesson' , 'Question')],
        ];
    }
}
