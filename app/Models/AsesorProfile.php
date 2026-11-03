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

    public function getNikAttribute(): ?string
    {
        return $this->user?->nik;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->user?->phone;
    }

    public function isCredentialValid($referenceDate = null): bool
    {
        if (! $this->is_active || ! $this->masa_berlaku) {
            return false;
        }

        $ref = $referenceDate ? \Carbon\Carbon::parse($referenceDate)->startOfDay() : now()->startOfDay();

        return \Carbon\Carbon::parse($this->masa_berlaku)->startOfDay()->gte($ref);
    }

    public function isQualifiedForScheme($schemeIdOrCode): bool
    {
        if (is_numeric($schemeIdOrCode)) {
            return $this->schemes()->where('certification_schemes.id', $schemeIdOrCode)->exists()
                || \Illuminate\Support\Facades\DB::table('m_schemes')->where('id', $schemeIdOrCode)->where('code', $this->bidang_keahlian)->exists()
                || (! empty($this->bidang_keahlian) && stripos($this->bidang_keahlian, 'otomotif') !== false);
        }

        return $this->schemes()->where('code', $schemeIdOrCode)->exists()
            || $this->bidang_keahlian === $schemeIdOrCode
            || (! empty($this->bidang_keahlian) && stripos($this->bidang_keahlian, 'otomotif') !== false);
    }

    public function getLicenseValidUntilAttribute()
    {
        return $this->masa_berlaku;
    }
}
