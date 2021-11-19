<?php

namespace App\Http\Controllers;

use App\Imports\InvsImport;
use App\Models\Course;
use App\Models\Inv;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\UserController;

class InvController extends Controller
{
    public function index()
    {
        return Inv::with(['users', 'course.department', 'room'])->orderBy('date_time')->get();
    }

    public function index_by_department(Request $request)
    {
        $request->validate([
           'user_id' => ['required', 'integer', 'exists:users,id']
        ]);
        $user = User::with(['department'])->where('id', $request->input('user_id'))->firstOrFail();
        if (!$user->department)
        {
            return response(['message' => $user->name." - Not attached to department"],402);
        }
        if (!auth()->user()->tokenCan('de'))
        {
            return response(['message' => $user->name." - Don't have permission."],402);
        }
        $courses = Course::select('id')->where('department_id', $user->department->id)->get();
        return Inv::with(['course.department', 'room'])->whereIn('course_id', $courses)->get();

    }

    public function index_groupBy()
    {
        $data = array();
        $invs = Inv::select('id', 'date_time', 'updated_at', 'room_id', 'users_limit')->with('room')->withCount('users')->orderBy('date_time')->get()->groupBy(function ($item) {
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
                $data[$key][$item->date_time]['users_limit'] += $item->users_limit;
                if($data[$key][$item->date_time]['updated_at'] < $item->updated_at)
                    $data[$key][$item->date_time]['updated_at'] = $item->updated_at;
            }
        }
        foreach ($data as $item)
            ksort($item);


        $settings = DB::table('settings')->get();
        return response(['invs' => $data, 'settings' => $settings], 201);
    }

    public function profile($id)
    {
        $inv = Inv::with(['users.department', 'course.department', 'room'])->where('id', $id)->first();
        return response($inv);
    }

