<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama_karyawan');
            $table->date('tanggal');
            $table->integer('overtime')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_lemburs');
    }
};
