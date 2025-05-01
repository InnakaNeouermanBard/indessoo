@extends('dashboard.layouts.main')
{{-- dashboard\presensi\index  --}}
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        @media (max-width: 425px) {

            #webcam-capture,
            #webcam-capture video {
                width: 300px !important;
                height: 380px !important;
                margin: auto;
                border-radius: 33px;
            }

            #map {
                height: 250px !important;
            }
        }

        @media (min-width: 640px) {

            #webcam-capture,
            #webcam-capture video {
                width: 480px !important;
                height: 640px !important;
                margin: auto;
                border-radius: 33px;
            }

            #map {
                height: 300px !important;
            }
        }

        @media (min-width: 768px) {

            #webcam-capture,
            #webcam-capture video {
                width: 640px !important;
                height: 480px !important;
                margin: auto;
                border-radius: 33px;
            }

            #map {
                height: 400px !important;
            }
        }

        #map-container {
            display: none;
            margin-top: 20px;
        }

        #map {
            width: 100%;
            height: 400px;
            border-radius: 15px;
        }

        .camera-map-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        @media (min-width: 1024px) {
            .camera-map-container {
                flex-direction: row;
            }

            #webcam-container,
            #map-container {
                width: 50%;
            }
        }
    </style>
@endsection


@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"
        integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Data status presensi karyawan
        const presensiKaryawanExists = {{ $presensiKaryawan ? 'true' : 'false' }};
        const presensiKaryawanKeluar = {{ $presensiKaryawan && $presensiKaryawan->jam_keluar != null ? 'true' : 'false' }};

        // Jadwal shift karyawan hari ini
        const jadwalShiftExists = {{ isset($jadwalHariIni) && $jadwalHariIni ? 'true' : 'false' }};
        const isLibur = {{ isset($jadwalHariIni) && $jadwalHariIni && $jadwalHariIni->is_libur ? 'true' : 'false' }};

        // Variabel untuk status webcam dan map
        let webcamInitialized = false;
        let mapInitialized = false;
        let image = null;
        let map = null;
        let marker = null;
        let circle = null;
        let userLatitude = null;
        let userLongitude = null;

        // Mendapatkan lokasi dan simpan di input hidden
        let lokasi = document.getElementById('lokasi');

        // Fungsi untuk mendapatkan lokasi
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(successCallback, errorCallBack, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Geolocation tidak didukung oleh browser ini.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        }

        // Panggil getLocation saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            getLocation();
        });

        function successCallback(position) {
            userLatitude = position.coords.latitude;
            userLongitude = position.coords.longitude;
            lokasi.value = userLatitude + ", " + userLongitude;

            // Jika map sudah diinisialisasi, perbarui posisi marker
            if (mapInitialized && map) {
                updateMarkerPosition(userLatitude, userLongitude);
            }
        }

        function errorCallBack(error) {
            console.error("Error getting location:", error);
            Swal.fire({
                title: "Error",
                text: "Tidak dapat mengakses lokasi Anda. Pastikan GPS diaktifkan. Error: " + error.message,
                icon: "error",
                confirmButtonText: "OK"
            });
        }

        // Inisialisasi map
        function initMap() {
            if (mapInitialized) return;

            document.getElementById('map-container').style.display = 'block';

            setTimeout(function() {
                try {
                    // Jika lokasi belum didapatkan, coba dapatkan lagi
                    if (!userLatitude || !userLongitude) {
                        getLocation();
                        // Gunakan lokasi default jika masih belum ada
                        if (!userLatitude || !userLongitude) {
                            userLatitude = -7.7956; // Default latitude (contoh: Jakarta)
                            userLongitude = 110.3695; // Default longitude (contoh: Jakarta)
                        }
                    }

                    map = L.map('map').setView([userLatitude, userLongitude], 17);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    // Tambahkan marker posisi user
                    marker = L.marker([userLatitude, userLongitude]).addTo(map);
                    marker.bindPopup("<b>Anda berada di sini</b>").openPopup();

                    // Tambahkan circle untuk menunjukkan radius kantor
                    circle = L.circle([{{ $lokasiKantor->latitude }}, {{ $lokasiKantor->longitude }}], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: {{ $lokasiKantor->radius }}
                    }).addTo(map);

                    // Pastikan map dirender dengan benar
                    setTimeout(function() {
                        map.invalidateSize();
                    }, 500);

                    mapInitialized = true;
                    console.log("Map berhasil dimuat");
                } catch (error) {
                    console.error("Error initializing map:", error);
                    Swal.fire({
                        title: "Error",
                        text: "Terjadi kesalahan saat memuat peta. Silakan refresh halaman.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            }, 1000);
        }

        // Fungsi untuk memperbarui posisi marker
        function updateMarkerPosition(lat, lng) {
            if (marker && map) {
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
            }
        }

        let notifikasi_presensi_masuk = document.getElementById('notifikasi_presensi_masuk');
        let notifikasi_presensi_keluar = document.getElementById('notifikasi_presensi_keluar');
        let notifikasi_presensi_gagal_radius = document.getElementById('notifikasi_presensi_gagal_radius');

        // Inisialisasi webcam
        function initWebcam() {
            if (webcamInitialized) return;

            document.getElementById('webcam-container').style.display = 'block';

            setTimeout(function() {
                try {
                    Webcam.set({
                        width: 640,
                        height: 480,
                        image_format: 'jpeg',
                        jpeg_quality: 90,
                        force_flash: false,
                        flip_horiz: false,
                    });
                    Webcam.attach('#webcam-capture');
                    webcamInitialized = true;
                    console.log("Webcam berhasil dimuat");
                } catch (error) {
                    console.error("Error initializing webcam:", error);
                    Swal.fire({
                        title: "Error",
                        text: "Terjadi kesalahan saat memuat webcam. Silakan refresh halaman.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            }, 1000);
        }

        // Tombol presensi masuk
        $("#btn-presensi-masuk").click(function() {
            // Cek jika karyawan libur
            if (jadwalShiftExists && isLibur) {
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda dijadwalkan libur hari ini, tidak perlu melakukan presensi!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Inisialisasi webcam dan map
            initWebcam();
            initMap();

            if (presensiKaryawanExists) {
                // Sudah presensi masuk
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda sudah melakukan presensi masuk hari ini!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
            } else {
                // Belum presensi masuk
                $("input[name='presensi']").val("masuk");

                // Jika webcam belum siap, tunda capture
                if (!webcamInitialized) {
                    setTimeout(function() {
                        if (webcamInitialized) {
                            captureAndSendPresensi();
                        } else {
                            Swal.fire({
                                title: "Peringatan",
                                text: "Webcam belum siap. Mohon tunggu beberapa saat dan coba lagi.",
                                icon: "warning",
                                confirmButtonText: "OK"
                            });
                        }
                    }, 2000);
                } else {
                    captureAndSendPresensi();
                }
            }
        });

        // Tombol presensi keluar
        $("#btn-presensi-keluar").click(function() {
            // Cek jika karyawan libur
            if (jadwalShiftExists && isLibur) {
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda dijadwalkan libur hari ini, tidak perlu melakukan presensi!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Inisialisasi webcam dan map
            initWebcam();
            initMap();

            if (!presensiKaryawanExists) {
                // Belum presensi masuk sama sekali
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda belum melakukan presensi masuk hari ini!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
            } else if (presensiKaryawanKeluar) {
                // Sudah presensi keluar
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda sudah melakukan presensi keluar hari ini!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
            } else {
                // Sudah presensi masuk, belum presensi keluar
                $("input[name='presensi']").val("keluar");

                // Jika webcam belum siap, tunda capture
                if (!webcamInitialized) {
                    setTimeout(function() {
                        if (webcamInitialized) {
                            captureAndSendPresensi();
                        } else {
                            Swal.fire({
                                title: "Peringatan",
                                text: "Webcam belum siap. Mohon tunggu beberapa saat dan coba lagi.",
                                icon: "warning",
                                confirmButtonText: "OK"
                            });
                        }
                    }, 2000);
                } else {
                    captureAndSendPresensi();
                }
            }
        });

        // Fungsi untuk mengambil foto dan mengirim data presensi
        function captureAndSendPresensi() {
            if (!webcamInitialized) {
                Swal.fire({
                    title: "Peringatan",
                    text: "Webcam belum siap. Mohon tunggu beberapa saat.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Perbarui lokasi sebelum mengirim
            getLocation();

            try {
                Webcam.snap(function(uri) {
                    image = uri;

                    // Tambahkan indikator loading
                    Swal.fire({
                        title: "Memproses...",
                        text: "Sedang mengirim data presensi",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "{{ route('karyawan.presensi.store') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            image: image,
                            lokasi: lokasi.value,
                            jenis: $("input[name='presensi']").val(),
                        },
                        cache: false,
                        success: function(res) {
                            Swal.close();

                            if (res.status == 200) {
                                if (res.jenis_presensi == "masuk") {
                                    notifikasi_presensi_masuk.play();
                                } else if (res.jenis_presensi == "keluar") {
                                    notifikasi_presensi_keluar.play();
                                }
                                Swal.fire({
                                    title: "Presensi",
                                    text: res.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });
                                setTimeout("location.href='{{ route('karyawan.dashboard') }}'", 3000);

                            } else if (res.status == 500) {
                                if (res.jenis_error == "radius") {
                                    notifikasi_presensi_gagal_radius.play();
                                }
                                Swal.fire({
                                    title: "Presensi",
                                    text: res.message,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            console.error("AJAX Error:", error);
                            Swal.fire({
                                title: "Error",
                                text: "Terjadi kesalahan saat mengirim data. Silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                });
            } catch (error) {
                console.error("Error during webcam capture:", error);
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengambil gambar. Pastikan webcam Anda berfungsi dengan baik.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $("#searchButton").click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ route('karyawan.history.search') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: $("#bulan").val(),
                        tahun: $("#tahun").val()
                    },
                    cache: false,
                    success: function(res) {
                        // $("#tabelPresensi").remove();
                        $("#searchPresensi").html(res);
                    }
                });
            });
        });
    </script>
@endsection


@section('container')
    <div class="mb-6">
        <audio id="notifikasi_presensi_masuk">
            <source src="{{ asset('audio/notifikasi_presensi_masuk.mp3') }}" type="audio/mpeg">
        </audio>
        <audio id="notifikasi_presensi_keluar">
            <source src="{{ asset('audio/notifikasi_presensi_keluar.mp3') }}" type="audio/mpeg">
        </audio>
        <audio id="notifikasi_presensi_gagal_radius">
            <source src="{{ asset('audio/notifikasi_presensi_gagal_radius.mp3') }}" type="audio/mpeg">
        </audio>
        <div class="-mx-3 flex flex-wrap">
            <div class="mb-6 w-full max-w-full px-3 sm:flex-none">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <input type="text" name="lokasi" id="lokasi" class="input input-primary" hidden>
                        <input type="text" name="presensi" id="presensi" value="" hidden>

                        <!-- Informasi Jadwal Shift Hari Ini -->
                        @if (isset($jadwalHariIni) && $jadwalHariIni)
                            <div class="mb-4 flex flex-col items-center justify-center">
                                @if ($jadwalHariIni->is_libur)
                                    <div class="alert alert-warning">
                                        <div class="flex items-center">
                                            <i class="ri-calendar-event-line mr-2 text-2xl"></i>
                                            <div>
                                                <h3 class="font-bold">Jadwal Libur</h3>
                                                <p>Anda dijadwalkan libur pada hari ini</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($jadwalHariIni->shift)
                                    <div class="alert alert-info">
                                        <div class="flex items-center">
                                            <i class="ri-time-line mr-2 text-2xl"></i>
                                            <div>
                                                <h3 class="font-bold">Jadwal Shift Hari Ini:
                                                    {{ $jadwalHariIni->shift->nama }}</h3>
                                                <p>Jam Kerja:
                                                    {{ Carbon\Carbon::parse($jadwalHariIni->shift->waktu_mulai)->format('H:i') }}
                                                    -
                                                    {{ Carbon\Carbon::parse($jadwalHariIni->shift->waktu_selesai)->format('H:i') }}
                                                    WIB</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mb-4 flex flex-col items-center justify-center">
                                <div class="alert alert-info">
                                    <div class="flex items-center">
                                        <i class="ri-information-line mr-2 text-2xl"></i>
                                        <div>
                                            <h3 class="font-bold">Informasi Shift</h3>
                                            <p>Tidak ada jadwal shift yang ditemukan untuk hari ini. Menggunakan jadwal
                                                default (08:00 - 16:00)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tombol Presensi -->
                        <div class="flex justify-center mb-4">
                            <div class="flex gap-4">
                                <button id="btn-presensi-masuk" class="btn btn-primary text-white">
                                    <i class="ri-camera-line text-lg"></i>
                                    Presensi Masuk
                                </button>

                                <button id="btn-presensi-keluar" class="btn btn-secondary text-white">
                                    <i class="ri-camera-line text-lg"></i>
                                    Presensi Keluar
                                </button>
                            </div>
                        </div>

                        <!-- Container untuk Webcam dan Map -->
                        <div class="camera-map-container">
                            <!-- Webcam Container - awalnya tersembunyi -->
                            <div id="webcam-container" style="display: none;">
                                <div id="webcam-capture" class="mx-auto"></div>
                            </div>

                            <!-- Map Container - awalnya tersembunyi -->
                            <div id="map-container" style="display: none;">
                                <div id="map" class="mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Presensi -->
    <div>
        <div class="-mx-3 flex flex-wrap">
            <div class="mb-6 mt-0 w-full max-w-full px-3">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl border-black-125 relative flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border shadow-xl">
                    <div class="rounded-t-4 mb-0 p-4 pb-0">
                        <div class="flex justify-between">
                            <h6 class="mb-2 font-bold dark:text-white">Riwayat Presensi</h6>
                        </div>
                    </div>

                    <div class="mt-3 flex w-full flex-col gap-y-5">
                        {{-- Input Filter --}}
                        <div class="flex flex-wrap">
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-1/2">
                                <div class="mb-4">
                                    <label for="bulan"
                                        class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Bulan</label>
                                    <select name="bulan" id="bulan"
                                        class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease select select-bordered block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white"
                                        required>
                                        <option disabled selected>Pilih Bulan!</option>
                                        @foreach ($bulan as $value => $item)
                                            <option value="{{ $value + 1 }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="md:flex-0 w-full max-w-full shrink-0 px-3 md:w-1/2">
                                <div class="mb-4">
                                    <label for="tahun"
                                        class="mb-2 ml-1 inline-block text-xs font-bold text-slate-700 dark:text-white/80">Tahun</label>
                                    <select name="tahun" id="tahun"
                                        class="focus:shadow-primary-outline dark:bg-slate-850 leading-5.6 ease select select-bordered block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 text-sm font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none dark:text-white"
                                        required>
                                        <option disabled selected>Pilih Tahun!</option>
                                        @php
                                            $tahunMulai = $riwayatPresensi[0]
                                                ? date('Y', strtotime($riwayatPresensi[0]->tanggal_presensi))
                                                : date('Y');
                                        @endphp
                                        @for ($tahun = $tahunMulai; $tahun <= date('Y'); $tahun++)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5 flex w-full max-w-full justify-center px-3">
                            <button type="button" id="searchButton" class="btn btn-warning btn-block">Search</button>
                        </div>

                        {{-- Tabel Riwayat Presensi --}}
                        <div id="searchPresensi" class="w-full overflow-x-auto px-10">
                            <table id="tabelPresensi"
                                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                <thead class="text-sm text-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th></th>
                                        <th>Hari</th>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Status</th>
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
                            <div class="mx-3 mb-5">
                                {{ $riwayatPresensi->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
