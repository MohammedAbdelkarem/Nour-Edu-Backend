<?php

namespace App\Services\Users\Auth;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Services\OTPService;
use App\Services\MainService;
use App\Exceptions\ApiException;
use App\Models\JWTPersonalTokens;
use App\Constants\MediaCollection;
use App\Services\JWTTokensService;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Constants\ExceptionMessages;
use App\Enums\PublishStatusEnum;
use App\Models\NotificationManagement;
use App\Services\Doctor\DoctorService;
use App\Models\Users\Profile\UserDevice;
use App\Models\Users\Profile\ArchivedUser;
use App\Models\Users\Profile\LoginHistory;
use App\Services\Base\ContextService;
use App\Services\Plan\PlanService;

/**
 * Class AuthService.
 */
class AuthService extends MainService

{
    public function __construct(
        protected OTPService $OTPService,
        protected JWTTokensService $jwtService,
        protected ContextService $contextService,
    ) {}
    public function registerStudent($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["parent_phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
            throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create or get the parent
        $parent = User::firstOrCreate(
            [
                'phone_number' => $validatedData['parent_phone_number'],
                'role_id' => 4
                ],
            [
                'phone_number' => $validatedData['parent_phone_number'],
                'role_id' => 4,
                'language' => config("app.locale"),
                ]
            );
        
        $parent->save();

        //create the student
        $student = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 5,
            'language' => config("app.locale"),
            'parent_id' => $parent->id, //link the student to the parent
            'e_level_id' => $validatedData['e_level_id'],
            'c_level_id' => $validatedData['c_level_id'],
            'name' => $validatedData['name'],
            'birth_date' => $validatedData['birth_date'],
            'is_male' => $validatedData['is_male'],
            'email' => $validatedData['email'],
        ]);

        if (isset($validatedData['image'])) 
            uploadFileOnMedia($validatedData['image'] , $student, MediaCollection::USER_COLLECTION);

        //Send otp
        $otp = $this->OTPService->createOTP($student->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($student);

        $data = [
            "otp"    => config("app.env") == "local" ? (string) $otp->otp : $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "student"   => [
                "id" => $student->id,
                "student_phone_number" => $student->phone_number,
            ],
        ];

        return $data;
    }
    public function loginForStudent($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 5)->first();
        
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => config("app.env") == "local" ? (string) $otp->otp : $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function loginForParent($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 4)->first();
        
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => config("app.env") == "local" ? (string) $otp->otp : $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function loginForTeacher($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 3)->first();
        
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => config("app.env") == "local" ? (string) $otp->otp : $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }

    public function activeSessions()
    {
        return LoginHistory::where('user_id', auth()->id())
            ->whereHas('token', function ($q) {
                $q->where('expire_at', '>', Carbon::now());
            })
            ->orderByDesc('created_at')
            ->get();
    }

    public function logoutSessions($ids)
    {
        $this->jwtService->invalidateSessionByDevice($ids);
    }

    public function logout($notiToken)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $this->jwtService->InvalidateTokenWithRelated(JWTAuth::getToken());
        if ($notiToken)
            $user->userDevices()->where('notification_token', $notiToken)->delete();
        
        if(auth()->user()->isStudent())
            $this->contextService->clearDownloads(auth()->id());
    }

    public function logoutAllDevices()
    {
        $user = auth()->user();

        $this->jwtService->InvalidateAllTokensByUserID($user->id);
        UserDevice::where('user_id', $user->id)->delete();
    }

    public function refresh()
    {
        $loginHistoryId = JWTPersonalTokens::where('token', JWTAuth::getToken())
            ->first()?->access_token()->first()?->login_history;

        $this->jwtService->InvalidateTokenWithRelated(JWTAuth::getToken());
        $data['tokens'] = $this->generateTokens(auth()->user(), $loginHistoryId);
        return $data;
    }

    public function generateTokens($user, $loginHistoryId)
    {
        $accessExpireIn = Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp;
        $refreshExpireIn = Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->timestamp;

        $accessToken  = JWTAuth::customClaims([
            'exp'               => $accessExpireIn,
            'api_access'        => true,
            'refresh_access'    => false,
        ])->fromUser($user);
        $refreshToken = JWTAuth::customClaims([
            'exp'               => $refreshExpireIn,
            'api_access'        => false,
            'refresh_access'    => true,
        ])->fromUser($user);

        //Store Tokens In DB
        $accessTokenDB  = $this->jwtService->store($accessToken, null, $loginHistoryId);
        $this->jwtService->store($refreshToken, $accessTokenDB->id);

        return [
            "access_token"      => $accessToken,
            "refresh_token"     => $refreshToken,
            "access_expire_in"  => $accessExpireIn,
            "refresh_expire_in" => $refreshExpireIn,
        ];
    }

    /**
     * Generate Token for otp request only
     * No need to store the token into DB because it's only for otp verification
     */
    public function generateLoginToken($user)
    {
        $accessExpireIn = Carbon::now()->addMinutes(config('jwt.otp_ttl'))->timestamp;

        $accessToken  = JWTAuth::customClaims([
            'exp'               => $accessExpireIn,
            'otp_access'        => true,
            'api_access'        => false,
            'refresh_access'    => false,
        ])->fromUser($user);

        return [
            "access_token"      => $accessToken,
            "access_expire_in"  => $accessExpireIn,
        ];
    }
}
