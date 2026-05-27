<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * Get the URL for the user's profile photo.
     * Maps the first word of the user's name (lowercased) to /assets/Images/{name}.png
     */
    public function getPhotoUrlAttribute(): string
    {
        // Priority 1: uploaded custom photo (user_{id}.png)
        $uploadedPath = public_path('assets/Images/user_' . $this->id . '.png');
        if (file_exists($uploadedPath)) {
            return asset('assets/Images/user_' . $this->id . '.png') . '?v=' . filemtime($uploadedPath);
        }

        // Priority 2: seeded photo by first name (agus.png, angel.png, etc.)
        $firstName = strtolower(explode(' ', trim($this->name))[0]);
        $seedPath = public_path('assets/Images/' . $firstName). '.png';
        if (file_exists($seedPath)) {
            return asset('assets/Images/' . $firstName . '.png');
        }

        // Fallback: auto-generated avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nik',
        'employee_id',
        'division_id',
        'name',
        'email',
        'password',
        'base_salary',
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
            'password' => 'hashed',
        ];
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /** Events this user is assigned to as PIC */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participants')
                    ->withPivot('is_pic')
                    ->withTimestamps();
    }
    public function weeklyReports()
    {
        return $this->hasMany(WeeklyReport::class);
    }

    public function dailyAttendances()
    {
        return $this->hasMany(DailyAttendance::class);
    }
}
