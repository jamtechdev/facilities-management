<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'reminder_day',
        'suggestion',
        'is_completed',
        'due_date',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    // Reminder day constants
    const DAY_30 = '30';
    const DAY_60 = '60';
    const DAY_90 = '90';

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
