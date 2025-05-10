<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $title . '.pdf' }}</title>

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
                    <span style="display: block; margin-top: 10px;">
                        <i>Jl. Raya Baturaden KM.10, Dusun III, Karangtengah, Kec. Baturaden, Kabupaten Banyumas, Jawa Tengah 53151</i>
                    </span>
                </td>
            </tr>
        </table>

        <table class="presensi-karyawan">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Karyawan</th>
            <th>NIK</th>
            {{-- <th>Jabatan / Departemen</th> --}}
            <th>Departemen</th>
            <th>Jumlah Kehadiran</th>
            <th>Jumlah Lembur</th>
            <th>Jumlah Izin</th>
            <th>Jumlah Sakit</th>
            <th>Jumlah Cuti</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($riwayatPresensi as $value => $item)
            <tr>
                <td>
                    {{ $value + 1 . '.' }}
                </td>
                <td>
                    {{ $item->nama_karyawan }}
                </td>
                <td>
                    {{ $item->nik }}
                </td>
                <td>
                    {{ $item->nama_departemen }}
                </td>
                <td>
                    {{ $item->total_kehadiran }}
                </td>
                <td>{{ $item->total_lembur }} jam</td>
                <td>{{ $item->total_izin }} hari</td>
                <td>{{ $item->total_sakit }} hari</td>
                <td>{{ $item->total_cuti }} hari</td>
            </tr>
        @endforeach
    </tbody>
</table>

    </section>

</body>

</html>
