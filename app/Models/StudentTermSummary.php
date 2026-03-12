<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTermSummary extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'term',
        'session',
        'total_score',
        'average_score',
        'position'
    ];

    /*
    ======================
    RELATIONSHIPS
    ======================
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class,'class_id');
    }
}
