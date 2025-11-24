<?php

namespace App\Http\Requests\Story;

use App\Enums\LevelEnum;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateStoryRequest extends BaseApiRequest
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
            'title' => ['required', 'string', 'min:4' , 'max:255'],
            'description' => ['present' , 'nullable', 'string'],
            'end_at' => ['required', 'date'],
            //can specify the rules for the id and the type depending on the project(exist , unique , ....)
            'storiable_id' => ['present' ,'nullable', 'min:1'],
            'storiable_type' => ['present' ,'nullable'],
            'external_link' => ['present' ,'nullable' , 'max:255'],
            'video'                      => [
                'nullable',
                'mimes:avi,mpeg,quicktime,mp4,mov,wmv',
                'max:51200' //50 mb
            ],
            'image'                      => [
                'required',
                'mimes:jpeg,jpg,png,webp',
                'max:4096'
            ],
        ];
    }
}
