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
}
