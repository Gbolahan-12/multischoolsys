<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ClassSection extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'name'];

    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('class_sections.school_id', auth()->user()->school_id);
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'section_id');
    }
}