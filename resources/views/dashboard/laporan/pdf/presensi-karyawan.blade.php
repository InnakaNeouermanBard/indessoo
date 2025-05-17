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
            size: A4
        }

        .title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
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
            padding: 0.5rem
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
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">
        <table style="width: 100%; height: 150px; border-collapse: collapse;">
            <tr>
                <td style="width: 180px; text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('img/logo-fix.png') }}" alt="logo" width="100" height="100"
                        style="border-radius: 21px" />
                </td>
                <td style="text-align: center; vertical-align: middle; padding: 1rem;">
                    <span class="title" style="display: block; margin-bottom: 5px;">
                        {{ strtoupper($title) }} <br>
                    </span>
                    <span class="title" style="display: block; margin-bottom: 5px;">
                        PERIODE {{ strtoupper(\Carbon\Carbon::make($bulan)->format('F')) }} TAHUN
                        {{ \Carbon\Carbon::make($bulan)->format('Y') }} <br>
                    </span>
                    <span class="title" style="display: block; margin-bottom: 5px;">
                        PT INDESSO AROMA BATURRADEN <br>
                    </span>
                </td>
            </tr>
        </table>


        <table class="identitas-karyawan">
            {{-- <tr>
                <td rowspan="7">
                    @if ($karyawan->foto)
                        <img src="{{ public_path("storage/unggah/karyawan/$karyawan->foto") }}" alt="foto-karyawan"
                            width="100" height="150" style="border-radius: 0.5rem" />
                    @else
                        <img src="{{ public_path('img/team-2.jpg') }}" alt="foto-karyawan" width="100" height="150"
                            style="border-radius: 0.5rem" />
                    @endif
                </td>
            </tr> --}}
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
            {{-- <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>:</td>
                <td>{{ $karyawan->departemen->nama }}</td>
            </tr>
            <tr>
                <td>Email / Telepon</td>
                <td>:</td>
                <td>{{ $karyawan->email }} / {{ $karyawan->telepon }}</td>
            </tr> --}}
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
                    use Carbon\Carbon;
                    // Inisialisasi variabel untuk perhitungan status per hari
                    $hariIzin = 0;
                    $hariSakit = 0;
                    $hariCuti = 0;
                    $totalJamLembur = 0;
                @endphp

                @foreach ($riwayatPresensi as $index => $item)
    @php
        $tanggal = Carbon::parse($item['tanggal'])->format('Y-m-d');
        $presensi = $item['presensi'];
        $pengajuan = $item['pengajuan'];

        // Status Izin/Sakit/Cuti
        $statusIzin = $statusSakit = $statusCuti = false;
        $statusLabel = '-';

        if ($pengajuan) {
            if ($pengajuan->status === 'I') {
                $statusIzin = true;
                $statusLabel = 'Izin';
                $hariIzin++;
            } elseif ($pengajuan->status === 'S') {
                $statusSakit = true;
                $statusLabel = 'Sakit';
                $hariSakit++;
            } elseif ($pengajuan->status === 'C') {
                $statusCuti = true;
                $statusLabel = 'Cuti';
                $hariCuti++;
            }
        }

        // Cek jam lembur
        $jamLembur = DB::table('form_lemburs')
            ->where('nik', $karyawan->nik)
            ->whereDate('tanggal', $tanggal)
            ->value('overtime') ?? 0;
        $totalJamLembur += $jamLembur;
    @endphp

    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ Carbon::parse($tanggal)->format('d-m-Y') }}</td>

        {{-- Jam Masuk --}}
        <td>
            @if ($presensi && !$statusIzin && !$statusSakit && !$statusCuti)
                {{ Carbon::parse($presensi->jam_masuk)->format('H:i') }}
            @else
                -
            @endif
        </td>

        {{-- Foto Masuk --}}
        <td>
            @if ($presensi && !$statusIzin && !$statusSakit && !$statusCuti && $presensi->foto_masuk)
                <img src="{{ public_path("storage/unggah/presensi/$presensi->foto_masuk") }}" width="50" height="50" />
            @else
                -
            @endif
        </td>

        {{-- Jam Keluar --}}
        <td>
            @if ($presensi && !$statusIzin && !$statusSakit && !$statusCuti)
                {{ $presensi->jam_keluar ? Carbon::parse($presensi->jam_keluar)->format('H:i') : 'Belum Presensi' }}
            @else
                -
            @endif
        </td>

        {{-- Foto Keluar --}}
        <td>
            @if ($presensi && !$statusIzin && !$statusSakit && !$statusCuti && $presensi->foto_keluar)
                <img src="{{ public_path("storage/unggah/presensi/$presensi->foto_keluar") }}" width="50" height="50" />
            @else
                -
            @endif
        </td>

        {{-- Keterangan Presensi (Terlambat / Tepat Waktu / Status Izin) --}}
        <td>
            @if ($statusIzin || $statusSakit || $statusCuti)
                <span class="status-indicator">{{ $statusLabel }}</span>
            @elseif ($presensi)
                @php
                    $shift = DB::table('shifts')
                        ->join('shift_schedules', 'shift_schedules.shift_id', '=', 'shifts.id')
                        ->where('shift_schedules.karyawan_nik', $karyawan->nik)
                        ->where('shift_schedules.tanggal', $tanggal)
                        ->first();

                    // Jika tidak ditemukan shift, gunakan default
                    if (!$shift) {
                        $shift = (object) [
                            'waktu_mulai' => '08:00:00',
                            'nama' => 'Default',
                        ];
                    }

                    $waktuMulaiShift = Carbon::make($shift->waktu_mulai);
                    $jamMasuk = Carbon::make($presensi->jam_masuk);
                @endphp

                @if ($jamMasuk && $jamMasuk->gt($waktuMulaiShift))
                    @php
                        $diff = $jamMasuk->diff($waktuMulaiShift);
                        $selisih = $diff->format('%h jam %I menit');
                    @endphp
                    <div>Terlambat <br> {{ $selisih }}</div>
                @else
                    <div>Tepat Waktu</div>
                @endif
            @else
                -
            @endif
        </td>

        {{-- Lembur --}}
        <td>{{ $jamLembur ? $jamLembur . ' jam' : '-' }}</td>

        {{-- Status Harian: Izin / Sakit / Cuti --}}
        <td>{{ $statusIzin ? 'Ya' : '-' }}</td>
        <td>{{ $statusSakit ? 'Ya' : '-' }}</td>
        <td>{{ $statusCuti ? 'Ya' : '-' }}</td>
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
    </section>

</body>

</html>
