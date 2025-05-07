<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'karyawan';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    // Tambahkan kolom kuota_cuti ke fillable
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'email',
        'password',
        'telepon',
        'jabatan',
        'kuota_cuti', // Tambahkan kuota_cuti
        'departemen_id',
        // field lainnya tetap sama
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class, 'karyawan_nik', 'nik');
    }
    public function sisaKuotaCuti()
    {
        // Hitung jumlah cuti yang sudah disetujui tahun ini
        $cutiTerpakai = DB::table('pengajuan_presensi')
            ->where('nik', $this->nik)
            ->where('status', 'C') // Status cuti
            ->where('status_approved', 2) // Yang sudah disetujui
            ->whereYear('tanggal_pengajuan', date('Y')) // Tahun ini 
            ->count();

        return $this->kuota_cuti - $cutiTerpakai;
    }
}
