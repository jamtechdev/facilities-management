<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'description',
        'category',
        'quantity',
        'min_stock_level',
        'unit',
        'unit_cost',
        'assigned_to_type',
        'assigned_to_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_USED = 'used';
    const STATUS_RETURNED = 'returned';

    // Relationships
    public function assignedTo(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper methods
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock_level;
    }
}
