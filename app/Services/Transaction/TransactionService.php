<?php

namespace App\Services\Transaction;

use App\Models\Coupon;
use App\Models\Transaction;
use App\Enums\AccessTypeEnum;
use App\Enums\CouponTypeEnum;
use App\Models\UnlockedContext;
use App\Enums\TransactionTypeEnum;
use App\Constants\ExceptionMessages;
use App\Services\Purchase\PurchaseService;

/**
 * Class TransactionService.
 */
class TransactionService
{

    public function __construct(
        protected PurchaseService $purchaseService
    ){}

    public function usePointsCopon($copon_code)
    {
        $copon = Coupon::where('coupon', $copon_code)->first();
        if(!$copon)
            return notFoundFailure([] , ExceptionMessages::MSG_CUPON_NOT_FOUND);

        if($copon->user_id != null)
            return $this->useOnePointsCopon($copon_code);
        else
            return $this->useManyPointsCopon($copon_code);
    }
    public function useOnePointsCopon($cupon)
    {
        $cupon = Coupon::where('coupon', $cupon)
            ->where('is_expired', 0)
            ->where('type', CouponTypeEnum::STUDENT_ONE_TIME->value)
            ->first();

        if(!$cupon)
            return notFoundFailure([] , ExceptionMessages::MSG_CUPON_NOT_FOUND);
        
        if($cupon->user_id != auth()->id())
            return forbiddenFailure([] , ExceptionMessages::MSG_CUPON_NOT_FOUND);

        $cupon->update([
            'number_of_uses' => $cupon->number_of_uses + 1,
            'is_expired' => 1,
            'used_at' => now(),
        ]);

        auth()->user()->balance += $cupon->amount;
        auth()->user()->save();

        $transaction = Transaction::create([
            'amount' => $cupon->amount,
            'user_id' => auth()->id(),
            'coupon_id' => $cupon->id,
            'transaction_type' => TransactionTypeEnum::COUPON_CHARGE->value,
        ]);

        return $transaction;
    }

    public function useManyPointsCopon($cupon)
    {
        $cupon = Coupon::where('coupon', $cupon)
            ->where('is_expired', 0)
            ->where('type', CouponTypeEnum::STUDENT_ONE_TIME->value)
            ->first();

        if(!$cupon)
            return notFoundFailure([] , ExceptionMessages::MSG_CUPON_NOT_FOUND);

        $this->checkIfManyPointsCoponHasBeenUsed($cupon);

        $cupon->update([
            'number_of_uses' => $cupon->number_of_uses + 1,
        ]);

        if($cupon->number_of_uses >= $cupon->number_of_max_uses)
        {
            $cupon->update([
                'is_expired' => 1
            ]);
        }

        auth()->user()->balance += $cupon->amount;
        auth()->user()->save();

        $transaction = Transaction::create([
            'amount' => $cupon->amount,
            'user_id' => auth()->id(),
            'coupon_id' => $cupon->id,
            'transaction_type' => TransactionTypeEnum::COUPON_CHARGE->value,
        ]);

        return $transaction;
    }

    public function useContextCopon($cupon)
    {
        $cupon = Coupon::where('coupon', $cupon)
            ->where('is_expired', 0)
            ->whereIn('type', [CouponTypeEnum::CONTEXT_MANY_TIMES->value, CouponTypeEnum::CONTEXT_ONE_TIME->value])
            ->first();

        if(!$cupon)
            return notFoundFailure([] , ExceptionMessages::MSG_CUPON_NOT_FOUND);

        $this->checkIflreadyUnlockedForCupon($cupon);

        $unlockedContext = $this->purchaseService->unlockContexts($cupon->context_id, getModelByPath($cupon->context_type) , auth()->id());
        
        $cupon->update([
            'number_of_uses' => $cupon->number_of_uses + 1,
        ]);

        if($cupon->number_of_uses >= $cupon->number_of_max_uses)
        {
            $cupon->update([
                'is_expired' => 1
            ]);
        }
            
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'coupon_id' => $cupon->id,
            'transaction_type' => TransactionTypeEnum::COUPON_PURCHASE->value,
            'unlocked_context_id' => $unlockedContext->id,
        ]);

        return $transaction;
    }

    public function directPurchase($data , $student_id = null)
    {
        $context = getModel($data['context_type'])::findByIdOrFail($data['context_id']);
        $balance = auth()->user()->balance;

        $this->checkIflreadyUnlockedForDirectPurchase($context->id , getModel($data['context_type']) , $student_id ?? auth()->id());

        if($context->access_type == AccessTypeEnum::FREE->value)
        {
            $unlockedContext = $this->purchaseService->unlockContexts($context->id, getModel($data['context_type']), $student_id ?? auth()->id());
        }
        else
        {
            if($balance < $context->price)
                return forbiddenFailure([] , ExceptionMessages::MSG_INSUFFICIENT_BALANCE);
            
            $unlockedContext = $this->purchaseService->unlockContexts($context->id, getModel($data['context_type']), $student_id ?? auth()->id());

            auth()->user()->balance -= $context->price;
            auth()->user()->save();

            $transaction = Transaction::create([
                'user_id' => $student_id ?? auth()->id(),
                'amount' => $context->price,
                'transaction_type' => TransactionTypeEnum::DIRECT_PURCHASE->value,
                'unlocked_context_id' => $unlockedContext->id,
            ]);
        }
    }

    public function getStudentTransactions($data , $student_id)
    {
        return getOrPaginate(
            Transaction::where('user_id', $student_id)
            ->with('coupon', 'unlockedContext'),
            $data
        );
    }

    public function getAdminTransactions($data)
    {
        return getOrPaginate(
            Transaction::filter($data)
                ->with('coupon', 'unlockedContext' , 'user'),
            $data
        );
    }

    private function checkIflreadyUnlockedForCupon($cupon)
    {
        $alreadyUnlocked = UnlockedContext::where('user_id', auth()->id())
            ->where('context_id', $cupon->context_id)
            ->where('context_type', $cupon->context_type)
            ->first();

        if($alreadyUnlocked)
            return forbiddenFailure([] , ExceptionMessages::MSG_CONTEXT_ALREADY_UNLOCKED);
    }

    private function checkIflreadyUnlockedForDirectPurchase($context_id , $context_type , $student_id)
    {
        $alreadyUnlocked = UnlockedContext::where('user_id', $student_id)
            ->where('context_id', $context_id)
            ->where('context_type', $context_type)
            ->first();

        if($alreadyUnlocked)
            return forbiddenFailure([] , ExceptionMessages::MSG_CONTEXT_ALREADY_UNLOCKED);
    }

    private function checkIfManyPointsCoponHasBeenUsed($cupon)
    {
        $hasBeenUsed = $cupon->transactions()->where('user_id', auth()->id())->exists();
        if($hasBeenUsed)
            return forbiddenFailure([] , ExceptionMessages::MSG_CUPON_NOT_FOUND);
    }
}
