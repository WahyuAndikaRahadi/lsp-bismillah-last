<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LspProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lsp',
        'kode_lsp',
        'no_lisensi_bnsp',
        'alamat',
        'telepon',
        'email',
        'website',
        'nama_ketua',
        'no_sk_lisensi',
        'masa_berlaku_lisensi',
        'logo',
        'ttd_ketua',
        'kop_surat',
    ];

    protected function casts(): array
    {
        return [
            'masa_berlaku_lisensi' => 'date',
        ];
    }

    public static function current(): ?self
    {
        return self::first();
    }
}
