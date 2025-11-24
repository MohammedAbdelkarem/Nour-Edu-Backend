<?php

namespace App\Http\Requests\Copon;

use App\Http\Requests\BaseApiRequest;

class CreateManyPointsCoponRequest extends BaseApiRequest
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
            'amount' => ['required', 'integer', 'min:1' , 'max:5000000'],
            'number_of_max_uses' => ['required', 'integer', 'min:1' , 'max:1000'],
            'expired_at' => ['required', 'date'],
        ];
    }
}
