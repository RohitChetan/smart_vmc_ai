<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}