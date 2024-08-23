<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHours extends Model
{
    protected $fillable = ['day_id', 'is_working_day', 'opening_time', 'closing_time'];

    protected $casts = [
        'is_working_day' => 'boolean',
    ];

    public function day()
    {
        return $this->belongsTo(Day::class);
    }
}
