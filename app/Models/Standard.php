<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Standard extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_standar',
        'judul_standar',
        'jenis_standar',
        'nomor_sk',
        'tahun',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function schemes(): HasMany
    {
        return $this->hasMany(CertificationScheme::class, 'standard_id');
    }

    public function getCodeAttribute(): string
    {
        return $this->kode_standar ?? '';
    }

    public function getTitleAttribute(): string
    {
        return $this->judul_standar ?? '';
    }
}