    public function create(Request $request)
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id', 'unique:invs,course_id'],
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
            $room = Room::where('id', $room_id)->firstOrFail();
            $inv = new Inv();
            $inv->date_time = $date_time;
            $inv->users_count = 0;
            $inv->users_limit = $room->users_limit;
            $inv->room_id = $room->id;
            $inv->duration = $duration;
            $inv->course_id = $course_id;
            $inv->save();
        }
        $user_controller = new UserController();
        $user_controller->setUsersLimit();
        return response(['message' => $course->code.' - '.$course->name.', invs created successfully.'],201);
    }

    public function removeInv(Request $request)
    {
        $request->validate([
           'id' => ['required', 'integer', 'exists:invs,id']
        ]);
        $id = $request->input('id');
        $inv = Inv::where('id', $id)->first();
        $inv->users()->detach();
        $course = Course::where('id', $inv->course_id)->first();
        $inv->delete();
        $invs = $this->index();
        $user_controller = new UserController();
        $user_controller->setUsersLimit();
        return response([
            'message' => 'Inv on '.Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_time)->toDateString().', Course: '.$course->code.' - '.$course->name.' Deleted successfully.',
            'invs' => $invs ], 201);
    }

    public function editInfo(Request $request)
    {
        $request->validate([
           'inv_id' => ['required', 'integer', 'exists:invs,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'users_limit' => ['required', 'integer'],
            'duration' => ['required', 'integer']
        ]);
        $inv = Inv::with(['users.department', 'course.department', 'room'])->where('id', $request->input('inv_id'))->first();
        $inv->course_id = $request->input('course_id');
        $inv->duration = $request->input('duration');
        $inv->users_limit = $request->input('users_limit');
        $inv->room_id = $request->input('room_id');
        $inv->save();

        return response(['message' => 'Inv updated successfully', 'inv' => $inv ],201);
    }

    public function editDateAndTime(Request $request)
    {
        $request->validate([
           'inv_id' => ['required', 'integer', 'exists:invs,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i']
        ]);
        $date_time = $request->input('date').' '.$request->input('time');
        $inv = Inv::where('id', $request->input('inv_id'))->first();
        $inv->date_time = $date_time;
        $inv->save();

        return response(['message' => 'date and time updated to '.Carbon::parse($date_time)->toDateString().' at '.Carbon::parse($date_time)->format('H:i A')],201);
    }

    public function attachUserByDate(Request $request)
    {
        $settings = DB::table('settings')->where('name', '=', 'manual_selection')->first();
        if (!$settings->value && !auth()->user()->tokenCan('admin'))
        {
            return response(['message' => 'Manual selection is disabled'],402);
        }
        if (Inv::where('date_time', $request->input('date_time'))->whereColumn('users_count', '<', 'users_limit')->first() != null)
        {
            $user = User::with('roles')->where('id', $request->input('user_id'))->first();
            $role = Role::where('slug','user')->firstOrFail();
            if (!$user->roles->contains($role->id))
            {
                return response(['message' => $user->name." doesn't have user permission to add invs"],402);
            }
            if($user->invs()->count() >= $user->invs_limit)
            {
                return response(['message' => $user->name.' reached invs limit'],402);
            }
            $inv = Inv::where('date_time', $request->input('date_time'))->whereColumn('users_count', '<', 'users_limit')->first();
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
        $settings = DB::table('settings')->where('name', '=', 'manual_selection')->first();
        if (!$settings->value && !auth()->user()->tokenCan('admin'))
        {
            return response(['message' => 'Manual selection is disabled'],402);
        }
        $role = Role::where('slug','user')->firstOrFail();
        $user = User::where('id', $request->input('user_id'))->first();
        if (!$user->roles->contains($role->id))
        {
            return response(['message' => $user->name." doesn't have user permission to remove invs"],402);
        }
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
        $settings = DB::table('settings')->get();
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
            return response(['sum' => $invs, 'users' => $users, 'max_slot' => $max_slot, 'settings' => $settings], 201);
        }
        else
        {
            $users = Role::where('name', 'user')->first()->users()->count();
            return response(['sum' => 0, 'users' => $users, 'max_slot' => 0, 'settings' => $settings], 201);
        }
    }

    public function randomDistribution()
    {
        $conflict_queue = array();
        $all_invs = Inv::all()->groupBy('date_time');
        $users = Role::where('name', 'user')->first()->users()->get()->shuffle();
        foreach ($users as $user)
            $user->invs()->detach();
        DB::table('invs')->update(['users_count' => 0]);
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
                   array_push($conflict_queue, $users[$i]);
               }
               else
               {
                   $inv->users()->attach($users[$i]['id']);
                   $inv->users_count += 1;
                   $inv->save();
               }
                $i++;
                if($i === count($users))
                {
                    while(Inv::where('date_time', $date_time)->whereColumn('users_count', '<', 'users_limit')->first() && count($conflict_queue))
                    {
                        $inv = Inv::where('date_time', $date_time)->whereColumn('users_count', '<', 'users_limit')->first();
                        $user = array_shift($conflict_queue);
                        $check = $user->invs()->get()->contains(function ($item, $key) use ($date_time) {
                            return $item->date_time == $date_time;
                        });
                        if(!$check)
                        {
                            $inv->users()->attach($user->id);
                            $inv->users_count += 1;
                            $inv->save();
                        }
                        else
                        {
                            array_push($conflict_queue, $user);
                        }
                    }
                    $i = 0;
                    $users = $users->shuffle();
                }

            }
        }
        $original_queue = $conflict_queue;
        $original_queue = array_reverse($original_queue);
        $reverse_queue = array_pop($conflict_queue);
        $users = Role::where('name', 'user')->first()->users()->with(['department'])->withCount('invs')->get();
        return response(['invs' => $all_invs, 'users' => $users, 'conflict_queue' => $original_queue, 'reverse_queue' => $reverse_queue],201);
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

    public function fileImport(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx']
        ]);
        $invs_conflicts = [];
        $file = $request->file('file');
        $import = new InvsImport;
        $array = Excel::toArray($import, $file);
        foreach ($array[0] as $row) {
            $course_code = $row['course'];
            $date = $row['date'];
            $time = $row['time'];
            for ($i = 0; $i < count($array[0]); $i++) {
                if ($array[0][$i]['course'] == $course_code) {
                    if ($array[0][$i]['date'] != $date || $array[0][$i]['time'] != $time)
                    {
                        $i+=2;
                        return response(['message' => strtoupper($course_code) . ' course date or time are not similar at row '.$i], 402);
                    }
                }
            }
        }
        Excel::import($import , $request->file('file'));
        $rooms = Room::all();
        foreach ($rooms as $room)
        {
            $room_invs = $room->invs()->get()->groupBy('date_time');
            foreach ($room_invs as $key => $invs)
            {
                if (count($invs) > 1)
                    array_push($invs_conflicts, $room->number.' has more than inv at same slot '.$key);
                foreach ($invs as $inv)
                {
                    if ($inv->duration > 0)
                    {
                        for ($i=1; $i<$inv->duration; $i++)
                        {
                            $tmp_date_time = Carbon::parse($inv->date_time)->addHours($i)->toDateTimeString();
                            if ($room->invs()->get()->where('date_time',$tmp_date_time)->count() > 0)
                                array_push($invs_conflicts, $room->number.' has overlap invs at slot '.$tmp_date_time);
                        }
                    }
                }
            }
        }
        $courses =  Course::has('invs')->get();
        foreach ($courses as $course)
        {
            $old_date = $course->invs()->first()->date_time;
            foreach ($course->invs()->get() as $inv)
            {
                if ($old_date != $inv->date_time)
                {
                    array_push($invs_conflicts, $course->code.' dates and time are not similar');
                }
                $old_date = $inv->date_time;
            }
        }
        $user_controller = new UserController();
        $user_controller->setUsersLimit();
        if (count($invs_conflicts)>0)
        {
            return response(['message' => $import->getRowCount().' Invs imported successfully but with some warnings.', 'type' => 'warning', 'invs_conflicts' => array_unique($invs_conflicts)], 201);
        }

        return response(['message' => $import->getRowCount().' Invs imported successfully', 'type' => 'success'], 201);
    }
    public function check_invs_conflicts()
    {
        $invs_conflicts = [];
        $rooms = Room::all();
        foreach ($rooms as $room)
        {
            $room_invs = $room->invs()->get()->groupBy('date_time');
            foreach ($room_invs as $key => $invs)
            {
                if (count($invs) > 1)
                    array_push($invs_conflicts, $room->number.' has more than inv at same slot '.$key);
                foreach ($invs as $inv)
                {
                    if ($inv->duration > 0)
                    {
                        for ($i=1; $i<$inv->duration; $i++)
                        {
                            $tmp_date_time = Carbon::parse($inv->date_time)->addHours($i)->toDateTimeString();
                            if ($room->invs()->get()->where('date_time',$tmp_date_time)->count() > 0)
                                array_push($invs_conflicts, $room->number.' has overlap invs at slot '.$tmp_date_time);
                        }
                    }
                }
            }
        }
        $courses =  Course::has('invs')->get();
        foreach ($courses as $course)
        {
            $old_date = $course->invs()->first()->date_time;
            foreach ($course->invs()->get() as $inv)
            {
                if ($old_date != $inv->date_time)
                {
                    array_push($invs_conflicts, $course->code.' dates and time are not similar');
                }
                $old_date = $inv->date_time;
            }
        }
        if (count($invs_conflicts)>0)
        {
            return response(['message' => 'Invs have some warnings to check.', 'type' => 'warning', 'invs_conflicts' => array_unique($invs_conflicts)], 201);
        }
        return response(['message' => 'All invs are good. No problems fount.', 'type' => 'success'],201);
    }
}
