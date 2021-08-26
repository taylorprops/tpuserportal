<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LoanOfficer
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

        if (!in_array(auth() -> user() -> group, ['loan_officer', 'admin'])) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
