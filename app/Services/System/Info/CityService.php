<?php

namespace App\Services\System\Info;

use App\Constants\ExceptionMessages;
use App\Constants\Resources;
use App\Exceptions\ApiException;
use App\Models\System\Info\City;
use App\Services\MainService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class CityService.
 */
class CityService extends MainService
{
    public function index($per_page, $search = null)
    {
        $cities = City::query()
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', strtolower($search) . '%');
            });
        return $per_page > 0 ? $cities->paginate($per_page) : $cities->get();
    }

    public function store($validatedData)
    {
        City::create([
            "name" => $validatedData["name"],
        ]);
    }

    public function show($id)
    {
        return findByIdOrFail(City::class, $id, Resources::CITY, 'female');
    }

    public function update($validatedData, $id)
    {
        $city = findByIdOrFail(City::class, $id, Resources::CITY, 'female');
        $city->name = $validatedData["name"];
        $city->save();
    }

    public function destroy($id)
    {
        $city = City::withCount('stores')->findOrFail($id);
        if ($city->stores_count > 0)
            throw new ApiException(
                message: trans(ExceptionMessages::MSG_CANNOT_DELETE_THIS_ITEM),
                statusCode: 400,
            );
        $city->delete();
    }
}
