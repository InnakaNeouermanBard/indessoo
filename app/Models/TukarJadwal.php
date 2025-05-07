<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TukarJadwal extends Model
{
    use HasFactory;

    protected $table = 'tukar_jadwal';

    protected $fillable = [
        'nik_pengaju',
        'nik_penerima',
        'tanggal_pengajuan',  // Tanggal pertukaran jadwal (single date)
        'status',             // Status pertukaran: pending, approved, rejected
        'alasan',             // Alasan pertukaran jadwal
    ];

    // Relasi dengan karyawan (pengaju)
    public function pengaju()
    {
        return $this->belongsTo(Karyawan::class, 'nik_pengaju', 'nik');
    }

    // Relasi dengan karyawan (penerima)
    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'nik_penerima', 'nik');
    }

    // Accessor untuk memeriksa apakah pertukaran dibuat hari ini
    public function getIsCreatedTodayAttribute()
    {
        return $this->created_at->isToday();
    }
}
