<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'main_teacher_id', 'assistant_teacher_id'];

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_student');
    }

    public function mainTeacher()
    {
        return $this->belongsTo(User::class, 'main_teacher_id');
    }

    public function assistantTeacher()
    {
        return $this->belongsTo(User::class, 'assistant_teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classes()
    {
        return $this->hasMany(GroupClass::class);
    }
}
