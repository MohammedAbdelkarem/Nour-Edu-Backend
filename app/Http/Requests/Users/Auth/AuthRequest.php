<?php

namespace App\Http\Requests\Users\Auth;

use App\Enums\GenderEnum;
use App\Rules\PhoneNumberRule;
use App\Rules\ShiftsOverlappingRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class AuthRequest extends BaseApiRequest
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
        return match ($this->route()->getActionMethod()) {
            "registerStudent" => $this->registerStudentRules(),
            "loginStudent" => $this->loginStudentRules(),
            "loginParent" => $this->loginParentRules(),
            "loginTeacher" => $this->loginTeacherRules(),
        };
    }

    public function registerStudentRules()
    {
        return [
            'phone_number' => ['required', new PhoneNumberRule() , Rule::unique('users' , 'phone_number')->where('role_id' , 5)],
            'parent_phone_number' => ['required', new PhoneNumberRule()],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email',  Rule::unique('users' , 'email')],
            'birth_date' => ['nullable', 'date'],
            'is_male' => ['nullable', 'bool'],
            'contry_id' => ['required', 'exists:contries,id'],
            'e_level_id' => ['required', 'exists:e_levels,id'],
            'c_level_id' => ['required', 'exists:c_levels,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
    public function loginStudentRules()
    {
        return [
             "phone_number" => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 5)],
        ];
    }
    public function loginParentRules()
    {
        return [
             "phone_number" => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 4)],
        ];
    }

    public function loginTeacherRules()
    {
        return [
             "phone_number" => ['required', new PhoneNumberRule() , Rule::exists('users' , 'phone_number')->where('role_id' , 3)],
        ];
    }

    public function messages()
    {
        return [];
    }
}
