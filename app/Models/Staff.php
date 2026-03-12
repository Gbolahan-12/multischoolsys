<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'school_id',
    ];

    public function classes()
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_staff'
        );
    }
    public function schooll(){
        return $this->belongsTo(school::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

    // public function subjectAssignments(){
    //     return $this->hasMany(Subje)
    // }
}

