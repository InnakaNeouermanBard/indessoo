<?php

namespace Database\Seeders;

use App\Enums\StatusPengajuanPresensi;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengajuanPresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            DB::table('pengajuan_presensi')->insert([
                "nik" => "12345",
                "tanggal_mulai" => date_create("2024-" . Carbon::now()->format('m') . "-" . $i)->format("Y-m
d"),
                "tanggal_selesai" => date_create("2024-" . Carbon::now()->format('m') . "-" . ($i + 1))
                    ->format("Y-m-d"), // Menambahkan tanggal_selesai 
                "status" => fake()->randomElement([
                    StatusPengajuanPresensi::IZIN,
                    StatusPengajuanPresensi::SAKIT
                ]),
                "keterangan" => fake()->sentence(),
                "status_approved" => fake()->randomElement(['1', '2', '3']),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);

            DB::table('pengajuan_presensi')->insert([
                "nik" => "12346",
                "tanggal_mulai" => date_create("2024-" . Carbon::now()->format('m') . "-" . $i)->format("Y-m
d"),
                "tanggal_selesai" => date_create("2024-" . Carbon::now()->format('m') . "-" . ($i + 1))
                    ->format("Y-m-d"), // Menambahkan tanggal_selesai 
                "status" => fake()->randomElement([
                    StatusPengajuanPresensi::IZIN,
                    StatusPengajuanPresensi::SAKIT
                ]),
                "keterangan" => fake()->sentence(),
                "status_approved" => fake()->randomElement(['1', '2', '3']),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
        }
    }
}
