<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('users.department')->withCount('users')->get();
        return response($roles);
    }
}
