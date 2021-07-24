<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inv extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'time', 'room_id', 'course_id'];

    public function users()
    {
        return $this->belongsToMany(User::class,'user_inv')->withPivot('created_at');
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
