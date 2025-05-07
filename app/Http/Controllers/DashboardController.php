<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        $nik = auth()->guard('karyawan')->user()->nik;

        // Ambil data presensi hari ini
        $presensiHariIni = DB::table('presensi')
            ->where('nik', $nik)
            ->where('tanggal_presensi', date('Y-m-d'))
            ->first();

        // Ambil jadwal shift karyawan untuk periode satu minggu (hari ini + 6 hari ke depan)
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(6)->format('Y-m-d');

        $jadwalShift = ShiftSchedule::where('karyawan_nik', $nik)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('shift')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Ambil data presensi bulan ini
        $riwayatPresensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tanggal_presensi', date('m'))
            ->whereYear('tanggal_presensi', date('Y'))
            ->orderBy('tanggal_presensi', 'desc')
            ->paginate(5);

        // Ambil rekap presensi bulan ini
        $rekapPresensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tanggal_presensi', date('m'))
            ->whereYear('tanggal_presensi', date('Y'))
            ->selectRaw('count(*) as jml_kehadiran, sum(if(jam_masuk > "08:00", 1, 0)) as jml_terlambat')
            ->first();

        // Ambil rekap pengajuan presensi bulan ini
        $rekapPengajuanPresensi = DB::table('pengajuan_presensi')
            ->where('nik', $nik)
            ->whereMonth('tanggal_mulai', date('m')) // Ganti tanggal_pengajuan dengan tanggal_mulai 
            ->whereYear('tanggal_mulai', date('Y')) // Ganti tanggal_pengajuan dengan tanggal_mulai 
            ->where('status_approved', 2) // Status disetujui 
            ->selectRaw('sum(if(status = "Sakit", 1, 0)) as jml_sakit, sum(if(status = "Izin", 1, 0)) as jml_izin')
            ->first();

        // Ambil data leaderboard presensi hari ini
        $leaderboard = DB::table('presensi as p')
            ->join('karyawan as k', 'p.nik', '=', 'k.nik')
            ->select('p.*', 'k.nama_lengkap', 'k.jabatan')
            ->where('p.tanggal_presensi', date('Y-m-d'))
            ->orderBy('p.jam_masuk', 'asc')
            ->paginate(5);

        return view('dashboard.index', compact(
            'title',
            'presensiHariIni',
            'jadwalShift',
            'riwayatPresensi',
            'rekapPresensi',
            'rekapPengajuanPresensi',
            'leaderboard'
        ));
    }
    public function indexAdmin()
    {
        $title = "Dashboard Admin";

        $hariIni = Carbon::now()->format("Y-m-d");

        $totalKaryawan = Karyawan::count();

        $rekapPresensi = DB::table("presensi")
            ->selectRaw("COUNT(nik) as jml_kehadiran, SUM(IF (jam_masuk > '08:00',1,0)) as jml_terlambat")
            ->where('tanggal_presensi', $hariIni)
            ->first();

        $rekapPengajuanPresensi = DB::table("pengajuan_presensi")
            ->selectRaw("SUM(IF (status = 'I',1,0)) as jml_izin, SUM(IF (status = 'S',1,0)) as jml_sakit")
            ->where('status_approved', 1)
            ->where('tanggal_mulai', $hariIni)
            ->first();

        return view("admin.dashboard", compact("title", "totalKaryawan", "rekapPresensi", "rekapPengajuanPresensi"));
    }
}
