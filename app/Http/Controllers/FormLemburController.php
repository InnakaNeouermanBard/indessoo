<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\FormLembur;
use Illuminate\Http\Request;

class FormLemburController extends Controller
{
    //
    public function index(Request $request)
    {

        $karyawan = FormLembur::all();
        $niks = Karyawan::all();
        return view('admin.form-lembur.index', compact('karyawan', 'niks'));
    }
    public function getKaryawanData($nik)
    {
        $karyawan = Karyawan::where('nik', $nik)->first();

        if ($karyawan) {
            return response()->json([
                'nik' => $karyawan->nik,
                'nama_lengkap' => $karyawan->nama_lengkap,
            ]);
        }

        return response()->json(null);
    }



    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'overtime' => 'required|integer',
        ]);

        // Cari nama karyawan berdasarkan NIK
        $karyawan = Karyawan::where('nik', $request->nik)->first();

        FormLembur::create([
            'nik' => $request->nik,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'tanggal' => $request->tanggal,
            'overtime' => $request->overtime,
        ]);

        return redirect()->route('form-lembur.index')->with('success', 'Data lembur berhasil disimpan.');
    }

    public function edit($id)
    {
        $karyawan = FormLembur::findOrFail($id);
        return response()->json($karyawan);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'nama_karyawan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'overtime' => 'required|integer',
        ]);

        $formLembur = FormLembur::findOrFail($id);
        $formLembur->update([
            'nik' => $request->nik,
            'nama_karyawan' => $request->nama_karyawan,
            'tanggal' => $request->tanggal,
            'overtime' => $request->overtime,
        ]);

        return redirect()->route('form-lembur.index')->with('success', 'Data lembur berhasil diperbarui.');
    }




    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:form_lemburs,id',
        ]);

        $formLembur = FormLembur::findOrFail($request->id);
        $formLembur->delete();

        return redirect()->route('form-lembur.index')->with('success', 'Data Lembur Karyawan berhasil dihapus.');
    }
}
