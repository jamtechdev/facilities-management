<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'client_id',
        'work_date',
        'clock_in_time',
        'clock_out_time',
        'hours_worked',
        'payable_hours',
        'notes',
        'is_approved',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'hours_worked' => 'decimal:2',
        'payable_hours' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function jobPhotos(): HasMany
    {
        return $this->hasMany(JobPhoto::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function calculateHours(): float
    {
        if (!$this->clock_in_time || !$this->clock_out_time) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->clock_in_time);
        $end = \Carbon\Carbon::parse($this->clock_out_time);
        return round($end->diffInMinutes($start) / 60, 2);
    }
}
