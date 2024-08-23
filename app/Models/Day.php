<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function workingHours()
    {
        return $this->hasOne(WorkingHours::class);
    }
}
