<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'agreed_weekly_hours',
        'agreed_monthly_hours',
        'billing_frequency',
        'lead_id',
        'notes',
        'is_active',
        'type',
        'converted_by',
        'converted_at',
        'lead_name',
        'lead_company',
        'lead_email',
        'lead_phone',
        'lead_avatar',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'converted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class)->withTrashed();
    }

    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'client_staff_assignments')
            ->withPivot('assigned_weekly_hours', 'assigned_monthly_hours', 'assignment_start_date', 'assignment_end_date', 'is_active')
            ->withTimestamps();
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function jobPhotos(): HasMany
    {
        return $this->hasMany(JobPhoto::class);
    }

    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function inventory(): MorphMany
    {
        return $this->morphMany(Inventory::class, 'assigned_to');
    }
}
