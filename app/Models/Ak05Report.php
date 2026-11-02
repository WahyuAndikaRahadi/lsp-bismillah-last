<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ak05Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'nomor_laporan',
        'aspek_positif',
        'kendala_hambatan',
        'saran_perbaikan',
        'ttd_asesor_lead',
        'tgl_laporan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_laporan' => 'date',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class, 'schedule_id');
    }

    public static function generateNomorLaporan(AssessmentSchedule $schedule): string
    {
        $year = date('Y');
        $month = date('m');

        return "LAP-AK05/{$schedule->kode_jadwal}/{$month}/{$year}";
    }
}
