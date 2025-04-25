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
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_nik');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->date('tanggal');
            $table->boolean('is_libur')->default(false);
            $table->timestamps();
        });

        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->foreign('karyawan_nik')->references('nik')->on('karyawan')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};
