<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupClass extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'room_id', 'day_id', 'start_time', 'end_time'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }
}
