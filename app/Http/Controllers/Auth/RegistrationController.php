<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function registration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:10|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:3|confirmed'
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $token = $user->createToken($user->email)->plainTextToken;
        return response([
            'user' => $user,
            'invs' => $user->invs()->get(),
            'token' => $token
        ]);
    }
}
