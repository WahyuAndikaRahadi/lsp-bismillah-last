<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TukVerification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'verification_date' => 'date',
        'facility_checklist' => 'array',
    ];

    public function tuk(): BelongsTo
    {
        return $this->belongsTo(Tuk::class, 'tuk_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(AsesorProfile::class, 'asesor_id');
    }
}
