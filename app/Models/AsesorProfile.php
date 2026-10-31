<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AsesorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_met',
        'no_blangko',
        'no_sertifikat',
        'masa_berlaku',
        'bidang_keahlian',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'masa_berlaku' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schemes(): BelongsToMany
    {
        return $this->belongsToMany(CertificationScheme::class, 'asesor_scheme', 'asesor_profile_id', 'scheme_id')
            ->withTimestamps();
    }
}
