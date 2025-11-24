<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\ApiMessages;
use App\Services\Complaint\ComplaintService;
use App\Http\Requests\Complaint\CreateComplaintRequest;

class ComplaintController extends Controller
{
    public function __construct(
        protected ComplaintService $complaintService,
    ) {}

    public function complaint(CreateComplaintRequest $request)
    {
        return createdSuccess(
            $this->complaintService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function process($id)
    {
        return createdSuccess(
            $this->complaintService->process($id),
            ApiMessages::MSG_SUCCESS,
        );
    }

}
