<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('form_lemburs', function (Blueprint $table) {
        $table->time('jam_mulai')->after('tanggal'); // Menambahkan kolom jam_mulai
        $table->time('jam_selesai')->after('jam_mulai'); // Menambahkan kolom jam_selesai
    });
}

public function down()
{
    Schema::table('form_lemburs', function (Blueprint $table) {
        $table->dropColumn(['jam_mulai', 'jam_selesai']);
    });
}

};
