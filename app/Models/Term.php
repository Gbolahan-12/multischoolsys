<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
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

    // -------------------------------------------------------
    // Global Scope
    // -------------------------------------------------------
    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('terms.school_id', auth()->user()->school_id);
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

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentClassAssignment::class);
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    public function setAsCurrent(): void
    {
        // Only one current term per school at a time
        static::withoutGlobalScope('school')
            ->where('school_id', $this->school_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->update(['is_current' => true]);

        // Also make the parent session current
        $this->session->setAsCurrent();
    }

    public function getFormattedNameAttribute(): string
    {
        return ucfirst($this->name) . ' Term — ' . $this->session->name;
    }
}