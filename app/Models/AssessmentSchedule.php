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

    public function scheduleAsesors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScheduleAsesor::class, 'schedule_id');
    }

    public function asesors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'schedule_asesors', 'schedule_id', 'asesor_id')
            ->withPivot(['peran', 'no_surat_tugas', 'tgl_surat_tugas'])
            ->withTimestamps();
    }

    public function ak05Report(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ak05Report::class, 'schedule_id');
    }

    public function plenoMeeting(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PlenoMeeting::class, 'schedule_id');
    }

    public function getScheduleCodeAttribute(): string
    {
        return $this->kode_jadwal;
    }

    public function getAssessmentDateAttribute()
    {
        return $this->tgl_asesmen;
    }

    public function getPraAssessmentDateAttribute()
    {
        return $this->tgl_pra_asesmen;
    }

    public function getQuotaAttribute(): int
    {
        return (int) $this->kuota_peserta;
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
