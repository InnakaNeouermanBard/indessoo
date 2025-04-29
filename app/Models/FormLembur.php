<?php
// model formlembur 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLembur extends Model
{
    //
    protected $fillable = [
        'nik',
        'nama_karyawan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'overtime',
        'status',
    ];
}
