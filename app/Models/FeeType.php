<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeeType extends Model
{
    use HasFactory;

    protected $fillable = ['school_id',
        'type', 'name', 'description'];

    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('fee_types.school_id', Auth::user()->school_id);
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
}