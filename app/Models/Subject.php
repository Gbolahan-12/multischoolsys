<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
    ];

    // -------------------------------------------------------
    // Global Scope
    // -------------------------------------------------------
    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('subjects.school_id', auth()->user()->school_id);
            }
        });
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classAssignments()
    {
        return $this->hasMany(ClassSubjectAssignment::class, 'subject_id');
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject_assignments', 'subject_id', 'class_id')
                    ->withPivot('teacher_id', 'session_id')
                    ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'subject_id');
    }
}