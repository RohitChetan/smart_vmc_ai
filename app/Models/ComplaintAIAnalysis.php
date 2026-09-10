<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintAIAnalysis extends Model
{
    protected $table = 'complaint_ai_analyses';

    protected $fillable = [
        'complaint_id',
        'model_name',
        'model_version',
        'predicted_category',
        'confidence',
        'detections',
        'raw_result',
        'status',
        'error_message',
    ];

    protected $casts = [
        'confidence' => 'float',
        'detections' => 'array',
        'raw_result' => 'array',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}