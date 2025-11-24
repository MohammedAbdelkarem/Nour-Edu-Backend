<?php

namespace App\Http\Requests\Complaint;

use App\Enums\ComplaintEnum;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rules\Enum;

class CreateComplaintRequest extends BaseApiRequest
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
            'doctor_id' => ['required' , 'exists:doctors,id'],
            'patient_id' => ['required' , 'exists:patients,id'],
            'reservation_id' => ['nullable' , 'exists:reservations,id'],
            'value' => ['required_without:other_value' , new Enum(ComplaintEnum::class)],
            'other_value' => ['required_without:value' , 'string' , 'max:65000'],
            "images" => ['nullable' , 'array'],
            "images.*.image" => [
                'required',
               'mimes:jpeg,jpg,png,webp',
               'max:4096'
            ],
        ];
    }
}
