<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'scheme_id',
        'tuk_id',
        'kode_jadwal',
        'nama_jadwal',
        'tgl_pra_asesmen',
        'tgl_asesmen',
        'tgl_selesai',
        'tgl_buka_daftar',
        'tgl_tutup_daftar',
        'kuota_peserta',
        'id_jadwal_bnsp',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pra_asesmen' => 'date',
            'tgl_asesmen' => 'date',
            'tgl_selesai' => 'date',
            'tgl_buka_daftar' => 'date',
            'tgl_tutup_daftar' => 'date',
            'kuota_peserta' => 'integer',
            'status' => ScheduleStatus::class,
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function tuk(): BelongsTo
    {
        return $this->belongsTo(Tuk::class, 'tuk_id');
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssessmentApplication::class, 'schedule_id');
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->applications()->where('status', '!=', 'ditolak')->count();
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota_peserta - $this->enrolled_count);
    }

    public function getFormattedTanggalAttribute(): string
    {
        if ($this->tgl_selesai && $this->tgl_selesai->ne($this->tgl_asesmen)) {
            return $this->tgl_asesmen->format('d M Y') . ' - ' . $this->tgl_selesai->format('d M Y');
        }

        return $this->tgl_asesmen->format('d M Y');
    }
}
