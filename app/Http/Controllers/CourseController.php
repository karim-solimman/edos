<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('department')->withCount('invs')->get();
        return response(['courses' => $courses], 201);
    }

    public function getByDepartment(Request $request)
    {
        $request->validate([
           'user_id' => ['required', 'integer', 'exists:users,id']
        ]);
        $user = User::with('department')->where('id',$request->input('user_id'))->firstOrFail();
        if (!$user->department)
        {
            return response(['message' => $user->name.' - Not attached to department.'],402);
        }
        if (!auth()->user()->tokenCan('de'))
        {
            return response(['message' => $user->name." - Don't have perimission."],402);
        }
        $courses = Course::where('department_id', $user->department->id)->get();
        return response(['courses' => $courses],201);
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

    public function editInformation(Request $request)
    {
        $request->validate([
           'course_id' => ['required', 'integer', 'exists:courses,id'],
            'course_code' => ['required', 'string'],
            'course_name' => ['required', 'string' , 'min:10'],
            'course_credit_hours' => ['required', 'integer'],
            'department_id' => ['required', 'integer', 'exists:departments,id']
        ]);
        $course = Course::where('id', $request->input('course_id'))->firstOrFail();
        if(Course::where('code', strtoupper($request->input('course_code')))->get()->count() > 0 && $course->code != strtoupper($request->input('course_code')))
        {
            return response(['message' => $course->code.' Already Exists'],402);
        }
        $course->code = strtoupper($request->input('course_code'));
        $course->name = $request->input('course_name');
        $course->credit_hours = $request->input('course_credit_hours');
        $course->department_id = $request->input('department_id');
        $course->save();

        return response(['message' => $course->code.' Updated successfully'],201);
    }

    public function updateDataTime(Request $request)
    {
        $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i']
        ]);
        $date_time = $request->input('date').' '.$request->input('time');
        $course = Course::with(['invs'])->where('id',$request->input('course_id'))->firstOrFail();
        if (count($course->invs) <= 0 )
        {
            return response(['message' => $course->code.' Has no invs to be updated'],402);
        }
        $course->invs()->update(['date_time' => $date_time]);
        return response(['message' => 'date and time updated to '.Carbon::parse($date_time)->toDateString().' at '.Carbon::parse($date_time)->format('H:i A')],201);
    }

    public function removeAllInvs(Request $request)
    {
        $request->validate([
           'course_id' => ['required', 'integer', 'exists:courses,id']
        ]);
        $course = Course::with(['invs'])->where('id',$request->input('course_id'))->firstOrFail();
        if (count($course->invs)>0)
        {
            foreach ($course->invs as $inv)
            {
                $inv->users()->detach();
            }
            $course->invs()->delete();
        }

        return response(['message' => $course->code.' All invs removed successfully'],201);
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
