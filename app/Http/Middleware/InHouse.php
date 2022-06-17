<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InHouse
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
        if (auth() -> user() -> group == 'mortgage' && auth() -> user() -> level != 'manager') {
            return redirect('/dashboard');
        }
        if (auth() -> user() -> group != 'in_house' && auth() -> user() -> group != 'mortgage') {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
