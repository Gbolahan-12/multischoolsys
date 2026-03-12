<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'level_id',
        'section_id',
        'form_teacher_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('classes.school_id', auth()->user()->school_id);
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

    public function level()
    {
        return $this->belongsTo(ClassLevel::class, 'level_id');
    }

    public function section()
    {
        return $this->belongsTo(ClassSection::class, 'section_id');
    }

    public function formTeacher()
    {
        return $this->belongsTo(User::class, 'form_teacher_id');
    }

    public function subjectAssignments()
    {
        return $this->hasMany(ClassSubjectAssignment::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject_assignments', 'class_id', 'subject_id')
                    ->withPivot('teacher_id', 'session_id')
                    ->withTimestamps();
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentClassAssignment::class, 'class_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'class_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    public function getFullNameAttribute(): string
    {
        $name = $this->name;
        if ($this->section) {
            $name .= ' ' . $this->section->name;
        }
        return $name;
    }

    public function studentsCount(?int $sessionId = null, ?int $termId = null): int
    {
        return $this->studentAssignments()
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->when($termId,    fn($q) => $q->where('term_id', $termId))
            ->count();
    }
}