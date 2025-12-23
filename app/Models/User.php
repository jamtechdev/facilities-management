<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    // Relationships
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function lead()
    {
        return $this->hasOne(Lead::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    // Helper methods
    public function isAdmin(): bool
    {
        // Check if user has admin dashboard access (permission-based)
        return $this->can('view admin dashboard');
    }

    public function isStaff(): bool
    {
        return $this->can('view staff dashboard');
    }

    public function isClient(): bool
    {
        return $this->can('view client dashboard');
    }

    public function isLead(): bool
    {
        return $this->can('view lead dashboard');
    }
}
