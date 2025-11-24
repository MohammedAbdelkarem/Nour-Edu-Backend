<?php

namespace App\Http\Requests\Copon;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseApiRequest;

class CreateOneContextCoponRequest extends BaseApiRequest
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
            'context_id' => ['required'],
            'context_type' => ['required', 'string' , Rule::in('Subject', 'Course')],
            'user_id' => ['required', 'exists:users,id'],
            'context_expired_at' => ['nullable', 'date'],
        ];
    }
}
