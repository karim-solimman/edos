<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
           'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->input('email'))->with('department')->first();
        if(!$user || !Hash::check($request->input('password'), $user->password ))
        {
            return response(['message' => "Email or password doesn't match our records"], 401);
        }

        $roles = [];
        foreach ($user->roles as $role)
            array_push($roles, $role->slug);

        $token = $user->createToken($user->email, $roles)->plainTextToken;
        $invs = $user->invs()->get();
        $roles = [];
        $roles = $user->roles()->get();
        $settings = DB::table('settings')->get();
        return response([
            'user' => $user,
            'token' => $token,
            'invs' => $invs,
            'roles' => $roles,
            'settings' => $settings
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response(['message' => 'Logged out'], 201);
    }
}
