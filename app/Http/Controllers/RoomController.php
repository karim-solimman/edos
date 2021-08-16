<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected $fillable = ['number'];

    public function index()
    {
        $rooms = Room::with('invs')->withCount('invs')->get();
        return response(['rooms' => $rooms], 201);
    }

    public function profile($id)
    {
        $room = Room::with(['invs.users', 'invs.course.department'])->withCount('invs')->where('id', $id)->first();
        return response(['room' => $room], 201);
    }

    public function create(Request $request)
    {
        $request->validate([
           'room_number' => ['required', 'max:4', 'regex:/[a-zA-Z][0-9][0-9][0-9]/', 'unique:rooms,number'],
            'users_limit' => ['nullable', 'integer']
        ]);
        $room = new Room();
        $room->number = $request->input('room_number');
        $room->users_limit = $request->input('users_limit');
        $room->save();
        return response(['message' => 'Room '.$room->number.' created successfully'],201);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'room_number' => ['required', 'max:4', 'regex:/[a-zA-Z][0-9][0-9][0-9]/'],
            'users_limit' => ['nullable', 'integer']
        ]);

        $room = Room::with(['invs'])->where('id', $request->input('room_id'))->firstOrFail();
        if (Room::where('number', strtoupper($request->input('room_number')))->get()->count() > 0 && $room->number != $request->input('room_number'))
        {
            return response(['message' => strtoupper($request->input('room_number')).' Already Exists'],402);
        }
        foreach ($room->invs as $inv)
        {
            if ($inv->users()->count() > $request->input('users_limit'))
                return response(['message' => 'Users limit is less than users count in some invs - remove users first'],402);
        }
        $room->number = strtoupper($request->input('room_number'));
        $room->users_limit = $request->input('users_limit');
        $room->invs()->update(['users_limit' => $room->users_limit]);
        $room->save();

        return response(['message' => $room->number.' Updated successfully'],201);
    }

    public function removeInvs(Request $request)
    {
        $request->validate([
           'room_id' => ['required', 'integer', 'exists:rooms,id']
        ]);
        $room = Room::with(['invs'])->where('id', $request->input('room_id'))->firstOrFail();
        foreach ($room->invs as $inv)
        {
            $inv->users()->detach();
        }
        $room->invs()->delete();
        return response(['message' => 'All invs removed successfully'], 201);
    }

    public function delete(Request $request)
    {
        $request->validate([
           'room_id' => ['required', 'integer', 'exists:rooms,id']
        ]);
        $room = Room::with(['invs'])->where('id', $request->input('room_id'))->firstOrFail();
        if(count($room->invs) > 0)
        {
            foreach ($room->invs as $inv)
            {
                $inv->users()->detach();
                $inv->delete();
            }
        }
        Room::destroy($room->id);
        $tmp_room = $room;
        return response(['message' => $tmp_room->number.' Removed successfully'],201);
    }
}
