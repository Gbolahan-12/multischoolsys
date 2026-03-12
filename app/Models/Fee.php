<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'fee_type_id',
        'session_id',
        'term_id',
        'class_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('fees.school_id', Auth::user()->school_id);
            }
        });
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------
    public function school()      { return $this->belongsTo(School::class); }
    public function feeType()     { return $this->belongsTo(FeeType::class); }
    public function session()     { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term()        { return $this->belongsTo(Term::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function payments()    { return $this->hasMany(Payment::class); }
    public function class()
{
    return $this->belongsTo(SchoolClass::class, 'class_id');
}

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    public function getTotalCollectedAttribute(): float
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getTotalOutstandingAttribute(): float
    {
        return $this->payments()->sum('balance');
    }
}