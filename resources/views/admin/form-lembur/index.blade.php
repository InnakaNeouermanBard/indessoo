<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Data Form Lembur') }}
            </h2>
            <label class="btn btn-primary btn-sm" for="create_modal">Tambah Karyawan</label>
        </div>
    </x-slot>

    <div class="container mx-auto px-5 pt-5">
        <form action="{{ route('form-lembur.index') }}" method="get" class="my-3">
            <div class="flex w-full flex-wrap gap-2 md:flex-nowrap">
                <input type="text" name="cari_nik" placeholder="Cari NIK" class="input input-bordered w-full"
                    value="{{ request()->cari_nik }}" />
                <button type="submit" class="btn btn-success w-full md:w-14">
                    <i class="ri-search-2-line text-lg text-white"></i>
                </button>
            </div>
        </form>

        <div class="w-full overflow-x-auto rounded-md bg-slate-200 px-10">
            <table class="table mb-4 w-full">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Overtime</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $value => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>{{ $item->nama_karyawan }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->overtime }}</td>
                            <td>
                                <label class="btn btn-warning btn-sm" for="edit_button"
                                    onclick="return edit_button('{{ $item->id }}')">
                                    <i class="ri-pencil-fill"></i>
                                </label>
                                <label class="btn btn-error btn-sm"
                                    onclick="return delete_button('{{ $item->id }}', '{{ $item->nama_lengkap }}')">
                                    <i class="ri-delete-bin-line"></i>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <input type="checkbox" id="create_modal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box relative">
            <!-- Tombol Batal di pojok kanan -->
            <label for="create_modal"
                class="absolute top-0 right-0 mt-2 mr-2 cursor-pointer text-lg btn btn-secondary text-white">
                Batal
            </label>

            <h3 class="text-lg font-bold">Tambah Lembur</h3>

            <form action="{{ route('form-lembur.store') }}" method="POST">
                @csrf

                <label class="form-control w-full">
                    <span class="label-text">Pilih Karyawan</span>
                    <select name="nik" class="input input-bordered w-full" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach ($niks as $karyawan)
                            <option value="{{ $karyawan->nik }}">
                                {{ $karyawan->nama_lengkap }} ({{ $karyawan->nik }})
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="form-control w-full">
                    <span class="label-text">Tanggal</span>
                    <input type="date" name="tanggal" class="input input-bordered w-full" required />
                </label>

                <label class="form-control w-full">
                    <span class="label-text">Overtime</span>
                    <input type="number" name="overtime" class="input input-bordered w-full" required />
                </label>

                <button type="submit" class="btn btn-success mt-3 w-full">Simpan</button>
            </form>


        </div>
    </div>


    {{-- Modal Edit --}}
    <!-- Modal Edit -->
    <input type="checkbox" id="edit_button" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box relative">
            <label for="edit_button"
                class="absolute top-0 right-0 mt-2 mr-2 cursor-pointer text-lg btn btn-secondary text-white">
                Batal
            </label>
            <h3 class="text-lg font-bold mb-2">Ubah Lembur</h3>

            <form id="form_edit" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

                <label class="form-control w-full">
                    <span class="label-text">NIK</span>
                    <input type="text" name="nik" id="edit_nik" class="input input-bordered w-full" readonly
                        required />
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Nama Karyawan</span>
                    <input type="text" name="nama_karyawan" id="edit_nama_karyawan"
                        class="input input-bordered w-full" readonly required />
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Tanggal</span>
                    <input type="date" name="tanggal" id="edit_tanggal" class="input input-bordered w-full"
                        required />
                </label>

                <label class="form-control w-full mt-2">
                    <span class="label-text">Overtime</span>
                    <input type="number" name="overtime" id="edit_overtime" class="input input-bordered w-full"
                        required />
                </label>

                <button type="submit" class="btn btn-warning mt-4 w-full">Perbarui</button>
            </form>
        </div>
    </div>


    <script>
        function edit_button(id) {
            $.ajax({
                type: "GET",
                url: "{{ url('admin/form-lembur') }}/" + id + "/edit",
                success: function(response) {
                    // Set data ke form input
                    $('#edit_id').val(response.id);
                    $('#edit_nik').val(response.nik);
                    $('#edit_nama_karyawan').val(response.nama_karyawan);
                    $('#edit_tanggal').val(response.tanggal);
                    $('#edit_overtime').val(response.overtime);

                    // Set action form update
                    $('#form_edit').attr('action', '/admin/form-lembur/' + response.id);

                    // Tampilkan modal
                    document.getElementById('edit_button').checked = true;
                },
                error: function(xhr) {
                    alert('Gagal mengambil data lembur.');
                }
            });

            return false;
        }

        function delete_button(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak bisa dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('form-lembur.delete') }}", {
                        _token: "{{ csrf_token() }}",
                        id: id
                    }, function(response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        location.reload();
                    });
                }
            });
        }
    </script>
    <script>
        // JavaScript untuk menangani perubahan pilihan Karyawan dan update otomatis data NIK
        document.getElementById('karyawan_id').addEventListener('change', function() {
            var nik = this.value;

            if (nik) {
                $.ajax({
                    url: "{{ route('form-lembur.getKaryawanData', '') }}/" + nik,
                    type: "GET",
                    success: function(response) {
                        if (response) {
                            document.getElementById('nik').value = response.nik;
                            document.getElementById('nama_lengkap').value = response.nama_lengkap;
                        } else {
                            document.getElementById('nik').value = '';
                            document.getElementById('nama_lengkap').value = '';
                            alert("Data karyawan tidak ditemukan.");
                        }
                    },
                    error: function() {
                        alert("Terjadi kesalahan dalam mengambil data.");
                    }
                });
            } else {
                document.getElementById('nik').value = '';
                document.getElementById('nama_lengkap').value = '';
            }
        });
    </script>
</x-app-layout>
