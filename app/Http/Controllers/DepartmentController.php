<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $fillable = ['name'];

    public function index()
    {
        $departments = Department::withCount(['courses', 'users'])->get();
        return response(['departments'=>$departments]);
    }

    public function profile($id)
    {
        $department = Department::with(['courses', 'courses.invs', 'courses.invs.room'])->where('id', $id)->first();
//        $courses_invs = $department->courses()->with('invs')->with('invs.room')->groupBy('invs')->get();
        return response(['department' => $department], 201);
    }

    public function create(Request $request)
    {
        $request->validate([
           'name' => ['string', 'min:10', 'required']
        ]);
        $department = new Department();
        $department->name = $request->input('name');
        $department->save();

        return response(['message' => $department->name.' Created Successfully'], 201);

    }
}
