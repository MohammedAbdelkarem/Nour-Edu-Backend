<?php

namespace App\Http\Requests\Responsibility;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class LinkingRequest extends BaseApiRequest
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
            'context_id' => ['required'],
            'context_type' => ['required' , 'string' , Rule::in('E_Level', 'C_Level', 'Course', 'Subject', 'Unit', 'Sub_Unit', 'Lesson')],
            'teacher_ids' => ['required', 'array'],
            'teacher_ids.*' => ['required', Rule::exists('users', 'id')->where('role_id', 3)],
        ];
    }

    public function messages(): array
    {
        return [
            'context_type.in' => 'The context type must be one of the following:E_Level, C_Level, Course, Subject, Unit, Sub_Unit, Lesson',
        ];
    }
}
