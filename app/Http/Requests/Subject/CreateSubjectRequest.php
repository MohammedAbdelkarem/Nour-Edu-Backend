<?php

namespace App\Http\Requests\Subject;

use App\Enums\PublishStatusEnum;
use App\Enums\AccessTypeEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubjectRequest extends BaseApiRequest
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
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'min:2', 'max:255', Rule::unique('subjects')->where('course_id', request('course_id'))],
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
            'video' => [
                'sometimes',
                'mimes:mp4,webm,mov,avi',
                'max:4096'
            ],
        ];
    }
}
