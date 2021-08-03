<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Inv;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['roles', 'department'])->withCount('invs')->get();
    }

    public function dashboard()
    {
        $roles = Role::withCount('users')->get();
        $invs = Inv::all()->count();
        $departments = Department::all()->count();
        $department_courses = Department::withCount('courses')->get();
        $department_invs = Department::withCount('invs')->get();
        $last_inv = Inv::orderByDesc('created_at')->first();
        $rooms = Room::all()->count();
        $courses = Course::all()->count();

        return response(['roles' => $roles, 'invs' => $invs,
            'departments' => $departments,
            'rooms' => $rooms,
            'courses' => $courses,
            'department_courses' => $department_courses,
            'department_invs' => $department_invs,
            'last_inv' => $last_inv], 201);
    }

    public function profile(Request $request)
    {
        $user = User::where('id', $request->input('user_id'))->with(['department', 'roles'])->first();
        $invs = $user->invs()->orderBy('date_time')->with('room')->withCount('users')->get();
        $roles = $user->roles()->get();
        return response(['user' => $user ,'invs' => $invs, 'roles' => $roles]);
    }
    public function userProfile($id)
    {
        $user = User::with(['roles', 'department', 'invs.room', 'invs.course.department'])->where('id', $id)->first();
        $departments = Department::all();
        return response(['user' => $user, 'departments' => $departments]);
    }
    public function checkEmail($email)
    {
        $check = User::where('email', $email)->get();
        if(count($check))
            return response(1);
        return response(0);
    }
    public function addInv(Request $request)
    {
        $user_id = $request->input('user_id');
        $inv_id = $request->input('inv_id');
        $user = User::where('id', $user_id)->first();
        $inv = Inv::where('id', $inv_id)->first();
        if($user->invs()->attach($inv_id, ['created_at' => now(), 'updated_at' => now()]))
        {
            $inv->users_count +=1;
        }
        $inv->save();
        $invs = $user->invs()->get();
        return response(['message' => 'Inv added successfully', 'invs' => $invs], 201);
    }
    public function removeInv(Request $request)
    {
        $user_id = $request->input('user_id');
        $inv_id = $request->input('inv_id');
        $inv = Inv::where('id', $inv_id)->first();
        $user = User::where('id', $user_id)->first();
        if($user->invs()->detach($inv_id))
        {
            $inv->users_count -=1;
        }
        $inv->save();
        $inv = Inv::with('users.department')->where('id', $inv_id)->first();
        $invs = $user->invs()->with(['room', 'course', 'course.department'])->orderBy('date_time')->get();
        return response([
            'message' => 'Inv on '.Carbon::createFromFormat('Y-m-d H:i:s',$inv->date_time)->toDateString().', removed successfully from '.$user->name ,
            'invs' => $invs,
            'inv' => $inv], 201);
    }
    public function attachRole(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_slug = $request->input('role_slug');
        $role = Role::where('slug', $role_slug)->first();
        $user = User::where('id', $user_id)->first();
        $user->roles()->attach($role->id);
        return response(['message' => $user->name.' Become '.$role->name.' Successfully'], 201);
    }
    public function detachRole(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_slug = $request->input('role_slug');
        $role = Role::where('slug', $role_slug)->first();
        $user = User::where('id', $user_id)->first();
        $user->roles()->detach($role->id);
        return response(['message' => $role->name.' Removed from '.$user->name.' Successfully'], 201);
    }
}
