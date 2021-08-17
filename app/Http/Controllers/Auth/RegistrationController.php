<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:5', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id']
        ]);
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->input('department_id'))
        {
            $user->department_id = $request->input('department_id');
        }
        $user->save();
        return response(['message' => $user->name.', Added successfully'], 201);
    }

    public function check_email(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        if($user = User::where('email', $request->input('email'))->where('password', NULL)->first())
            return response(['message' => 'found', 'user' => $user], 201);
        if ($user = User::where('email', $request->input('email'))->whereNotNull('password')->first())
            return response(['message' => $user->email.', already registered'], 402);
    }

    public function set_password(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:users,id'],
            'password' => ['required','string', 'min:3', 'confirmed']
        ]);
        $user = User::where('id', $request->input('id'))->first();
        $user->password = Hash::make($request->input('password'));
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->save();

        return response(['message' => 'Password set successfully'], 201);
    }
}
