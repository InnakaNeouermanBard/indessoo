<x-app-layout>
    {{-- index admin presensi  --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Monitoring Presensi') }}
            </h2>
        </div>
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

            <!-- Tombol Notifikasi -->
            {{-- Notification Component (dapat dimasukkan ke file partials atau komponen) --}}

            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost btn-circle relative">
                    <div class="indicator">
                        <i class="ri-notification-3-line text-xl"></i>
                        @if ($countTukarJadwalToday > 0)
                            <span
                                class="absolute top-0 right-0 h-5 w-5 rounded-full bg-red-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ $countTukarJadwalToday }}
                            </span>
                        @endif
                    </div>
                </label>
                <div tabindex="0"
                    class="dropdown-content menu p-0 mt-2 shadow-lg bg-base-100 rounded-box w-80 max-h-[80vh] overflow-y-auto">
                    <div class="bg-primary text-white px-4 py-3 flex items-center justify-between">
                        <h3 class="font-bold text-lg">Notifikasi</h3>
                        <span class="badge badge-sm">{{ $countTukarJadwalToday }} baru</span>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @if ($recentExchanges->count() > 0)
                            @foreach ($recentExchanges as $exchange)
                                <div class="p-4 {{ $exchange->created_at->isToday() ? 'bg-blue-50' : '' }}">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <i class="ri-exchange-line text-lg text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $exchange->pengaju->nama_lengkap }} ↔
                                                {{ $exchange->penerima->nama_lengkap }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Tanggal pertukaran:
                                                {{ \Carbon\Carbon::parse($exchange->tanggal_pengajuan)->format('d M Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $exchange->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <a href="#" class="btn-detail text-blue-600 hover:text-blue-800"
                                            data-id="{{ $exchange->id }}">
                                            {{-- <i class="ri-eye-line"></i> --}}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center text-gray-500">
                                <div
                                    class="bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center h-12 w-12">
                                    <i class="ri-inbox-line text-xl text-gray-400"></i>
                                </div>
                                <p>Tidak ada notifikasi baru</p>
                            </div>
                        @endif
                    </div>

                    <div class="p-3 bg-gray-50 border-t">
                        <a href="{{ route('tukar-jadwal.riwayat') }}" class="btn btn-primary btn-block btn-sm">
                            Lihat Semua Riwayat
                        </a>
                    </div>
                </div>
            </div>



            <form action="{{ route('admin.monitoring-presensi') }}" method="get" enctype="multipart/form-data"
                class="my-3">
                <div class="flex w-full flex-wrap gap-2 md:flex-nowrap">
                    <input type="date" name="tanggal_presensi" placeholder="Pencarian"
                        class="input input-bordered w-full"
                        value="{{ request()->tanggal_presensi ? request()->tanggal_presensi : Carbon\Carbon::now()->format('Y-m-d') }}" />
                    <button type="submit" class="btn btn-success w-full md:w-14">
                        <i class="ri-search-2-line text-lg text-white"></i>
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
                            <td class="text-slate-500 dark:text-slate-300">
                                @if ($item->jam_masuk > Carbon\Carbon::make('08:00:00')->format('H:i:s'))
                                    @php
                                        $masuk = Carbon\Carbon::make($item->jam_masuk);
                                        $batas = Carbon\Carbon::make('08:00:00');
                                        $diff = $masuk->diff($batas);
                                        if ($diff->format('%h') != 0) {
                                            $selisih = $diff->format('%h jam %I menit');
                                        } else {
                                            $selisih = $diff->format('%I menit');
                                        }
                                    @endphp
                                    <div class="w-fit rounded-md bg-error p-1 text-white">Terlambat {{ $selisih }}
                                    </div>
                                @else
                                    <div class="w-fit rounded-md bg-success p-1 text-white">Tepat Waktu</div>
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
            const detailButtons = document.querySelectorAll('.dropdown-content .btn-detail');

            detailButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent dropdown from closing
                    const id = this.getAttribute('data-id');

                    // Show the details of the notification
                    showDetailModal(id);
                });
            });
        });

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

    <!-- Modal -->
    <div id="detailModal" class="modal">
        <div class="modal-box relative">
            <h2 class="text-xl font-bold mb-4" id="modalTitle">Detail Notifikasi</h2>
            <div id="modalContent"></div>
            <div class="modal-action">
                <button class="btn" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('detailModal').classList.remove('modal-open');
        }
    </script>

    <style>
        /* Dropdown yang posisinya selalu di tengah */
        .dropdown-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Menjaga posisi di tengah */
            z-index: 9999;
            /* Pastikan di atas elemen lain */
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background-color: rgba(0, 0, 0, 0.5);
            /* Transparan hitam untuk latar belakang */
            width: 80%;
            max-width: 500px;
        }

        .modal-open {
            display: block;
        }

        .modal-box {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
        }
    </style>
</x-app-layout>
