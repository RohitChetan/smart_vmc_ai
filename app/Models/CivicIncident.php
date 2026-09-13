<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CivicIncident extends Model
{
    protected $fillable = [
        'incident_number',
        'category_id',
        'department_id',
        'ward_id',
        'latitude',
        'longitude',
        'location_accuracy',
        'title',
        'description',
        'priority',
        'status',
        'report_count',
        'duplicate_confidence',
        'first_reported_at',
        'last_reported_at',
        'due_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'location_accuracy' => 'float',
        'report_count' => 'integer',
        'duplicate_confidence' => 'float',
        'first_reported_at' => 'datetime',
        'last_reported_at' => 'datetime',
        'due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    // public function complaints(): HasMany
    // {
    //     return $this->hasMany(Complaint::class);
    // }
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'incident_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(IncidentAssignment::class, 'incident_id');
    }

    public function resolutionProofs(): HasMany
    {
        return $this->hasMany(ResolutionProof::class, 'incident_id');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(
            IncidentEscalation::class,
            'incident_id'
        );
    }
}