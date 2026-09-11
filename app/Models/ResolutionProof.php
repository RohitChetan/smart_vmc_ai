<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolutionProof extends Model
{
    protected $fillable = [
        'complaint_id',
        'incident_id',
        'submitted_by',
        'type',
        'file_path',
        'mime_type',
        'file_size',
        'latitude',
        'longitude',
        'location_accuracy',
        'captured_at',
        'ai_status',
        'ai_confidence',
        'ai_result',
        'remarks',
        'verified_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'location_accuracy' => 'float',
        'captured_at' => 'datetime',
        'ai_confidence' => 'float',
        'ai_result' => 'array',
        'verified_at' => 'datetime',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(CivicIncident::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}