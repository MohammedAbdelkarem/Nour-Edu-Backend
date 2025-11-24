<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Constants\ExceptionMessages;
use Symfony\Component\HttpFoundation\Response;

class CheckParentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->isParent())
            return $next($request);
        return failure(
            ExceptionMessages::MSG_THIS_IS_NOT_YOUR_ROUTE,
            403
        );
    }
}
