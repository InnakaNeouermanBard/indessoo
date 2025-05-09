<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Departemen::create([
        //     'kode' => 'D001',
        //     'nama' => 'Produksi',
        // ]);
        // Departemen::create([
        //     'kode' => 'D002',
        //     'nama' => 'Maintenance',
        // ]);
        // Departemen::create([
        //     'kode' => 'D003',
        //     'nama' => 'Quality Control',
        // ]);
        Departemen::create([
            'kode' => 'D004',
            'nama' => 'Gudang',
        ]);
        Departemen::create([
            'kode' => 'D005',
            'nama' => 'HSE',
        ]);
        Departemen::create([
            'kode' => 'D006',
            'nama' => 'Staff',
        ]);
    }
}
