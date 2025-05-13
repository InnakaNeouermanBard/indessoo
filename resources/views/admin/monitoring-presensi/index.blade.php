<x-app-layout>
    {{-- index admin presensi  --}}
    <x-slot name="header">
        <div class="flex items-center justify-start"> <!-- Ganti justify-between dengan justify-start -->
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Absensi') }}
            </h2>
            <hr>
        </div>
        <h3> Karyawan Outsorcing <h3>
    </x-slot>


    <div class="container mx-auto px-5 pt-5">
        <div>
            <!-- admin/tukar-jadwal.blade.php -->

            @php
                // Hitung jumlah tukar jadwal hari ini
                $countTukarJadwalToday = \App\Models\TukarJadwal::whereDate(
                    'created_at',
                    \Carbon\Carbon::today(),
                )->count();

                // Definisikan $recentExchanges jika belum tersedia
                if (!isset($recentExchanges)) {
                    $recentExchanges = \App\Models\TukarJadwal::with(['pengaju', 'penerima'])
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                }
            @endphp
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // PENTING: Pastikan elemen ada sebelum menambahkan event listener
                const notificationBtn = document.getElementById('notificationBtn');
                const notificationDropdown = document.getElementById('notificationDropdown');

                // Jika elemen tidak ada, keluar dari fungsi
                if (!notificationBtn || !notificationDropdown) {
                    console.warn('Elemen notifikasi tidak ditemukan!');
                    return;
                }

                // Fungsi untuk toggle visibilitas dropdown
                notificationBtn.addEventListener('click', function(event) {
                    // Pastikan dropdown tidak hilang saat klik pada dropdown itu sendiri
                    event.stopPropagation();
                    notificationDropdown.classList.toggle('hidden'); // Menampilkan/Menyembunyikan dropdown
                    console.log('Toggle notifikasi dropdown');
                });

                // Menutup dropdown jika diklik di luar
                window.addEventListener('click', function(event) {
                    // Jika klik di luar tombol atau dropdown, sembunyikan dropdown
                    if (notificationBtn && !notificationBtn.contains(event.target) &&
                        !notificationDropdown.contains(event.target)) {
                        notificationDropdown.classList.add('hidden'); // Menyembunyikan dropdown
                    }
                });

                // Event listener untuk setiap tombol detail di dalam dropdown
                const setupDetailButtons = function() {
                    const detailButtons = document.querySelectorAll('.btn-detail');
                    console.log('Setup tombol detail:', detailButtons.length);

                    detailButtons.forEach(button => {
                        // Hapus event listener lama jika ada untuk menghindari duplikasi
                        button.removeEventListener('click', handleDetailClick);
                        // Tambahkan event listener baru
                        button.addEventListener('click', handleDetailClick);
                    });
                };

                // Handler untuk tombol detail
                const handleDetailClick = function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Mencegah dropdown menutup

                    const id = this.getAttribute('data-id');
                    console.log('Button detail diklik:', id);

                    // Cari parent item notifikasi (li, div, atau elemen lain)
                    const notificationItem = this.closest('li') ||
                        this.closest('.notification-item') ||
                        this.closest('.dropdown-item') ||
                        this.parentElement;

                    if (notificationItem) {
                        console.log('Menghapus item notifikasi');

                        // Tampilkan detail notifikasi terlebih dahulu
                        showDetailModal(id);

                        // Animasi fade out
                        notificationItem.style.transition = 'opacity 0.3s';
                        notificationItem.style.opacity = '0';

                        // Hapus elemen setelah animasi
                        setTimeout(() => {
                            notificationItem.remove();
                            updateNotificationCounter();
                            markNotificationAsRead(id);
                        }, 300);
                    } else {
                        console.warn('Parent notifikasi tidak ditemukan');
                        showDetailModal(id);
                    }
                };

                // Setup awal
                setupDetailButtons();

                // Tambahkan MutationObserver untuk menangani jika ada notifikasi baru
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            setupDetailButtons();
                        }
                    });
                });

                // Amati perubahan di dalam dropdown
                if (notificationDropdown) {
                    observer.observe(notificationDropdown, {
                        childList: true,
                        subtree: true
                    });
                }
            });

            // Fungsi untuk memperbarui counter notifikasi
            function updateNotificationCounter() {
                const notifCounter = document.getElementById('notification-counter') ||
                    document.querySelector('.notification-counter');

                if (notifCounter) {
                    let currentCount = parseInt(notifCounter.textContent);
                    if (!isNaN(currentCount) && currentCount > 0) {
                        currentCount--;
                        notifCounter.textContent = currentCount;

                        if (currentCount === 0) {
                            notifCounter.classList.add('hidden');
                        }
                    }
                }
            }

            // Fungsi untuk menandai notifikasi sebagai dibaca
            function markNotificationAsRead(id) {
                if (!id) return;

                $.ajax({
                    url: '/admin/notifications/mark-as-read/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log('Notifikasi berhasil ditandai sebagai dibaca');
                    },
                    error: function(xhr, status, error) {
                        console.error('Gagal menandai notifikasi:', error);
                    }
                });
            }
        </script>

        <form action="{{ route('admin.monitoring-presensi') }}" method="get" enctype="multipart/form-data" class="my-3">
            <div class="flex gap-2 items-center">
                <!-- Input untuk Tanggal Presensi -->
                <input type="date" name="tanggal_presensi" class="input input-bordered w-32 md:w-40"
                    value="{{ request()->tanggal_presensi ?? Carbon\Carbon::now()->format('Y-m-d') }}" />

                <!-- Input untuk NIK -->
                <input type="text" name="nik" placeholder="NIK Karyawan"
                    class="input input-bordered w-32 md:w-40" value="{{ request()->nik }}" />

                <!-- Input untuk Nama Karyawan -->
                <input type="text" name="nama_karyawan" placeholder="Nama Karyawan"
                    class="input input-bordered w-32 md:w-40" value="{{ request()->nama_karyawan }}" />

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-success w-10 h-10 flex items-center justify-center p-0">
                    <i class="ri-search-2-line text-white text-lg"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="w-full overflow-x-auto rounded-md bg-slate-200 px-10">
        <table id="tabelPresensi"
            class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
            <thead class="text-sm text-black">
                <tr>
                    <th></th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monitoring as $value => $item)
                    <tr class="hover">
                        <td class="font-bold">{{ $monitoring->firstItem() + $value }}</td>
                        <td>{{ $item->nik }}</td>
                        <td>{{ $item->nama_karyawan }}</td>
                        <td>{{ $item->tanggal_presensi }}</td>
                        <td>{{ $item->jam_masuk }}</td>
                        <td>
                            @if ($item->jam_keluar)
                                {{ $item->jam_keluar }}
                            @else
                                <div class="w-fit rounded-md bg-error p-1 text-white">Belum Presensi</div>
                            @endif
                        </td>
                        <td>
                            @php
                                // Ambil shift berdasarkan nik
                                $shift = DB::table('shifts')
                                    ->join('shift_schedules', 'shift_schedules.shift_id', '=', 'shifts.id')
                                    ->where('shift_schedules.karyawan_nik', $item->nik)
                                    ->first();

                                if ($shift) {
                                    $waktuMulaiShift = Carbon\Carbon::make($shift->waktu_mulai);
                                    $masuk = Carbon\Carbon::make($item->jam_masuk);
                                } else {
                                    $waktuMulaiShift = null;
                                    $masuk = null;
                                }
                            @endphp


                            @if ($masuk > $waktuMulaiShift)
                                @php
                                    $diff = $masuk->diff($waktuMulaiShift); // Hitung selisih waktu
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
                        <td>
                            <label for="detail_modal_{{ $item->nik }}" class="btn btn-primary btn-sm">
                                Detail
                            </label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mx-3 mb-5">
            {{ $monitoring->links() }}
        </div>
    </div>
    </div>

    {{-- Modal Detail untuk Setiap Karyawan --}}
    @foreach ($monitoring as $item)
        <input type="checkbox" id="detail_modal_{{ $item->nik }}" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box">
                <div class="mb-3 flex justify-between">
                    <h3 class="text-lg font-bold">Detail Presensi</h3>
                    <label for="detail_modal_{{ $item->nik }}" class="cursor-pointer">
                        <i class="ri-close-large-fill"></i>
                    </label>
                </div>
                <div class="mb-4">
                    <h4 class="font-semibold">Informasi Karyawan</h4>
                    <p><span class="font-medium">NIK:</span> {{ $item->nik }}</p>
                    <p><span class="font-medium">Nama:</span> {{ $item->nama_karyawan }}</p>
                    <p><span class="font-medium">Tanggal:</span> {{ $item->tanggal_presensi }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-md p-3">
                        <h5 class="font-semibold mb-2">Foto Masuk</h5>
                        <div class="avatar">
                            <div class="w-full rounded">
                                <img src="{{ asset("storage/unggah/presensi/$item->foto_masuk") }}"
                                    alt="{{ $item->foto_masuk }}" />
                            </div>
                        </div>
                        <p class="mt-2"><span class="font-medium">Jam Masuk:</span> {{ $item->jam_masuk }}</p>
                        <button class="btn btn-sm btn-info mt-2"
                            onclick="return viewLokasi('lokasi_masuk', '{{ $item->nik }}')">
                            Lihat Lokasi
                        </button>
                    </div>
                    <div class="border rounded-md p-3">
                        <h5 class="font-semibold mb-2">Foto Keluar</h5>
                        <div class="avatar">
                            <div class="w-full rounded">
                                @if ($item->foto_keluar)
                                    <img src="{{ asset("storage/unggah/presensi/$item->foto_keluar") }}"
                                        alt="{{ $item->foto_keluar }}" />
                                @else
                                    <div class="flex items-center justify-center h-32 bg-gray-200 rounded-md">
                                        <p class="text-gray-500">Belum ada foto keluar</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p class="mt-2"><span class="font-medium">Jam Keluar:</span>
                            @if ($item->jam_keluar)
                                {{ $item->jam_keluar }}
                            @else
                                <span class="text-error">Belum Presensi</span>
                            @endif
                        </p>
                        @if ($item->jam_keluar)
                            <button class="btn btn-sm btn-info mt-2"
                                onclick="return viewLokasi('lokasi_keluar', '{{ $item->nik }}')">
                                Lihat Lokasi
                            </button>
                        @endif
                    </div>
                </div>
                <div class="modal-action">
                    <label for="detail_modal_{{ $item->nik }}" class="btn btn-outline">Kembali</label>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Awal Modal View Lokasi --}}
    <input type="checkbox" id="view_modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <div class="mb-3 flex justify-between">
                <h3 class="judul-lokasi text-lg font-bold"></h3>
                <label for="view_modal" class="cursor-pointer">
                    <i class="ri-close-large-fill"></i>
                </label>
            </div>
            <div>
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text font-semibold">
                            <span class="label-text font-semibold">Koordinat</span>
                            <span class="label-text-alt" id="loading_edit1"></span>
                        </span>
                    </div>
                    <input type="text" name="lokasi" placeholder="Lokasi"
                        class="input input-bordered w-full text-blue-700" readonly />
                    <div id="lokasi-map" class="mx-auto mt-3 h-80 w-full rounded-md"></div>
                </label>
            </div>
        </div>
    </div>
    {{-- Akhir Modal View Lokasi --}}

    <script>
        @if (session()->has('success'))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has('error'))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        function maps(latitude, longitude) {
            let map = L.map('lokasi-map').setView([latitude, longitude], 17);
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

        function viewLokasi(tipe, nik) {
            // Buka modal lokasi
            document.getElementById('view_modal').checked = true;

            // Loading effect start
            let loading = `<span class="loading loading-dots loading-md text-purple-600"></span>`;
            $("#loading_edit1").html(loading);

            $.ajax({
                type: "post",
                url: "{{ route('admin.monitoring-presensi.lokasi') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "tipe": tipe,
                    "nik": nik,
                },
                success: function(data) {
                    // console.log(data);
                    let items = [];
                    $.each(data, function(key, val) {
                        items.push(val);
                    });

                    $(".judul-lokasi").html(tipe);
                    $("input[name='lokasi']").val(items[0]);

                    // Loading effect end
                    loading = "";
                    $("#loading_edit1").html(loading);

                    let lokasi = items[0].split(",");
                    maps(lokasi[0], lokasi[1]);
                },
                error: function(xhr, status, error) {
                    // Tambahkan error handling
                    $("#loading_edit1").html("");
                    Swal.fire({
                        title: "Error",
                        text: "Gagal memuat data lokasi. Silakan coba lagi.",
                        icon: "error",
                        confirmButtonColor: '#6419E6',
                    });
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menangani klik pada detail button notifikasi
            // Gunakan delegasi event untuk mengatasi button yang baru ditambahkan
            document.addEventListener('click', function(e) {
                // Cek apakah yang diklik adalah btn-detail atau elemen di dalamnya
                const detailButton = e.target.closest('.btn-detail');
                if (detailButton) {
                    e.preventDefault();

                    // Ambil ID dari atribut data
                    const id = detailButton.getAttribute('data-id');
                    console.log('Button detail diklik dengan ID:', id);

                    // Show the details of the notification
                    showDetailModal(id);

                    // Cari elemen notifikasi parent - coba beberapa selector yang umum
                    // Gunakan closest dengan selector yang lebih umum
                    const notificationItem = detailButton.closest('li') ||
                        detailButton.closest('.notification-item') ||
                        detailButton.closest('.dropdown-item');

                    // Log untuk debugging
                    console.log('Elemen notifikasi yang ditemukan:', notificationItem);

                    if (notificationItem) {
                        // Tambahkan animasi fadeOut sebelum menghapus item
                        notificationItem.style.transition = 'opacity 0.3s';
                        notificationItem.style.opacity = '0';

                        // Sembunyikan notifikasi
                        setTimeout(() => {
                            notificationItem.remove();

                            // Perbarui counter notifikasi jika ada
                            updateNotificationCounter();

                            // Tandai sebagai dibaca di server
                            markNotificationAsRead(id);
                        }, 300);
                    }
                }
            });
        });

        function updateNotificationCounter() {
            const notifCounter = document.getElementById('notification-counter') ||
                document.querySelector('.notification-counter');

            if (notifCounter) {
                let currentCount = parseInt(notifCounter.textContent);
                if (!isNaN(currentCount) && currentCount > 0) {
                    currentCount--;
                    notifCounter.textContent = currentCount;

                    // Sembunyikan counter jika tidak ada notifikasi
                    if (currentCount === 0) {
                        notifCounter.classList.add('hidden');
                    }
                }
            }
        }

        function markNotificationAsRead(id) {
            // Log untuk debugging
            console.log('Menandai notifikasi sebagai telah dibaca:', id);

            // Gunakan $.ajax karena kodenya menggunakan jQuery
            $.ajax({
                url: '/admin/notifications/mark-as-read/' + id,
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log('Notifikasi berhasil ditandai sebagai dibaca');
                },
                error: function(xhr, status, error) {
                    console.error('Gagal menandai notifikasi:', error);
                }
            });
        }

        function showDetailModal(id) {
            // Fetch the details dynamically (if needed)
            fetch(`/admin/tukar-jadwal/detail/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Populate modal content
                    alert(`Detail: ${data.pengaju} ↔ ${data.penerima}`); // Replace with modal code
                });
        }
    </script>
</x-app-layout>
