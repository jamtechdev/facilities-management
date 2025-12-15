<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientStaffAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'staff_id',
        'assigned_weekly_hours',
        'assigned_monthly_hours',
        'assignment_start_date',
        'assignment_end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'assigned_weekly_hours' => 'decimal:2',
        'assigned_monthly_hours' => 'decimal:2',
        'assignment_start_date' => 'date',
        'assignment_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
