<?php

namespace App\Http\Controllers\Mobile\Progress;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Services\Progress\ProgressService;

class ProgressController extends Controller
{
    public function __construct(
        protected ProgressService $progressService,
    ) {}

    public function updateStudyMinutes(Request $request)
    {
        return success(
            $this->progressService->updateStudyMinutes($request->minutes),
            ApiMessages::MSG_SUCCESS,
        );
    }
    public function getProgress()
    {
        return success(
            $this->progressService->progress(auth()->id()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function getLeaderboard()
    {
        return success(
            $this->progressService->leaderboard(),
            ApiMessages::MSG_SUCCESS,
            // UserResource::class,
        );
    }
}
