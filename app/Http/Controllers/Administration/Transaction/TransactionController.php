<?php

namespace App\Http\Controllers\Administration\Transaction;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Copon\CreateManyContextCoponRequest;
use App\Http\Requests\Copon\CreateManyPointsCoponRequest;
use App\Http\Requests\Copon\CreateOneContextCoponRequest;
use App\Http\Requests\Copon\CreateOnePointsCoponRequest;
use App\Services\Transaction\TransactionService;
use App\Http\Requests\Transaction\CreateCuponRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Http\Requests\Transaction\CreateContextCuponRequest;
use App\Http\Resources\Copon\CopnoResource;
use App\Services\Copon\CoponService;

class TransactionController extends Controller
{
    public function __construct(
        protected CoponService $coponService,
        protected TransactionService $transactionService
    ) {}

    public function get(Request $request)
    {
        return success(
            $this->transactionService->getAdminTransactions($request->all()),
            ApiMessages::MSG_SUCCESS,
            TransactionResource::class,
            $request->has('per_page')
        );
    }
    public function createOnePointsCopon(CreateOnePointsCoponRequest $request)
    {   
        return success(
            $this->coponService->createOnePointsCupon($request->validated()),
            ApiMessages::MSG_SUCCESS,
            CopnoResource::class,
        );
    }

    public function createManyPointsCopon(CreateManyPointsCoponRequest $request)
    {
        return success(
            $this->coponService->createManyPointsCupon($request->validated()),
            ApiMessages::MSG_SUCCESS,
            CopnoResource::class,
        );   
    }
    public function createOneContextCopon(CreateOneContextCoponRequest $request)
    {
        return success(
            $this->coponService->createOneContextCupon($request->validated()),
            ApiMessages::MSG_SUCCESS,
            CopnoResource::class,
        );   
    }

    public function createManyContextCopon(CreateManyContextCoponRequest $request)
    {
        return success(
            $this->coponService->createManyContextCupon($request->validated()),
            ApiMessages::MSG_SUCCESS,
            CopnoResource::class,
        );
    }
        

    public function setCoponsAsExpired(Request $request)
    {   
        return success(
            $this->coponService->setAsExpired($request->only('ids')),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function getCopons(Request $request)
    {
        return success(
            $this->coponService->get($request->all()),
            ApiMessages::MSG_SUCCESS,
            CopnoResource::class,
            $request->has('per_page')
        );
    }
}