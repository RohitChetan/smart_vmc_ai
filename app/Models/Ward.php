<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    protected $fillable = [
        'ward_no',
        'name',
        'address',
        'boundary_geojson',
        'is_active',
    ];

    protected $casts = [
        'boundary_geojson' => 'array',
        'is_active' => 'boolean',
    ];

    public function incidents(): HasMany
    {
        return $this->hasMany(CivicIncident::class, 'ward_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'ward_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'ward_id');
    }
}
