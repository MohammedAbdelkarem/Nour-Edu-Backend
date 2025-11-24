<?php

namespace App\Services\Answer;

use App\Models\Answer;
use App\Services\MainService;
use App\Services\Base\ContextService;

class AnswerService extends MainService
{
    public function __construct(
        protected ContextService $contextService
    ) {}
    public function changePriority($contextsData)
    {
        $this->contextService->changeContextsPriority($contextsData , Answer::class);
    }
}
