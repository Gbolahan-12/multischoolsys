<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SingleResultUpload extends Model
{
    //
    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'school_id',
        'assessment_type',
        'score',
        'term',
        'session',
        'uploaded_by',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function getGradeAttribute()
    {
        $score = $this->score;

        return match (true) {
            $score >= 70 => 'A',
            $score >= 60 => 'B',
            $score >= 50 => 'C',
            $score >= 45 => 'D',
            $score >= 40 => 'E',
            default => 'F',
        };
    }

    public function getRemarkAttribute()
    {
        return match ($this->grade) {
            'A' => 'Excellent',
            'B' => 'Very Good',
            'C' => 'Good',
            'D' => 'Fair',
            'E' => 'Pass',
            default => 'Fail',
        };
    }
}
