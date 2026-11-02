<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlenoMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'nomor_ba_pleno',
        'tanggal_pleno',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'ketua_komite',
        'anggota_komite',
        'catatan_pleno',
        'status',
        'ttd_ketua',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pleno' => 'date',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class, 'schedule_id');
    }

    public static function generateNomorBaPleno(): string
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

        return "{$nomorUrut}/BA-PLENO/{$romawi}/{$tahun}";
    }

    public static function generateNomorSertifikat(CertificationScheme $scheme): string
    {
        $tahun = date('Y');
        $prefix = "BNSP-{$scheme->kode_skema}-{$tahun}-";
        $latest = AssessmentApplication::where('no_sertifikat', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($latest && preg_match('/(\d{4})$/', $latest->no_sertifikat, $matches)) {
            $next = str_pad(((int) $matches[1]) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $prefix.$next;
    }
}
