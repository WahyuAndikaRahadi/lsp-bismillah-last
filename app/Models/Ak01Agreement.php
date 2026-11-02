<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ak01Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'asesor_id',
        'hari_tanggal',
        'waktu',
        'tuk_nama',
        'is_asesi_agreed',
        'is_asesor_agreed',
        'ttd_asesi',
        'tgl_ttd_asesi',
        'ttd_asesor',
        'tgl_ttd_asesor',
    ];

    protected function casts(): array
    {
        return [
            'hari_tanggal' => 'date',
            'is_asesi_agreed' => 'boolean',
            'is_asesor_agreed' => 'boolean',
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

    public function isFullySigned(): bool
    {
        return ! empty($this->ttd_asesi) && ! empty($this->ttd_asesor);
    }

    public function getSignedByAsesorAtAttribute()
    {
        return $this->tgl_ttd_asesor;
    }

    public function getIsSignedByAsesorAttribute(): bool
    {
        return ! empty($this->tgl_ttd_asesor) || (bool) $this->is_asesor_agreed;
    }
}
