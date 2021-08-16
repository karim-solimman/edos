<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('department')->withCount('invs')->get();
        return response(['courses' => $courses], 201);
    }

    public function profile($id)
    {
        $course = Course::where('id', $id)->with(['department', 'invs', 'invs.room', 'invs.users'])->first();
        $departments = Department::all();
        return response(['course' => $course, 'departments' => $departments], 201);
    }

    public function create(Request $request)
    {
        $request->validate([
            'course_code' => ['required', 'string', 'min:4', 'unique:courses,code'],
            'course_name' => ['required', 'string', 'min:10'],
            'course_credit_hours' => ['required', 'integer'],
            'department_id' => ['required', 'integer', 'exists:departments,id']
        ]);
        $course = new Course();
        $course->code = $request->input('course_code');
        $course->name = $request->input('course_name');
        $course->credit_hours = $request->input('course_credit_hours');
        $course->department_id = $request->input('department_id');
        $course->save();
        return response(['message' => $course->code.' - '.$course->name.' Added successfully']);
    }

    public function delete($id)
    {
        $course = Course::with(['invs'])->where('id', $id)->firstOrFail();
        if(count($course->invs) > 0)
        {
            foreach ($course->invs as $inv)
            {
                $inv->users()->detach();
                $inv->delete();
            }
        }
        Course::destroy($id);
        return response(['message' => $course->code.' - '.$course->name.' Removed successfully']);
    }
}
