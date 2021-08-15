<?php

namespace App\Imports;

use App\Models\Inv;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InvsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $rows = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        ++$this->rows;
        $course_code = strtoupper($row['course']);
        $course = DB::table('courses')->where('code', 'like', "%{$course_code}%")->first();
        $room_number = strtoupper($row['room']);
        $room = DB::table('roomw')->where('number', 'like',"%{$room_number}%")->first();
        $date_time = Date::excelToDateTimeObject($row['date'].$row['time'])->format('Y-m-d H:i');
        return new Inv([
            'date_time' => $date_time,
            'duration' => $row['duration'],
            'users_count' => 0,
            'users_limit' => $room->users_limit,
            'course_id' => $course->id
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => ['required'],
            'time' => ['required'],
            'duration' => ['required', 'integer'],
            'course' => ['required', 'string'],
            'room' => ['required', 'regex: /[a-zA-Z][0-9][0-9][0-9]/']
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
