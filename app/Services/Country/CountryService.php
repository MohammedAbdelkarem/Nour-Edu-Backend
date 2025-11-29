<?php

namespace App\Services\Country;

use App\Models\Contry;
use App\Constants\MediaCollection;
use App\Services\Base\ContextService;

/**
 * Class CountryService.
 */
class CountryService
{
    public function __construct(
        protected ContextService $contextService
    ) {}

    public function getAll($data)
    {
        return getOrPaginate(
            Contry::orderBy('created_at', 'desc')
                    ->with(['eLevels' , 'cities']),
            $data
        );
    }

    public function getList()
    {
        return Contry::with('cities')->get();
    }

    public function show($id)
    {
        return Contry::findByIdOrFail($id, ['eLevels' , 'cities']);
    }

    public function store($data)
    {
        $contry = Contry::create($data);

        if (isset($data['image'])) {
            uploadFileOnMedia($data['image'], $contry, MediaCollection::CONTRY_FLAG_COLLECTION);
        }

        $contry->save();
    }

    public function update($data, $id)
    {
        $contry = Contry::findByIdOrFail($id);

        $contry->update($data);

        $contry->save();
    }

    public function destroy($id)
    {
        $contry = Contry::findByIdOrFail($id);

        $this->contextService->checkIfHasRegisterdStudentsBeforeDeleting($id , Contry::class);
        $this->contextService->checkIfHasContentBeforeDeleting($id , ELevel::class);
        
        $contry->delete();
    }
}
