<?php

namespace App\Services\System\Info;

use App\Enums\ContactTypes;
use App\Constants\Resources;
use App\Services\MainService;
use App\Rules\PhoneNumberRule;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use App\Constants\ExceptionMessages;
use App\Models\System\Info\ContactUs;
use Illuminate\Support\Facades\Validator;


class ContactUsService extends MainService
{
    public function index()
    {
        return ContactUs::query()->with("creator")->get();
    }

    public function types(): array
    {
        return ContactTypes::values();
    }

    public function store($validatedData)
    {
        if (!$this->validateURL($validatedData["link"], $validatedData["type"]))
            throw new ApiException(null, trans(ExceptionMessages::MSG_INVALID_URL), 400);

        ContactUs::create([
            "link"      => $validatedData["link"],
            "type"      => $validatedData["type"],
            "created_by" => auth()->id(),
        ]);
    }

    public function show($id)
    {
        return findByIdOrFail(ContactUs::class, $id , 'male' , Resources::ITEM);
    }

    public function update($validatedData, $id)
    {
        $contact = findByIdOrFail(ContactUs::class, $id, 'male' , Resources::ITEM);

        if (!$this->validateURL($validatedData["link"] ?? $contact->link, $validatedData["type"] ?? $contact->type))
            throw new ApiException(null, trans(ExceptionMessages::MSG_INVALID_URL), 400);

        $contact->link = $validatedData["link"];
        $contact->type = $validatedData["type"];

        $contact->created_by = auth()->id();
        $contact->save();
    }

    public function destroy($id)
    {
        findByIdOrFail(ContactUs::class, $id, 'male' , Resources::ITEM)->delete();
    }

    public function validateURL($url, $type)
    {
        //Email
        if ($type == "email") {
            $validator = Validator::make(['url' => $url], [
                'url' => 'required|email|string',
            ]);
            if (!$validator->fails())
                return true;
        } //Phone Number
        elseif ($type == "phone-number" || $type == "whatsApp") {
            $validator = Validator::make(['url' => $url], [
                'url' => ["required", "string", new PhoneNumberRule(true)],
            ]);
            if (!$validator->fails())
                return true;
        } else {
            if ((filter_var($url, FILTER_VALIDATE_URL)))
                return true;
        }
        return false;
    }
}
