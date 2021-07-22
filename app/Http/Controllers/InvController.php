<?php

namespace App\Http\Controllers;

use App\Models\Inv;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvController extends Controller
{
    public function index()
    {
        return Inv::with(['users', 'course.department', 'room'])->withCount('users')->get();
    }

    public function profile($id)
    {
        $inv = Inv::with(['users', 'course.department', 'room'])->where('id', $id)->first();
        return response($inv);
    }

    public function removeUser(Request $request)
    {
        $user_id = $request->input('user_id');
        $inv_id = $request->input('inv_id');
        $inv = Inv::where('id', $inv_id)->first();
        $user = User::where('id', $user_id)->first();
        $inv->users()->detach($user_id);
        return response(['message' => 'User '.$user->name.' Removed successfully', 'users' => $inv->users()->get()], 201);
    }

    public function create(Request $request)
    {
        $course_id = $request->input('course_id');
        $rooms = array_map('intVal', explode(',', $request->input('rooms')));
        $date = $request->input('date');
        $time = $request->input('time');
        $date_time = Carbon::parse($date.' '.$time)->format('Y-m-d H:m:s');
        return response(['message' => 'Inv received successfully','course_id' => $course_id, 'rooms' => $rooms, 'date' => $date, 'time' => $time,'date_time' => $date_time],201);
    }
}
