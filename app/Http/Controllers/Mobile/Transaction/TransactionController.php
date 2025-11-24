<?php

namespace App\Http\Controllers\Mobile\Transaction;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Transaction\TransactionService;
use App\Http\Requests\Transaction\UseCuponRequest;
use App\Http\Resources\Transaction\TransactionResource;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function usePointsCopon(UseCuponRequest $request)
    {
        return success(
            $this->transactionService->usePointsCopon($request->cupon),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function useContextCupon(UseCuponRequest $request)
    {
        $transaction = $this->transactionService->useContextCopon($request->cupon);
        
        return success(
            $transaction,
            ApiMessages::MSG_SUCCESS
        );
    }

    public function directPurchase(Request $request)
    {
        $request->validate([
            'context_type' => 'required|string',
            'context_id' => 'required|integer'
        ]);

        $transaction = $this->transactionService->directPurchase($request->all());
        
        return success(
            $transaction,
            ApiMessages::MSG_SUCCESS
        );
    }

    public function getStudentTransactions(Request $request , $student_id = null)
    {
        return success(
            $this->transactionService->getStudentTransactions($request->all(), $student_id ?? auth()->id()),
            ApiMessages::MSG_SUCCESS,
            TransactionResource::class,
            $request->has('per_page')
        );
    }
}