<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ak02Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'asesor_id',
        'rekomendasi_akhir',
        'tindak_lanjut',
        'komentar_asesor',
        'ttd_asesi',
        'tgl_ttd_asesi',
        'ttd_asesor',
        'tgl_ttd_asesor',
    ];

    protected function casts(): array
    {
        return [
            'tgl_ttd_asesi' => 'datetime',
            'tgl_ttd_asesor' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(AssessmentApplication::class, 'application_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Ak02UnitAssessment::class, 'ak02_assessment_id');
    }

    public function isKompeten(): bool
    {
        return $this->rekomendasi_akhir === 'K';
    }
}
