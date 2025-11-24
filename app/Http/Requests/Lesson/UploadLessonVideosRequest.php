<?php

namespace App\Http\Requests\Lesson;

use App\Http\Requests\BaseApiRequest;

class UploadLessonVideosRequest extends BaseApiRequest
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
            'duration' => ['required', 'integer', 'min:1'],
            'video' => [
                'required',
                'mimes:mp4,webm,mov,avi',
                'max:5368709120', // 5GB
            ],
            'quality' => [
                'required',
                'in:144,240,360,480,720,1080',
            ],
        ];
    }
}
