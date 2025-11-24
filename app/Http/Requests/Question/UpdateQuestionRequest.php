<?php

namespace App\Http\Requests\Question;

use App\Enums\QuestionTypeEnum;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends BaseApiRequest
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
            'text' => ['required', 'string', 'min:10', 'max:1000'],
            'hint' => ['nullable', 'string', 'max:500'],
            'type' => ['required', Rule::in(QuestionTypeEnum::values())],
            'answers' => ['required', 'array', 'min:2', 'max:10'],
            'answers.*.text' => ['required', 'string', 'min:2', 'max:500'],
            'answers.*.is_correct' => ['required', 'boolean'],
            'answers.*.priority' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
