<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MkvaAssignment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'assignment_date' => 'date',
        'review_aspects' => 'array',
    ];

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(AsesorProfile::class, 'asesor_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class, 'schedule_id');
    }
}
