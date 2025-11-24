<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;

class ChangePriorityRequest extends FormRequest
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
            'context' => 'required|array',
            'context.*' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'context.required' => 'The context array is required.',
            'context.array' => 'The context must be an array.',
            'context.*.required' => 'Each context priority is required.',
            'context.*.integer' => 'Each context priority must be an integer.',
            'context.*.min' => 'Each context priority must be at least 1.',
        ];
    }
}
