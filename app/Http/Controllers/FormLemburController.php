<?php
// formLemburController 
namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\FormLembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormLemburController extends Controller
{
    public function index(Request $request)
    {
        $query = FormLembur::query();

        if ($request->has('cari_nik') && !empty($request->cari_nik)) {
            $query->where('nik', 'like', '%' . $request->cari_nik . '%');
        }

        $karyawan = $query->get();
        $niks = Karyawan::all();
        return view('admin.form-lembur.index', compact('karyawan', 'niks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'overtime' => 'required|numeric',
        ]);

        $karyawan = Karyawan::where('nik', $request->nik)->first();

        FormLembur::create([
            'nik' => $request->nik,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'overtime' => $request->overtime,
            'status' => 'pending',
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
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'overtime' => 'required|numeric',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $formLembur = FormLembur::findOrFail($id);
        $formLembur->update([
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'overtime' => $request->overtime,
            'status' => $request->status,
        ]);

        return redirect()->route('form-lembur.index')->with('success', 'Data lembur berhasil diperbarui.');
    }

    public function show($id)
    {
        $lembur = FormLembur::findOrFail($id);
        return response()->json($lembur);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:form_lemburs,id',
        ]);

        $formLembur = FormLembur::findOrFail($request->id);
        $formLembur->delete();

        return response()->json([
            'message' => 'Data Lembur Karyawan berhasil dihapus.'
        ]);
    }

    // KARYAWAN METHODS
    public function karyawanIndex()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('email', $user->email)->first();
        $title = "Form Lembur";
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $formLembur = FormLembur::where('nik', $karyawan->nik)->paginate(10);

        // Array bulan untuk dropdown filter
        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        // Pastikan compact mengirim variable bulan
        return view('dashboard.form-lembur.index', compact('formLembur', 'karyawan', 'bulan', 'title'));
    }

    public function karyawanStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'overtime' => 'required|numeric',
        ]);

        $user = Auth::user();
        $karyawan = Karyawan::where('email', $user->email)->first();

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        FormLembur::create([
            'nik' => $karyawan->nik,
            'nama_karyawan' => $karyawan->nama_lengkap,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'overtime' => $request->overtime,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.form-lembur.index')->with('success', 'Form lembur berhasil diajukan.');
    }
    public function karyawanSearch(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('email', $user->email)->first();

        if (!$karyawan) {
            return view('karyawan.form-lembur.partials.search-results');
        }

        $query = FormLembur::where('nik', $karyawan->nik);

        // Filter by month and year
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)
                ->whereYear('tanggal', $request->tahun);
        }

        $formLembur = $query->orderBy('tanggal', 'desc')->get();

        return view('karyawan.form-lembur.partials.search-results', compact('formLembur'));
    }
}
