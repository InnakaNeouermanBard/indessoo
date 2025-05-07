<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengajuan_presensi', function (Blueprint $table) {
            // Tambah kolom tanggal_mulai dan tanggal_selesai jika belum ada 
            if (!Schema::hasColumn('pengajuan_presensi', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->after('status');
            }

            if (!Schema::hasColumn('pengajuan_presensi', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->after('tanggal_mulai');
            }

            // Opsional: hapus kolom tanggal_pengajuan jika sudah tidak dipakai 
            if (Schema::hasColumn('pengajuan_presensi', 'tanggal_pengajuan')) {
                $table->dropColumn('tanggal_pengajuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_presensi', function (Blueprint $table) {
            // Rollback: hapus kolom tanggal_mulai dan tanggal_selesai 
            if (Schema::hasColumn('pengajuan_presensi', 'tanggal_mulai')) {
                $table->dropColumn('tanggal_mulai');
            }

            if (Schema::hasColumn('pengajuan_presensi', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }

            // Rollback: kembalikan kolom tanggal_pengajuan jika dihapus sebelumnya 
            if (!Schema::hasColumn('pengajuan_presensi', 'tanggal_pengajuan')) {
                $table->date('tanggal_pengajuan')->nullable()->after('status');
            }
        });
    }
};
