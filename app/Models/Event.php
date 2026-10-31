<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_event',
        'nama_event',
        'tahun_anggaran',
        'sumber_anggaran',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(AssessmentSchedule::class, 'event_id')->orderBy('tgl_asesmen');
    }

    public function getFormattedPeriodeAttribute(): string
    {
        return $this->tgl_mulai->format('d M Y') . ' s.d. ' . $this->tgl_selesai->format('d M Y');
    }
}
