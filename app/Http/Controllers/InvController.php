<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Inv;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;

class InvController extends Controller
{
    public function index()
    {
        return Inv::with(['users', 'course.department', 'room'])->orderBy('date_time')->get();
    }

    public function index_groupBy()
    {
        $data = array();
        $invs = Inv::select('id', 'date_time', 'updated_at', 'room_id')->with('room')->withCount('users')->orderBy('date_time')->get()->groupBy(function ($item) {
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
                $data[$key][$item->date_time]["updated_at"] = $item->updated_at;
            }
        }
        foreach ($invs as $key => $inv)
        {
            foreach ($inv as $item)
            {
                $data[$key][$item->date_time]['users_count'] += $item->users_count;
                $data[$key][$item->date_time]['users_limit'] += $item->room->users_limit;
                if($data[$key][$item->date_time]['updated_at'] < $item->updated_at)
                    $data[$key][$item->date_time]['updated_at'] = $item->updated_at;
            }
        }
        foreach ($data as $item)
            ksort($item);

        return response(['invs' => $data]);
    }

    public function profile($id)
    {
        $inv = Inv::with(['users.department', 'course.department', 'room'])->where('id', $id)->first();
        return response($inv);
    }

    public function create(Request $request)
    {
        $check = Inv::where('course_id', $request->input('course_id'))->get()->count();
        if ($check > 0)
        {
            return response(['message' => 'Course already exists in invs.'], 402);
        }
        $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id' ],
            'rooms' => ['required'],
            'duration' => ['nullable'],
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
        foreach ($rooms as $room_id)
        {
            $room = Room::where('id', $room_id)->first();
            $inv = new Inv();
            $inv->date_time = $date_time;
            $inv->users_count = 0;
            $inv->users_limit = $room->users_limit;
            $inv->room_id = $room->id;
            $inv->duration = $duration;
            $inv->course_id = $course_id;
            $inv->save();
        }
        return response(['message' => $course->code.' - '.$course->name.', invs created successfully.'],201);
    }

    public function removeInv(Request $request)
    {
        $request->validate([
           'id' => ['required', 'integer', 'exists:invs,id']
        ]);
        $id = $request->input('id');
        $inv = Inv::where('id', $id)->first();
        if ($inv->users()->count() > 0 )
        {
            return response(['message' => 'There are users attached to this inv. Remove them first'], 402);
        }
        else
        {
            $course = Course::where('id', $inv->course_id)->first();
            $inv->delete();
            $invs = $this->index();
            return response([
                'message' => 'Inv on '.Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_time)->toDateString().', Course: '.$course->code.' - '.$course->name.' Deleted successfully.',
                'invs' => $invs ], 201);
        }
    }

    public function addUser(Request $request)
    {
        if (Inv::where('date_time', $request->input('date_time'))->whereColumn('users_count', '<', 'users_limit')->first() != null)
        {
            $inv = Inv::where('date_time', $request->input('date_time'))->whereColumn('users_count', '<', 'users_limit')->first();
            $user = User::where('id', $request->input('user_id'))->first();
            $user->invs()->attach($inv->id, ['created_at' => now(), 'updated_at' => now()]);
            $inv->users_count += 1;
            $inv->save();
            $invs = $user->invs()->get();
            return response(['message' => 'Inv on '.Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_time)->toDateString().', added successfully', 'invs' => $invs], 201);
        }
        else
        {
            return response(['message' => 'Invs are full on date '.Carbon::createFromFormat('Y-m-d H:i:s', $request->input('date_time'))->toDateString()],402);
        }

    }

    public function removeUserByDate(Request $request)
    {
        $user = User::where('id', $request->input('user_id'))->first();
        $inv = $user->invs()->where('date_time', $request->input('date_time'))->first();
        $user->invs()->detach($inv->id);
        $inv->users_count -=1;
        $inv->save();
        $invs = $user->invs()->get();
        return response(['message' => 'Inv on '.Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_time)->toDateString().', removed successfully', 'invs' => $invs],201);
    }

    public function removeUser(Request $request)
    {
        $user_id = $request->input('user_id');
        $inv_id = $request->input('inv_id');
        $inv = Inv::where('id', $inv_id)->first();
        $user = User::where('id', $user_id)->first();
        $inv->users_count -=1;
        $inv->save();
        $inv->users()->detach($user_id);
        return response(['message' => 'User '.$user->name.' Removed successfully', 'users' => $inv->users()->get()], 201);
    }

    public function invStatistics()
    {
        if(Inv::all()->count() > 0 && Role::where('name', 'user')->first()->users()->count() )
        {
            $invs = Inv::all()->sum('users_limit') ;
            $users = Role::where('name', 'user')->first()->users()->count();
            $max_inv = Inv::all()->groupBy('date_time');
            $array = [];
            foreach ($max_inv as $key => $timeslot)
            {
                $array[$key] = 0;
                foreach ($timeslot as $slot)
                {
                    $array[$key] += $slot->users_limit;
                }
            }
            $max_slot['date_time'] = array_search(max($array), $array);
            $max_slot['users_count'] = max($array);
            return response(['sum' => $invs, 'users' => $users, 'max_slot' => $max_slot], 201);
        }
        else
        {
            $users = Role::where('name', 'user')->first()->users()->count();
            return response(['sum' => 0, 'users' => $users, 'max_slot' => 0], 201);
        }
    }

    public function randomDistribution()
    {
        $all_invs = Inv::all()->groupBy('date_time');
        $users = Role::where('name', 'user')->first()->users()->get()->shuffle();
        $i = 0;
        foreach ($all_invs as $date_time => $period_invs)
        {
            while($inv = Inv::where('date_time', $date_time)->whereColumn('users_count', '<', 'users_limit')->first())
            {
               $check = $users[$i]->invs()->get()->contains(function ($item, $key) use ($date_time) {
                    return $item->date_time == $date_time;
                });
               if ($check)
               {
                   $i++;
               }
               else
               {
                   $inv->users()->attach($users[$i]['id']);
                   $inv->users_count += 1;
                   $inv->save();
                   $i++;
                   if($i === count($users))
                   {
                       $i = 0;
                       $users = $users->shuffle();
                   }
               }

            }
        }

        $users = Role::where('name', 'user')->first()->users()->with(['department'])->withCount('invs')->get();
        return response(['invs' => $all_invs, 'users' => $users],201);
    }

    public function detachAllUsers()
    {
        $invs = Inv::all();
        foreach ($invs as $inv)
        {
            $inv->users()->detach();
            $inv->users_count = 0;
            $inv->save();
        }
        return response(['message' => 'All invs detached successfully'], 201);
    }
    public function flushInvs()
    {
        $invs = Inv::all();
        foreach ($invs as $inv)
        {
            $inv->users()->detach();
        }
        DB::table('invs')->delete();
        return response(['message' => 'All invs deleted successfully'], 201);
    }
}
