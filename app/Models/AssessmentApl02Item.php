<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentApl02Item extends Model
{
    use HasFactory;

    protected $table = 't_assessment_apl02_items';

    protected $fillable = [
        'assessment_id',
        'kuk_id',
        'assessment_value',
        'selected_proof_type',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function kuk(): BelongsTo
    {
        return $this->belongsTo(Kuk::class);
    }
}
