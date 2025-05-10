<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Lokasi Kantor') }}
            </h2>
            <label class="btn btn-primary btn-sm" for="create_modal">Tambah Data</label>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <div class="w-full overflow-x-auto rounded-md bg-slate-200 px-10">
            <table id="tabelPresensi"
                class="table mb-4 w-full border-collapse items-center border-gray-200 align-top dark:border-white/40">
                <thead class="text-sm text-black">
                    <tr>
                        <th></th>
                        <th>Kota</th>
                        <th>Alamat</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Radius (meter)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lokasiKantor as $value => $item)
                        <tr class="hover">
                            <td class="font-bold">{{ $lokasiKantor->firstItem() + $value }}</td>
                            <td>{{ $item->kota }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->latitude }}</td>
                            <td>{{ $item->longitude }}</td>
                            <td>{{ $item->radius }} m</td>
                            <td>
                                <div class="form-control">
                                    <label class="cursor-pointer label justify-start">
                                        <input type="checkbox" class="toggle toggle-success toggle-status"
                                            data-id="{{ $item->id }}" {{ $item->is_used ? 'checked' : '' }} />
                                        <span class="label-text ml-2"
                                            id="status-text-{{ $item->id }}">{{ $item->is_used ? 'Aktif' : 'Tidak Aktif' }}</span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <label class="btn btn-warning btn-sm" for="edit_button"
                                    onclick="return edit_button('{{ $item->id }}')">
                                    <i class="ri-pencil-fill"></i>
                                </label>
                                <label class="btn btn-error btn-sm"
                                    onclick="return delete_button('{{ $item->id }}', '{{ $item->kota }}')">
                                    <i class="ri-delete-bin-line"></i>
                                </label>
                                <button class="btn btn-info btn-sm"
                                    onclick="showMap('{{ $item->id }}', '{{ $item->kota }}', '{{ $item->latitude }}', '{{ $item->longitude }}', '{{ $item->radius }}')">
                                    <i class="ri-map-pin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mx-3 mb-5">
                {{ $lokasiKantor->links() }}
            </div>
        </div>
    </div>

    {{-- Awal Modal Create --}}
    <input type="checkbox" id="create_modal" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <div class="mb-3 flex justify-between">
                <h3 class="text-lg font-bold">Tambah {{ $title }}</h3>
                <label for="create_modal" class="cursor-pointer">
                    <i class="ri-close-large-fill"></i>
                </label>
            </div>
            <div>
                <form action="{{ route('admin.lokasi-kantor.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <button type="reset" class="btn btn-neutral btn-sm">Reset</button>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Kota<span class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="text" name="kota" placeholder="Kota"
                            class="input input-bordered w-full text-blue-700" value="{{ old('kota') }}" required />
                        @error('kota')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Alamat<span class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <textarea name="alamat" placeholder="Alamat" class="textarea textarea-bordered w-full text-blue-700">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Latitude<span
                                        class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="text" name="latitude" placeholder="Latitude"
                            class="input input-bordered w-full text-blue-700" value="{{ old('latitude') }}" required />
                        @error('latitude')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Longitude<span
                                        class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="text" name="longitude" placeholder="Longitude"
                            class="input input-bordered w-full text-blue-700" value="{{ old('longitude') }}"
                            required />
                        @error('longitude')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Radius (meter)<span
                                        class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <input type="number" min="0" name="radius" placeholder="Radius dalam meter"
                            class="input input-bordered w-full text-blue-700" value="{{ old('radius') }}" required />
                        @error('radius')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <div>
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Status<span
                                        class="text-red-500">*</span></span>
                            </span>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Aktif</span>
                                <input type="radio" name="is_used" value='1'
                                    class="radio checked:bg-success" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Tidak Aktif</span>
                                <input type="radio" name="is_used" value='0' class="radio checked:bg-error"
                                    checked />
                            </label>
                        </div>
                        @error('is_used')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success mt-3 w-full text-white">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    {{-- Akhir Modal Create --}}

    {{-- Awal Modal Edit --}}
    <input type="checkbox" id="edit_button" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <div class="mb-3 flex justify-between">
                <h3 class="text-lg font-bold">Ubah {{ $title }}</h3>
                <label for="edit_button" class="cursor-pointer">
                    <i class="ri-close-large-fill"></i>
                </label>
            </div>
            <div>
                <form action="{{ route('admin.lokasi-kantor.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="id" hidden>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Kota<span class="text-red-500">*</span></span>
                                <span class="label-text-alt" id="loading_edit1"></span>
                            </span>
                        </div>
                        <input type="text" name="kota" placeholder="Kota"
                            class="input input-bordered w-full text-blue-700" value="{{ old('kota') }}" required />
                        @error('kota')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Alamat<span
                                        class="text-red-500">*</span></span>
                                <span class="label-text-alt" id="loading_edit2"></span>
                            </span>
                        </div>
                        <textarea name="alamat" placeholder="Alamat" class="textarea textarea-bordered w-full text-blue-700">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Latitude<span
                                        class="text-red-500">*</span></span>
                                <span class="label-text-alt" id="loading_edit3"></span>
                            </span>
                        </div>
                        <input type="text" name="latitude" placeholder="Latitude"
                            class="input input-bordered w-full text-blue-700" value="{{ old('latitude') }}"
                            required />
                        @error('latitude')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Longitude<span
                                        class="text-red-500">*</span></span>
                                <span class="label-text-alt" id="loading_edit4"></span>
                            </span>
                        </div>
                        <input type="text" name="longitude" placeholder="Longitude"
                            class="input input-bordered w-full text-blue-700" value="{{ old('longitude') }}"
                            required />
                        @error('longitude')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Radius (meter)<span
                                        class="text-red-500">*</span></span>
                                <span class="label-text-alt" id="loading_edit6"></span>
                            </span>
                        </div>
                        <input type="number" min="0" name="radius" placeholder="Radius dalam meter"
                            class="input input-bordered w-full text-blue-700" value="{{ old('radius') }}" required />
                        @error('radius')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </label>
                    <div>
                        <div class="label">
                            <span class="label-text font-semibold">
                                <span class="label-text font-semibold">Status<span
                                        class="text-red-500">*</span></span>
                                <span class="label-text-alt" id="loading_edit5"></span>
                            </span>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Aktif</span>
                                <input type="radio" name="is_used" value='1'
                                    class="radio checked:bg-success" />
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Tidak Aktif</span>
                                <input type="radio" name="is_used" value='0' class="radio checked:bg-error"
                                    checked />
                            </label>
                        </div>
                        @error('is_used')
                            <div class="label">
                                <span class="label-text-alt text-sm text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-warning mt-3 w-full text-slate-700">Perbarui</button>
                </form>
            </div>
        </div>
    </div>
    {{-- Akhir Modal Edit --}}

    {{-- Awal Modal Map - Menggunakan dialog DaisyUI --}}
    <dialog id="map_modal" class="modal">
        <div class="modal-box max-w-3xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold mb-4" id="map_title">Lokasi Kantor</h3>
            <div id="map" class="w-full h-96 rounded-lg"></div>
        </div>
    </dialog>
    {{-- Akhir Modal Map --}}

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script>
        @if (session()->has('success'))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#007bff', // Warna biru untuk tombol OK
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has('error'))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#007bff', // Warna biru untuk tombol OK
                confirmButtonText: 'OK',
            });
        @endif

        // Variabel map global
        let mapInstance = null;

        // Fungsi untuk toggle status
        $(document).ready(function() {
            $('.toggle-status').on('change', function() {
                const id = $(this).data('id');
                const isChecked = $(this).prop('checked');
                const $this = $(this); // Simpan referensi untuk digunakan dalam callback
                const $labelText = $('#status-text-' + id);

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.lokasi-kantor.toggle-status') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id
                    },
                    success: function(response) {
                        if (response.success) {
                            // Perbarui teks label segera
                            $labelText.text(response.is_used ? 'Aktif' : 'Tidak Aktif');

                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#007bff',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // Kembalikan toggle ke status sebelumnya jika terjadi kesalahan
                            $this.prop('checked', !isChecked);
                            $labelText.text(!isChecked ? 'Aktif' : 'Tidak Aktif');

                            Swal.fire({
                                title: 'Gagal',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#007bff',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Kembalikan toggle ke status sebelumnya jika terjadi kesalahan
                        $this.prop('checked', !isChecked);
                        $labelText.text(!isChecked ? 'Aktif' : 'Tidak Aktif');

                        Swal.fire({
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengubah status',
                            icon: 'error',
                            confirmButtonColor: '#007bff',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });

        // Fungsi untuk menampilkan peta
        function showMap(id, kota, lat, lng, radius) {
            console.log("showMap dipanggil dengan:", {
                id,
                kota,
                lat,
                lng,
                radius
            });

            // Pastikan modal ada di DOM
            const modal = document.getElementById('map_modal');
            if (!modal) {
                console.error("Modal tidak ditemukan");
                return;
            }

            // Pastikan semua parameter tersedia
            if (!lat || !lng || !radius) {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Data lokasi tidak lengkap',
                    icon: 'error',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Pastikan nilai adalah numerik
            lat = parseFloat(lat);
            lng = parseFloat(lng);
            radius = parseFloat(radius);

            // Set judul modal
            document.getElementById('map_title').textContent = 'Lokasi Kantor: ' + kota;

            // Buka modal (DaisyUI dialog)
            modal.showModal();

            // Hapus map sebelumnya jika ada
            if (mapInstance) {
                mapInstance.remove();
                mapInstance = null;
            }

            // Inisialisasi map baru dengan timeout untuk memastikan modal sudah terbuka
            setTimeout(function() {
                try {
                    // Initialize the map
                    mapInstance = L.map('map').setView([lat, lng], 15);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(mapInstance);

                    // Tambahkan marker
                    const marker = L.marker([lat, lng]).addTo(mapInstance);
                    marker.bindPopup("<b>Kantor: " + kota + "</b><br>Lokasi: " + lat + ", " + lng).openPopup();

                    // Tambahkan circle radius
                    const circle = L.circle([lat, lng], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.3,
                        radius: radius
                    }).addTo(mapInstance);

                    // Fit bounds to circle
                    mapInstance.fitBounds(circle.getBounds());

                    // Invalidate size karena modal mungkin mempengaruhi rendering
                    mapInstance.invalidateSize();

                    console.log("Peta berhasil dibuat");
                } catch (error) {
                    console.error("Error saat membuat peta:", error);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memuat peta: ' + error.message,
                        icon: 'error',
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'OK'
                    });
                }
            }, 300);
        }

        function edit_button(id) {
            // Loading effect start
            let loading = `<span class="loading loading-dots loading-md text-purple-600"></span>`;
            $("#loading_edit1").html(loading);
            $("#loading_edit2").html(loading);
            $("#loading_edit3").html(loading);
            $("#loading_edit4").html(loading);
            $("#loading_edit5").html(loading);
            $("#loading_edit6").html(loading);

            $.ajax({
                type: "get",
                url: "{{ route('admin.lokasi-kantor.edit') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(data) {
                    // console.log(data);
                    let items = [];
                    $.each(data, function(key, val) {
                        items.push(val);
                    });

                    $("input[name='id']").val(items[0]);
                    $("input[name='kota']").val(items[1]);
                    $("textarea[name='alamat']").html(items[2]);
                    $("input[name='latitude']").val(items[3]);
                    $("input[name='longitude']").val(items[4]);
                    $("input[name='radius']").val(items[5]);
                    if (items[6] == 1) {
                        $("input[name='is_used'][value='1']").prop('checked', true);
                    } else if (items[6] == 0) {
                        $("input[name='is_used'][value='0']").prop('checked', true);
                    }

                    // Loading effect end
                    loading = "";
                    $("#loading_edit1").html(loading);
                    $("#loading_edit2").html(loading);
                    $("#loading_edit3").html(loading);
                    $("#loading_edit4").html(loading);
                    $("#loading_edit5").html(loading);
                    $("#loading_edit6").html(loading);
                }
            });
        }

        function delete_button(id, kota) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data yang dihapus tidak dapat dipulihkan kembali!</p>" +
                    "<div class='divider'></div>" +
                    "<div class='flex flex-col'>" +
                    "<b>Lokasi: " + kota + "</b>" +
                    "</div>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#007bff', // Warna biru untuk tombol OK
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('admin.lokasi-kantor.delete') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#007bff', // Warna biru untuk tombol OK
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.responseJSON.message
                            })
                        }
                    });
                }
            });
        }
    </script>

    <style>
        .swal2-confirm {
            background-color: #007bff !important;
            /* Warna biru untuk tombol OK */
            color: white !important;
            /* Teks tombol OK menjadi putih */
            border-color: #007bff !important;
            /* Border tombol OK menjadi biru */
        }
    </style>
</x-app-layout>
