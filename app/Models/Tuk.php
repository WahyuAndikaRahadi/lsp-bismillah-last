<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_tuk',
        'nama_tuk',
        'jenis_tuk',
        'alamat',
        'telepon',
        'email',
        'penanggung_jawab',
        'pic_phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->nama_tuk ?? '';
    }

    public function getCodeAttribute(): string
    {
        return $this->kode_tuk ?? '';
    }

    public function getTypeAttribute(): string
    {
        return $this->jenis_tuk ?? '';
    }

    public function getJenisBadgeClassesAttribute(): string
    {
        return match ($this->jenis_tuk) {
            'Mandiri' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
            'Tempat Kerja' => 'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20',
            default => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
        };
    }
}
