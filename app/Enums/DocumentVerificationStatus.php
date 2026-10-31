<?php

namespace App\Enums;

enum DocumentVerificationStatus: string
{
    case Pending = 'pending';
    case Memenuhi = 'memenuhi';
    case TidakMemenuhi = 'tidak_memenuhi';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Belum Diperiksa',
            self::Memenuhi => 'Memenuhi Syarat',
            self::TidakMemenuhi => 'Tidak Memenuhi',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-500/20',
            self::Memenuhi => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-600/20',
            self::TidakMemenuhi => 'bg-rose-50 text-rose-800 ring-1 ring-rose-600/20',
        };
    }
}
