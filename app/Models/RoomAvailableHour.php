<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailableHour extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'day_id', 'start_time', 'end_time'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }
}
