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
            font-size: 18px; /* Make the title font slightly larger */
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
            width: 120px; /* Increased logo size */
            height: 120px;
            border-radius: 21px;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
            padding: 1rem;
        }

        .header-title {
            font-size: 22px; /* Larger font size for title */
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
                    <span style="display: block; margin-top: 10px;">
                        <i>Jl. Raya Baturaden KM.10, Dusun III, Karangtengah, Kec. Baturaden, Kabupaten Banyumas, Jawa Tengah 53151.</i>
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
                @foreach ($riwayatPresensi as $value => $item)
                    <tr>
                        <td>{{ $value + 1 }}</td>
                        <td>{{ \Carbon\Carbon::make($item->tanggal_presensi)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::make($item->jam_masuk)->format('H:i') }}</td>
                        <td><img src="{{ public_path("storage/unggah/presensi/$item->foto_masuk") }}" width="50"
                                height="50" /></td>
                        <td>{{ \Carbon\Carbon::make($item->jam_keluar)->format('H:i') ?? 'Belum Presensi' }}</td>
                        <td><img src="{{ public_path("storage/unggah/presensi/$item->foto_keluar") }}" width="50"
                                height="50" /></td>
                        <td>
                            @php
                                // Ambil shift berdasarkan nik
                                $shift = DB::table('shifts')
                                    ->join('shift_schedules', 'shift_schedules.shift_id', '=', 'shifts.id')
                                    ->where('shift_schedules.karyawan_nik', $item->nik) // Sesuaikan dengan kolom nik
                                    ->first();

                                // Ambil waktu mulai shift
                                $waktuMulaiShift = Carbon\Carbon::make($shift->waktu_mulai);
                                $masuk = Carbon\Carbon::make($item->jam_masuk); // Waktu masuk karyawan
                            @endphp

                            @if ($masuk->gt($waktuMulaiShift))  <!-- Jika waktu masuk lebih besar dari waktu mulai shift -->
                                @php
                                    $diff = $masuk->diff($waktuMulaiShift);  // Hitung selisih antara jam masuk dan waktu mulai shift
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
                        </td>

                        <!-- Hanya tampilkan jumlah lembur, izin, sakit, dan cuti -->
                        <td>{{ $lembur }} jam</td>
                        <td>{{ $izin }} hari</td>
                        <td>{{ $sakit }} hari</td>
                        <td>{{ $totalCuti }} hari</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

</body>

</html>
