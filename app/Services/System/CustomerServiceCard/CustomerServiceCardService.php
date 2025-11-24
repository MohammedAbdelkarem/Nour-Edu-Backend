<?php

namespace App\Services\System\CustomerServiceCard;

use App\Constants\NotificationMessages;
use App\Constants\Resources;
use App\Enums\CustomerServiceCard\CustomerServiceCardStatus;
use App\Enums\CustomerServiceCard\CustomerServiceCardTypes;
use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
use App\Models\System\CustomerService\CustomerServiceCard;
use App\Services\MainService;
use Illuminate\Support\Facades\DB;

/**
 * Class CustomerServiceCardService.
 */
class CustomerServiceCardService extends MainService
{
    public function getTypesStatus()
    {
        return [
            "types" => CustomerServiceCardTypes::transValues(),
            "status" => CustomerServiceCardStatus::transValues(),
        ];
    }

    public function indexUser($per_page, $search, $type = null, $status = null, bool $my)
    {
        $user = auth()->user();

        return CustomerServiceCard::query()
            ->where("user_id", auth()->id())
            ->when($status, function ($query) use ($status) {
                if ($status == CustomerServiceCardStatus::PENDING->value)
                    $query->whereNull('admin_answer');
                elseif ($status == CustomerServiceCardStatus::CLOSED->value)
                    $query->whereNotNull('admin_answer');
            })
            ->when($type, function ($query) use ($type) {
                $query->where("type", $type);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['title', 'description'], 'like', '%' . strtolower($search) . '%');
            })
            ->with([
                "user" => fn($q) => $q->withTrashed(),
                'media'
            ])
            ->orderByDesc("created_at")
            ->paginate($per_page);
    }

    public function indexAdmin($per_page = 10, $search, $type = null, $status = null)
    {
        return CustomerServiceCard::query()->withTrashed()
            ->when($status, function ($query) use ($status) {
                if ($status == CustomerServiceCardStatus::PENDING->value)
                    $query->whereNull('admin_answer');
                elseif ($status == CustomerServiceCardStatus::CLOSED->value)
                    $query->whereNotNull('admin_answer');
            })
            ->when($type, function ($query) use ($type) {
                $query->where("type", $type);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['title', 'description'], 'like', '%' . strtolower($search) . '%');
            })
            ->with([
                "user" => fn($q) => $q->withTrashed(),
                'media'
            ])
            ->orderByDesc("created_at")
            ->paginate($per_page);
    }

    public function store($validatedData)
    {
        DB::beginTransaction();
        $card = CustomerServiceCard::create([
            "user_id"       => auth()->id(),
            "title"         => $validatedData["title"],
            "description"   => $validatedData["description"],
            "date"          => $validatedData["date"],
            "type"          => $validatedData["type"],
        ]);

        if (isset($validatedData["media"]))
            $this->storeMedia($card, $validatedData);

        DB::commit();
    }

    public function storeMedia($card, $validatedData)
    {
        foreach ($validatedData["media"] as $media) {
            $card->media()->create([
                "media_url" => $this->storeFile(
                    file: $media,
                    path: "customer_card/{$card->id}",
                ),
            ]);
        }
    }

    public function showUser($id)
    {
        return CustomerServiceCard::query()
            ->where("user_id", auth()->id())
            ->with([
                "user" => fn($q) => $q->withTrashed(),
                'media'
            ])
            ->findOrFail($id);
    }


    public function showAdmin($id)
    {
        return CustomerServiceCard::withTrashed()
            ->with([
                "user" => fn($q) => $q->withTrashed(),
                'media'
            ])
            ->findOrFail($id);
    }

    public function update($id, $validatedData)
    {
        $card = findByIdOrFail(CustomerServiceCard::class, $id, Resources::CARD, 'female', asQuery: true);
        if (auth()->user()->isUser())
            $card->whereNull('admin_answer')->where("user_id", auth()->id());
        $card = $card->firstOrFail();

        DB::beginTransaction();
        $card->update([
            "title"        => $validatedData["title"],
            "description"  => $validatedData["description"],
            "type"         => $validatedData["type"],
            "date"         => $validatedData["date"],
        ]);

        $this->updateMedia($card, $validatedData);

        DB::commit();
    }

    public function updateMedia($card, $validatedData)
    {
        //Delete Media
        if (isset($validatedData["delete_media"]))
            foreach ($validatedData["delete_media"] as $dm) {
                $dbMedia = $card->media()->where("id", $dm)->first();
                $this->storageDelete($dbMedia->media_url ?? "");
                $dbMedia->delete();
            }

        //Add New Media
        if (isset($validatedData["media"]) && auth()->user()->isUser())
            foreach ($validatedData["media"] as $media) {
                $card->media()->create([
                    "media_url" => $this->storeFile(
                        file: $media,
                        path: "customer_card/{$card->id}",
                    ),
                ]);
            }
    }

    public function close($validatedData)
    {
        $card = findByIdOrFail(CustomerServiceCard::class, $validatedData['card_id'], Resources::CARD, 'female');
        $card->admin_answer = $validatedData['answer'];
        $card->save();

         $this->sendDirectNotification(
            $card->user_id,
            $this->notificationMessage(NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_TITLE),
            $this->notificationMessage(NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_BODY, ["name" => $card->title]),
            NotificationTypes::COMPLAINTS->value,
            'ar',
            false,
            "",
            [],
            true,
            [],
            true
        );
    }

    public function destroy($id) //Soft delete
    {
        findByIdOrFail(
            CustomerServiceCard::class,
            $id,
            Resources::CARD,
            'female',
            ["user_id" => auth()->id()]
        )->delete();
    }

    public function destroyByAdmin($id)
    {
        $card = findByIdOrFail(
            CustomerServiceCard::class,
            $id,
            Resources::CARD,
            'female',
        );

        $card->delete();

        $this->sendDirectNotification(
            $card->user_id,
            $this->notificationMessage(NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_TITLE),
            $this->notificationMessage(NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_BODY, ["name" => $card->title]),
            NotificationTypes::COMPLAINTS->value,
            'ar',
            false,
            "",
            [],
            true,
            [],
            true
        );
    }
}