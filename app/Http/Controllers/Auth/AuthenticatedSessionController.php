<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

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

        if (!Auth::attempt(['email' => $request -> email, 'password' => $request -> password, 'active' => 'yes'])) {
            dd('error');
        }
        $request -> authenticate();

        $request -> session() -> regenerate();

        // TODO: Add login middleware

        $user = User::find(auth() -> user() -> id);
        $group = auth() -> user() -> group;

        if($user -> active != 'yes') {
            return redirect('/');
        }
        $user_details = null;
        if($group == 'agent') {
            $user_details = $user -> agent;
        } else if($group == 'mortgage') {
            $user_details = $user -> loan_officer;
        }


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
}
