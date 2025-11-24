<?php

namespace App\Http\Requests\File;

use App\Enums\LevelEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateFileRequest extends FormRequest
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
            'title' => ['required' , 'string' , 'max:255'],
            'context_id' => ['required' , 'integer'],
            'context_type' => ['required' , 'string' , Rule::in('Subject', 'Unit', 'Sub_Unit', 'Lesson')],
            'file' => ['required' , 'max:10240' , 'file' , 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
        ];
    }

    public function messages(): array
    {
        return [
            'context_type.in' => 'The context type must be one of the following: Subject, Unit, Sub_Unit, Lesson',
        ];
    }
}
