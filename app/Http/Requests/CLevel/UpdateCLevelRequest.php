<?php

namespace App\Http\Requests\CLevel;

use App\Enums\PublishStatusEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCLevelRequest extends BaseApiRequest
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
            'name' => ['sometimes', 'string', 'min:2', 'max:255', Rule::unique('c_levels')->where('e_level_id', request('e_level_id'))->ignore($this->route('id'))],
            'bio' => ['sometimes', 'string'],
            // 'publish_status' => ['sometimes', Rule::in(PublishStatusEnum::values())],
        ];
    }
}
