<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'designation',
        'email',
        'phone',
        'city',
        'source',
        'stage',
        'assigned_staff_id',
        'converted_to_client_id',
        'notes',
        'converted_at',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    // Stage constants
    const STAGE_NEW_LEAD = 'new_lead';
    const STAGE_IN_PROGRESS = 'in_progress';
    const STAGE_QUALIFIED = 'qualified';
    const STAGE_NOT_QUALIFIED = 'not_qualified';
    const STAGE_JUNK = 'junk';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function convertedToClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'converted_to_client_id');
    }

    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function followUpTasks(): HasMany
    {
        return $this->hasMany(FollowUpTask::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    // Helper methods
    public function isQualified(): bool
    {
        return $this->stage === self::STAGE_QUALIFIED;
    }

    public function canConvertToClient(): bool
    {
        return $this->stage === self::STAGE_QUALIFIED && !$this->converted_to_client_id;
    }
}
