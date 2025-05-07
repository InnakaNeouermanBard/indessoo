<?php
// shiftschedulecontroller 
// app/Http/Controllers/ShiftScheduleController.php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Filter berdasarkan bulan dan tahun
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $cari_nik = $request->cari_nik ?? '';

        // Dapatkan karyawan yang memiliki jadwal di bulan tersebut
        $karyawanWithSchedules = ShiftSchedule::selectRaw('DISTINCT karyawan_nik')
            ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
                return $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
            })
            ->when($cari_nik, function ($query) use ($cari_nik) {
                return $query->where('karyawan_nik', 'like', '%' . $cari_nik . '%');
            })
            ->with(['karyawan' => function ($query) {
                $query->select('nik', 'nama_lengkap', 'departemen_id');
            }])
            ->get()
            ->pluck('karyawan');

        // Dapatkan jadwal lengkap
        $jadwal = ShiftSchedule::with(['karyawan', 'shift'])
            ->when($cari_nik, function ($query) use ($cari_nik) {
                return $query->where('karyawan_nik', 'like', '%' . $cari_nik . '%');
            })
            ->when($bulan && $tahun, function ($query) use ($bulan, $tahun) {
                return $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
            })
            ->orderBy('karyawan_nik')
            ->orderBy('tanggal')
            ->get();

        $shifts = Shift::all();
        $karyawan = Karyawan::all();

        // Menyiapkan data untuk dropdown bulan dan tahun
        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $tahunList = [];
        $tahunSekarang = date('Y');
        for ($i = $tahunSekarang - 2; $i <= $tahunSekarang + 2; $i++) {
            $tahunList[$i] = $i;
        }

        return view('admin.jadwal.index', compact(
            'jadwal',
            'shifts',
            'karyawan',
            'karyawanWithSchedules',
            'bulanList',
            'tahunList',
            'bulan',
            'tahun',
            'cari_nik'
        ));
    }
    public function show($id)
    {
        // Karena kita tidak memerlukan halaman detail individu,
        // kita redirect ke index
        return redirect()->route('jadwal-shift.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        ShiftSchedule::create([
            'karyawan_nik' => $request->karyawan_nik,
            'tanggal' => $request->tanggal,
            'shift_id' => $request->shift_id,
            'is_libur' => $request->is_libur ?? false,
        ]);

        return redirect()->route('jadwal-shift.index')->with('success', 'Jadwal shift berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwal = ShiftSchedule::with('karyawan')->findOrFail($id);
        return response()->json($jadwal);
    }

    public function update(Request $request, $id)
    {
        $jadwal = ShiftSchedule::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $jadwal->update([
            'tanggal' => $request->tanggal,
            'shift_id' => $request->shift_id,
            'is_libur' => $request->is_libur ?? false,
        ]);

        return redirect()->route('jadwal-shift.index')->with('success', 'Jadwal shift berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ShiftSchedule::destroy($id);
        return redirect()->back()->with('success', 'Jadwal shift berhasil dihapus.');
    }

    /**
     * Tampilkan form untuk pembuatan jadwal massal
     */
    public function createMassal()
    {
        $karyawan = Karyawan::all();
        $shifts = Shift::all();

        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $tahunList = [];
        $tahunSekarang = (int)date('Y'); // Pastikan ini integer
        for ($i = $tahunSekarang - 1; $i <= $tahunSekarang + 2; $i++) {
            $tahunList[$i] = $i;
        }

        // Pattern jadwal default
        $patternOptions = [
            'rotasi_4_3' => 'Rotasi 4-3 (4 Hari Kerja, 3 Hari Libur)',
            'rotasi_5_2' => 'Rotasi 5-2 (5 Hari Kerja, 2 Hari Libur)',
            'rotasi_6_1' => 'Rotasi 6-1 (6 Hari Kerja, 1 Hari Libur)',
            'mingguan' => 'Pola Mingguan (Shift berubah per minggu)',
            'custom' => 'Custom Pattern'
        ];

        return view('admin.jadwal.create-massal', compact('karyawan', 'shifts', 'bulanList', 'tahunList', 'patternOptions'));
    }

    /**
     * Simpan jadwal massal
     */
    public function storeMassal(Request $request)
    {
        $request->validate([
            'karyawan_nik' => 'required|array',
            'karyawan_nik.*' => 'exists:karyawan,nik',
            'bulan_mulai' => 'required',
            'tahun_mulai' => 'required',
            'durasi' => 'required|integer|min:1|max:3',
            'pattern_type' => 'required|string',
        ]);

        $bulanMulai = (int)$request->bulan_mulai; // Konversi ke integer
        $tahunMulai = (int)$request->tahun_mulai; // Konversi ke integer
        $durasi = (int)$request->durasi; // Konversi ke integer
        $patternType = $request->pattern_type;

        // Calculate start and end dates - pastikan parameter adalah integer
        $startDate = Carbon::createFromDate($tahunMulai, $bulanMulai, 1);
        $endDate = (clone $startDate)->addMonths($durasi)->subDay();

        // Begin database transaction
        DB::beginTransaction();

        try {
            foreach ($request->karyawan_nik as $nik) {
                if ($patternType === 'mingguan') {
                    // Gunakan metode khusus untuk jadwal mingguan
                    $this->generateWeeklySchedule($nik, $startDate, $endDate, $request);
                } else {
                    // Logika lain (dipertahankan untuk kompatibilitas)
                    $pattern = [];

                    switch ($patternType) {
                        case 'rotasi_4_3':
                            $pattern = $this->generatePattern($request->shift_pagi, $request->shift_siang, $request->shift_malam, 4, 3);
                            break;
                        case 'rotasi_5_2':
                            $pattern = $this->generatePattern($request->shift_pagi, $request->shift_siang, $request->shift_malam, 5, 2);
                            break;
                        case 'rotasi_6_1':
                            $pattern = $this->generatePattern($request->shift_pagi, $request->shift_siang, $request->shift_malam, 6, 1);
                            break;
                        case 'custom':
                            $customPattern = json_decode($request->custom_pattern, true);
                            if (is_array($customPattern)) {
                                $pattern = $customPattern;
                            }
                            break;
                        default:
                            return redirect()->back()->with('error', 'Tipe pattern tidak valid');
                    }

                    $currentDate = clone $startDate;
                    $patternIndex = 0;
                    $patternLength = count($pattern);

                    // Generate schedules for the selected period
                    while ($currentDate <= $endDate) {
                        $daySchedule = $pattern[$patternIndex % $patternLength];

                        // Check if there's an existing schedule for this employee on this date
                        $existingSchedule = ShiftSchedule::where('karyawan_nik', $nik)
                            ->where('tanggal', $currentDate->format('Y-m-d'))
                            ->first();

                        if ($existingSchedule) {
                            // Update existing schedule
                            $existingSchedule->update([
                                'shift_id' => $daySchedule['shift_id'] ?? null,
                                'is_libur' => $daySchedule['is_libur'] ?? false,
                            ]);
                        } else {
                            // Create new schedule
                            ShiftSchedule::create([
                                'karyawan_nik' => $nik,
                                'tanggal' => $currentDate->format('Y-m-d'),
                                'shift_id' => $daySchedule['shift_id'] ?? null,
                                'is_libur' => $daySchedule['is_libur'] ?? false,
                            ]);
                        }

                        $currentDate->addDay();
                        $patternIndex++;
                    }
                }
            }

            DB::commit();
            return redirect()->route('jadwal-shift.index')->with('success', 'Jadwal shift massal berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate jadwal mingguan dengan mempertimbangkan Sabtu Minggu libur
     */
    private function generateWeeklySchedule($nik, $startDate, $endDate, $request)
    {
        $currentDate = clone $startDate;

        // Dapatkan shift untuk setiap minggu
        $shifts = [
            1 => $request->shift_minggu1,
            2 => $request->shift_minggu2,
            3 => $request->shift_minggu3,
            4 => $request->shift_minggu4,
            5 => $request->shift_minggu5,
        ];

        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek; // 0 (Minggu) sampai 6 (Sabtu)
            $weekOfMonth = ceil($currentDate->day / 7); // Minggu ke berapa dalam bulan (1-5)

            if ($weekOfMonth > 5) $weekOfMonth = 5; // Pastikan tidak melebihi array 5 minggu

            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6); // Sabtu (6) dan Minggu (0)

            // Cek jika ada jadwal yang sudah ada
            $existingSchedule = ShiftSchedule::where('karyawan_nik', $nik)
                ->where('tanggal', $currentDate->format('Y-m-d'))
                ->first();

            // Untuk hari Sabtu dan Minggu selalu libur
            if ($isWeekend) {
                if ($existingSchedule) {
                    $existingSchedule->update([
                        'shift_id' => null,
                        'is_libur' => true,
                    ]);
                } else {
                    ShiftSchedule::create([
                        'karyawan_nik' => $nik,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'shift_id' => null,
                        'is_libur' => true,
                    ]);
                }
            } else {
                // Hari kerja (Senin-Jumat), gunakan shift sesuai minggu
                $shiftId = $shifts[$weekOfMonth] ?? null;

                if ($existingSchedule) {
                    $existingSchedule->update([
                        'shift_id' => $shiftId,
                        'is_libur' => empty($shiftId), // Libur jika tidak ada shift
                    ]);
                } else {
                    ShiftSchedule::create([
                        'karyawan_nik' => $nik,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'shift_id' => $shiftId,
                        'is_libur' => empty($shiftId), // Libur jika tidak ada shift
                    ]);
                }
            }

            $currentDate->addDay();
        }
    }

    /**
     * Generate patterns for weekly scheduling
     */
    private function generateMingguanPattern($minggu1, $minggu2, $minggu3, $minggu4, $minggu5)
    {
        // Metode ini tidak digunakan untuk pola mingguan karena pola mingguan
        // menggunakan logika yang berbeda di generateWeeklySchedule
        // Namun tetap disiapkan sebagai placeholder
        return [];
    }

    public function debugCreateMassal()
    {
        try {
            $karyawan = Karyawan::all();
            $shifts = Shift::all();

            $bulanList = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ];

            $tahunList = [];
            $tahunSekarang = (int)date('Y');

            echo "Tahun sekarang: " . $tahunSekarang . " (tipe: " . gettype($tahunSekarang) . ")<br>";

            for ($i = $tahunSekarang - 1; $i <= $tahunSekarang + 2; $i++) {
                $tahunList[$i] = $i;
                echo "Tahun list item: " . $i . " (tipe: " . gettype($i) . ")<br>";
            }

            // Test Carbon createFromDate dengan berbagai tipe data
            echo "<h3>Testing Carbon createFromDate</h3>";

            // Test dengan integer
            $bulan_int = 1;
            $tahun_int = 2025;
            echo "Mencoba Carbon::createFromDate({$tahun_int}, {$bulan_int}, 1) - tipe data: int<br>";
            $date1 = Carbon::createFromDate($tahun_int, $bulan_int, 1);
            echo "Berhasil! Hasil: " . $date1->format('Y-m-d') . "<br>";

            // Test dengan string
            $bulan_str = '01';
            $tahun_str = '2025';
            echo "Mencoba Carbon::createFromDate({$tahun_str}, {$bulan_str}, 1) - tipe data: string<br>";
            try {
                $date2 = Carbon::createFromDate($tahun_str, $bulan_str, 1);
                echo "Berhasil! Hasil: " . $date2->format('Y-m-d') . "<br>";
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "<br>";
            }

            die('Debug selesai');
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    /**
     * Generate pattern for rotation schedules
     */
    private function generatePattern($shiftPagi, $shiftSiang, $shiftMalam, $workDays, $offDays)
    {
        $pattern = [];

        // Add work days with rotating shifts
        if ($workDays > 0) {
            for ($i = 0; $i < $workDays; $i++) {
                $shiftId = null;

                // Rotate between shifts if provided
                if ($i % 3 == 0 && $shiftPagi) {
                    $shiftId = $shiftPagi;
                } elseif ($i % 3 == 1 && $shiftSiang) {
                    $shiftId = $shiftSiang;
                } elseif ($i % 3 == 2 && $shiftMalam) {
                    $shiftId = $shiftMalam;
                } else {
                    // Default to first shift if specific shift not provided
                    $shiftId = $shiftPagi ?: $shiftSiang ?: $shiftMalam;
                }

                $pattern[] = [
                    'shift_id' => $shiftId,
                    'is_libur' => false
                ];
            }
        }

        // Add off days
        for ($i = 0; $i < $offDays; $i++) {
            $pattern[] = [
                'shift_id' => null,
                'is_libur' => true
            ];
        }

        return $pattern;
    }

    /**
     * Show detailed calendar view for a specific employee
     */
    public function karyawanDetail($nik, Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $karyawan = Karyawan::where('nik', $nik)->firstOrFail();
        $shifts = Shift::all();

        // Get all days in the selected month
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $startDate->daysInMonth;

        // Fetch all schedules for this employee in the selected month
        $jadwalData = ShiftSchedule::where('karyawan_nik', $nik)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        // Prepare calendar data dengan cara yang lebih baik menggunakan tanggal sebagai key
        $calendarData = [];
        foreach ($jadwalData as $jadwal) {
            $day = Carbon::parse($jadwal->tanggal)->day;
            $calendarData[$day] = [
                'jadwal' => $jadwal,
                'shift' => $jadwal->shift,
                'is_libur' => $jadwal->is_libur,
                'tanggal' => $jadwal->tanggal
            ];
        }

        // Prepare month and year lists for the dropdown
        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $tahunList = [];
        $tahunSekarang = date('Y');
        for ($i = $tahunSekarang - 2; $i <= $tahunSekarang + 2; $i++) {
            $tahunList[$i] = $i;
        }

        return view('admin.jadwal.karyawan-detail', compact(
            'karyawan',
            'calendarData',
            'shifts',
            'bulan',
            'tahun',
            'bulanList',
            'tahunList'
        ));
    }

    /**
     * Update a single day schedule for an employee via AJAX
     */
    public function updateSingleDay(Request $request)
    {
        $request->validate([
            'karyawan_nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
            'is_libur' => 'boolean',
        ]);

        // Memastikan format tanggal YYYY-MM-DD tanpa waktu untuk menghindari masalah timezone
        $tanggal = $request->tanggal;

        // Jika tanggal memiliki komponen waktu, hilangkan
        if (strlen($tanggal) > 10) {
            $tanggal = substr($tanggal, 0, 10);
        }

        // Debug output

        $jadwal = ShiftSchedule::where('karyawan_nik', $request->karyawan_nik)
            ->where('tanggal', $tanggal)
            ->first();

        if ($jadwal) {

            $jadwal->update([
                'shift_id' => $request->shift_id ?: null, // Konversi string kosong menjadi null
                'is_libur' => $request->is_libur ?? false,
            ]);
        } else {

            $jadwal = ShiftSchedule::create([
                'karyawan_nik' => $request->karyawan_nik,
                'tanggal' => $tanggal,
                'shift_id' => $request->shift_id ?: null, // Konversi string kosong menjadi null
                'is_libur' => $request->is_libur ?? false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'debug_tanggal' => $tanggal // Menambahkan ini untuk debugging
        ]);
    }
    public function getDaySchedule(Request $request)
    {
        $request->validate([
            'karyawan_nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
        ]);

        // Memastikan format tanggal YYYY-MM-DD tanpa waktu untuk menghindari masalah timezone
        $tanggal = $request->tanggal;

        // Jika tanggal memiliki komponen waktu, hilangkan
        if (strlen($tanggal) > 10) {
            $tanggal = substr($tanggal, 0, 10);
        }

        // Log untuk debugging
        Log::debug("Mencari jadwal untuk karyawan: {$request->karyawan_nik}, tanggal: {$tanggal}");

        // Gunakan carbon untuk memastikan format tanggal
        $formattedDate = Carbon::parse($tanggal)->format('Y-m-d');

        // Query dengan kondisi yang lebih tepat
        $jadwal = ShiftSchedule::where('karyawan_nik', $request->karyawan_nik)
            ->whereDate('tanggal', $formattedDate)
            ->first();

        Log::debug("Jadwal ditemukan: " . json_encode($jadwal));

        return response()->json([
            'success' => true,
            'jadwal' => $jadwal,
            'debug_tanggal' => $formattedDate, // Mengembalikan tanggal yang diformat
        ]);
    }
}
