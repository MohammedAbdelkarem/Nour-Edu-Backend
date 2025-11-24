<?php

namespace App\Services\Copon;

use App\Enums\CouponTypeEnum;
use App\Enums\AccessTypeEnum;
use App\Constants\ExceptionMessages;
use App\Models\Coupon;

/**
 * Class CoponService.
 */
class CoponService
{
    public function createOnePointsCupon($data)
    {
        // dd($data['user_id']);
        $copon = Coupon::create([
            'coupon' => generateUniqueCoupon(),
            'type' => CouponTypeEnum::STUDENT_ONE_TIME,
            'amount' => $data['amount'],
            'user_id' => $data['user_id'],
        ]);

        return $copon->fresh();
    }
    public function createManyPointsCupon($data)
    {
        $copon = Coupon::create([
            'coupon' => generateUniqueCoupon(),
            'type' => CouponTypeEnum::STUDENT_ONE_TIME,
            'amount' => $data['amount'],
            'number_of_max_uses' => $data['number_of_max_uses'],
            'expired_at' => $data['expired_at'],
        ]);

        return $copon->fresh();
    }
    public function createOneContextCupon($data)
    {
        $context = getModel($data['context_type'])::find($data['context_id']);

        if($context->access_type == AccessTypeEnum::FREE->value)
            return forbiddenFailure([] , ExceptionMessages::MSG_CANNOT_CREATE_CUZ_CONTEXT_IS_FREE);

        $cupon = Coupon::create([
            'coupon' => generateUniqueCoupon(),
            'type' => CouponTypeEnum::CONTEXT_ONE_TIME,
            'user_id' => $data['user_id'],
            'context_id' => $data['context_id'],
            'context_type' => getModel($data['context_type']),
            'context_expired_at' => $data['context_expired_at'],
        ]);

        return $cupon->fresh();
    }
    public function createManyContextCupon($data)
    {
        $context = getModel($data['context_type'])::find($data['context_id']);

        if($context->access_type == AccessTypeEnum::FREE->value)
            return forbiddenFailure([] , ExceptionMessages::MSG_CANNOT_CREATE_CUZ_CONTEXT_IS_FREE);


        $cupon = Coupon::create([
            'coupon' => generateUniqueCoupon(),
            'type' => CouponTypeEnum::CONTEXT_MANY_TIMES,
            'expired_at' => $data['expired_at'],
            'context_id' => $data['context_id'],
            'context_type' => getModel($data['context_type']),
            'context_expired_at' => $data['context_expired_at'],
            'number_of_max_uses' => $data['number_of_max_uses'],
        ]);

        return $cupon->fresh();
    }
    public function get($data)
    {
        return getOrPaginate(
            Coupon::filter($data)->orderBy('created_at', 'desc')->with('context'),
            $data
        );
    }

    public function setAsExpired($data)
    {
        Coupon::whereIn('id', $data['ids'])
            ->update(['is_expired' => 1]);
    }

    public function delete($id)
    {
        $copon = Coupon::findByIdOrFail($id);

        if($copon->number_of_uses > 0)
            return forbiddenFailure([] , ExceptionMessages::MSG_CANNOT_DELETE_CUZ_HAS_USED);

        $copon->delete();
    }
}
