<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TransactionCoordinator
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
        if (! in_array(auth()->user()->group, ['transaction_coordinator', 'in_house'])) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
