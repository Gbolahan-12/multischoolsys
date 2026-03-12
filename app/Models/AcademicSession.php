<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    // Global Scope — auto-filter by school
    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('academic_sessions.school_id', auth()->user()->school_id);
            }
        });
    }

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class, 'session_id');
    }

    public function currentTerm()
    {
        return $this->hasOne(Term::class, 'session_id')->where('is_current', true);
    }

    public function students()
    {
        return $this->hasMany(StudentClassAssignment::class, 'session_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'session_id');
    }

    public function fees()
    {
        return $this->hasMany(Fee::class, 'session_id');
    }

    // Scopes
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    // Helpers
    public function setAsCurrent(): void
    {
        // Unset any existing current session for this school first
        static::withoutGlobalScope('school')
            ->where('school_id', $this->school_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->update(['is_current' => true]);
    }
}