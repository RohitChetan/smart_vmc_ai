<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentEscalation extends Model
{
    protected $fillable = [
        'incident_id',
        'level',
        'reason',
        'triggered_at',
        'resolved_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(
            CivicIncident::class,
            'incident_id'
        );
    }
}