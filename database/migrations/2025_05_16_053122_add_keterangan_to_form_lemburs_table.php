<?php

// database/migrations/xxxx_xx_xx_add_keterangan_to_form_lemburs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToFormLembursTable extends Migration
{
    public function up()
    {
        Schema::table('form_lemburs', function (Blueprint $table) {
            $table->text('keterangan')->nullable();  // Menambahkan kolom keterangan
        });
    }

    public function down()
    {
        Schema::table('form_lemburs', function (Blueprint $table) {
            $table->dropColumn('keterangan');  // Menghapus kolom keterangan jika rollback
        });
    }
};