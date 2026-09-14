<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'department_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(CivicIncident::class, 'category_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'ai_category_id');
    }
}
