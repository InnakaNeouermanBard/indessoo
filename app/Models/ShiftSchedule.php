<?php
// model shifschedule 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    //
    protected $fillable = [
        'karyawan_nik',
        'shift_id',
        'tanggal',
        'is_libur',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nik', 'nik');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
