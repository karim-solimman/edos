<?php

namespace App\Imports;

use App\Models\Inv;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InvsImport  implements ToModel, WithHeadingRow, WithValidation
{
    private $rows = 0;
    public function model(array $row)
    {
        ++$this->rows;
        $date = Date::excelToDateTimeObject($row['date'])->format('Y-m-d');
        $time = Date::excelToDateTimeObject($row['time'])->format('H:i');
        $course_code = strtoupper($row['course']);
        $course = DB::table('courses')->where('code', 'like', "%{$course_code}%")->first();
        $room_number = strtoupper($row['room']);
        $room = DB::table('rooms')->where('number', 'like',"%{$room_number}%")->first();
        return new Inv([
            'date_time' => $date." ".$time,
            'duration' => $row['duration'],
            'users_count' => 0,
            'users_limit' => $room->users_limit,
            'course_id' => $course->id,
            'room_id' => $room->id
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => ['required'],
            'time' => ['required'],
            'duration' => ['nullable', 'integer', 'min:1', 'max:5'],
            'course' => Rule::exists('courses','code'),
            'room' => ['required', 'regex: /[a-zA-Z][0-9][0-9][0-9]/', 'exists:rooms,number']
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
