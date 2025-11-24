<?php

namespace App\Http\Requests\System\CustomerServiceCard;

use App\Constants\ExceptionMessages;
use App\Enums\CustomerServiceCard\CustomerServiceCardTypes;
use App\Exceptions\ApiException;
use App\Http\Requests\BaseApiRequest;
use App\Models\System\CustomerService\CustomerServiceCard;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CustomerServiceCardRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'store'  => $this->storeRules(),
            'update' => $this->updateRules(),
            'close'  => $this->closeRules(),
        };
    }

    public function prepareForValidation()
    {
        //Check Time Between Customer Service Cards Creation
        if ($this->route()->getActionMethod() == 'store')
            if ($card = CustomerServiceCard::withTrashed()
                ->where('user_id', auth()->id())
                ->where('created_at', '>', Carbon::now()->subMinutes(config('_custom.time_between_store_service_cards')))
                ->latest()->first()
            )
                throw new ApiException(
                    message: trans(
                        'exception_messages.time_store_customer_card',
                        ['minutes' => Carbon::now()->diffInMinutes(Carbon::parse($card->created_at)->addMinutes(config('_custom.time_between_store_service_cards')))]
                    ),
                    statusCode: 400,
                );
    }

    public function storeRules(): array
    {
        return [
            'title'         => ['required', 'string', 'between:5,255'],
            'description'   => ['required', 'string', 'between:5,3000'],
            'type'          => ['required', Rule::in(CustomerServiceCardTypes::values())],
            'date'          => ['required_if:type,' . CustomerServiceCardTypes::BUG->value, 'nullable', 'date'],
            'media'         => ['nullable', 'array', 'max:' . config('_custom.max_media_per_customer_card')],
            'media.*'       => [
                'required',
                'file',
                'image',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4096,max_height=4096',
                'mimes:png,jpg,jpeg,webpm'
            ],
        ];
    }

    public function updateRules(): array
    {
        return [
            'title'             => ['required', 'string', 'between:5,255'],
            'description'       => ['required', 'string', 'between:5,3000'],
            'type'              => ['required', Rule::in(CustomerServiceCardTypes::values())],
            'date'              => ['required_if:type,' . CustomerServiceCardTypes::BUG->value, 'nullable', 'date'],
            'delete_media'      => ['nullable', 'array'],
            'delete_media.*'    => ['required', Rule::exists('customer_card_media', 'id')->where('card_id', $this->id)],
            'media'             => ['nullable', 'array', 'max:' . config('_custom.max_media_per_customer_card')],
            'media.*'           => [
                'required',
                'file',
                'image',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4096,max_height=4096',
                'mimes:png,jpg,jpeg,webpm'
            ],
        ];
    }

    public function closeRules()
    {
        return [
            'card_id'   => ['required', Rule::exists('customer_cards', 'id')->whereNull('admin_answer')],
            'answer'    => ['required', 'string', 'max:2000'],
        ];
    }
    

    public function passedValidation()
    {
        if ($this->route()->getActionMethod() == 'update' && auth()->user()->isUser()) {
            $card = CustomerServiceCard::where("user_id", auth()->id())->findOrFail($this->id);
            //Count What Media To Delete
            $toDeleteMedia = isset($this['delete_media'])
                ? $card->media()->whereIn('id', $this->delete_media)->count() :
                0;

            //Count New Media
            $newMedia = isset($this['media'])
                ? count($this->media) :
                0;

            //Count Exists Media
            $existsMedia = $card->media()->count();

            if ($existsMedia - $toDeleteMedia + $newMedia  > config('_custom.max_media_per_customer_card')) {
                throw new HttpResponseException(response()->json([
                    'status_code' => 422,
                    'message' => trans(ExceptionMessages::MSG_MAX_MEDIA_COUNT, ['count' => config('_custom.max_media_per_customer_card')]),
                    'data' => null
                ], 422));
            }
        }
    }

    public function messages(): array
    {
        return [];
    }
}
