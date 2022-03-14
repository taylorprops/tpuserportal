<?php

namespace App\Http\Middleware\API;

use Closure;
use Illuminate\Http\Request;

class TPAgentPortal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') == 'production' && $_SERVER['HTTP_REFERER'] == 'https://tpagentportal.com/') {
            return $next($request);
        } elseif (config('app.env') == 'local') {
            return $next($request);
        }
        abort(403, 'Unauthorized');
    }
}
