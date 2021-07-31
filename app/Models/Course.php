<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'name', 'credit_hours', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function invs()
    {
        return $this->hasMany(Inv::class)->orderBy('date_time');
    }
}
