<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'admission_number',
        'first_name',
        'last_name',
        'other_name',
        'gender',
        'date_of_birth',
        'photo',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------
    // Global Scope
    // -------------------------------------------------------
    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->role !== 'super-admin') {
                $builder->where('students.school_id', auth()->user()->school_id);
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
        return $this->hasMany(StudentClassAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(StudentClassAssignment::class)
            ->whereHas('session', fn ($q) => $q->where('is_current', true))
            ->whereHas('term', fn ($q) => $q->where('is_current', true));
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->other_name} {$this->last_name}");
    }

    public function getShortNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Auto-generate admission number:

public static function generateAdmissionNumber(int $schoolId): string
{
    $school  = \App\Models\School::find($schoolId);
    $acronym = $school?->acronym ?? 'STU';

    // Count existing students in this school
    $count = static::withoutGlobalScope('school')
        ->where('school_id', $schoolId)
        ->count();

    $next = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    return "{$acronym}/STU/{$next}";
    // e.g. AMC/STU/001
}
}
