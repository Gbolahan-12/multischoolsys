<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_id',
        'amount_paid',
        'balance',
        'status',
        'payment_date',
        'payment_method',
        'reference',
        'note',
        'received_by',
    ];

    protected $casts = [
        'amount_paid'  => 'decimal:2',
        'balance'      => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('payments.school_id', Auth::user()->school_id);
            }
        });
    }

    public function school()     { return $this->belongsTo(School::class); }
    public function student()    { return $this->belongsTo(Student::class); }
    public function fee()        { return $this->belongsTo(Fee::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function receivedBy()
{
    return $this->belongsTo(\App\Models\User::class, 'received_by');
}

    // Scopes
    public function scopePaid(Builder $q)    { return $q->where('status', 'paid'); }
    public function scopeOwing(Builder $q)   { return $q->where('status', 'owing'); }
    public function scopePartial(Builder $q) { return $q->where('status', 'partial'); }
}