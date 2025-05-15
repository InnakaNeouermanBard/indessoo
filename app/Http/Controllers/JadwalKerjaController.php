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
    public function index()
    {
        $files = ExcelFile::latest()->paginate(10);
        return view('admin.jadwal.indexExcel', compact('files'));
    }


    public function indexKaryawan()
    {
        $files = ExcelFile::latest()->paginate(10);
        $title = 'Jadwal';
        return view('dashboard.jadwal.index', compact('files', 'title'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('excel_uploads', 'public'); // simpan di storage/app/public

        ExcelFile::create([
            'original_name' => $uploaded->getClientOriginalName(),
            'file_path'     => $path, // contoh: excel_uploads/nama.xlsx
        ]);

        return redirect()
            ->route('jadwalkerja.index')
            ->with('success', 'File Excel berhasil di-upload dan disimpan.');
    }

    public function download($id)
    {
        $excelFile = ExcelFile::findOrFail($id);

        // Gunakan disk 'public' sesuai tempat penyimpanan file
        if (Storage::disk('public')->exists($excelFile->file_path)) {
            return response()->download(
                Storage::disk('public')->path($excelFile->file_path),
                $excelFile->original_name
            );
        } else {
            return redirect()->route('jadwalkerja.index')->with('error', 'File tidak ditemukan.');
        }
    }

    public function downloadKaryawan($id)
    {
        $excelFile = ExcelFile::findOrFail($id);

        if (Storage::disk('public')->exists($excelFile->file_path)) {
            return response()->download(
                Storage::disk('public')->path($excelFile->file_path),
                $excelFile->original_name
            );
        } else {
            return redirect()->route('karyawan.jadwalkerja.index')->with('error', 'File tidak ditemukan.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $excelFile = ExcelFile::findOrFail($id);

        // Hapus file dari disk 'public'
        Storage::disk('public')->delete($excelFile->file_path);

        $excelFile->delete();

        return redirect()
            ->route('jadwalkerja.index')
            ->with('success', 'File berhasil dihapus.');
    }
}
