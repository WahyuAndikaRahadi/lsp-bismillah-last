<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsesiProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kebangsaan',
        'alamat',
        'rt_rw',
        'provinsi_kode',
        'provinsi_nama',
        'kota_kode',
        'kota_nama',
        'kecamatan_kode',
        'kecamatan_nama',
        'kelurahan_kode',
        'kelurahan_nama',
        'kode_pos',
        'telepon',
        'pendidikan_terakhir',
        'pekerjaan',
        'nama_perusahaan',
        'jabatan',
        'alamat_kantor',
        'telp_kantor',
        'email_kantor',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt_rw ? 'RT/RW '.$this->rt_rw : null,
            $this->kelurahan_nama ? 'Kel. '.$this->kelurahan_nama : null,
            $this->kecamatan_nama ? 'Kec. '.$this->kecamatan_nama : null,
            $this->kota_nama,
            $this->provinsi_nama,
            $this->kode_pos,
        ]);

        return implode(', ', $parts);
    }
}
