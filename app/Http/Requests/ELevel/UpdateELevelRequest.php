<?php

namespace App\Http\Requests\ELevel;

use App\Enums\PublishStatusEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateELevelRequest extends BaseApiRequest
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
            'name' => ['sometimes', 'unique:e_levels,name,' . $this->id, 'string', 'min:2', 'max:255'],
            'bio' => ['sometimes' , 'string'],
            // 'publish_status' => ['sometimes', Rule::in(PublishStatusEnum::values())],
        ];
    }
}
