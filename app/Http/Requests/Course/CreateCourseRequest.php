<?php

namespace App\Http\Requests\Course;

use App\Enums\PublishStatusEnum;
use App\Enums\AccessTypeEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateCourseRequest extends BaseApiRequest
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
            'e_level_id' => ['required', 'exists:e_levels,id'],
            'c_level_id' => ['required', 'exists:c_levels,id'],
            'name' => ['required', 'string', 'min:2', 'max:255', Rule::unique('courses')->where('c_level_id', request('c_level_id'))],
            'bio' => ['sometimes', 'string'],
            'price' => ['sometimes', 'integer', 'min:1'],
            'access_type' => ['required', Rule::in(AccessTypeEnum::values())],
            // 'publish_status' => ['sometimes', Rule::in(PublishStatusEnum::values())],
            'image' => [
                'sometimes',
                'mimes:png,jpg,jpeg,webp,svg',
                'max:4096'
            ],
            'icon' => [
                'sometimes',
                'mimes:svg',
                'max:4096'
            ],
        ];
    }
}
