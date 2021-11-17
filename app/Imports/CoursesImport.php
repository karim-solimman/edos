<?php

namespace App\Imports;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CoursesImport implements ToModel,WithHeadingRow, WithValidation
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
        $dep_name = $row['department'];
        $dep = DB::table('departments')->where('name', 'like', "%{$dep_name}%")->first();
        return new Course([
            'code' => strtoupper($row['code']),
            'name' => ucwords(strtolower($row['name'])),
            'credit_hours' => $row['credit'],
            'department_id' => $dep->id,
        ]);
    }
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:courses,code'],
            'name' => ['required'],
            'credit' => ['nullable', 'integer', 'digits:1'],
        ];
    }
    public function getRowCount(): int
    {
        return $this->rows;
    }
}
