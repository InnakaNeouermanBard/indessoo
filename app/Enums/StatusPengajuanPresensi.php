<?php

namespace App\Enums;

enum StatusPengajuanPresensi: string
{
    case IZIN = "I";
    case SAKIT = "S";
    case CUTI = "C";

    public function name(): string
    {
        return match ($this) {
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::CUTI => 'Cuti',
        };
    }
}
