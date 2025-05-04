<?php

namespace App\Http\Controllers;

use App\Models\ExcelFile;
use Illuminate\Http\Request;
use App\Imports\JadwalKerjaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

class JadwalKerjaController extends Controller
{
    //
    public function index()
    {
        // Ambil daftar file terbaru, 10 per halaman
        $files = ExcelFile::latest()->paginate(10);
        return view('admin.jadwal.index', compact('files'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Simpan file ke storage
        $uploaded = $request->file('file');
        // Simpan ke storage/app/public/excel_uploads
        $path = $uploaded->store('excel_uploads', 'public');


        // Simpan metadata ke DB
        ExcelFile::create([
            'original_name' => $uploaded->getClientOriginalName(),
            'file_path'     => $path,
        ]);

        // Redirect supaya refresh tidak duplikat
        return redirect()
            ->route('jadwalkerja.index')
            ->with('success', 'File Excel berhasil di-upload dan disimpan.');
    }

    public function download($id)
    {
        $excelFile = ExcelFile::findOrFail($id);
        return response()->download(
            Storage::path($excelFile->file_path),
            $excelFile->original_name
        );
    }

    public function destroy(int $id): RedirectResponse
    {
        $excelFile = ExcelFile::findOrFail($id);

        // Hapus file fisik
        Storage::delete($excelFile->file_path);

        // Hapus record DB
        $excelFile->delete();

        return redirect()
            ->route('jadwalkerja.index')
            ->with('success', 'File berhasil dihapus.');
    }
}
