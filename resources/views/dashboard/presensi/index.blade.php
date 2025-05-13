@extends('dashboard.layouts.main')
{{-- dashboard\presensi\index  --}}
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        /* CSS responsif yang lebih rapi */
        #webcam-capture,
        #webcam-capture video {
            border-radius: 15px;
            margin: 0 auto;
            object-fit: cover;
        }

        /* Mobile */
        @media (max-width: 425px) {

            #webcam-capture,
            #webcam-capture video {
                width: 300px !important;
                height: 380px !important;
            }

            #map {
                height: 250px !important;
                margin-top: 15px;
            }
        }

        /* Tablet */
        @media (min-width: 426px) and (max-width: 767px) {

            #webcam-capture,
            #webcam-capture video {
                width: 480px !important;
                height: 360px !important;
            }

            #map {
                height: 300px !important;
                margin-top: 20px;
            }
        }

        /* Desktop */
        @media (min-width: 768px) {

            #webcam-capture,
            #webcam-capture video {
                width: 640px !important;
                height: 480px !important;
            }

            #map {
                height: 350px !important;
                margin-top: 25px;
            }
        }

        /* Peta selalu di bawah, tidak bersebelahan */
        .camera-map-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        #map-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto 0;
        }

        #webcam-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .swal2-confirm {
            background-color: #007bff !important;
            /* Warna biru untuk tombol OK */
            color: white !important;
            /* Teks tombol OK menjadi putih */
        }

        .swal2-cancel {
            background-color: #007bff !important;
            /* Warna biru untuk tombol OK */
            color: white !important;
            /* Teks tombol OK menjadi putih */
            border-color: #007bff !important;
            /* Border tombol OK menjadi biru */
        }

        #map {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .presensi-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .presensi-status {
            margin-bottom: 20px;
        }

        /* Transisi lebih halus */
        #webcam-container,
        #map-container {
            transition: all 0.3s ease-in-out;
        }

        /* Style untuk marker kantor */
        .kantor-marker {
            display: flex;
            align-items: center;
            justify-content: center;
            color: blue;
            background-color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        /* Tambahkan informasi lokasi aktif */
        .lokasi-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .lokasi-info h4 {
            margin-top: 0;
            margin-bottom: 5px;
            font-weight: bold;
            color: #007bff;
        }

        .lokasi-list {
            max-height: 150px;
            overflow-y: auto;
            padding: 5px;
        }

        .lokasi-item {
            padding: 5px 0;
            border-bottom: 1px dashed #e0e0e0;
        }

        .lokasi-item:last-child {
            border-bottom: none;
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
        let userLatitude = null;
        let userLongitude = null;
        let locationInterval = null;

        // Mendapatkan lokasi dan simpan di input hidden
        let lokasi = document.getElementById('lokasi');

        // Fungsi untuk mendapatkan lokasi - dengan Promise untuk lebih baik menangani async
        function getLocationPromise() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error("Geolocation tidak didukung oleh browser ini."));
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        userLatitude = position.coords.latitude;
                        userLongitude = position.coords.longitude;
                        lokasi.value = userLatitude + ", " + userLongitude;

                        // Jika map sudah diinisialisasi, perbarui posisi marker
                        if (mapInitialized && map) {
                            updateMarkerPosition(userLatitude, userLongitude);
                        }
                        resolve({
                            lat: userLatitude,
                            lng: userLongitude
                        });
                    },
                    (error) => {
                        console.error("Error getting location:", error);
                        reject(error);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }

        // Fungsi untuk mendapatkan lokasi (backward compatibility)
        function getLocation() {
            getLocationPromise().catch(error => {
                Swal.fire({
                    title: "Error",
                    text: "Tidak dapat mengakses lokasi Anda. Pastikan GPS diaktifkan. Error: " + error
                        .message,
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
        }

        // Fungsi untuk memulai pembaruan lokasi otomatis
        function startLocationUpdates() {
            // Hentikan interval sebelumnya jika ada
            if (locationInterval) {
                clearInterval(locationInterval);
            }

            // Perbarui lokasi setiap 5 detik
            locationInterval = setInterval(() => {
                getLocationPromise()
                    .then(location => {
                        console.log("Lokasi diperbarui:", location);
                    })
                    .catch(error => {
                        console.error("Gagal memperbarui lokasi:", error);
                    });
            }, 5000);
        }

        // Fungsi untuk menghentikan pembaruan lokasi
        function stopLocationUpdates() {
            if (locationInterval) {
                clearInterval(locationInterval);
                locationInterval = null;
            }
        }

        // Panggil getLocation saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi data awal
            getLocation();

            // Preload audio untuk mengurangi delay
            document.getElementById('notifikasi_presensi_masuk').load();
            document.getElementById('notifikasi_presensi_keluar').load();
            document.getElementById('notifikasi_presensi_gagal_radius').load();
        });

        // Fungsi untuk membuat peta dengan multiple lokasi kantor aktif
        function createMap(lat, lng) {
            map = L.map('map').setView([lat, lng], 17);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Tambahkan marker posisi user
            marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup("<b>Anda berada di sini</b>").openPopup();

            // Konversi koleksi lokasi kantor dari PHP ke array JavaScript
            const lokasiKantorArray = {!! json_encode($lokasiKantor) !!};

            // Array untuk menyimpan semua bounds (untuk fitBounds nanti)
            const bounds = [];
            bounds.push([lat, lng]); // Tambahkan posisi user ke bounds

            // Tambahkan circle dan marker untuk setiap lokasi kantor aktif
            lokasiKantorArray.forEach(lokasiKantor => {
                const kantorLat = parseFloat(lokasiKantor.latitude);
                const kantorLng = parseFloat(lokasiKantor.longitude);
                const radiusKantor = parseFloat(lokasiKantor.radius);

                // Tambahkan ke bounds untuk fitBounds
                bounds.push([kantorLat, kantorLng]);

                // Tambahkan circle untuk menunjukkan radius kantor
                const circle = L.circle([kantorLat, kantorLng], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.3,
                    radius: radiusKantor
                }).addTo(map);

                // Tambahkan marker untuk lokasi kantor
                const markerKantor = L.marker([kantorLat, kantorLng], {
                    icon: L.divIcon({
                        className: 'kantor-marker',
                        html: '<i class="ri-building-2-line" style="font-size: 20px; color: blue;"></i>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);

                // Tambahkan popup informasi lokasi kantor
                markerKantor.bindPopup("<b>Kantor " + lokasiKantor.kota + "</b><br>Radius: " + radiusKantor +
                    " meter");

                // Hitung jarak user ke lokasi kantor ini untuk debugging
                const distance = map.distance([lat, lng], [kantorLat, kantorLng]);
                console.log("Jarak ke kantor " + lokasiKantor.kota + ": " + distance.toFixed(2) +
                    " meter (Radius: " + radiusKantor + " meter)");
            });

            // Pastikan map dirender dengan benar
            setTimeout(function() {
                map.invalidateSize();

                // Jika ada lokasi kantor, sesuaikan tampilan peta untuk menampilkan semua lokasi
                if (bounds.length > 1) {
                    // Buat L.latLngBounds dari array koordinat
                    const mapBounds = L.latLngBounds(bounds);
                    // Sesuaikan zoom agar semua marker terlihat
                    map.fitBounds(mapBounds, {
                        padding: [50, 50] // Padding untuk estetika
                    });
                }
            }, 300);

            mapInitialized = true;
            console.log("Map berhasil dimuat dengan " + lokasiKantorArray.length + " lokasi kantor aktif");
        }

        // Fungsi untuk inisialisasi map dengan optimasi dan dukungan multiple lokasi kantor
        function initMap() {
            if (mapInitialized) return Promise.resolve();

            return new Promise((resolve, reject) => {
                document.getElementById('map-container').style.display = 'block';

                // Gunakan timeout lebih pendek untuk mengurangi delay
                setTimeout(function() {
                    try {
                        // Jika lokasi belum didapatkan, coba dapatkan lagi
                        if (!userLatitude || !userLongitude) {
                            getLocationPromise()
                                .then(location => {
                                    createMap(location.lat, location.lng);
                                    resolve();
                                })
                                .catch(error => {
                                    // Gunakan lokasi default jika masih belum ada
                                    createMap(-7.7956, 110.3695);
                                    resolve();
                                });
                        } else {
                            createMap(userLatitude, userLongitude);
                            resolve();
                        }
                    } catch (error) {
                        console.error("Error initializing map:", error);
                        reject(error);
                        Swal.fire({
                            title: "Error",
                            text: "Terjadi kesalahan saat memuat peta. Silakan refresh halaman.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                }, 300); // timeout yang lebih pendek
            });
        }

        // Fungsi untuk memperbarui posisi marker
        function updateMarkerPosition(lat, lng) {
            if (marker && map) {
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
            }
        }

        // Referensi ke elemen audio
        let notifikasi_presensi_masuk = document.getElementById('notifikasi_presensi_masuk');
        let notifikasi_presensi_keluar = document.getElementById('notifikasi_presensi_keluar');
        let notifikasi_presensi_gagal_radius = document.getElementById('notifikasi_presensi_gagal_radius');

        // Inisialisasi webcam dengan optimasi
        function initWebcam() {
            if (webcamInitialized) return Promise.resolve();

            return new Promise((resolve, reject) => {
                document.getElementById('webcam-container').style.display = 'block';

                // Gunakan timeout lebih pendek
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
                        resolve();
                    } catch (error) {
                        console.error("Error initializing webcam:", error);
                        reject(error);
                        Swal.fire({
                            title: "Error",
                            text: "Terjadi kesalahan saat memuat webcam. Silakan refresh halaman.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                }, 300); // timeout lebih pendek
            });
        }

        // Fungsi gabungan untuk inisialisasi webcam dan peta secara paralel
        async function initCameraAndMap() {
            try {
                // Inisialisasi secara paralel untuk mengurangi delay
                await Promise.all([
                    initWebcam(),
                    initMap()
                ]);

                // Mulai pembaruan lokasi otomatis
                startLocationUpdates();

                return true;
            } catch (error) {
                console.error("Error initializing camera and map:", error);
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mempersiapkan kamera dan peta. Silakan refresh halaman.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return false;
            }
        }

        // Tombol presensi masuk
        $("#btn-presensi-masuk").click(async function() {
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

            if (presensiKaryawanExists) {
                // Sudah presensi masuk
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda sudah melakukan presensi masuk hari ini!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Tampilkan loading saat inisialisasi
            Swal.fire({
                title: "Mempersiapkan...",
                text: "Menyiapkan kamera dan peta",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Inisialisasi webcam dan map secara paralel
            const isInitialized = await initCameraAndMap();
            Swal.close();

            if (!isInitialized) return;

            // Atur jenis presensi
            $("input[name='presensi']").val("masuk");
            captureAndSendPresensi();
        });

        // Tombol presensi keluar
        $("#btn-presensi-keluar").click(async function() {
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

            if (!presensiKaryawanExists) {
                // Belum presensi masuk sama sekali
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda belum melakukan presensi masuk hari ini!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            if (presensiKaryawanKeluar) {
                // Sudah presensi keluar
                Swal.fire({
                    title: "Peringatan",
                    text: "Anda sudah melakukan presensi keluar hari ini!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Tampilkan loading saat inisialisasi
            Swal.fire({
                title: "Mempersiapkan...",
                text: "Menyiapkan kamera dan peta",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Inisialisasi webcam dan map secara paralel
            const isInitialized = await initCameraAndMap();
            Swal.close();

            if (!isInitialized) return;

            // Atur jenis presensi
            $("input[name='presensi']").val("keluar");
            captureAndSendPresensi();
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
            getLocationPromise()
                .then(() => {
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
                                    stopLocationUpdates(); // Hentikan pembaruan lokasi

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
                                        setTimeout(
                                            "location.href='{{ route('karyawan.dashboard') }}'",
                                            2000);

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
                                    stopLocationUpdates(); // Hentikan pembaruan lokasi
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
                        stopLocationUpdates(); // Hentikan pembaruan lokasi
                        console.error("Error during webcam capture:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Terjadi kesalahan saat mengambil gambar. Pastikan webcam Anda berfungsi dengan baik.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error",
                        text: "Tidak dapat mengakses lokasi Anda. Pastikan GPS diaktifkan: " + error.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
        }
    </script>
    <script>
        $(document).ready(function() {
            $("#searchButton").click(function(e) {
                e.preventDefault();
                // Tambahkan validasi sederhana
                const bulan = $("#bulan").val();
                const tahun = $("#tahun").val();

                if (!bulan || bulan === "Pilih Bulan!") {
                    Swal.fire({
                        title: "Peringatan",
                        text: "Mohon pilih bulan terlebih dahulu",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }

                if (!tahun || tahun === "Pilih Tahun!") {
                    Swal.fire({
                        title: "Peringatan",
                        text: "Mohon pilih tahun terlebih dahulu",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }

                // Tambahkan loading indicator
                $("#searchPresensi").html(
                    '<div class="flex justify-center my-4"><span class="loading loading-spinner loading-lg"></span></div>'
                );

                $.ajax({
                    type: "POST",
                    url: "{{ route('karyawan.history.search') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: bulan,
                        tahun: tahun
                    },
                    cache: false,
                    success: function(res) {
                        $("#searchPresensi").html(res);
                    },
                    error: function(xhr, status, error) {
                        $("#searchPresensi").html(
                            '<div class="alert alert-error">Terjadi kesalahan saat memuat data</div>'
                        );
                        console.error("AJAX Error:", error);
                    }
                });
            });
        });
    </script>

    <!-- Script untuk Modal Tukar Jadwal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elemen-elemen yang diperlukan
            const modal = document.getElementById('tukarJadwalModal');
            const openButton = document.getElementById('openTukarJadwalBtn');
            const closeButton = document.getElementById('closeTukarJadwalBtn');

            // Tambahkan overlay background
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'fixed inset-0 bg-black opacity-50';
            modalOverlay.style.zIndex = '40';
            modalOverlay.style.display = 'none';
            document.body.appendChild(modalOverlay);

            // Buka Modal - tanpa loading
            openButton.addEventListener('click', function() {
                modal.style.display = 'block';
                modalOverlay.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Mencegah scrolling
            });

            // Tutup Modal
            function closeModal() {
                modal.style.display = 'none';
                modalOverlay.style.display = 'none';
                document.body.style.overflow = '';
            }

            closeButton.addEventListener('click', closeModal);

            // Tutup Modal jika mengklik di luar modal (pada overlay)
            modalOverlay.addEventListener('click', closeModal);

            // Form submission validation
            const tukarJadwalForm = document.querySelector('#tukarJadwalModal form');
            if (tukarJadwalForm) {
                tukarJadwalForm.addEventListener('submit', function(e) {
                    const nikPenerima = this.querySelector('select[name="nik_penerima"]').value;
                    const tanggalPengajuan = this.querySelector('input[name="tanggal_pengajuan"]').value;
                    const alasan = this.querySelector('textarea[name="alasan"]').value;

                    if (!nikPenerima) {
                        e.preventDefault();
                        Swal.fire({
                            title: "Peringatan",
                            text: "Silakan pilih karyawan tujuan terlebih dahulu",
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        return false;
                    }

                    if (!tanggalPengajuan) {
                        e.preventDefault();
                        Swal.fire({
                            title: "Peringatan",
                            text: "Silakan pilih tanggal pertukaran terlebih dahulu",
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        return false;
                    }

                    if (!alasan.trim()) {
                        e.preventDefault();
                        Swal.fire({
                            title: "Peringatan",
                            text: "Silakan berikan alasan pertukaran jadwal",
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        return false;
                    }

                    // Konfirmasi sebelum submit
                    e.preventDefault();
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin mengajukan pertukaran jadwal ini? Pastikan karyawan tujuan sudah mengetahui dan menyetujui pertukaran ini.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Ajukan",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection


@section('container')
    <div class="mb-6">
        {{-- Audio elements moved here but preloaded via JS --}}
        <audio id="notifikasi_presensi_masuk" preload="auto">
            <source src="{{ asset('audio/notifikasi_presensi_masuk.mp3') }}" type="audio/mpeg">
        </audio>
        <audio id="notifikasi_presensi_keluar" preload="auto">
            <source src="{{ asset('audio/notifikasi_presensi_keluar.mp3') }}" type="audio/mpeg">
        </audio>
        <audio id="notifikasi_presensi_gagal_radius" preload="auto">
            <source src="{{ asset('audio/notifikasi_presensi_gagal_radius.mp3') }}" type="audio/mpeg">
        </audio>


        <!-- Notifikasi sukses/error -->
        @if (session('success'))
            <div class="alert alert-success shadow-lg mb-4">
                <div class="flex items-center">
                    <i class="ri-checkbox-circle-line mr-2 text-2xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error shadow-lg mb-4">
                <div class="flex items-center">
                    <i class="ri-error-warning-line mr-2 text-2xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Informasi Lokasi Kantor Aktif -->
        <div class="lokasi-info mb-4">
            <h4 class="text-lg text-dark font-bold mb-2">Lokasi Kantor Aktif ({{ $lokasiKantor->count() }})</h4>
            <div class="lokasi-list">
                @forelse($lokasiKantor as $lokasi)
                    <div class="lokasi-item">
                        <i class="ri-map-pin-line text-blue-500 mr-1"></i>
                        <strong class="text-black">{{ $lokasi->kota }}</strong>
                        <p class="text-black">(Radius: {{ $lokasi->radius }}m)</p>
                    </div>
                @empty
                    <div class="text-error">Tidak ada lokasi kantor aktif yang tersedia</div>
                @endforelse
            </div>
        </div>

        <!-- Tombol Ajukan Tukar Jadwal -->
        <div class="mb-6">
            <button id="openTukarJadwalBtn" class="btn btn-warning text-white">
                <i class="ri-exchange-line text-lg mr-1"></i> Ajukan Tukar Jadwal
            </button>
        </div>

        <!-- Modal Tukar Jadwal (tersembunyi secara default) -->
        <div id="tukarJadwalModal" style="display: none;" class="fixed inset-0 z-50 overflow-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg w-full max-w-lg relative mx-auto">
                    <!-- Header Modal dengan close button -->
                    <div class="flex items-center justify-between p-4 border-b">
                        <h2 class="text-xl font-semibold">Ajukan Tukar Jadwal</h2>
                        <button id="closeTukarJadwalBtn" class="text-gray-500 hover:text-gray-800">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>

                    <!-- Form Tukar Jadwal - Direct Submit tanpa JavaScript processing -->
                    <div class="p-4 text-black dark:text-white">
                        <form action="{{ route('presensi.tukar-jadwal') }}" method="POST">
                            @csrf

                            <!-- Karyawan Tujuan -->
                            <div class="mb-4 text-black dark:text-white">
                                <label class="block mb-2 font-medium text-black dark:text-black">Karyawan Tujuan:</label>
                                <select name="nik_penerima"
                                    class="w-full p-2 border border-gray-300 rounded-md bg-white text-black dark:text-black"
                                    required>
                                    <option value="" selected disabled>Pilih karyawan untuk tukar jadwal</option>
                                    @foreach ($karyawan as $karyawanItem)
                                        @if ($karyawanItem->nik != auth()->guard('karyawan')->user()->nik)
                                            <option value="{{ $karyawanItem->nik }}">{{ $karyawanItem->nama_lengkap }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal Tukar Jadwal -->
                            <div class="mb-4">
                                <label class="block mb-2 font-medium text-black dark:text-black">Tanggal Tukar
                                    Jadwal:</label>
                                <div class="relative">
                                    <input type="date" name="tanggal_pengajuan"
                                        class="w-full p-2 border text-black dark:text-black border-gray-300 rounded-md"
                                        min="{{ date('Y-m-d') }}" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="ri-calendar-line text-gray-500"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Alasan -->
                            <div class="mb-4">
                                <label class="block mb-2 text-black dark:text-black font-medium">Alasan:</label>
                                <textarea name="alasan" rows="4" class="w-full p-2 border border-gray-300 rounded-md"
                                    placeholder="Berikan alasan pertukaran jadwal" required></textarea>
                            </div>

                            <!-- Informasi Penting -->
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <div class="flex items-center mb-2 text-blue-700">
                                    <i class="ri-information-line mr-2 text-xl"></i>
                                    <span class="font-medium">Informasi Penting</span>
                                </div>
                                <ul class="text-blue-700 list-disc pl-6 space-y-1">
                                    <li>Pertukaran jadwal akan langsung diproses tanpa persetujuan admin</li>
                                    <li>Pastikan karyawan yang dituju sudah mengetahui dan menyetujui pertukaran ini</li>
                                    <li>Jadwal yang berhasil ditukar tidak dapat dibatalkan</li>
                                </ul>
                            </div>

                            <!-- Submit Button - Direct Submit -->
                            <button type="submit"
                                class="w-full bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 transition-colors">
                                Ajukan & Proses Tukar Jadwal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="-mx-3 flex flex-wrap">
            <div class="mb-6 w-full max-w-full px-3 sm:flex-none">
                <div
                    class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl">
                    <div class="flex-auto p-4">
                        <input type="text" name="lokasi" id="lokasi" class="input input-primary" hidden>
                        <input type="text" name="presensi" id="presensi" value="" hidden>

                        <!-- Informasi Jadwal Shift Hari Ini -->
                        <div class="mb-4 presensi-status">
                            @if (isset($jadwalHariIni) && $jadwalHariIni)
                                @if ($jadwalHariIni->is_libur)
                                    <div class="alert alert-warning shadow-lg">
                                        <div class="flex items-center">
                                            <i class="ri-calendar-event-line mr-2 text-2xl"></i>
                                            <div>
                                                <h3 class="font-bold">Jadwal Libur</h3>
                                                <p>Anda dijadwalkan libur pada hari ini</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($jadwalHariIni->shift)
                                    <div class="alert alert-info shadow-lg">
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
                            @else
                                <div class="alert alert-info shadow-lg">
                                    <div class="flex items-center">
                                        <i class="ri-information-line mr-2 text-2xl"></i>
                                        <div>
                                            <h3 class="font-bold">Informasi Shift</h3>
                                            <p>Tidak ada jadwal shift yang ditemukan untuk hari ini. Menggunakan jadwal
                                                default (08:00 - 16:00)</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Tombol Presensi -->
                        <div class="presensi-buttons">
                            <button id="btn-presensi-masuk" class="btn btn-primary text-white">
                                <i class="ri-login-box-line text-lg mr-1"></i>
                                Presensi Masuk
                            </button>

                            <button id="btn-presensi-keluar" class="btn btn-secondary text-white">
                                <i class="ri-logout-box-line text-lg mr-1"></i>
                                Presensi Keluar
                            </button>
                        </div>

                        <!-- Container untuk Webcam dan Map - Sekarang selalu vertikal -->
                        <div class="camera-map-container">
                            <!-- Webcam Container - awalnya tersembunyi -->
                            <div id="webcam-container" style="display: none;">
                                <div id="webcam-capture" class="shadow-lg"></div>
                            </div>

                            <!-- Map Container - awalnya tersembunyi, selalu di bawah -->
                            <div id="map-container" style="display: none;">
                                <div id="map" class="shadow-lg"></div>
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
                        <div id="searchPresensi" class="w-full overflow-x-auto px-3 md:px-6">
                            <table id="tabelPresensi"
                                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                                <thead
                                    class="bg-gray-50 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th class="px-4 py-3 text-center">#</th>
                                        <th class="px-4 py-3">Hari</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Jam Masuk</th>
                                        <th class="px-4 py-3">Jam Keluar</th>
                                        <th class="px-4 py-3">Status</th>
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
                                                $item->jam_masuk < $jamMasukStandar
                                                    ? 'text-success font-medium'
                                                    : 'text-error font-medium';
                                            $statusKeluar =
                                                $item->jam_keluar > $jamKeluarStandar
                                                    ? 'text-success font-medium'
                                                    : 'text-error font-medium';
                                        @endphp
                                        <tr
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                                            <td class="px-4 py-3 text-center font-bold">
                                                {{ $riwayatPresensi->firstItem() + $value }}</td>
                                            <td class="px-4 py-3 text-slate-500 dark:text-slate-300">
                                                {{ date('l', strtotime($item->tanggal_presensi)) }}</td>
                                            <td class="px-4 py-3 text-slate-500 dark:text-slate-300">
                                                {{ date('d-m-Y', strtotime($item->tanggal_presensi)) }}</td>
                                            <td class="px-4 py-3 {{ $statusMasuk }}">
                                                {{ date('H:i:s', strtotime($item->jam_masuk)) }}</td>
                                            @if ($item != null && $item->jam_keluar != null)
                                                <td class="px-4 py-3 {{ $statusKeluar }}">
                                                    {{ date('H:i:s', strtotime($item->jam_keluar)) }}</td>
                                            @else
                                                <td class="px-4 py-3 text-yellow-500 font-medium">Belum Presensi</td>
                                            @endif
                                            <td class="px-4 py-3">
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
