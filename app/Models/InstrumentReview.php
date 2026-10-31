<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstrumentReview extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'review_date' => 'date',
        'review_criteria' => 'array',
    ];

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(AsesorProfile::class, 'asesor_id');
    }
}
