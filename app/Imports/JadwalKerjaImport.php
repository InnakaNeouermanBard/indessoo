<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class JadwalKerjaImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    /**
     * @param Collection $rows
     * @return Collection
     */
    public function collection(Collection $rows)
    {
        // Kembalikan baris sebagai koleksi associative
        return $rows;
    }
}
