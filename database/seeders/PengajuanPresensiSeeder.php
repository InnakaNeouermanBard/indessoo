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
            // Gunakan Carbon untuk menangani tanggal
            $tanggalMulai = Carbon::create(2024, Carbon::now()->month, $i); // Membuat tanggal mulai
            $tanggalSelesai = $tanggalMulai->copy()->addDay(); // Tanggal selesai 1 hari setelah tanggal mulai

            DB::table('pengajuan_presensi')->insert([
                "nik" => "12345",
                "tanggal_mulai" => $tanggalMulai->format("Y-m-d"), // Format yang benar
                "tanggal_selesai" => $tanggalSelesai->format("Y-m-d"), // Format yang benar
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
                "tanggal_mulai" => $tanggalMulai->format("Y-m-d"),
                "tanggal_selesai" => $tanggalSelesai->format("Y-m-d"),
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
