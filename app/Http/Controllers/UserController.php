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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['roles', 'department'])->withCount('invs')->get();
    }

    public function dashboard()
    {
        if (auth()->user()->tokenCan('admin'))
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
        else
        {
            return response(['message' => 'Unauthorized'], 402);
        }
    }

    public function fileImport(Request $request)
    {
        $request->validate([
            'file' => ['file', 'mimes:xls,xlsx']
        ]);
        $file = $request->file('file');
        if($file)
        {
            $import = new UsersImport;
            Excel::import($import , $request->file('file'));
            return response(['message' => $import->getRowCount().' Users imported successfully'], 201);
        }
        else
        {
            return response(['message' => 'file error'], 402);
        }
    }

    public function profile(Request $request)
    {
        $user = User::where('id', $request->input('user_id'))->with(['department', 'roles'])->first();
        $invs = $user->invs()->orderBy('date_time')->with(['room', 'course.department', 'users'])->withCount('users')->get();
        $roles = $user->roles()->get();
        $settings = DB::table('settings')->get();
        return response(['user' => $user ,'invs' => $invs, 'roles' => $roles, 'settings' => $settings]);
    }

    public function userProfile($id)
    {
        $user = User::with(['roles', 'department', 'invs.room', 'invs.course.department'])->where('id', $id)->first();
        $departments = Department::all(); //for editUser component at front end
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
        $request->validate([
           'user_id' => ['required', 'integer', 'exists:users,id'],
           'inv_id' => ['required', 'integer', 'exists:invs,id']
        ]);
        $user_id = $request->input('user_id');
        $user = User::with('roles')->where('id', $user_id)->first();
        $role = Role::where('slug', 'user')->firstOrFail();
        if (!$user->roles->contains($role->id))
        {
            return response(['message' => $user->name." doesn't have user permission to add invs"],402);
        }
        $inv_id = $request->input('inv_id');
        $inv = Inv::where('id', $inv_id)->first();
        if ($user->invs()->count() < $user->invs_limit && $user->invs()->attach($inv_id, ['created_at' => now(), 'updated_at' => now()])) {
            $inv->users_count += 1;
        }
        $inv->save();
        $invs = $user->invs()->get();
        return response(['message' => 'Inv added successfully', 'invs' => $invs], 201);
    }

    public function removeInv(Request $request)
    {
        $settings = DB::table('settings')->where('name','=','manual_selection')->first();
        if(!$settings->value && !auth()->user()->tokenCan('admin') ) {
            return response(['message' => 'Manual selection is disabled.'],402);
        }
        $role = Role::where('slug', 'user')->firstOrFail();
        $user_id = $request->input('user_id');
        $user = User::with('roles')->where('id', $user_id)->first();
        if (!$user->roles->contains($role->id))
        {
            return response(['message' => $user->name." Doesn't has permission to remove invs"], 402);
        }
        $inv_id = $request->input('inv_id');
        $inv = Inv::where('id', $inv_id)->first();
        if($user->invs()->detach($inv_id))
        {
            $inv->users_count -=1;
        }
        $inv->save();
        $invs = $user->invs()->with(['room', 'course', 'course.department'])->orderBy('date_time')->get();
        $inv = Inv::with(['room', 'course.department' ,'users.department'])->where('id', $inv_id)->first();
        return response([
            'message' => 'Inv on '.Carbon::createFromFormat('Y-m-d H:i:s',$inv->date_time)->toDateString().', removed successfully from '.$user->name ,
            'invs' => $invs,
            'inv' => $inv], 201);
    }

    public function removeAllUserInvs($id)
    {
        $user = User::where('id', $id)->first();
        $user->invs()->decrement('users_count', 1);
        $user->invs()->detach();
        return response(['message' => 'All invs for '.$user->name.' deleted successfully'],201);
    }

    public function setUsersLimit()
    {
        DB::table('users')->update(['invs_limit' => 0]);
        $users = Role::where('name', 'user')->first()->users()->get()->shuffle();
        $user_index = 0;
        $invs = Inv::all();
        foreach ($invs as $inv)
        {
            for ($i=0; $i<$inv->users_limit; $i++)
            {
                $users[$user_index]->invs_limit += 1;
                $users[$user_index]->save();
                $user_index += 1;
                if($user_index == count($users))
                    $user_index = 0;
            }
        }
        return response([ 'message' => 'Users invs limit set'], 201);
    }

    public function resetPassword($id)
    {
        $user = User::where('id', $id)->first();
        $user->password = NULL;
        $user->email_verified_at = NULL;
        $user->save();

        return response(['message' => 'Password for '.$user->name.' reset successfully'],201);
    }

    public function attachRole(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_slug = $request->input('role_slug');
        $role = Role::where('slug', $role_slug)->first();
        $user = User::where('id', $user_id)->first();
        $user->roles()->attach($role->id);
        if($role->slug == "user")
        {
            $this->setUsersLimit();
        }
        return response(['message' => $user->name.' Become '.$role->name.' Successfully'], 201);
    }
    public function detachRole(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_slug = $request->input('role_slug');
        $role = Role::where('slug', $role_slug)->first();
        $user = User::where('id', $user_id)->first();
        $user->roles()->detach($role->id);
        if($role->slug == "user")
        {
            $this->setUsersLimit();
        }
        return response(['message' => $role->name.' Removed from '.$user->name.' Successfully'], 201);
    }

    public function updateInformation(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'user_name' => ['required', 'string', 'max:30', 'min:5', 'regex:/^[aA-zZ]+\s[aA-zZ]+$/'],
            'user_email' => ['required', 'email']
        ]);
        $user_name = ucwords(strtolower($request->input('user_name')));
        $user = User::where('id', $request->input('user_id'))->first();
        $user->name = $user_name;
        $user->email = $request->input('user_email');
        $user->save();

        return response(['message' => $user->name.' information updated successfully', 'user' => $user],201);
    }


    public function updateDepartment(Request $request)
    {
        $request->validate([
           'user_id' => ['required', 'integer', 'exists:users,id'],
           'department_id' => ['required', 'integer', 'exists:departments,id']
        ]);
        $user = User::where('id', $request->input('user_id'))->first();
        $department = Department::where('id', $request->input('department_id'))->first();
        $user->department_id = $department->id;
        $user->save();
        return response(['message' => $user->name.' department updated to '.$department->name]);
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id']
        ]);

        $user = User::where('id', $request->input('user_id'))->first();
        foreach ($user->roles as $role)
        {
            if ($role->name == 'admin')
            {
                return response(['message' => $user->name." is Admin, can't delete admins"], 402);
            }
        }
        $tmp_user = $user;
        $user->roles()->detach();
        $user->invs()->decrement('users_count', 1);
        $user->invs()->detach();
        $user->delete();

        return response(['message' => $tmp_user->name.' removed successfully'], 201);
    }


}
