<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLembur extends Model
{
    //
    protected $fillable = [
        'nik',
        'nama_karyawan',
        'tanggal',
        'overtime',
    ];
}
