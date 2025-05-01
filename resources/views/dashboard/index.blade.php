@extends('dashboard.layouts.main')
@section('container')
    <div>
        <!-- row 1 -->
        <div class="-mx-3 flex flex-wrap lg:gap-y-3">
            <!-- Jam Masuk Kerja -->
            <div class="mb-6 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p
                                        class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                        Jam Masuk Kerja</p>
                                    <h5 class="mb-2 font-bold dark:text-white">
                                        @php
                                            $today = \Carbon\Carbon::now()->format('Y-m-d');
                                            $jadwalHariIni = $jadwalShift->where('tanggal', $today)->first();
                                        @endphp
                                        @if ($jadwalHariIni && $jadwalHariIni->shift)
                                            {{ \Carbon\Carbon::parse($jadwalHariIni->shift->waktu_mulai)->format('H:i') }}
                                            WIB
                                        @else
                                            08:00 WIB
                                        @endif
                                    </h5>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div
                                    class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-blue-500 to-violet-500 text-center">
                                    <i class="ri-time-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jam Pulang Kerja -->
            <div class="mb-6 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p
                                        class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                        Jam Pulang Kerja</p>
                                    <h5 class="mb-2 font-bold dark:text-white">
                                        @if ($jadwalHariIni && $jadwalHariIni->shift)
                                            {{ \Carbon\Carbon::parse($jadwalHariIni->shift->waktu_selesai)->format('H:i') }}
                                            WIB
                                        @else
                                            16:00 WIB
                                        @endif
                                    </h5>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div
                                    class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-red-600 to-orange-600 text-center">
                                    <i class="ri-time-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Masuk Kerja Hari Ini -->
            <div class="mb-6 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p
                                        class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                        Masuk Kerja Hari Ini</p>
                                    <h5 class="mb-2 font-bold dark:text-white">
                                        {{ $presensiHariIni != null ? date('H:i:s', strtotime($presensiHariIni->jam_masuk)) . ' WIB' : 'Belum Presensi' }}
                                    </h5>
                                    <p class="mb-0 dark:text-white dark:opacity-60">
                                        @if ($presensiHariIni != null && $jadwalHariIni && $jadwalHariIni->shift)
                                            @php
                                                $jamMasukShift = \Carbon\Carbon::parse(
                                                    $jadwalHariIni->shift->waktu_mulai,
                                                )->format('H:i:s');
                                            @endphp
                                            @if (date('H:i:s', strtotime($presensiHariIni->jam_masuk)) < $jamMasukShift)
                                                <span
                                                    class="text-sm font-bold leading-normal text-emerald-500 dark:text-emerald-300">Anda
                                                    Datang Lebih Awal</span>
                                            @elseif (date('H:i:s', strtotime($presensiHariIni->jam_masuk)) > $jamMasukShift)
                                                <span
                                                    class="text-sm font-bold leading-normal text-red-600 dark:text-red-300">Anda
                                                    Datang Terlambat</span>
                                            @endif
                                        @elseif ($presensiHariIni != null)
                                            @if (date('H:i:s', strtotime($presensiHariIni->jam_masuk)) < date_create('08:00:00')->format('H:i:s'))
                                                <span
                                                    class="text-sm font-bold leading-normal text-emerald-500 dark:text-emerald-300">Anda
                                                    Datang Lebih Awal</span>
                                            @elseif (date('H:i:s', strtotime($presensiHariIni->jam_masuk)) > date_create('08:00:00')->format('H:i:s'))
                                                <span
                                                    class="text-sm font-bold leading-normal text-red-600 dark:text-red-300">Anda
                                                    Datang Terlambat</span>
                                            @endif
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div
                                    class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-emerald-500 to-teal-400 text-center">
                                    <i class="ri-login-circle-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pulang Kerja Hari Ini -->
            <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p
                                        class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                        Pulang Kerja Hari Ini</p>
                                    <h5 class="mb-2 font-bold dark:text-white">
                                        {{ $presensiHariIni != null && $presensiHariIni->jam_keluar != null ? date('H:i:s', strtotime($presensiHariIni->jam_keluar)) . ' WIB' : 'Belum Presensi' }}
                                    </h5>
                                    <p class="mb-0 dark:text-white dark:opacity-60">
                                        @if ($presensiHariIni != null && $presensiHariIni->jam_keluar != null && $jadwalHariIni && $jadwalHariIni->shift)
                                            @php
                                                $jamKeluarShift = \Carbon\Carbon::parse(
                                                    $jadwalHariIni->shift->waktu_selesai,
                                                )->format('H:i:s');
                                            @endphp
                                            @if (date('H:i:s', strtotime($presensiHariIni->jam_keluar)) < $jamKeluarShift)
                                                <span
                                                    class="text-sm font-bold leading-normal text-red-600 dark:text-red-300">Anda
                                                    Pulang Lebih Awal</span>
                                            @elseif (date('H:i:s', strtotime($presensiHariIni->jam_keluar)) > $jamKeluarShift)
                                                <span
                                                    class="text-sm font-bold leading-normal text-emerald-500 dark:text-emerald-300">Anda
                                                    Pulang Lebih Lama</span>
                                            @endif
                                        @elseif ($presensiHariIni != null && $presensiHariIni->jam_keluar != null)
                                            @if (date('H:i:s', strtotime($presensiHariIni->jam_keluar)) < date_create('16:00:00')->format('H:i:s'))
                                                <span
                                                    class="text-sm font-bold leading-normal text-red-600 dark:text-red-300">Anda
                                                    Pulang Lebih Awal</span>
                                            @elseif (date('H:i:s', strtotime($presensiHariIni->jam_keluar)) > date_create('16:00:00')->format('H:i:s'))
                                                <span
                                                    class="text-sm font-bold leading-normal text-emerald-500 dark:text-emerald-300">Anda
                                                    Pulang Lebih Lama</span>
                                            @endif
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div
                                    class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-orange-500 to-yellow-500 text-center">
                                    <i class="ri-logout-circle-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Shift Karyawan -->
        <div class="-mx-3 mt-6 flex flex-wrap">
            <div class="mb-6 mt-0 w-full max-w-full px-3">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl border-black-125 relative flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border shadow-xl">
                    <div class="rounded-t-4 mb-0 p-4 pb-0">
                        <div class="flex justify-between">
                            <h6 class="mb-2 font-bold dark:text-white">Jadwal Shift Minggu Ini</h6>
                        </div>
                    </div>

                    <div class="flex-auto p-4">
                        @if ($jadwalShift->count() > 0)
                            <div class="w-full overflow-x-auto">
                                <table
                                    class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                    <thead class="text-sm text-gray-800 dark:text-gray-300">
                                        <tr>
                                            <th>Hari/Tanggal</th>
                                            <th>Shift</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jadwalShift as $jadwal)
                                            <tr
                                                class="hover {{ \Carbon\Carbon::parse($jadwal->tanggal)->isToday() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                <td class="text-slate-500 dark:text-slate-300">
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('l') }}<br>
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d-m-Y') }}
                                                </td>
                                                <td>
                                                    @if ($jadwal->is_libur)
                                                        <span class="text-red-500 font-bold">Libur</span>
                                                    @elseif($jadwal->shift)
                                                        <span class="font-bold" style="color: {{ $jadwal->shift->warna }}">
                                                            {{ $jadwal->shift->nama }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($jadwal->is_libur)
                                                        <span class="text-red-500">-</span>
                                                    @elseif($jadwal->shift)
                                                        {{ \Carbon\Carbon::parse($jadwal->shift->waktu_mulai)->format('H:i') }}
                                                        WIB
                                                    @else
                                                        <span class="text-gray-500">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($jadwal->is_libur)
                                                        <span class="text-red-500">-</span>
                                                    @elseif($jadwal->shift)
                                                        {{ \Carbon\Carbon::parse($jadwal->shift->waktu_selesai)->format('H:i') }}
                                                        WIB
                                                    @else
                                                        <span class="text-gray-500">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (\Carbon\Carbon::parse($jadwal->tanggal)->isPast())
                                                        <span class="badge badge-ghost">Selesai</span>
                                                    @elseif(\Carbon\Carbon::parse($jadwal->tanggal)->isToday())
                                                        <span class="badge badge-primary">Hari Ini</span>
                                                    @else
                                                        <span class="badge badge-info">Akan Datang</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex justify-center items-center p-10">
                                <div class="text-center">
                                    <i class="ri-calendar-todo-line text-5xl text-gray-400"></i>
                                    <p class="mt-2 text-lg font-medium text-gray-500">Belum ada jadwal shift yang dibuat</p>
                                    <p class="text-sm text-gray-400">Silahkan hubungi administrator untuk pembuatan jadwal
                                        shift</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- row 2 -->
        <div class="-mx-3 mt-6 flex flex-wrap">
            <div class="mb-6 mt-0 w-full max-w-full px-3">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl border-black-125 relative flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border shadow-xl">
                    <div class="rounded-t-4 mb-0 p-4 pb-0">
                        <div class="flex justify-between">
                            <h6 class="mb-2 dark:text-white">Riwayat Presensi Bulan <span
                                    class="font-bold">{{ date('F') }}</span></h6>
                        </div>
                    </div>

                    <div class="mb-5 flex flex-wrap">
                        <!-- Rekap Hadir -->
                        <div class="mb-3 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                            <div
                                class="dark:bg-slate-900 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                                <div class="flex-auto p-4">
                                    <div class="-mx-3 flex flex-row">
                                        <div class="w-2/3 max-w-full flex-none px-3">
                                            <div>
                                                <p
                                                    class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                                    Hadir</p>
                                                <h5 class="mb-2 font-bold dark:text-white">
                                                    {{ $rekapPresensi->jml_kehadiran }}</h5>
                                            </div>
                                        </div>
                                        <div class="basis-1/3 px-3 text-right">
                                            <div
                                                class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-blue-500 to-blue-400 text-center">
                                                <i
                                                    class="ri-body-scan-line relative top-3 text-2xl leading-none text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rekap Izin -->
                        <div class="mb-3 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                            <div
                                class="dark:bg-slate-900 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                                <div class="flex-auto p-4">
                                    <div class="-mx-3 flex flex-row">
                                        <div class="w-2/3 max-w-full flex-none px-3">
                                            <div>
                                                <p
                                                    class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                                    Sakit</p>
                                                <h5 class="mb-2 font-bold dark:text-white">
                                                    {{ $rekapPengajuanPresensi->jml_sakit }}</h5>
                                            </div>
                                        </div>
                                        <div class="basis-1/3 px-3 text-right">
                                            <div
                                                class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-emerald-500 to-teal-400 text-center">
                                                <i
                                                    class="ri-hospital-line relative top-3 text-2xl leading-none text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rekap Sakit -->
                        <div class="mb-3 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                            <div
                                class="dark:bg-slate-900 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                                <div class="flex-auto p-4">
                                    <div class="-mx-3 flex flex-row">
                                        <div class="w-2/3 max-w-full flex-none px-3">
                                            <div>
                                                <p
                                                    class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                                    Izin</p>
                                                <h5 class="mb-2 font-bold dark:text-white">
                                                    {{ $rekapPengajuanPresensi->jml_izin }}</h5>
                                            </div>
                                        </div>
                                        <div class="basis-1/3 px-3 text-right">
                                            <div
                                                class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-yellow-500 to-amber-400 text-center">
                                                <i
                                                    class="ri-file-list-3-line relative top-3 text-2xl leading-none text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rekap Telat -->
                        <div class="mb-3 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                            <div
                                class="dark:bg-slate-900 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                                <div class="flex-auto p-4">
                                    <div class="-mx-3 flex flex-row">
                                        <div class="w-2/3 max-w-full flex-none px-3">
                                            <div>
                                                <p
                                                    class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">
                                                    Terlambat</p>
                                                <h5 class="mb-2 font-bold dark:text-white">
                                                    {{ $rekapPresensi->jml_terlambat ? $rekapPresensi->jml_terlambat : 0 }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="basis-1/3 px-3 text-right">
                                            <div
                                                class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-red-600 to-orange-500 text-center">
                                                <i
                                                    class="ri-timer-2-line relative top-3 text-2xl leading-none text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-y-10">
                        {{-- Tabel Rekap Presensi --}}
                        <div class="w-full overflow-x-auto lg:w-1/2 lg:flex-none">
                            <h1 class="ml-3 text-lg font-semibold dark:text-white">Rekap Presensi</h1>
                            <table
                                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                <thead class="text-sm text-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th></th>
                                        <th>Hari</th>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($riwayatPresensi as $value => $item)
                                        @php
                                            // Cari jadwal shift untuk tanggal tersebut
                                            $jadwalShift = App\Models\ShiftSchedule::where(
                                                'karyawan_nik',
                                                auth()->guard('karyawan')->user()->nik,
                                            )
                                                ->where('tanggal', $item->tanggal_presensi)
                                                ->with('shift')
                                                ->first();

                                            // Set jam default
                                            $jamMasukStandar = '08:00:00';
                                            $jamKeluarStandar = '16:00:00';

                                            // Jika ada jadwal shift, gunakan jam dari shift
                                            if ($jadwalShift && $jadwalShift->shift) {
                                                $jamMasukStandar = Carbon\Carbon::parse(
                                                    $jadwalShift->shift->waktu_mulai,
                                                )->format('H:i:s');
                                                $jamKeluarStandar = Carbon\Carbon::parse(
                                                    $jadwalShift->shift->waktu_selesai,
                                                )->format('H:i:s');
                                            }

                                            // Status presensi
                                            $statusMasuk =
                                                $item->jam_masuk < $jamMasukStandar ? 'text-success' : 'text-error';
                                            $statusKeluar =
                                                $item->jam_keluar > $jamKeluarStandar ? 'text-success' : 'text-error';
                                        @endphp
                                        <tr class="hover">
                                            <td class="font-bold">{{ $riwayatPresensi->firstItem() + $value }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ date('l', strtotime($item->tanggal_presensi)) }}</td>
                                            <td class="text-slate-500 dark:text-slate-300">
                                                {{ date('d-m-Y', strtotime($item->tanggal_presensi)) }}</td>
                                            <td class="{{ $statusMasuk }}">
                                                {{ date('H:i:s', strtotime($item->jam_masuk)) }}</td>
                                            @if ($item != null && $item->jam_keluar != null)
                                                <td class="{{ $statusKeluar }}">
                                                    {{ date('H:i:s', strtotime($item->jam_keluar)) }}</td>
                                            @else
                                                <td>Belum Presensi</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mx-3 mb-3">
                                {{ $riwayatPresensi->links() }}
                            </div>
                        </div>

                        {{-- Tabel Leaderboard Hari ini --}}
                        <div class="w-full overflow-x-auto lg:w-1/2 lg:flex-none">
                            <h1 class="ml-3 text-lg font-semibold dark:text-white">
                                Leaderboard
                                <span class="font-bold text-blue-700 dark:text-blue-500">{{ date('d-m-Y') }}</span>
                            </h1>
                            <table
                                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                <thead class="text-sm text-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th></th>
                                        <th>Nama</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Shift</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leaderboard as $value => $item)
                                        @php
                                            // Cari jadwal shift untuk karyawan ini hari ini
                                            $jadwalShift = App\Models\ShiftSchedule::where('karyawan_nik', $item->nik)
                                                ->where('tanggal', date('Y-m-d'))
                                                ->with('shift')
                                                ->first();
                                        @endphp
                                        <tr class="hover">
                                            <td class="font-bold">{{ $leaderboard->firstItem() + $value }}</td>
                                            <td class="w-3/10 whitespace-nowrap p-2">
                                                <div class="flex items-center px-2 py-1">
                                                    <div>
                                                        <h1
                                                            class="mb-0 font-bold leading-normal text-slate-500 dark:text-slate-300">
                                                            {{ $item->nama_lengkap }}</h1>
                                                        <p
                                                            class="mb-0 text-xs leading-tight text-slate-500 dark:text-slate-300">
                                                            {{ $item->jabatan }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="{{ $item->jam_masuk < '08:00' ? 'text-success' : 'text-error' }}">
                                                {{ date('H:i:s', strtotime($item->jam_masuk)) }}</td>
                                            @if ($item != null && $item->jam_keluar != null)
                                                <td
                                                    class="{{ $item->jam_keluar > '16:00' ? 'text-success' : 'text-error' }}">
                                                    {{ date('H:i:s', strtotime($item->jam_keluar)) }}</td>
                                            @else
                                                <td>Belum Presensi</td>
                                            @endif
                                            <td>
                                                @if ($jadwalShift && $jadwalShift->is_libur)
                                                    <span class="badge badge-error">Libur</span>
                                                @elseif($jadwalShift && $jadwalShift->shift)
                                                    <span class="badge badge-info">{{ $jadwalShift->shift->nama }}</span>
                                                @else
                                                    <span class="badge badge-ghost">Reguler</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mx-3 mb-3">
                                {{ $leaderboard->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
