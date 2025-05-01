<?php
// model shift 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    //
    protected $fillable = ['nama', 'warna', 'waktu_mulai', 'waktu_selesai'];

    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
}
