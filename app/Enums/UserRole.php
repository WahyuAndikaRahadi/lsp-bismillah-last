<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Asesor = 'asesor';
    case Asesi = 'asesi';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin LSP',
            self::Asesor => 'Asesor Kompetensi',
            self::Asesi => 'Asesi (Peserta)',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20',
            self::Asesor => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
            self::Asesi => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Asesor => 'asesor.dashboard',
            self::Asesi => 'asesi.dashboard',
        };
    }
}
