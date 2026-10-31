<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyCompetencyUnit extends Model
{
    use HasFactory;

    protected $table = 'competency_units';

    protected $fillable = [
        'scheme_id',
        'kode_unit',
        'judul_unit',
        'jenis_standar',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(CompetencyElement::class, 'unit_id')->orderBy('urutan');
    }
}
