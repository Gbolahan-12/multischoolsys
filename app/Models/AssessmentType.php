<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentType extends Model
{
    //
    protected $fillable = [
        'name',
        'max_score',
        'school_id'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
