<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request -> authenticate();

        $request -> session() -> regenerate();

        // TODO: Add login middleware

        $user = User::find(auth() -> user() -> id);
        //$group = auth() -> user() -> group;

        if ($user -> active != 'yes') {
            Auth::logout();

            return back() -> withErrors(['Your account is inactive']);
        }
        // $user_details = null;
        // if($group == 'agent') {
        //     $user_details = $user -> agent;
        // } else if($group == 'mortgage') {
        //     $user_details = $user -> loan_officer;
        // }

        return redirect() -> intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web') -> logout();

        $request -> session() -> invalidate();

        $request -> session() -> regenerateToken();

        return redirect('/');
    }

    public function checkSession()
    {
        return response() -> json(['isLoggedIn' => Auth::check()]);
    }
}
