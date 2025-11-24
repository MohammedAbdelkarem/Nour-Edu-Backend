<?php

namespace App\Http\Requests\Course;

use App\Enums\PublishStatusEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends BaseApiRequest
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
            'name' => ['sometimes', 'string', 'min:2', 'max:255', Rule::unique('courses')->where('c_level_id', request('c_level_id'))->ignore($this->route('id'))],
            'bio' => ['sometimes', 'string'],
            'price' => ['sometimes', 'integer', 'min:0'],
            // 'publish_status' => ['sometimes', Rule::in(PublishStatusEnum::values())],
        ];
    }
}
