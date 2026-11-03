<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CertificationDecision extends Model
{
    protected $table = 't_certification_decisions';

    protected $fillable = [
        'assessment_id',
        'meeting_id',
        'decision',
        'notes',
        'verified_by',
        'signed_at',
        'certificate_number',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class, 'decision_id');
    }
}
