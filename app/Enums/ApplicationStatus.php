<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Diajukan = 'diajukan';
    case Revisi = 'revisi';
    case Diterima = 'diterima';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Diajukan => 'Menunggu Verifikasi',
            self::Revisi => 'Perlu Revisi',
            self::Diterima => 'Diterima (Terverifikasi)',
            self::Ditolak => 'Ditolak',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-500/20',
            self::Diajukan => 'bg-amber-50 text-amber-800 ring-1 ring-amber-600/20',
            self::Revisi => 'bg-orange-50 text-orange-800 ring-1 ring-orange-600/20',
            self::Diterima => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/20',
            self::Ditolak => 'bg-rose-50 text-rose-800 ring-1 ring-rose-600/20',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::Draft, self::Revisi]);
    }

    public function isVerified(): bool
    {
        return $this === self::Diterima;
    }
}
