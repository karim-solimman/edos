<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Inv;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['courses', 'users', 'invs'])->orderBy('name')->get();
        return response(['departments'=>$departments]);
    }

    public function profile($id)
    {
        $department = Department::with(['courses', 'courses.invs', 'courses.invs.room'])->where('id', $id)->first();
        return response(['department' => $department], 201);
    }

    public function create(Request $request)
    {
        $request->validate([
           'name' => ['string', 'min:10', 'required']
        ]);
        $department = new Department();
        $department_name = ucwords(strtolower($request->input('name')));
        $department->name = $department_name;
        $department->save();

        return response(['message' => $department->name.' Created Successfully'], 201);

    }

    public function updateName(Request $request)
    {
        $request->validate([
           'department_id' => ['required', 'integer', 'exists:departments,id'],
            'department_name' => ['required', 'string', 'min:10']
        ]);
        $department = Department::where('id', $request->input('department_id'))->first();
        $department->name = $request->input('department_name');
        $department->save();

        return response(['message' => $department->name.' Updated Successfully'],201);
    }

    public function flushAllInvs(Request $request)
    {
        $request->validate([
           'department_id' => ['required', 'integer', 'exists:departments,id']
        ]);
        $department = Department::with(['invs'])->where('id', $request->input('department_id'))->first();
        foreach ($department->invs as $inv)
        {
            $inv->users()->detach();
        }
        Inv::destroy($department->invs);
        $department = Department::with(['courses','courses.invs', 'courses.invs.room'])->where('id', $request->input('department_id'))->first();
        return response(['message' => 'All invs deleted from '.$department->name, 'department' => $department],201);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'department_id' => ['required', 'integer', 'exists:departments,id']
        ]);
        $department = Department::where('id', $request->input('department_id'))->first();
        $department->users()->update(['department_id' => null]);
        $department->courses()->update(['department_id'=> null]);
        $tmp_dep = $department;
        $department->delete();

        return response(['message' => $tmp_dep->name.' removed successfully'],201);
    }
}
