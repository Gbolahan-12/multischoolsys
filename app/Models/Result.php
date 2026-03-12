<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'subject_id',
        'session_id',
        'term_id',
        'ca1_score',
        'ca2_score',
        'exam_score',
        'bonus_mark',
        'bonus_component',
        'total_score',
        'grade',
        'remark',
        'uploaded_by',
    ];

    protected $casts = [
        'ca1_score'   => 'decimal:2',
        'ca2_score'   => 'decimal:2',
        'exam_score'  => 'decimal:2',
        'bonus_mark'  => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    // Max scores per component
    const MAX_CA1  = 20;
    const MAX_CA2  = 20;
    const MAX_EXAM = 60;

    // -------------------------------------------------------
    // Global Scope
    // -------------------------------------------------------
    protected static function booted(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('results.school_id', auth()->user()->school_id);
            }
        });
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------
    public function school()      { return $this->belongsTo(School::class); }
    public function student()     { return $this->belongsTo(Student::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function session()     { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term()        { return $this->belongsTo(Term::class); }
    public function uploadedBy()  { return $this->belongsTo(User::class, 'uploaded_by'); }

    // -------------------------------------------------------
    // Core computation
    // -------------------------------------------------------

    /**
     * Apply bonus to the correct component and compute total.
     * Bonus cannot push component above its max.
     * Total cannot exceed 100.
     */
    public static function compute(
        float  $ca1,
        float  $ca2,
        float  $exam,
        float  $bonus,
        string $bonusComponent // 'ca1', 'ca2', 'exam', or ''
    ): array {
        // Cap raw scores at their maximums
        $ca1  = min($ca1,  self::MAX_CA1);
        $ca2  = min($ca2,  self::MAX_CA2);
        $exam = min($exam, self::MAX_EXAM);

        // Apply bonus to the correct component — cap at component max
        if ($bonus > 0 && $bonusComponent) {
            switch ($bonusComponent) {
                case 'ca1':
                    $space = self::MAX_CA1 - $ca1;
                    $ca1   = $ca1 + min($bonus, $space);
                    break;
                case 'ca2':
                    $space = self::MAX_CA2 - $ca2;
                    $ca2   = $ca2 + min($bonus, $space);
                    break;
                case 'exam':
                    $space = self::MAX_EXAM - $exam;
                    $exam  = $exam + min($bonus, $space);
                    break;
            }
        }

        $total = min(100, round($ca1 + $ca2 + $exam, 2));
        $grade  = self::computeGrade($total);
        $remark = self::computeRemark($grade);

        return compact('ca1', 'ca2', 'exam', 'total', 'grade', 'remark');
    }

    public static function computeGrade(float $total): string
    {
        return match(true) {
            $total >= 75 => 'A',
            $total >= 60 => 'B',
            $total >= 50 => 'C',
            $total >= 40 => 'D',
            $total >= 30 => 'E',
            default      => 'F',
        };
    }

    public static function computeRemark(string $grade): string
    {
        return match($grade) {
            'A'     => 'Excellent',
            'B'     => 'Very Good',
            'C'     => 'Good',
            'D'     => 'Pass',
            'E'     => 'Poor',
            default => 'Fail',
        };
    }
}