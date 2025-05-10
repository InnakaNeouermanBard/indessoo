<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $title . ' ' . $karyawan->nama_lengkap . '.pdf' }}</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4;
        }

        .title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            /* Make the title font slightly larger */
            font-weight: 800;
            line-height: 1.5rem;
        }

        table {
            border-collapse: collapse;
        }

        .identitas-karyawan {
            margin-top: 2rem;
        }

        .identitas-karyawan td {
            padding: 0.25rem;
        }

        .presensi-karyawan {
            width: 100%;
            margin-top: 1.5rem;
        }

        .presensi-karyawan tbody>tr>td {
            text-align: center;
            padding: 0.5rem;
        }

        .presensi-karyawan th {
            font-weight: bold;
            background: salmon;
            padding: 0.5rem;
            font-size: 14px;
        }

        .presensi-karyawan>tbody>tr>td {
            font-size: 12px;
        }

        .presensi-karyawan,
        .presensi-karyawan>thead>tr>th,
        .presensi-karyawan>tbody>tr>td {
            border: 1px solid black;
            padding: 0.5rem;
        }

        .pengesahan-atasan {
            width: 100%;
            margin-top: 2rem;
        }

        .atasan td {
            text-align: center;
            vertical-align: bottom;
            height: 10rem;
        }

        .tempat td {
            text-align: right;
        }

        /* Adjust the logo and text layout */
        .header-logo {
            width: 120px;
            /* Increased logo size */
            height: 120px;
            border-radius: 21px;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
            padding: 1rem;
        }

        .header-title {
            font-size: 22px;
            /* Larger font size for title */
        }

        .status-indicator {
            font-weight: bold;
            font-size: 16px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .checkmark {
            font-size: 16px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>

<body class="A4">

    <section class="sheet padding-10mm">
        <table style="width: 100%; height: 150px; border-collapse: collapse;">
            <tr>
                <td style="width: 180px; text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('img/logo-fix.png') }}" alt="logo" class="header-logo" />
                </td>
                <td class="header-text">
                    <span class="title header-title" style="display: block; margin-bottom: 5px;">
                        {{ strtoupper($title) }} <br>
                    </span>
                    <span class="title header-title" style="display: block; margin-bottom: 5px;">
                        PERIODE {{ strtoupper(\Carbon\Carbon::make($bulan)->format('F')) }} TAHUN
                        {{ \Carbon\Carbon::make($bulan)->format('Y') }} <br>
                    </span>
                    <span class="title header-title" style="display: block; margin-bottom: 5px;">
                        PT INDESSO AROMA BATURRADEN<br>
                    </span>
                </td>
            </tr>
        </table>


        <table class="identitas-karyawan">
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td>Email / Telepon</td>
                <td>:</td>
                <td>{{ $karyawan->email }} / {{ $karyawan->telepon }}</td>
            </tr>
        </table>


        <table class="presensi-karyawan">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Foto</th>
                    <th>Jam Keluar</th>
                    <th>Foto</th>
                    <th>Keterangan</th>
                    <th>Lembur</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Cuti</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Inisialisasi variabel untuk perhitungan status per hari
                    $hariIzin = 0;
                    $hariSakit = 0;
                    $hariCuti = 0;
                    $totalJamLembur = 0;
                @endphp

                @foreach ($riwayatPresensi as $value => $item)
                    @php
                        // Cek apakah pada tanggal tersebut ada pengajuan izin/sakit/cuti
                        $tanggalPresensi = \Carbon\Carbon::make($item->tanggal_presensi)->format('Y-m-d');

                        // Cek izin untuk tanggal ini
                        $checkIzin = DB::table('pengajuan_presensi')
                            ->where('nik', $item->nik)
                            ->where('status', 'I')
                            ->where(function ($query) use ($tanggalPresensi) {
                                $query
                                    ->where('tanggal_mulai', '<=', $tanggalPresensi)
                                    ->where('tanggal_selesai', '>=', $tanggalPresensi);
                            })
                            ->exists();

                        // Cek sakit untuk tanggal ini
                        $checkSakit = DB::table('pengajuan_presensi')
                            ->where('nik', $item->nik)
                            ->where('status', 'S')
                            ->where(function ($query) use ($tanggalPresensi) {
                                $query
                                    ->where('tanggal_mulai', '<=', $tanggalPresensi)
                                    ->where('tanggal_selesai', '>=', $tanggalPresensi);
                            })
                            ->exists();

                        // Cek cuti untuk tanggal ini
                        $checkCuti = DB::table('pengajuan_presensi')
                            ->where('nik', $item->nik)
                            ->where('status', 'C')
                            ->where(function ($query) use ($tanggalPresensi) {
                                $query
                                    ->where('tanggal_mulai', '<=', $tanggalPresensi)
                                    ->where('tanggal_selesai', '>=', $tanggalPresensi);
                            })
                            ->exists();

                        // Cek lembur untuk tanggal ini
                        $jamLembur =
                            DB::table('form_lemburs')
                                ->where('nik', $item->nik)
                                ->where('tanggal', $tanggalPresensi)
                                ->value('overtime') ?? 0;

                        // Tambahkan ke total
                        if ($checkIzin) {
                            $hariIzin++;
                        }
                        if ($checkSakit) {
                            $hariSakit++;
                        }
                        if ($checkCuti) {
                            $hariCuti++;
                        }
                        $totalJamLembur += $jamLembur;
                    @endphp

                    <tr>
                        <td>{{ $value + 1 }}</td>
                        <td>{{ \Carbon\Carbon::make($item->tanggal_presensi)->format('d-m-Y') }}</td>
                        <td>
                            @if ($checkIzin || $checkSakit || $checkCuti)
                                -
                            @else
                                {{ \Carbon\Carbon::make($item->jam_masuk)->format('H:i') }}
                            @endif
                        </td>
                        <td>
                            @if ($checkIzin || $checkSakit || $checkCuti)
                                -
                            @else
                                <img src="{{ public_path("storage/unggah/presensi/$item->foto_masuk") }}"
                                    width="50" height="50" />
                            @endif
                        </td>
                        <td>
                            @if ($checkIzin || $checkSakit || $checkCuti)
                                -
                            @else
                                {{ \Carbon\Carbon::make($item->jam_keluar)->format('H:i') ?? 'Belum Presensi' }}
                            @endif
                        </td>
                        <td>
                            @if ($checkIzin || $checkSakit || $checkCuti)
                                -
                            @else
                                <img src="{{ public_path("storage/unggah/presensi/$item->foto_keluar") }}"
                                    width="50" height="50" />
                            @endif
                        </td>
                        <td>
                            @if ($checkIzin)
                                <span class="status-indicator">IZIN</span>
                            @elseif($checkSakit)
                                <span class="status-indicator">SAKIT</span>
                            @elseif($checkCuti)
                                <span class="status-indicator">CUTI</span>
                            @else
                                @php
                                    // Ambil shift berdasarkan nik
                                    $shift = DB::table('shifts')
                                        ->join('shift_schedules', 'shift_schedules.shift_id', '=', 'shifts.id')
                                        ->where('shift_schedules.karyawan_nik', $item->nik)
                                        ->where('shift_schedules.tanggal', $tanggalPresensi)
                                        ->first();

                                    // Jika tidak ditemukan, gunakan shift default
                                    if (!$shift) {
                                        $shift = (object) [
                                            'waktu_mulai' => '08:00:00',
                                            'nama' => 'Default',
                                        ];
                                    }

                                    // Ambil waktu mulai shift
                                    $waktuMulaiShift = Carbon\Carbon::make($shift->waktu_mulai);
                                    $masuk = Carbon\Carbon::make($item->jam_masuk); // Waktu masuk karyawan
                                @endphp

                                @if ($masuk && $waktuMulaiShift && $masuk->gt($waktuMulaiShift))
                                    <!-- Jika waktu masuk lebih besar dari waktu mulai shift -->
                                    @php
                                        $diff = $masuk->diff($waktuMulaiShift); // Hitung selisih antara jam masuk dan waktu mulai shift
                                        if ($diff->format('%h') != 0) {
                                            $selisih = $diff->format('%h jam %I menit');
                                        } else {
                                            $selisih = $diff->format('%I menit');
                                        }
                                    @endphp
                                    <div>Terlambat <br> {{ $selisih }}</div>
                                @else
                                    <div>Tepat Waktu</div>
                                @endif
                            @endif
                        </td>

                        <!-- Status per hari -->
                        <td>{{ $jamLembur ? $jamLembur . ' jam' : '-' }}</td>
                        <td>{!! $checkIzin ? '<span class="checkmark">✓</span>' : '-' !!}</td>
                        <td>{!! $checkSakit ? '<span class="checkmark">✓</span>' : '-' !!}</td>
                        <td>{!! $checkCuti ? '<span class="checkmark">✓</span>' : '-' !!}</td>
                    </tr>
                @endforeach

                <!-- Baris Total -->
                <tr class="total-row">
                    <td colspan="7" style="text-align: right"><strong>TOTAL</strong></td>
                    <td><strong>{{ $totalJamLembur }} jam</strong></td>
                    <td><strong>{{ $hariIzin }} hari</strong></td>
                    <td><strong>{{ $hariSakit }} hari</strong></td>
                    <td><strong>{{ $hariCuti }} hari</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Tanda tangan -->
        <table class="pengesahan-atasan" style="margin-top: 30px;">
            <tr class="tempat">
                <td colspan="2">Baturraden, {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
            </tr>
            <tr class="atasan">
                <td>
                    <p>Mengetahui,</p>
                    <p>HR Manager</p>
                    <br>
                    <br>
                    <br>
                    <p><strong>____________</strong></p>
                </td>
                <td>
                    <p>Dibuat Oleh,</p>
                    <p>HR Staff</p>
                    <br>
                    <br>
                    <br>
                    <p><strong>{{ auth()->user()->name }}</strong></p>
                </td>
            </tr>
        </table>
    </section>

</body>

</html>
