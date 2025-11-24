<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseApiRequest;

class CreateAppVersionRequest extends BaseApiRequest
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
            'version' => 'required|string|max:255|unique:app_versions,version',
            'url' => 'required_without:file|string|max:255',
            'file' => 'required_without:url|mimes:apk,zip',
            'is_force_update' => 'required|boolean',
            'app_type' => 'required|string|in:student,teacher,parent',
            'description' => 'nullable|string|max:255',
        ];
    }
}
