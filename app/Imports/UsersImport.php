<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithValidation, WithHeadingRow
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
        return new User([
            'name' => ucwords(strtolower($row['name'])),
            'email' => $row['email'],
            'department_id' => $dep->id,
        ]);
    }

    public function rules(): array
    {
        return  [
            'name' => ['required', 'string', 'regex:/^[aA-zZ]+\s[aA-zZ]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
