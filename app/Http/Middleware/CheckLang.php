<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLang
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale(request()->header('x-lang') == 'ar' ? 'ar' : 'en');
        return $next($request);
    }
}
