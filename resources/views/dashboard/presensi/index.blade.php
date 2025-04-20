@extends('dashboard.layouts.main')

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
        }

        @media (min-width: 640px) {

            #webcam-capture,
            #webcam-capture video {
                width: 480px !important;
                height: 640px !important;
                margin: auto;
                border-radius: 33px;
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

        // Variabel untuk status webcam
        let webcamInitialized = false;
        let image = null;

        // Inisialisasi webcam setelah semua elemen DOM dimuat
        $(document).ready(function() {
            // Tunggu sedikit untuk memastikan DOM dan library sepenuhnya dimuat
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
                    // Menampilkan pesan kesalahan ke pengguna
                    Swal.fire({
                        title: "Error",
                        text: "Terjadi kesalahan saat memuat webcam. Silakan refresh halaman.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            }, 1000);
        });

        let lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallBack);
        }

        function successCallback(position) {
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;
            lokasi.value = latitude + ", " + longitude;

            let map = L.map('map').setView([latitude, longitude], 17);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            let marker = L.marker([latitude, longitude]).addTo(map);
            marker.bindPopup("<b>Anda berada di sini</b>").openPopup();

            let circle = L.circle([{{ $lokasiKantor->latitude }}, {{ $lokasiKantor->longitude }}], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: {{ $lokasiKantor->radius }}
            }).addTo(map);
        }

        function errorCallBack(position) {
            console.error("Error getting location:", position);
            Swal.fire({
                title: "Error",
                text: "Tidak dapat mengakses lokasi Anda. Pastikan GPS diaktifkan.",
                icon: "error",
                confirmButtonText: "OK"
            });
        }

        let notifikasi_presensi_masuk = document.getElementById('notifikasi_presensi_masuk');
        let notifikasi_presensi_keluar = document.getElementById('notifikasi_presensi_keluar');
        let notifikasi_presensi_gagal_radius = document.getElementById('notifikasi_presensi_gagal_radius');

        // Tombol presensi masuk
        $("#btn-presensi-masuk").click(function() {
            if (!webcamInitialized) {
                Swal.fire({
                    title: "Peringatan",
                    text: "Webcam belum siap. Mohon tunggu beberapa saat.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

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
                captureAndSendPresensi();
            }
        });

        // Tombol presensi keluar
        $("#btn-presensi-keluar").click(function() {
            if (!webcamInitialized) {
                Swal.fire({
                    title: "Peringatan",
                    text: "Webcam belum siap. Mohon tunggu beberapa saat.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

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
                captureAndSendPresensi();
            }
        });

        // Fungsi untuk mengambil foto dan mengirim data presensi
        function captureAndSendPresensi() {
            try {
                Webcam.snap(function(uri) {
                    image = uri;

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
                                setTimeout("location.href='{{ route('karyawan.dashboard') }}'", 5000);

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
@endsection

@section('container')
    <div>
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
                        <div id="webcam-capture" class="mx-auto"></div>
                        <div class="flex justify-center mt-4">
                            <input type="text" name="presensi" id="presensi" value="" hidden>
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
                    </div>
                </div>
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative mt-3 flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <div id="map" class="mx-auto h-80 w-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
