<?php

namespace App\Http\Requests\Quiz;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuizRequest extends BaseApiRequest
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
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'period' => ['required', 'integer', 'min:1', 'max:480'], // Max 8 hours
            'degree' => ['required', 'integer', 'min:1', 'max:1000'],
            'pass_degree' => ['required', 'integer', 'min:1', 'max:1000'],
            // 'questions' => ['required', 'array', 'min:1'],
            // 'questions.*.id' => ['required', 'integer', 'exists:questions,id'],
            // 'questions.*.priority' => ['required', 'integer', 'min:0'],
        ];
    }
}
