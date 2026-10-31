<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificationScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'standard_id',
        'kode_skema',
        'nama_skema',
        'jenis_skema',
        'kategori',
        'biaya',
        'ringkasan',
        'dokumen_skema',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'biaya' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(CompetencyUnit::class, 'scheme_id')->orderBy('urutan');
    }

    public function competencyUnits(): HasMany
    {
        return $this->units();
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(SchemeRequirement::class, 'scheme_id')->orderBy('urutan');
    }

    public function asesors(): BelongsToMany
    {
        return $this->belongsToMany(AsesorProfile::class, 'asesor_scheme', 'scheme_id', 'asesor_profile_id')
            ->withTimestamps();
    }

    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    public function getTotalUnitsAttribute(): int
    {
        return $this->units()->count();
    }
}
