<?php

namespace App\Models;

use App\Enums\Apl02Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apl02Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'asesor_id',
        'status',
        'rekomendasi_asesor',
        'catatan_asesor',
        'ttd_asesi',
        'tgl_ttd_asesi',
        'ttd_asesor',
        'tgl_ttd_asesor',
    ];

    protected function casts(): array
    {
        return [
            'status' => Apl02Status::class,
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

    public function items(): HasMany
    {
        return $this->hasMany(Apl02Item::class, 'apl02_assessment_id');
    }

    public function isCompleted(): bool
    {
        return $this->items()->whereNull('penilaian_mandiri')->count() === 0;
    }

    public function isAllKompeten(): bool
    {
        return $this->items()->where('penilaian_mandiri', '!=', 'K')->count() === 0;
    }
}
