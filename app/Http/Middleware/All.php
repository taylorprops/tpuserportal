<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class All
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! in_array(auth()->user()->group, ['agent', 'in_house', 'mortgage', 'title', 'transaction_coordinator'])) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
