<?php

namespace App\Http\Requests\Administration\Teacher;

use App\Enums\LevelEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateTeacherRequest extends BaseApiRequest
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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone_number' => ['required', 'string', Rule::unique('users', 'phone_number')->where('role_id', 3)],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email'],
            'bio' => ['sometimes', 'max:6000'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'is_male' => ['sometimes', 'boolean'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
