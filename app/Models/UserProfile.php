<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'qualification',
        'employment_date',
    ];

    protected $casts = [
        'date_of_birth'   => 'date',
        'employment_date' => 'date',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}