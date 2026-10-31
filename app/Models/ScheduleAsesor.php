<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleAsesor extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'asesor_id',
        'peran',
        'no_surat_tugas',
        'tgl_surat_tugas',
    ];

    protected function casts(): array
    {
        return [
            'tgl_surat_tugas' => 'date',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class, 'schedule_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    /**
     * Auto generate format nomor Surat Tugas Asesor: {NOMOR}/ST-LSP/{BULAN_ROMAWI}/{TAHUN}
     */
    public static function generateNomorSuratTugas(): string
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $currentMonth = (int) date('n');
        $romawi = $bulanRomawi[$currentMonth] ?? 'X';
        $tahun = date('Y');

        $latestCount = static::whereYear('created_at', $tahun)->count() + 1;
        $nomorUrut = str_pad($latestCount, 3, '0', STR_PAD_LEFT);

        return "{$nomorUrut}/ST-LSP/{$romawi}/{$tahun}";
    }
}
