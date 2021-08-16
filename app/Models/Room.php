<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['number', 'users_limit'];

    public function invs()
    {
        return $this->hasMany(Inv::class)->orderBy('date_time');
    }
}
