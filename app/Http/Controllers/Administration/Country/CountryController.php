<?php

namespace App\Http\Controllers\Administration\Country;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Country\CountryService;
use App\Http\Resources\Country\CountryResource;
use App\Http\Requests\Country\CreateCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;

class CountryController extends Controller
{
    public function __construct(
        protected CountryService $countryService,
    ) {}

    public function index(Request $request)
    {
        return success(
            $this->countryService->getAll($request->all()),
            ApiMessages::MSG_SUCCESS,
            CountryResource::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->countryService->show($id),
            ApiMessages::MSG_SUCCESS,
            CountryResource::class
        );
    }

    public function store(CreateCountryRequest $request)
    {
        return createdSuccess(
            $this->countryService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
            CountryResource::class
        );
    }

    public function update(UpdateCountryRequest $request, $id)
    {
        return success(
            $this->countryService->update($request->validated(), $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function destroy($id)
    {
        return success(
            $this->countryService->destroy($id),
            ApiMessages::MSG_SUCCESS
        );
    }
}
