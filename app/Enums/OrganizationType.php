<?php

namespace App\Enums;

enum OrganizationType: string
{
    case DinasPariwisata = 'dinas_pariwisata';
    case Pokdarwis = 'pokdarwis';
    case HumasDestinasi = 'humas_destinasi';
    case KontenKreator = 'konten_kreator';
    case Akademisi = 'akademisi';
    case Universitas = 'universitas';
    case Lppm = 'lppm';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::DinasPariwisata => 'Dinas Pariwisata',
            self::Pokdarwis => 'Pokdarwis',
            self::HumasDestinasi => 'Humas Destinasi',
            self::KontenKreator => 'Konten Kreator',
            self::Akademisi => 'Akademisi',
            self::Universitas => 'Universitas',
            self::Lppm => 'LPPM',
            self::Lainnya => 'Lainnya',
        };
    }
}
