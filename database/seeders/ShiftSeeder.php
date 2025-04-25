<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('shifts')->insert([
            [
                'nama' => 'Pagi',
                'warna' => '#00ff00',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '16:00:00',
            ],
            [
                'nama' => 'Siang',
                'warna' => '#0000ff',
                'waktu_mulai' => '12:00:00',
                'waktu_selesai' => '20:00:00',
            ],
            [
                'nama' => 'Malam',
                'warna' => '#ff00ff',
                'waktu_mulai' => '20:00:00',
                'waktu_selesai' => '04:00:00',
            ],
        ]);
    }
}
