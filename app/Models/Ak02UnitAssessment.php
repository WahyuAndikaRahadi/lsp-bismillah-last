<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ak02UnitAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ak02_assessment_id',
        'unit_id',
        'metode_observasi_langsung',
        'metode_portofolio',
        'metode_pertanyaan',
        'rekomendasi_unit',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'metode_observasi_langsung' => 'boolean',
            'metode_portofolio' => 'boolean',
            'metode_pertanyaan' => 'boolean',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Ak02Assessment::class, 'ak02_assessment_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CompetencyUnit::class, 'unit_id');
    }

    public function isKompeten(): bool
    {
        return $this->rekomendasi_unit === 'K';
    }
}
