<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    // protected $fillable = [
    //     'complaint_number',
    //     'user_id',
    //     'user_category_id',
    //     'ai_category_id',
    //     'department_id',
    //     'ward_id',
    //     'latitude',
    //     'longitude',
    //     'location_accuracy',
    //     'description',
    //     'priority',
    //     'status',
    //     'ai_confidence',
    //     'ai_decision_reason',
    //     'submitted_at',
    //     'due_at',
    //     'resolved_at',
    //     'closed_at',
    // ];

    protected $fillable = [
        'complaint_number',
        'user_id',
        'incident_id',
        'user_category_id',
        'ai_category_id',
        'department_id',
        'ward_id',
        'latitude',
        'longitude',
        'location_accuracy',
        'description',
        'priority',
        'status',
        'ai_confidence',
        'ai_decision_reason',
        'submitted_at',
        'due_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'location_accuracy' => 'float',
        'ai_confidence' => 'float',
        'submitted_at' => 'datetime',
        'due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userCategory(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'user_category_id'
        );
    }

    public function aiCategory(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'ai_category_id'
        );
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ComplaintMedia::class);
    }

    public function aiAnalyses(): HasMany
    {
        return $this->hasMany(ComplaintAIAnalysis::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ComplaintAssignment::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ComplaintStatusHistory::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(CivicIncident::class);
    }
}