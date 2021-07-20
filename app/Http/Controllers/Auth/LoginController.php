<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
           'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if(!$user || !Hash::check($request->input('password'), $user->password ))
        {
            return response(['message' => "Email or password doesn't match our records"], 401);
        }

        $token = $user->createToken($user->email)->plainTextToken;
        $invs = $user->invs()->get();
        $roles = $user->roles()->get();
        return response([
            'user' => $user,
            'token' => $token,
            'invs' => $invs,
            'roles' => $roles
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response(['message' => 'Logged out'], 201);
    }
}
