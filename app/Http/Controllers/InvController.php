<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Inv;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvController extends Controller
{
    public function index()
    {
        return Inv::with(['users', 'course.department', 'room'])->withCount('users')->orderBy('date_time')->get();
    }

    public function index_groupBy()
    {
        $data = array();
        $invs = Inv::select('id', 'date_time', 'room_id')->with('room')->withCount('users')->orderBy('date_time')->get()->groupBy(function ($item) {
            return Carbon::createFromFormat('Y-m-d H:i:s',$item->date_time)->toDateString();
        });
        foreach ($invs as $key => $inv)
        {
            $data[$key] = array();
            foreach ($inv as $item)
            {
                $data[$key][$item->date_time] = array();
                $data[$key][$item->date_time]['users_count'] = 0;
                $data[$key][$item->date_time]["users_limit"] = 0;
            }
        }
        foreach ($invs as $key => $inv)
        {
            foreach ($inv as $item)
            {
                $data[$key][$item->date_time]['users_count'] += $item->users_count;
                $data[$key][$item->date_time]['users_limit'] += $item->room->users_limit;
            }
        }
        foreach ($data as $item)
            ksort($item);
        return response(['invs' => $data]);
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
        $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id' ],
            'rooms' => ['required'],
            'duration' => ['nullable', 'integer'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i']
        ]);
        $course_id = $request->input('course_id');
        $duration = $request->input('duration');
        $rooms = array_map('intVal', explode(',', $request->input('rooms')));
        $date = $request->input('date');
        $time = $request->input('time');
        $date_time = Carbon::createFromFormat('Y-m-d H:i',$date.' '.$time)->toDateTimeString();
        $course = Course::where('id', $course_id)->first();
        foreach ($rooms as $room)
        {
            $inv = new Inv();
            $inv->date_time = $date_time;
            $inv->room_id = $room;
            $inv->duration = $duration;
            $inv->course_id = $course_id;
            $inv->save();
        }
        return response(['message' => $course->code.' - '.$course->name.', invs created successfully.'],201);
    }
}
