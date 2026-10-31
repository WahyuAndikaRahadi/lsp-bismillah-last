<?php

namespace App\Enums;

enum Apl02Status: string
{
    case Draft = 'draft';
    case Diajukan = 'diajukan';
    case Diverifikasi = 'diverifikasi';
    case PerluRevisi = 'perlu_revisi';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft Mandiri',
            self::Diajukan => 'Menunggu Tinjauan Asesor',
            self::Diverifikasi => 'Diverifikasi Asesor (Lanjut Uji)',
            self::PerluRevisi => 'Perlu Perbaikan Bukti',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-500/20',
            self::Diajukan => 'bg-amber-50 text-amber-800 ring-1 ring-amber-600/20',
            self::Diverifikasi => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/20',
            self::PerluRevisi => 'bg-orange-50 text-orange-800 ring-1 ring-orange-600/20',
        };
    }

    public function isVerified(): bool
    {
        return $this === self::Diverifikasi;
    }
}
