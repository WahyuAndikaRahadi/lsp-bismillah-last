<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apl02Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'apl02_assessment_id',
        'kuk_id',
        'penilaian_mandiri',
        'bukti_pendukung',
        'is_valid',
        'is_asli',
        'is_terkini',
        'is_memadai',
        'catatan_asesor',
    ];

    protected function casts(): array
    {
        return [
            'is_valid' => 'boolean',
            'is_asli' => 'boolean',
            'is_terkini' => 'boolean',
            'is_memadai' => 'boolean',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Apl02Assessment::class, 'apl02_assessment_id');
    }

    public function kuk(): BelongsTo
    {
        return $this->belongsTo(WorkKeyCriteria::class, 'kuk_id');
    }

    public function isKompeten(): bool
    {
        return $this->penilaian_mandiri === 'K';
    }

    public function isAllVatm(): bool
    {
        return $this->is_valid && $this->is_asli && $this->is_terkini && $this->is_memadai;
    }
}
