<?php

namespace App\Http\Requests\Administration\Teacher;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class UpdateTeacherReqeust extends BaseApiRequest
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
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            // 'phone_number' => ['required', 'string', Rule::unique('users', 'phone_number')->where('role_id', 3)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('id'))],
            'bio' => ['sometimes', 'max:6000'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'is_male' => ['sometimes', 'boolean'],
            'image'                      => [
                'nullable',
                'mimes:jpeg,jpg,png,webp',
                'max:4096'
            ],
        ];
    }
}
