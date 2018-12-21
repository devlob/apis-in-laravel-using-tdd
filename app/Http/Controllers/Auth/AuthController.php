<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            $token = User::whereEmail($request->email)->first()->createToken($request->email)->accessToken;

            return response()->json(['token' => $token]);
        }
    }

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $user = Socialite::driver($provider)->user();

        \Log::info('user', [$user]);
        \Log::info($user->token);

        return redirect()->away("http://localhost:8000?token=$user->token");
    }
}