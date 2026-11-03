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

    protected static function booted(): void
    {
        static::saved(function ($tuk) {
            if (\Illuminate\Support\Facades\Schema::hasTable('m_tuks')) {
                \Illuminate\Support\Facades\DB::table('m_tuks')->updateOrInsert(
                    ['id' => $tuk->id],
                    [
                        'name' => $tuk->nama_tuk ?? $tuk->name ?? ('TUK ' . $tuk->id),
                        'address' => $tuk->alamat,
                        'phone' => $tuk->telepon,
                        'is_active' => $tuk->is_active ?? true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        static::deleted(function ($tuk) {
            if (\Illuminate\Support\Facades\Schema::hasTable('m_tuks')) {
                \Illuminate\Support\Facades\DB::table('m_tuks')->where('id', $tuk->id)->delete();
            }
        });
    }

    public function verifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TukVerification::class, 'tuk_id');
    }

    public function isVerified(): bool
    {
        return $this->verifications()
            ->where('recommendation', 'layak')
            ->whereIn('status', ['diverifikasi', 'disetujui'])
            ->exists();
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
