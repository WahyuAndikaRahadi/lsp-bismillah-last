<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case Direncanakan = 'direncanakan';
    case Pendaftaran = 'pendaftaran';
    case PraAsesmen = 'pra_asesmen';
    case Asesmen = 'asesmen';
    case Pleno = 'pleno';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Direncanakan => 'Direncanakan',
            self::Pendaftaran => 'Buka Pendaftaran',
            self::PraAsesmen => 'Pra-Asesmen',
            self::Asesmen => 'Pelaksanaan Asesmen',
            self::Pleno => 'Rapat Pleno',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Direncanakan => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-600/20',
            self::Pendaftaran => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
            self::PraAsesmen => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
            self::Asesmen => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/20',
            self::Pleno => 'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20',
            self::Selesai => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
            self::Dibatalkan => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
        };
    }

    public function isRegistrationOpen(): bool
    {
        return $this === self::Pendaftaran;
    }
}
