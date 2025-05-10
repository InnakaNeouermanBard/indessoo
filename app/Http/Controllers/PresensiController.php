<?php
// presensi controller 
namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\TukarJadwal;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\StatusPengajuanPresensi;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    // Metode yang sudah ada tetap sama
    public function index()
    {
        $karyawan = Karyawan::all();
        $riwayatPresensi = DB::table("presensi")
            ->where('nik', auth()->guard('karyawan')->user()->nik)
            ->orderBy("tanggal_presensi", "desc")
            ->paginate(10);
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $title = 'Absensi';
        // Cek apakah karyawan sudah presensi pada hari ini
        $presensiKaryawan = DB::table('presensi')
            ->where('nik', auth()->guard('karyawan')->user()->nik)
            ->where('tanggal_presensi', date('Y-m-d'))
            ->first();

        // Ambil lokasi kantor
        $lokasiKantor = LokasiKantor::where('is_used', true)->first();

        // Ambil jadwal shift karyawan hari ini
        $jadwalHariIni = ShiftSchedule::where('karyawan_nik', auth()->guard('karyawan')->user()->nik)
            ->where('tanggal', date('Y-m-d'))
            ->with('shift')
            ->first();

        return view('dashboard.presensi.index', compact('title', 'presensiKaryawan', 'lokasiKantor', 'bulan', 'riwayatPresensi', 'jadwalHariIni', 'karyawan'));
    }

    public function store(Request $request)
    {
        $jenisPresensi = $request->jenis;
        $nik = auth()->guard('karyawan')->user()->nik;

        // Menggunakan waktu server saat ini dengan zona waktu Asia/Jakarta
        $now = Carbon::now('Asia/Jakarta');
        $tglPresensi = $now->format('Y-m-d');
        $jam = $now->format('H:i:s');

        // Cek jadwal shift karyawan hari ini
        $jadwalShift = ShiftSchedule::where('karyawan_nik', $nik)
            ->where('tanggal', $tglPresensi)
            ->with('shift')
            ->first();

        // Jika karyawan libur, tolak presensi
        if ($jadwalShift && $jadwalShift->is_libur) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => "Anda dijadwalkan libur hari ini, tidak perlu melakukan presensi.",
            ]);
        }

        $lokasi = $request->lokasi;
        $folderPath = "public/unggah/presensi/";
        $folderName = $nik . "-" . $tglPresensi . "-" . $jenisPresensi;

        $lokasiKantor = LokasiKantor::where('is_used', true)->first();
        $langtitudeKantor = $lokasiKantor->latitude;
        $longtitudeKantor = $lokasiKantor->longitude;
        $lokasiUser = explode(",", $lokasi);
        $langtitudeUser = $lokasiUser[0];
        $longtitudeUser = $lokasiUser[1];

        $jarak = round($this->validation_radius_presensi($langtitudeKantor, $longtitudeKantor, $langtitudeUser, $longtitudeUser), 2);
        if ($jarak > 50) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => "Anda berada di luar radius kantor. Jarak Anda " . $jarak . " meter dari kantor",
                'jenis_error' => "radius",
            ]);
        }

        $image = $request->image;
        $imageParts = explode(";base64", $image);
        $imageBase64 = base64_decode($imageParts[1]);

        $fileName = $folderName . ".png";
        $file = $folderPath . $fileName;

        if ($jenisPresensi == "masuk") {
            $data = [
                "nik" => $nik,
                "tanggal_presensi" => $tglPresensi,
                "jam_masuk" => $jam,
                "foto_masuk" => $fileName,
                "lokasi_masuk" => $lokasi,
                "created_at" => $now,
                "updated_at" => $now,
            ];
            $store = DB::table('presensi')->insert($data);
        } elseif ($jenisPresensi == "keluar") {
            $data = [
                "jam_keluar" => $jam,
                "foto_keluar" => $fileName,
                "lokasi_keluar" => $lokasi,
                "updated_at" => $now,
            ];
            $store = DB::table('presensi')
                ->where('nik', auth()->guard('karyawan')->user()->nik)
                ->where('tanggal_presensi', $tglPresensi)
                ->update($data);
        }

        if ($store) {
            Storage::put($file, $imageBase64);
        } else {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => "Gagal presensi",
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
            'success' => true,
            'message' => "Berhasil presensi",
            'jenis_presensi' => $jenisPresensi,
        ]);
    }

    public function validation_radius_presensi($langtitudeKantor, $longtitudeKantor, $langtitudeUser, $longtitudeUser)
    {
        $theta = $longtitudeKantor - $longtitudeUser;
        $hitungKoordinat = (sin(deg2rad($langtitudeKantor)) * sin(deg2rad($langtitudeUser))) + (cos(deg2rad($langtitudeKantor)) * cos(deg2rad($langtitudeUser)) * cos(deg2rad($theta)));
        $miles = rad2deg(acos($hitungKoordinat)) * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return $meters;
    }

    public function history()
    {
        $title = 'Riwayat Presensi';
        $riwayatPresensi = DB::table("presensi")
            ->where('nik', auth()->guard('karyawan')->user()->nik)
            ->orderBy("tanggal_presensi", "asc")
            ->paginate(10);
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return view('dashboard.presensi.history', compact('title', 'riwayatPresensi', 'bulan'));
    }

    public function searchHistory(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = DB::table('presensi')
            ->where('nik', auth()->guard('karyawan')->user()->nik)
            ->whereMonth('tanggal_presensi', $bulan)
            ->whereYear('tanggal_presensi', $tahun)
            ->orderBy("tanggal_presensi", "asc")
            ->get();
        return view('dashboard.presensi.search-history', compact('data'));
    }

    public function pengajuanPresensi()
    {
        $title = "Form Perizinan";
        $riwayatPengajuanPresensi = DB::table("pengajuan_presensi")
            ->where('nik', auth()->guard('karyawan')->user()->nik)
            ->orderBy("tanggal_mulai", "asc")
            ->paginate(10);
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Ambil data karyawan untuk mendapatkan sisa kuota cuti
        $karyawan = Karyawan::find(auth()->guard('karyawan')->user()->nik);
        $sisaKuota = $karyawan->sisaKuotaCuti();

        return view('dashboard.presensi.izin.index',  compact('title', 'riwayatPengajuanPresensi', 'bulan', 'sisaKuota'));
    }

    public function pengajuanPresensiCreate()
    {
        $title = "Form Pengajuan Presensi";
        $statusPengajuan = StatusPengajuanPresensi::cases();

        // Ambil data karyawan untuk mendapatkan sisa kuota cuti
        $karyawan = Karyawan::find(auth()->guard('karyawan')->user()->nik);
        $sisaKuota = $karyawan->sisaKuotaCuti();

        return view('dashboard.presensi.izin.create', compact('title', 'statusPengajuan', 'sisaKuota'));
    }

    public function pengajuanPresensiStore(Request $request)
    {
        $nik = auth()->guard('karyawan')->user()->nik;

        // Validasi input
        $request->validate([
            'status' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah di rentang tanggal sudah pernah mengajukan
        $cekPengajuan = DB::table('pengajuan_presensi')
            ->where('nik', $nik)
            ->where(function ($query) use ($request) {
                $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                    ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai]);
            })
            ->where(function ($query) {
                $query->where('status_approved', 0)
                    ->orWhere('status_approved', 1);
            })
            ->first();

        if ($cekPengajuan) {
            return to_route('karyawan.izin')->with("error", "Anda sudah memiliki pengajuan pada rentang tanggal tersebut.");
        }

        // *Cek status pengajuan, hanya kurangi kuota jika statusnya Cuti*
        if ($request->status == 'C') {
            $karyawan = Karyawan::find($nik);
            $sisaKuota = $karyawan->sisaKuotaCuti();
            $jumlahHari = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai));

            // Pastikan kuota cukup
            if ($sisaKuota < $jumlahHari) {
                return to_route('karyawan.izin')->with("error", "Kuota cuti Anda tidak mencukupi untuk rentang tanggal ini ($jumlahHari hari).");
            }

            // Jika status Cuti, kurangi kuota cuti
            $karyawan->kuota_cuti -= $jumlahHari;
            $karyawan->save();
        }

        // Simpan pengajuan presensi
        DB::table('pengajuan_presensi')->insert([
            'nik' => $nik,
            'status' => $request->status,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return to_route('karyawan.izin')->with("success", "Berhasil menambahkan pengajuan");
    }

    public function searchPengajuanHistory(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = DB::table('pengajuan_presensi')
            ->where('nik', auth()->guard('karyawan')->user()->nik)
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->orderBy("tanggal_mulai", "asc")
            ->get();
        return view('dashboard.presensi.izin.search-history', compact('data'));
    }

    public function monitoringPresensi(Request $request)
{
    $query = DB::table('presensi as p')
        ->join('karyawan as k', 'p.nik', '=', 'k.nik')
        ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
        ->orderBy('k.nama_lengkap', 'asc')
        ->orderBy('d.kode', 'asc')
        ->select('p.*', 'k.nama_lengkap as nama_karyawan', 'd.nama as nama_departemen');

    // Filter berdasarkan tanggal presensi
    if ($request->tanggal_presensi) {
        $query->whereDate('p.tanggal_presensi', $request->tanggal_presensi);
    } else {
        $query->whereDate('p.tanggal_presensi', Carbon::now());
    }

    // Filter berdasarkan NIK
    if ($request->has('nik') && !empty($request->nik)) {
        $query->where('p.nik', 'like', '%' . $request->nik . '%');
    }

    // Filter berdasarkan Nama Karyawan
    if ($request->has('nama_karyawan') && !empty($request->nama_karyawan)) {
        $query->where('k.nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
    }

    // Eksekusi query dan paginate hasilnya
    $monitoring = $query->paginate(10);

    $lokasiKantor = LokasiKantor::where('is_used', true)->first();

    // Tambahkan data untuk notifikasi
    $recentExchanges = TukarJadwal::with(['pengaju', 'penerima'])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('admin.monitoring-presensi.index', compact('monitoring', 'lokasiKantor', 'recentExchanges'));
}

    // Tambahkan metode baru untuk mengelola kuota cuti karyawan (admin)
    public function manajemenKuotaCuti()
    {
        $title = 'Manajemen Kuota Cuti';
        $karyawan = Karyawan::with('departemen')
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(10);

        return view('admin.monitoring-presensi.kuota-cuti', compact('title', 'karyawan'));
    }

    public function updateKuotaCuti(Request $request, $nik)
    {
        $request->validate([
            'kuota_cuti' => 'required|integer|min:0'
        ]);

        $karyawan = Karyawan::find($nik);
        $karyawan->kuota_cuti = $request->kuota_cuti;
        $karyawan->save();

        return redirect()->route('admin.kuota-cuti')->with('success', 'Kuota cuti berhasil diperbarui');
    }

    public function viewLokasi(Request $request)
    {
        if ($request->tipe == "lokasi_masuk") {
            $data = DB::table('presensi')->where('nik', $request->nik)->first('lokasi_masuk');
            return $data;
        } elseif ($request->tipe == "lokasi_keluar") {
            $data = DB::table('presensi')->where('nik', $request->nik)->first('lokasi_keluar');
            return $data;
        }
    }

    public function laporan(Request $request)
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $karyawan = Karyawan::orderBy('nama_lengkap', 'asc')->get();
        return view('admin.laporan.presensi', compact('bulan', 'karyawan'));
    }

    public function laporankaryawan(Request $request)
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $karyawan = Karyawan::orderBy('nama_lengkap', 'asc')->get();
        $title = "laporan";
        return view('dashboard.laporan.presensi', compact('bulan', 'karyawan', 'title'));
    }


    public function laporanPresensiKaryawanKaryawan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $title = 'Laporan Presensi Karyawan';
        $bulan = $request->bulan;

        $karyawan = auth()->guard('karyawan')->user(); // hanya karyawan yang login
        $riwayatPresensi = DB::table("presensi")
            ->where('nik', $karyawan->nik)
            ->whereMonth('tanggal_presensi', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($bulan)->format('Y'))
            ->orderBy("tanggal_presensi", "asc")
            ->get();

        $shift = $karyawan->shift;  // Mengambil shift karyawan melalui relasi shift()

        // Ambil data pengajuan presensi (Izin, Sakit, Cuti)
        $izin = DB::table('pengajuan_presensi')
            ->where('nik', $request->karyawan)
            ->where('status', 'I')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->count();

        $sakit = DB::table('pengajuan_presensi')
            ->where('nik', $request->karyawan)
            ->where('status', 'S')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->count();

        $cuti = DB::table('pengajuan_presensi')
            ->where('nik', $request->karyawan) // Filter berdasarkan NIK karyawan
            ->where('status', 'C') // Status Cuti
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m')) // Bulan yang dipilih
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y')) // Tahun yang dipilih
            ->get(); // Ambil semua pengajuan cuti

        $totalCuti = 0;

        // Loop melalui semua pengajuan cuti untuk menghitung total hari cuti
        foreach ($cuti as $item) {
            // Hitung jumlah hari cuti untuk setiap pengajuan
            $totalCuti += Carbon::parse($item->tanggal_mulai)->diffInDays(Carbon::parse($item->tanggal_selesai)) + 1;
        }

        // Ambil data lembur
        $lembur = DB::table('form_lemburs')
            ->where('nik', $request->karyawan)
            ->whereMonth('tanggal', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal', Carbon::make($bulan)->format('Y'))
            ->sum('overtime');

        // Kirimkan data ke view dan PDF
        $pdf = Pdf::loadView('dashboard.laporan.pdf.presensi-karyawan', compact(
            'title',
            'bulan',
            'karyawan',
            'riwayatPresensi',
            'izin',
            'sakit',
            'totalCuti',
            'lembur',
            'shift',
        ));

        // Streaming PDF ke browser
        return $pdf->stream($title . ' ' . $karyawan->nama_lengkap . '.pdf');
    }

    public function laporanPresensiKaryawan(Request $request)
    {
        $title = 'Laporan Presensi Karyawan';
        $bulan = $request->bulan;
        $karyawan = Karyawan::find($request->karyawan); // Ambil data karyawan berdasarkan NIK

        // Ambil riwayat presensi karyawan
        $riwayatPresensi = DB::table("presensi")
            ->where('nik', $request->karyawan)
            ->whereMonth('tanggal_presensi', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($bulan)->format('Y'))
            ->orderBy("tanggal_presensi", "asc")
            ->get();

        $shift = $karyawan->shift;  // Mengambil shift karyawan melalui relasi shift()

        // Ambil data pengajuan presensi (Izin, Sakit, Cuti)
        $izin = DB::table('pengajuan_presensi')
            ->where('nik', $request->karyawan)
            ->where('status', 'I')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->count();

        $sakit = DB::table('pengajuan_presensi')
            ->where('nik', $request->karyawan)
            ->where('status', 'S')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->count();

        $cuti = DB::table('pengajuan_presensi')
            ->where('nik', $request->karyawan) // Filter berdasarkan NIK karyawan
            ->where('status', 'C') // Status Cuti
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m')) // Bulan yang dipilih
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y')) // Tahun yang dipilih
            ->get(); // Ambil semua pengajuan cuti

        $totalCuti = 0;

        // Loop melalui semua pengajuan cuti untuk menghitung total hari cuti
        foreach ($cuti as $item) {
            // Hitung jumlah hari cuti untuk setiap pengajuan
            $totalCuti += Carbon::parse($item->tanggal_mulai)->diffInDays(Carbon::parse($item->tanggal_selesai)) + 1;
        }

        // Ambil data lembur
        $lembur = DB::table('form_lemburs')
            ->where('nik', $request->karyawan)
            ->whereMonth('tanggal', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal', Carbon::make($bulan)->format('Y'))
            ->sum('overtime');

        // Kirimkan data ke view dan PDF
        $pdf = Pdf::loadView('admin.laporan.pdf.presensi-karyawan', compact(
            'title',
            'bulan',
            'karyawan',
            'riwayatPresensi',
            'izin',
            'sakit',
            'totalCuti',
            'lembur',
            'shift',
        ));

        // Streaming PDF ke browser
        return $pdf->stream($title . ' ' . $karyawan->nama_lengkap . '.pdf');
    }

    public function laporanPresensiSemuaKaryawan(Request $request)
{
    $title = 'Laporan Presensi Semua Karyawan';
    $bulan = $request->bulan;

    // Ambil riwayat presensi semua karyawan
    $riwayatPresensi = DB::table("presensi as p")
        ->join('karyawan as k', 'p.nik', '=', 'k.nik')
        ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
        ->whereMonth('tanggal_presensi', Carbon::make($bulan)->format('m'))
        ->whereYear('tanggal_presensi', Carbon::make($bulan)->format('Y'))
        ->select(
            'p.nik',
            'k.nama_lengkap as nama_karyawan',
            'k.jabatan as jabatan_karyawan',
            'd.nama as nama_departemen'
        )
        ->selectRaw("COUNT(p.nik) as total_kehadiran, SUM(IF (jam_masuk > '08:00',1,0)) as total_terlambat")
        ->groupBy(
            'p.nik',
            'k.nama_lengkap',
            'k.jabatan',
            'd.nama'
        )
        ->orderBy("tanggal_presensi", "asc")
        ->get();

    // Ambil data izin, sakit, cuti dan lembur untuk setiap karyawan
    $riwayatPresensi = $riwayatPresensi->map(function ($item) use ($bulan) {

        // Mengambil data Izin
        $izin = DB::table('pengajuan_presensi')
            ->where('nik', $item->nik)
            ->where('status', 'I')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->count();

        // Mengambil data Sakit
        $sakit = DB::table('pengajuan_presensi')
            ->where('nik', $item->nik)
            ->where('status', 'S')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->count();

        // Mengambil data Cuti
        $cuti = DB::table('pengajuan_presensi')
            ->where('nik', $item->nik)
            ->where('status', 'C')
            ->whereMonth('tanggal_mulai', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_mulai', Carbon::make($bulan)->format('Y'))
            ->get();

        $totalCuti = 0;
        foreach ($cuti as $itemCuti) {
            $totalCuti += Carbon::parse($itemCuti->tanggal_mulai)->diffInDays(Carbon::parse($itemCuti->tanggal_selesai)) + 1;
        }

        // Mengambil data Lembur
        $lembur = DB::table('form_lemburs')
            ->where('nik', $item->nik)
            ->whereMonth('tanggal', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal', Carbon::make($bulan)->format('Y'))
            ->sum('overtime');

        // Menambahkan data izin, sakit, cuti, dan lembur ke dalam item
        $item->total_izin = $izin;
        $item->total_sakit = $sakit;
        $item->total_cuti = $totalCuti;
        $item->total_lembur = $lembur;

        return $item;
    });

    // Generate PDF
    $pdf = Pdf::loadView('admin.laporan.pdf.presensi-semua-karyawan', compact(
        'title',
        'bulan',
        'riwayatPresensi'
    ));

    return $pdf->stream($title . '.pdf');
}


    public function laporanPresensiSemuaKaryawanKaryawan(Request $request)
    {
        $title = 'Laporan Presensi Semua Karyawan';
        $bulan = $request->bulan;
        $riwayatPresensi = DB::table("presensi as p")
            ->join('karyawan as k', 'p.nik', '=', 'k.nik')
            ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
            ->whereMonth('tanggal_presensi', Carbon::make($bulan)->format('m'))
            ->whereYear('tanggal_presensi', Carbon::make($bulan)->format('Y'))
            ->select(
                'p.nik',
                'k.nama_lengkap as nama_karyawan',
                'k.jabatan as jabatan_karyawan',
                'd.nama as nama_departemen'
            )
            ->selectRaw("COUNT(p.nik) as total_kehadiran, SUM(IF (jam_masuk > '08:00',1,0)) as total_terlambat")
            ->groupBy(
                'p.nik',
                'k.nama_lengkap',
                'k.jabatan',
                'd.nama'
            )
            ->orderBy("tanggal_presensi", "asc")
            ->get();

        // return view('admin.laporan.pdf.presensi-semua-karyawan', compact('title', 'bulan', 'riwayatPresensi'));
        $pdf = Pdf::loadView('dashboard.laporan.pdf.presensi-semua-karyawan', compact('title', 'bulan', 'riwayatPresensi'));
        return $pdf->stream($title . '.pdf');
    }

    public function indexAdmin(Request $request)
    {
        $title = 'Administrasi Presensi';

        $departemen = Departemen::get();

        $query = DB::table('pengajuan_presensi as p')
            ->join('karyawan as k', 'k.nik', '=', 'p.nik')
            ->join('departemen as d', 'k.departemen_id', '=', 'd.id')
            ->where('p.tanggal_mulai', '>=', Carbon::now()->startOfMonth()->format("Y-m-d"))
            ->where('p.tanggal_mulai', '<=', Carbon::now()->endOfMonth()->format("Y-m-d"))
            ->select('p.*', 'k.nama_lengkap as nama_karyawan', 'd.nama as nama_departemen', 'd.id as id_departemen', 'k.kuota_cuti')
            ->orderBy('p.tanggal_mulai', 'asc');

        if ($request->nik) {
            $query->where('k.nik', 'LIKE', '%' . $request->nik . '%');
        }
        if ($request->karyawan) {
            $query->where('k.nama_lengkap', 'LIKE', '%' . $request->karyawan . '%');
        }
        if ($request->departemen) {
            $query->where('d.id', $request->departemen);
        }
        if ($request->tanggal_awal) {
            $query->WhereDate('p.tanggal_mulai', '>=', Carbon::parse($request->tanggal_awal)->format('Y-m-d'));
        }
        if ($request->tanggal_akhir) {
            $query->WhereDate('p.tanggal_', '<=', Carbon::parse($request->tanggal_akhir)->format('Y-m-d'));
        }
        if ($request->status) {
            $query->Where('p.status', $request->status);
        }
        if ($request->status_approved) {
            $query->Where('p.status_approved', $request->status_approved);
        }

        $pengajuan = $query->paginate(10);

        return view('admin.monitoring-presensi.administrasi-presensi', compact('title', 'pengajuan', 'departemen'));
    }

    public function persetujuanPresensi(Request $request)
    {
        if ($request->ajuan == "terima") {
            $pengajuan = DB::table('pengajuan_presensi')->where('id', $request->id)->update([
                'status_approved' => 2
            ]);
            if ($pengajuan) {
                return response()->json(['success' => true, 'message' => 'Pengajuan presensi telah diterima']);
            } else {
                return response()->json(['success' => false, 'message' => 'Pengajuan presensi gagal diterima']);
            }
        } elseif ($request->ajuan == "tolak") {
            $pengajuan = DB::table('pengajuan_presensi')->where('id', $request->id)->update([
                'status_approved' => 3
            ]);
            if ($pengajuan) {
                return response()->json(['success' => true, 'message' => 'Pengajuan presensi telah ditolak']);
            } else {
                return response()->json(['success' => false, 'message' => 'Pengajuan presensi gagal ditolak']);
            }
        } elseif ($request->ajuan == "batal") {
            $pengajuan = DB::table('pengajuan_presensi')->where('id', $request->id)->update([
                'status_approved' => 1
            ]);
            if ($pengajuan) {
                return response()->json(['success' => true, 'message' => 'Pengajuan presensi telah dibatalkan']);
            } else {
                return response()->json(['success' => false, 'message' => 'Pengajuan presensi gagal dibatalkan']);
            }
        }
    }

    public function ajukanTukarJadwal(Request $request)
    {
        // Validasi input
        $request->validate([
            'nik_penerima' => 'required|exists:karyawan,nik',
            'tanggal_pengajuan' => 'required|date',
            'alasan' => 'required|string|max:255',
        ]);

        // Pastikan pengaju tidak mengajukan pertukaran jadwal dengan dirinya sendiri
        if ($request->nik_penerima == auth()->guard('karyawan')->user()->nik) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengajukan pertukaran jadwal dengan diri Anda sendiri.')
                ->withInput();
        }

        // Ambil data karyawan
        $nikPengaju = auth()->guard('karyawan')->user()->nik;
        $nikPenerima = $request->nik_penerima;
        $tanggalPengajuan = Carbon::parse($request->tanggal_pengajuan);

        // Cek jadwal shift untuk kedua karyawan pada tanggal tersebut
        $jadwalPengaju = ShiftSchedule::where('karyawan_nik', $nikPengaju)
            ->where('tanggal', $tanggalPengajuan->format('Y-m-d'))
            ->first();

        $jadwalPenerima = ShiftSchedule::where('karyawan_nik', $nikPenerima)
            ->where('tanggal', $tanggalPengajuan->format('Y-m-d'))
            ->first();

        // Ambil nama karyawan untuk pesan yang lebih informatif
        $namaPenerima = Karyawan::where('nik', $nikPenerima)->value('nama_lengkap');

        // Validasi: pastikan kedua karyawan memiliki jadwal pada tanggal tersebut
        if (!$jadwalPengaju) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki jadwal pada tanggal tersebut.')
                ->withInput();
        }

        if (!$jadwalPenerima) {
            return redirect()->back()
                ->with('error', $namaPenerima . ' tidak memiliki jadwal pada tanggal tersebut.')
                ->withInput();
        }

        // Proses pertukaran jadwal menggunakan transaksi database
        try {
            DB::beginTransaction();

            // Simpan data sementara jadwal pengaju
            $pengajuShiftId = $jadwalPengaju->shift_id;
            $pengajuIsLibur = $jadwalPengaju->is_libur;

            // Tukar jadwal
            $jadwalPengaju->shift_id = $jadwalPenerima->shift_id;
            $jadwalPengaju->is_libur = $jadwalPenerima->is_libur;
            $jadwalPengaju->save();

            $jadwalPenerima->shift_id = $pengajuShiftId;
            $jadwalPenerima->is_libur = $pengajuIsLibur;
            $jadwalPenerima->save();

            // Simpan catatan pertukaran jadwal
            TukarJadwal::create([
                'nik_pengaju' => $nikPengaju,
                'nik_penerima' => $nikPenerima,
                'tanggal_pengajuan' => $tanggalPengajuan,
                'status' => 'approved', // Langsung disetujui
                'alasan' => $request->alasan,
            ]);

            DB::commit();

            // Tampilkan detail jadwal yang berhasil ditukar dalam pesan sukses
            $namaPengaju = auth()->guard('karyawan')->user()->nama_lengkap;
            $tanggalFormatted = $tanggalPengajuan->format('d-m-Y');

            return redirect()->route('karyawan.presensi')
                ->with('success', "Pertukaran jadwal dengan {$namaPenerima} pada tanggal {$tanggalFormatted} berhasil dilakukan!");
        } catch (\Exception $e) {
            DB::rollBack();

            // Log error untuk debugging
            Log::error('Tukar Jadwal Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses pertukaran jadwal. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Fungsi untuk memproses pertukaran jadwal (untuk single date)
     */
    private function prosessTukarJadwal($nikPengaju, $nikPenerima, $tanggalPengajuan)
    {
        // Ambil jadwal shift untuk pengaju dan penerima pada tanggal tersebut
        $jadwalPengaju = ShiftSchedule::where('karyawan_nik', $nikPengaju)
            ->where('tanggal', $tanggalPengajuan->format('Y-m-d'))
            ->first();

        $jadwalPenerima = ShiftSchedule::where('karyawan_nik', $nikPenerima)
            ->where('tanggal', $tanggalPengajuan->format('Y-m-d'))
            ->first();

        // Validasi: pastikan kedua karyawan memiliki jadwal pada tanggal tersebut
        if (!$jadwalPengaju || !$jadwalPenerima) {
            return false;
        }

        // Mulai transaksi database untuk memastikan integritas data
        DB::beginTransaction();

        try {
            // Simpan data sementara
            $pengajuShiftId = $jadwalPengaju->shift_id;
            $pengajuIsLibur = $jadwalPengaju->is_libur;

            // Tukar jadwal pengaju dengan penerima
            $jadwalPengaju->shift_id = $jadwalPenerima->shift_id;
            $jadwalPengaju->is_libur = $jadwalPenerima->is_libur;
            $jadwalPengaju->save();

            // Tukar jadwal penerima dengan pengaju
            $jadwalPenerima->shift_id = $pengajuShiftId;
            $jadwalPenerima->is_libur = $pengajuIsLibur;
            $jadwalPenerima->save();

            // Commit transaksi jika semua berhasil
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            return false;
        }
    }

    /**
     * Untuk admin melihat riwayat tukar jadwal
     */
    public function riwayatTukarJadwal(Request $request)
    {
        $title = 'Riwayat Pertukaran Jadwal';

        // Query dasar
        $query = TukarJadwal::with(['pengaju', 'penerima']);

        // Filter berdasarkan search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('pengaju', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            })->orWhereHas('penerima', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tanggal pengajuan
        if ($request->has('tanggal_awal') && !empty($request->tanggal_awal)) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_awal);
        }

        if ($request->has('tanggal_akhir') && !empty($request->tanggal_akhir)) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_akhir);
        }

        // Urutkan berdasarkan waktu pembuatan (terbaru di atas)
        $tukarJadwal = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk notifikasi
        $countTukarJadwalToday = TukarJadwal::whereDate('created_at', Carbon::today())->count();
        $recentExchanges = TukarJadwal::with(['pengaju', 'penerima'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.jadwal.riwayat-tukar-jadwal', compact('title', 'tukarJadwal', 'countTukarJadwalToday', 'recentExchanges'));
    }

    /**
     * Mendapatkan detail tukar jadwal untuk ditampilkan di modal
     */
    public function detailTukarJadwal($id)
    {
        $tukarJadwal = TukarJadwal::with(['pengaju.departemen', 'penerima.departemen'])
            ->findOrFail($id);

        // Ambil tanggal pertukaran
        $tanggalPengajuan = Carbon::parse($tukarJadwal->tanggal_pengajuan);

        // Ambil jadwal shift untuk kedua karyawan pada tanggal ini
        $jadwalPengaju = ShiftSchedule::with('shift')
            ->where('karyawan_nik', $tukarJadwal->nik_pengaju)
            ->where('tanggal', $tanggalPengajuan->format('Y-m-d'))
            ->first();

        $jadwalPenerima = ShiftSchedule::with('shift')
            ->where('karyawan_nik', $tukarJadwal->nik_penerima)
            ->where('tanggal', $tanggalPengajuan->format('Y-m-d'))
            ->first();

        // Untuk tampilan di modal
        $jadwalDisplay = null;
        if ($jadwalPengaju && $jadwalPenerima) {
            $jadwalDisplay = [
                'tanggal' => $tanggalPengajuan->format('Y-m-d'),
                'hari' => $tanggalPengajuan->format('l'),
                'jadwal_pengaju' => $jadwalPengaju->is_libur ? 'Libur' : ($jadwalPengaju->shift ? $jadwalPengaju->shift->nama . ' (' .
                    Carbon::parse($jadwalPengaju->shift->waktu_mulai)->format('H:i') . '-' .
                    Carbon::parse($jadwalPengaju->shift->waktu_selesai)->format('H:i') . ')' : 'Regular'),
                'jadwal_penerima' => $jadwalPenerima->is_libur ? 'Libur' : ($jadwalPenerima->shift ? $jadwalPenerima->shift->nama . ' (' .
                    Carbon::parse($jadwalPenerima->shift->waktu_mulai)->format('H:i') . '-' .
                    Carbon::parse($jadwalPenerima->shift->waktu_selesai)->format('H:i') . ')' : 'Regular'),
            ];
        }

        // Return data dalam format JSON
        return response()->json([
            'success' => true,
            'pengaju' => $tukarJadwal->pengaju,
            'penerima' => $tukarJadwal->penerima,
            'tanggal_pengajuan' => $tukarJadwal->tanggal_pengajuan,
            'alasan' => $tukarJadwal->alasan,
            'waktu_pengajuan' => Carbon::parse($tukarJadwal->created_at)->format('d M Y H:i'),
            'jadwal' => $jadwalDisplay ? [$jadwalDisplay] : [],
        ]);
    }

    /**
     * Menerima pengajuan tukar jadwal (untuk fitur admin approval jika dibutuhkan)
     */
    public function terimaAjuanTukarJadwal($id)
    {
        $tukarJadwal = TukarJadwal::findOrFail($id);

        // Jika sudah approved, tidak perlu diproses lagi
        if ($tukarJadwal->status == 'approved') {
            return redirect()->back()->with('info', 'Pertukaran jadwal ini sudah disetujui sebelumnya.');
        }

        $tanggalPengajuan = Carbon::parse($tukarJadwal->tanggal_pengajuan);

        $berhasil = $this->prosessTukarJadwal(
            $tukarJadwal->nik_pengaju,
            $tukarJadwal->nik_penerima,
            $tanggalPengajuan
        );

        if (!$berhasil) {
            return redirect()->back()->with('error', 'Gagal menukar jadwal. Pastikan kedua karyawan memiliki jadwal pada tanggal tersebut.');
        }

        $tukarJadwal->status = 'approved';
        $tukarJadwal->save();

        return redirect()->back()->with('success', 'Pertukaran jadwal berhasil disetujui dan diproses!');
    }

    /**
     * Menolak pengajuan tukar jadwal (untuk fitur admin approval jika dibutuhkan)
     */
    public function tolakAjuanTukarJadwal($id)
    {
        $tukarJadwal = TukarJadwal::findOrFail($id);

        if ($tukarJadwal->status == 'approved') {
            return redirect()->back()->with('error', 'Pertukaran jadwal ini sudah disetujui dan tidak dapat ditolak.');
        }

        $tukarJadwal->status = 'rejected';
        $tukarJadwal->save();

        return redirect()->back()->with('success', 'Pertukaran jadwal berhasil ditolak.');
    }
}
