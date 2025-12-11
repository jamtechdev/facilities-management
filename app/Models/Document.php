<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'description',
        'uploaded_by',
    ];

    // Document type constants
    const TYPE_AGREEMENT = 'agreement';
    const TYPE_PROPOSAL = 'proposal';
    const TYPE_SIGNED_FORM = 'signed_form';
    const TYPE_IMAGE = 'image';
    const TYPE_ID = 'id';
    const TYPE_CERTIFICATE = 'certificate';
    const TYPE_OTHER = 'other';

    // Relationships
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
