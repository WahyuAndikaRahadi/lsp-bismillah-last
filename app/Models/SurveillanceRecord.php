<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveillanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'email',
        'telepon',
        'no_sertifikat',
        'no_registrasi',
        'scheme_id',
        'tempat_kerja',
        'jabatan',
        'alamat_kantor',
        'proyek_saat_ini',
        'kesesuaian_skk',
        'keterangan',
        'status',
        'verified_by',
        'tgl_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tgl_verifikasi' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'terverifikasi' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'tidak_aktif' => 'bg-rose-50 text-rose-700 border border-rose-200',
            default => 'bg-amber-50 text-amber-700 border border-amber-200',
        };
    }

    public function kesesuaianLabel(): string
    {
        return match ($this->kesesuaian_skk) {
            'Y' => 'Sesuai Standar Kerja',
            'N' => 'Tidak Sesuai',
            default => 'Lainnya / Alih Profesi',
        };
    }
}
