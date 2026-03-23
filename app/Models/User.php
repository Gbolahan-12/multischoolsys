<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'school_id',
        'school_email',
        'fullname',
        'role',
        'email',
        'staff_id',
        'password',
        'is_active',
        'banned_at',
        'banned_by',
        'last_login_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // protected static function booted(): void
    // {
    //     static::addGlobalScope('school', function (Builder $builder) {
    //         if (Auth::check()) {
    //             $builder->where('users.school_id', Auth::user()->school_id);
    //         }
    //     });
    // }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function bannedUsers()
    {
        return $this->hasMany(User::class, 'banned_by');
    }

    public function formTeacherOf()
    {
        return $this->hasMany(SchoolClass::class, 'form_teacher_id');
    }

    public function subjectAssignments()
    {
        return $this->hasMany(ClassSubjectAssignment::class, 'teacher_id');
    }

    public function uploadedResults()
    {
        return $this->hasMany(Result::class, 'uploaded_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // -------------------------------------------------------
    // Role Helper Methods
    // -------------------------------------------------------

    public function isProprietor(): bool
    {
        return $this->role === 'proprietor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isBanned(): bool
    {
        return ! is_null($this->banned_at);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('role', 'staff');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    // app/Models/User.php

    public static function generateStaffId(int $schoolId): string
    {
        $school = \App\Models\School::find($schoolId);
        $acronym = $school?->acronym ?? 'STF';

        $count = static::where('school_id', $schoolId)
            ->whereIn('role', ['admin', 'staff', 'school-user'])
            ->count();

        $next = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "{$acronym}/STA/{$next}";
        // e.g. AMC/STA/001
    }
}
